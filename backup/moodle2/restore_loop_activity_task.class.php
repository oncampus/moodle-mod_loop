<?php 
/**
 * loop restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */

require_once($CFG->dirroot . '/mod/loop/backup/moodle2/restore_loop_stepslib.php'); // Because it exists (must)

class restore_loop_activity_task extends restore_activity_task {

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
		$this->add_step(new restore_loop_activity_structure_step('loop_structure', 'loop.xml'));
	}

	/**
	 * Define the contents in the activity that must be
	 * processed by the link decoder
	 */
	static public function define_decode_contents() {
		$contents = array();

		$contents[] = new restore_decode_content('loop', array('intro'), 'loop');

		return $contents;
	}

	/**
	 * Define the decoding rules for links belonging
	 * to the activity to be executed by the link decoder
	 */
	static public function define_decode_rules() {
		$rules = array();

		$rules[] = new restore_decode_rule('LOOPVIEWBYID', '/mod/loop/view.php?id=$1', 'course_module');
		$rules[] = new restore_decode_rule('LOOPINDEX', '/mod/loop/index.php?id=$1', 'course');

		return $rules;

	}

	/**
	 * Define the restore log rules that will be applied
	 * by the {@link restore_logs_processor} when restoring
	 * loop logs. It must return one array
	 * of {@link restore_log_rule} objects
	 */
	static public function define_restore_log_rules() {
		$rules = array();

		$rules[] = new restore_log_rule('loop', 'add', 'view.php?id={course_module}', '{loop}');
		$rules[] = new restore_log_rule('loop', 'update', 'view.php?id={course_module}', '{loop}');
		$rules[] = new restore_log_rule('loop', 'view', 'view.php?id={course_module}', '{loop}');
		$rules[] = new restore_log_rule('loop', 'choose', 'view.php?id={course_module}', '{loop}');
		$rules[] = new restore_log_rule('loop', 'choose again', 'view.php?id={course_module}', '{loop}');
		

		return $rules;
	}

	/**
	 * Define the restore log rules that will be applied
	 * by the {@link restore_logs_processor} when restoring
	 * course logs. It must return one array
	 * of {@link restore_log_rule} objects
	 *
	 * Note this rules are applied when restoring course logs
	 * by the restore final task, but are defined here at
	 * activity level. All them are rules not linked to any module instance (cmid = 0)
	 */
	static public function define_restore_log_rules_for_course() {
		$rules = array();

		// Fix old wrong uses (missing extension)
		$rules[] = new restore_log_rule('loop', 'view all', 'index?id={course}', null, null, null, 'index.php?id={course}');
		$rules[] = new restore_log_rule('loop', 'view all', 'index.php?id={course}', null);

		return $rules;
	}

}