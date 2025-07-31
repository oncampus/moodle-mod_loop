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

defined('MOODLE_INTERNAL') || die;

global $CFG;

function loop_supports(string $feature): ?bool {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return false;
        case FEATURE_COMPLETION_HAS_RULES:
            return false;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_NO_VIEW_LINK:
            return false;
        default:
            return null;
    }
}

/**
 * Add a new loop instance
 *
 * @param object $data The form data
 * @param object $mform The form object
 * @return int The ID of the new instance
 */
function loop_add_instance(object $data, object $mform): int {
    global $DB;

    $data->timemodified = time();
    $data->timecreated = time();

    $data->id = $DB->insert_record('loop', $data);

    $loopsystem = $DB->get_record_sql("select * from {loop_systems} where " . $DB->sql_compare_text('url') . " = '" . $data->url . "'");


    $data->personalized_access = $loopsystem->personalized_access;
    $data->student_role_allocation = $loopsystem->student_role_allocation;
    $data->teacher_role_allocation = $loopsystem->teacher_role_allocation;

    return $data->id;
}


function loop_update_instance(object $data): bool {
    global $DB;

    $data->timemodified = time();
    $data->id = $data->instance;

    $DB->update_record('loop', $data);

    return true;
}


function loop_delete_instance(int $id): bool {
    global $DB;

    if (!$loop = $DB->get_record('loop', ['id' => $id])) {
        return false;
    }

    $result = true;

    if (!$DB->delete_records('loop', ['id' => $loop->id])) {
        $result = false;
    }

    return $result;
}


function loop_get_coursemodule_info(object $coursemodule): ?cached_cm_info {
    global $DB;

    if (!$loop = $DB->get_record('loop', ['id' => $coursemodule->instance])) {
            return null;
    }

    if ($loop->page != '') {
        $page = $loop->page;
    } else if ($loop->chapter != '') {
        $page = $loop->chapter;
    } else {
        $page = '';
    }


    $info = new cached_cm_info();
    $info->name = $loop->name;

    $info->content = format_module_intro('loop', $loop, $coursemodule->id, false);

    $linkurl = new moodle_url('/mod/loop/link.php', ['loop' => $loop->url, 'page' => $page, 'skin' => $loop->theme]);
    $info->onclick = "window.open('$linkurl', '', ''); return false;";

    return $info;
}
