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
 * Structure step to restore one loop activity
 *
 * @package   mod_loop
 * @copyright 2025 oncampus GmbH
 * @author    Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_loop_activity_structure_step extends restore_activity_structure_step {
    /**
     * Define the structure of the restore.
     *
     * @return restore_path_element
     * @throws base_step_exception
     */
    protected function define_structure(): array {

        $paths = [];
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('loop', '/activity/loop');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the loop.
     *
     * @param array $data
     * @throws base_step_exception
     * @throws dml_exception
     */
    protected function process_loop($data): void {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Insert the loop record.
        $newitemid = $DB->insert_record('loop', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * After execute.
     */
    protected function after_execute(): void {
        // Add loop related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_loop', 'intro', null);
    }
}
