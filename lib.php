<?php
/**
 * @package mod_loop
 * @author  Marc Vorreiter <marc.vorreiter@th-luebeck.de>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;

function loop_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return false;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return false;
        case FEATURE_COMPLETION_HAS_RULES:
            return false;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return false;
        default:
            return null;
    }
}

function loop_add_instance($data) {
    global $DB;

    $data->timemodified = time();
    $data->id = $DB->insert_record('loop', $data);

    $instances = $DB->get_records('loop', array('course' => $data->course));

    return $data->id;
}


function loop_update_instance($data) {
    global $DB;

    $data->timemodified = time();
    $data->id = $data->instance;

    $DB->update_record('loop', $data);

    return true;
}


function loop_delete_instance($id) {
    global $DB;

    if (!$loop = $DB->get_record('loop', array('id' => $id))) {
        return false;
    }

    $result = true;

    if (!$DB->delete_records('loop', array('id' => $loop->id))) {
        $result = false;
    }

    return $result;
}