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
 * Loop module view page
 *
 * @package    mod_loop
 * @author     Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

$id = optional_param('id', 0, PARAM_INT);    // Course Module ID.
$l = optional_param('l', 0, PARAM_INT);     // Loop ID.

if ($id) {
    $PAGE->set_url('/mod/loop/index.php', ['id' => $id]);
    if (! $cm = get_coursemodule_from_id('loop', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $course = $DB->get_record("course", ["id" => $cm->course])) {
        print_error('coursemisconf');
    }

    if (! $loop = $DB->get_record("loop", ["id" => $cm->instance])) {
        print_error('invalidcoursemodule');
    }
} else {
    $PAGE->set_url('/mod/loop/index.php', ['l' => $l]);
    if (! $loop = $DB->get_record("loop", ["id" => $l])) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", ["id" => $loop->course])) {
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance("loop", $loop->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
}

require_login($course, true, $cm);

// Determine page parameter based on available fields
if (!empty($loop->page)) {
    $page = $loop->page;
} else if (!empty($loop->chapter)) {
    $page = $loop->chapter;
} else {
    $page = '';
}

$loopurl = str_replace('https://', '', $loop->url);
$linkurl = new moodle_url('/mod/loop/link.php', ['loop' => $loop->url, 'page' => $page, 'skin' => $loop->theme]);

redirect($linkurl);
