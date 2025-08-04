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
 * External API for Loop themes operations
 *
 * @package    mod_loop
 * @copyright  2025 oncampus GmbH
 * @author     Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_loop\external;


use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_warnings;

/**
 * External API for Loop themes operations
 *
 * @package    mod_loop
 * @author     Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class themes extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_themes_parameters(): external_function_parameters {
        return new external_function_parameters([
            'url' => new external_value(PARAM_TEXT, 'Loop system URL', VALUE_REQUIRED),
        ]);
    }

    /**
     * Get the loop themes for a given URL
     *
     * @param string $url The loop system URL
     * @return array The loop themes data
     * @throws \invalid_parameter_exception
     */
    public static function get_themes(string $url): array {
        global $DB;

        $params = self::validate_parameters(self::get_themes_parameters(), [
            'url' => $url,
        ]);

        $url = $params['url'];

        // Validate that the loop system exists.
        if (!$DB->record_exists('loop_systems', ['url' => $url])) {
            return [
                'themes' => null,
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

        // Get the themes using the existing function.
        $themes = get_loop_themes($url);

        if ($themes === false) {
            return [
                'themes' => null,
                'warnings' => [
                    [
                        'item' => 'url',
                        'itemid' => 0,
                        'warningcode' => 'apierror',
                        'message' => 'Failed to retrieve loop themes',
                    ],
                ],
            ];
        }

        return [
            'themes' => $themes,
            'warnings' => [],
        ];
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     */
    public static function get_themes_returns(): external_single_structure {
        return new external_single_structure([
            'themes' => new external_value(PARAM_RAW, 'Loop themes as JSON string', VALUE_OPTIONAL),
            'warnings' => new external_warnings(),
        ]);
    }
}
