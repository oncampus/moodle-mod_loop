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

require_once("../../config.php");

global $DB;

$sid = required_param('sid', PARAM_ALPHANUM);
$username = required_param('username', PARAM_USERNAME);


try {
    $userid = $DB->get_field('sessions', 'userid', ['sid' => $sid], MUST_EXIST);
} catch (Exception $e) {
    die();
}

try {
    $sessionusername = $DB->get_field('user', 'username', ['id' => $userid], MUST_EXIST);
} catch (Exception $e) {
    die();
}

if ($username == $sessionusername) {
    echo 1;
} else {
    die();
}
