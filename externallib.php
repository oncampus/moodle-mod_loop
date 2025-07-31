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
 * @package mod_loop
 * @author  Marc Vorreiter <marc.vorreiter@th-luebeck.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;

require_once($CFG->dirroot . '/mod/loop/locallib.php');

class mod_loop_external extends external_api {
    public static function get_structure_parameters() {
        return new external_function_parameters([
                'url' => new external_value(PARAM_TEXT, 'loop url'),
        ]);
    }

    public static function get_structure($url) {
        global $DB, $CFG;

        $params = self::validate_parameters(self::get_structure_parameters(), [
                'url' => $url,
        ]);

        $return = [];
        $return['structure'] = json_encode(get_loop_structure($url));

        return $return;
    }

    public static function get_structure_returns() {
        return new external_single_structure([
                'structure' => new external_value(PARAM_RAW, 'loop structure'),
        ]);
    }


    public static function get_themes_parameters() {
        return new external_function_parameters([
                'url' => new external_value(PARAM_TEXT, 'loop url'),
        ]);
    }

    public static function get_themes($url) {
        global $DB, $CFG;

        $params = self::validate_parameters(self::get_themes_parameters(), [
                'url' => $url,
        ]);

        $return = [];
        $return['themes'] = json_encode(get_loop_themes($url));

        return $return;
    }

    public static function get_themes_returns() {
        return new external_single_structure([
                'themes' => new external_value(PARAM_RAW, 'loop themes'),
        ]);
    }
}
