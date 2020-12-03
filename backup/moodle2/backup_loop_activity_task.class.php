<?php
 
require_once($CFG->dirroot . '/mod/loop/backup/moodle2/backup_loop_stepslib.php'); // Because it exists (must)
require_once($CFG->dirroot . '/mod/loop/backup/moodle2/backup_loop_settingslib.php'); // Because it exists (optional)
 
/**
 * loop backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
class backup_loop_activity_task extends backup_activity_task {
 
    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }
 
    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // loop only has one structure step
    	$this->add_step(new backup_loop_activity_structure_step('loop_structure', 'loop.xml'));
    }
 
	/**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     */
    static public function encode_content_links($content) {
        global $CFG;
 
        $base = preg_quote($CFG->wwwroot,"/");
 
        // Link to the list of loops
        $search="/(".$base."\/mod\/loop\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@LOOPINDEX*$2@$', $content);
 
        // Link to loop view by moduleid
        $search="/(".$base."\/mod\/loop\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@LOOPVIEWBYID*$2@$', $content);
 
        return $content;
    }
}