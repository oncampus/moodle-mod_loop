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
 * External API for Loop structure operations
 *
 * @package    mod_loop
 * @copyright  2025 oncampus GmbH
 * @author     Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_loop\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/loop/locallib.php');

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_warnings;
use dml_exception;
use invalid_parameter_exception;

/**
 * External API for Loop structure operations
 *
 * @package    mod_loop
 * @copyright  2025 oncampus GmbH
 * @author     Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class structure extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'url' => new external_value(PARAM_TEXT, 'Loop system URL', VALUE_REQUIRED),
        ]);
    }

    /**
     * Get the loop structure for a given URL
     *
     * @param string $url The loop system URL
     * @return array The loop structure data
     * @throws invalid_parameter_exception|dml_exception
     */
    public static function execute(string $url): array {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), [
            'url' => $url,
        ]);

        $url = $params['url'];

        // Validate that the loop system exists.
        $courseloops = get_config('mod_loop', 'course_loops');
        if (!$courseloops && !$DB->record_exists('loop_systems', ['url' => $url])) {
            return [
                'structure' => null,
                'warnings' => [
                    [
                        'item' => 'url',
                        'itemid' => 0,
                        'warningcode' => 'invalidsystem',
                        'message' => 'Loop system not found',
                    ],
                ],
            ];
        }

        // Get the structure using the existing function.
        $structure = get_loop_structure($url);

        if ($structure === false) {
            return [
                'structure' => null,
                'warnings' => [
                    [
                        'item' => 'url',
                        'itemid' => 0,
                        'warningcode' => 'apierror',
                        'message' => 'Failed to retrieve loop structure',
                    ],
                ],
            ];
        }

        return [
            'structure' => $structure,
            'warnings' => [],
        ];
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'structure' => new external_value(PARAM_RAW, 'Loop structure as JSON string', VALUE_OPTIONAL),
            'warnings' => new external_warnings(),
        ]);
    }
}
