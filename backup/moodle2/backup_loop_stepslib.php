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
 * Define all the backup steps that will be used by the backup_loop_activity_task
 * @package mod_loop
 */

/**
 * Define the complete loop structure for backup, with file and id annotations
 * @package mod_loop
 */
class backup_loop_activity_structure_step extends backup_activity_structure_step {
    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated

        $loop = new backup_nested_element('loop', ['id'], [
                'course', 'name', 'intro', 'introformat', 'url',
                'chapter', 'page', 'personalized_access', 'student_role_allocation',
                'teacher_role_allocation', 'theme', 'externalid', 'timecreated', 'timemodified']);

        // Build the tree

        // Define sources

        $loop->set_source_table('loop', ['id' => backup::VAR_ACTIVITYID]);

        // Define id annotations

        // Define file annotations

        // Return the root element (loop), wrapped into standard activity structure

        return $this->prepare_activity_structure($loop);
    }
}
