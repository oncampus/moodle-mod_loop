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
 * loop restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 * @package mod_loop
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
    public static function define_decode_contents() {
        $contents = [];

        $contents[] = new restore_decode_content('loop', ['intro'], 'loop');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    public static function define_decode_rules() {
        $rules = [];

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
    public static function define_restore_log_rules() {
        $rules = [];

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
    public static function define_restore_log_rules_for_course() {
        $rules = [];

        // Fix old wrong uses (missing extension)
        $rules[] = new restore_log_rule('loop', 'view all', 'index?id={course}', null, null, null, 'index.php?id={course}');
        $rules[] = new restore_log_rule('loop', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
