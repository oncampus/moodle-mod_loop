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
 * @package mod_loop
 */
class restore_loop_activity_structure_step extends restore_activity_structure_step {
    protected function define_structure() {

        $paths = [];
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('loop', '/activity/loop');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_loop($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();


        // insert the loop record
        $newitemid = $DB->insert_record('loop', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }


    protected function after_execute() {
        // Add loop related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_loop', 'intro', null);
    }
}
