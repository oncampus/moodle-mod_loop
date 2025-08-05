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
 * Loop link.
 *
 * @package   mod_loop
 * @copyright 2025 oncampus GmbH
 * @author    Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

global $CFG, $USER;
require_login();

$loop = required_param('loop', PARAM_HOST);
$page = optional_param('page', '', PARAM_RAW);
$skin = optional_param('skin', '', PARAM_TEXT);

$wikiroot = 'https://' . $loop . '/mediawiki/';

$username = $USER->username;
$wgemoodlelooptoken = get_config('mod_loop', 'token');

if (empty($wgemoodlelooptoken)) {
    throw new moodle_exception('error:notoken', 'mod_loop', '', null, 'Loop token is not configured in site administration');
}

$token = md5($username . $wgemoodlelooptoken);

try {
    $sid = $DB->get_field('sessions', 'sid', ['userid' => $USER->id], IGNORE_MULTIPLE);
    if (!$sid) {
        throw new moodle_exception('error:nosession', 'mod_loop', '', null, 'No valid session found for user');
    }
} catch (Exception $e) {
    throw new moodle_exception('error:dberror', 'mod_loop', '', null, 'Database error: ' . $e->getMessage());
}

$sidencrypted = openssl_encrypt($sid, 'AES-128-ECB', $wgemoodlelooptoken);
if ($sidencrypted === false) {
    throw new moodle_exception('error:encryption', 'mod_loop', '', null, 'Failed to encrypt session ID');
}
$sidencryptedencoded = urlencode($sidencrypted);

$moodleurl = str_replace('https://', '', $CFG->wwwroot);

$url = $wikiroot . 'index.php/' . urldecode($page) .
    '?auth=moodle' .
    '&moodle=' . $moodleurl .
    '&loop=' . $loop .
    '&skin=' . $skin .
    '&u=' . $username .
    '&t=' . $token .
    '&p=' . $page .
    '&sid=' . $sidencryptedencoded;

$output = <<<HTML
<html>
<head>
    <meta http-equiv="refresh" content="0; URL={$url}">
</head>
</html>
HTML;

echo $output;
