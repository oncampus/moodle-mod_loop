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
 * Loop local library.
 *
 * @package   mod_loop
 * @copyright 2025 oncampus GmbH
 * @author    Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\notification;

/**
 * Get allowed loops.
 *
 * @param int|string $courseid
 * @return array
 * @throws dml_exception
 */
function get_allowed_loops(int|string $courseid): array {
    if (get_config('mod_loop', 'course_loops')) {
        return get_allowed_loops_course($courseid);
    }
    return get_allowed_loops_system();
}

/**
 * Returns all loops that are allowed for this course.
 * @param int|string $courseid
 * @return array
 * @throws dml_exception
 * @throws Exception
 */
function get_allowed_loops_course(int|string $courseid): array {
    $token = get_config('mod_loop', 'token');
    $url = 'https://moodalis.oncampus.de/files/course_loops.php?courseid=' . $courseid . '&token=' . $token;
    $url = str_replace(' ', '%20', $url);

    $cha = curl_init();
    curl_setopt($cha, CURLOPT_URL, $url);
    curl_setopt($cha, CURLOPT_ENCODING, "UTF-8");
    curl_setopt($cha, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cha, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($cha, CURLOPT_TIMEOUT, 10);
    $jsonresult = curl_exec($cha);
    $curlerror = curl_error($cha);

    $loops = [];

    if ($jsonresult !== false && empty($curlerror)) {
        $decoded = json_decode($jsonresult, true);
        if (is_array($decoded)) {
            $loops = $decoded;
        }
    }

    if (empty($loops)) {
        if (!empty($curlerror)) {
            $msg = get_string('error_loops_server_connection', 'mod_loop', $curlerror);
        } else {
            $httpcode = 'HTTP ' . curl_getinfo($cha, CURLINFO_HTTP_CODE);
            $msg = get_string('error_loops_server_response', 'mod_loop', $httpcode);
        }
        notification::add($msg, \core\output\notification::NOTIFY_WARNING);
    }

    return $loops;
}

/**
 * Returns all Loops that are allowed for this moodle
 * @return array
 * @throws dml_exception
 */
function get_allowed_loops_system(): array {
    global $DB;
    return $DB->get_records('loop_systems');
}


/**
 * Get loop structure.
 *
 * @param string $url
 * @return bool|string
 * @throws dml_exception
 */
function get_loop_structure(string $url): bool|string {
    global $CFG;

    $wgemoodlelooptoken = get_config('mod_loop', 'token');

    $moodleurl = str_replace('https://', '', $CFG->wwwroot);

    $loopstructureurl = 'https://' .
                        $url .
                        '/mediawiki/api.php?action=loopauth-structure&auth=api&m=' .
                        $moodleurl .
                        '&t=' .
                        $wgemoodlelooptoken .
                        '&format=json';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loopstructureurl);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        return false;
    }

    $structureresult = json_decode($result, true);
    $structure = $structureresult['loopauth-structure']['structure'];

    $return = [];
    $return['---'] = '---';
    if (isset($structure) && is_array($structure)) {
        foreach ($structure as $structureitem) {
            $key = urlencode($structureitem['toctext']);
            $level = $structureitem['toclevel'];
            $number = $structureitem['tocnumber'];
            if ($number != 0) {
                $value = str_pad('', $level - 1, " ") . $number . ' ' . $structureitem['toctext'];
                $return[$key] = $value;
            }
        }
    }

    $returnarray = [];
    $n = 1;
    foreach ($return as $key => $value) {
        $returnarray[] = ['key' => $key, 'value' => $value];
        $n++;
    }

    return json_encode($returnarray);
}

/**
 * Get loop themes.
 *
 * @param string $url
 * @return bool|string
 * @throws dml_exception
 */
function get_loop_themes(string $url): bool|string {
    global $DB;

    if (!$loop = $DB->get_record_sql("select * from {loop_systems} where " . $DB->sql_compare_text('url') . " = '" . $url . "'")) {
        return false;
    }
    $allowedthemes = json_decode($loop->allowed_themes);

    $returnarray = [];

    if (isset($allowedthemes) && is_array($allowedthemes)) {
        foreach ($allowedthemes as $allowedtheme) {
            $returnarray[] = ['key' => $allowedtheme, 'value' => $allowedtheme];
        }
    }

    return json_encode($returnarray);
}
