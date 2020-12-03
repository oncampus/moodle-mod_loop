<?php 
/**
 * Structure step to restore one loop activity
 */
class restore_loop_activity_structure_step extends restore_activity_structure_step {
 
    protected function define_structure() {
 
        $paths = array();
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