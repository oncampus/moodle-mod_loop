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
 * Loop backup task that provides all the settings and steps to perform one
 * complete backup of the activity.
 *
 * @package   mod_loop
 * @copyright 2025 oncampus GmbH
 * @author    Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/loop/backup/moodle2/backup_loop_stepslib.php'); // Because it exists (must).
require_once($CFG->dirroot . '/mod/loop/backup/moodle2/backup_loop_settingslib.php'); // Because it exists (optional).

/**
 * Loop backup task that provides all the settings and steps to perform one
 * complete backup of the activity.
 */
class backup_loop_activity_task extends backup_activity_task {
    /**
     * Define (add) particular settings this activity can have.
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have.
     * @throws base_task_exception
     */
    protected function define_my_steps(): void {
        // Loop only has one structure step.
        $this->add_step(new backup_loop_activity_structure_step('loop_structure', 'loop.xml'));
    }

    /**
     * Code the transformations to perform in the activity in order to get transportable (encoded) links.
     */
    public static function encode_content_links($content): array|string|null {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, "/");

        // Link to the list of loops.
        $search = "/(" . $base . "\/mod\/loop\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@LOOPINDEX*$2@$', $content);

        // Link to loop view by moduleid.
        $search = "/(" . $base . "\/mod\/loop\/view.php\?id\=)([0-9]+)/";
        return preg_replace($search, '$@LOOPVIEWBYID*$2@$', $content);
    }
}
