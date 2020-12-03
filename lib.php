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

function loop_add_instance($data, $mform) {
    global $DB;

    $data->timemodified = time();
    $data->timecreated = time();
    
    $data->id = $DB->insert_record('loop', $data);

    //$data->intro = '';
    $data->introformat = FORMAT_MOODLE;
    
    $loop_system = $DB->get_record_sql("select * from {loop_systems} where " . $DB->sql_compare_text('url') . " = '".$data->url."'" );
    

    $data->personalized_access = $loop_system->personalized_access;
    $data->student_role_allocation = $loop_system->student_role_allocation;
    $data->teacher_role_allocation = $loop_system->teacher_role_allocation;
    $data->theme = '';
    		
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


function loop_get_coursemodule_info($coursemodule) {
	global $DB, $OUTPUT;
	
	if (!$loop = $DB->get_record('loop', array('id'=>$coursemodule->instance))) {
			return NULL;
	}
	
	if ($loop->page != '') {
		$page = $loop->page;
	} elseif ($loop->chapter != '') {
		$page = $loop->chapter;
	} else {
		$page = '';
	}
	
	
	$info = new cached_cm_info();
	$info->name = $loop->name;
	
	$info->content = format_module_intro('loop', $loop, $coursemodule->id, false);
	
	$linkurl = new moodle_url('/mod/loop/link.php', ['loop' => $loop->url, 'page' => $page]);
	$info->onclick = "window.open('$linkurl', '', ''); return false;";
	
	return $info;

}
