<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Scheduled task to get allowed loops from external systems
 *
 * @package     mod_loop
 * @copyright   2025 oncampus GmbH
 * @author      Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_loop\task;

use coding_exception;
use core\task\scheduled_task;
use dml_exception;
use Exception;
use stdClass;

/**
 * Scheduled task to get allowed loops from external systems.
 */
class get_allowed_loops_task extends scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     * @throws coding_exception
     */
    public function get_name(): string {
        return get_string('get_allowed_loops_task', 'mod_loop');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     *
     * @throws Exception When the task fails
     */
    public function execute(): void {
        try {
            $this->get_allowed_loops();
        } catch (Exception $e) {
            // Log the error and re-throw for retry mechanism.
            debugging('Loop task failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
            throw $e;
        }
    }

    /**
     * Retrieves and processes the list of allowed LOOPs from an external server, updating or inserting
     * records in the database accordingly. Deletes outdated LOOP entries from the database.
     *
     * Uses a token to fetch data from a remote URL, processes the data to ensure it matches the
     * local database, and updates or inserts records as needed. Additionally, it identifies obsolete
     * entries in the database and removes them.
     *
     * @return bool Returns true after successfully processing the allowed LOOPs.
     * @throws dml_exception
     * @throws Exception
     */
    private function get_allowed_loops(): bool {
        global $DB;

        mtrace('Getting list of allowed LOOPs from Moodalis');

        $token = get_config('mod_loop', 'token');

        $cha = curl_init();
        curl_setopt($cha, CURLOPT_URL, ('https://moodalis.oncampus.de/files/lms_loops.php?token=' . $token));
        curl_setopt($cha, CURLOPT_ENCODING, "UTF-8");
        curl_setopt($cha, CURLOPT_RETURNTRANSFER, true);
        $jsonresult = curl_exec($cha);

        if (!$jsonresult) {
            throw new Exception("Error getting data from server: " . curl_error($cha));
        }

        $loops = json_decode($jsonresult, true);

        $processedloops = [];

        foreach ($loops as $loop => $loopdata) {
            mtrace("Checking LOOP " . $loop);
            unset($loopsystem);

            if ($DB->record_exists('loop_systems', ['externalid' => $loop])) {
                mtrace("LOOP already exisits");

                $loopsystem = $DB->get_record('loop_systems', ['externalid' => $loop]);
                $processedloops[] = $loopsystem->id;

                if (
                    ($loopsystem->name != $loopdata['name']) ||
                    ($loopsystem->url != $loopdata['url']) ||
                    ($loopsystem->personalized_access != $loopdata['personalized_access']) ||
                    ($loopsystem->student_role_allocation != $loopdata['student_role_allocation']) ||
                    ($loopsystem->teacher_role_allocation != $loopdata['teacher_role_allocation']) ||
                    ($loopsystem->allowed_themes != json_encode($loopdata['allowed_themes']))
                ) {
                    $updateloopsystem = new stdClass();
                    $updateloopsystem->id = $loopsystem->id;
                    $updateloopsystem->externalid = $loop;
                    $updateloopsystem->name = $loopdata['name'];
                    $updateloopsystem->url = $loopdata['url'];
                    $updateloopsystem->personalized_access = $loopdata['personalized_access'];
                    $updateloopsystem->student_role_allocation = $loopdata['student_role_allocation'];
                    $updateloopsystem->teacher_role_allocation = $loopdata['teacher_role_allocation'];
                    $updateloopsystem->allowed_themes = json_encode($loopdata['allowed_themes']);
                    $updateloopsystem->timemodified = time();
                    $DB->update_record('loop_systems', $updateloopsystem);

                    mtrace("LOOP updated");
                } else {
                    mtrace("no update neccessary");
                }
            } else {
                mtrace("New LOOP");

                $newloopsystem = new stdClass();
                $newloopsystem->externalid = $loop;
                $newloopsystem->name = $loopdata['name'];
                $newloopsystem->url = $loopdata['url'];
                $newloopsystem->personalized_access = $loopdata['personalized_access'];
                $newloopsystem->student_role_allocation = $loopdata['student_role_allocation'];
                $newloopsystem->teacher_role_allocation = $loopdata['teacher_role_allocation'];
                $newloopsystem->allowed_themes = json_encode($loopdata['allowed_themes']);
                $newloopsystem->timemodified = time();
                $newloopsystem->timecreated = time();
                $newid = $DB->insert_record('loop_systems', $newloopsystem);
                $processedloops[] = $newid;
                mtrace("inserted into Database: " . $newid);
            }
        }

        if (!empty($processedloops)) {
            $placeholders = str_repeat('?,', count($processedloops) - 1) . '?';
            $sql = "SELECT * FROM {loop_systems} WHERE id NOT IN ($placeholders)";
            $todelete = $DB->get_records_sql($sql, $processedloops);
        } else {
            $todelete = [];
        }

        if (is_array($todelete)) {
            foreach ($todelete as $delloop) {
                mtrace("delete LOOP with id: " . $delloop->id);
                $DB->delete_records('loop_systems', ['id' => $delloop->id]);
            }
        }

        return true;
    }
}
