<?php
 
/**
 * Define all the backup steps that will be used by the backup_loop_activity_task
 */

/**
 * Define the complete loop structure for backup, with file and id annotations
 */
class backup_loop_activity_structure_step extends backup_activity_structure_step {

	protected function define_structure() {

		// To know if we are including userinfo
		$userinfo = $this->get_setting_value('userinfo');

		// Define each element separated
		
		$loop = new backup_nested_element('loop', array('id'), array(
				'course', 'name', 'intro', 'introformat', 'url',
				'chapter', 'page', 'personalized_access', 'student_role_allocation',
				'teacher_role_allocation', 'theme', 'externalid', 'timecreated','timemodified'));

		// Build the tree

		// Define sources
		
		$loop->set_source_table('loop', array('id' => backup::VAR_ACTIVITYID));

		// Define id annotations

		// Define file annotations

		// Return the root element (loop), wrapped into standard activity structure
		
		return $this->prepare_activity_structure($loop);

	}
}