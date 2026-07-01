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
 * Define the complete loop structure for backup, with file and id annotations
 * @package     mod_loop
 * @copyright   2025 oncampus GmbH
 * @author      Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_loop_activity_structure_step extends backup_activity_structure_step {
    /**
     * Define the structure of the backup.
     *
     * @return backup_nested_element
     * @throws base_step_exception
     * @throws base_element_struct_exception
     */
    protected function define_structure(): backup_nested_element {

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.

        $loop = new backup_nested_element('loop', ['id'], [
                'course', 'name', 'intro', 'introformat', 'url',
                'chapter', 'page', 'personalized_access', 'student_role_allocation',
                'teacher_role_allocation', 'theme', 'externalid', 'timecreated', 'timemodified']);

        // Build the tree.
        // Define sources.

        $loop->set_source_table('loop', ['id' => backup::VAR_ACTIVITYID]);

        // Define id annotations.
        // Define file annotations.
        // Return the root element (loop), wrapped into standard activity structure.

        return $this->prepare_activity_structure($loop);
    }
}
