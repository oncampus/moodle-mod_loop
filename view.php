<?php
/**
 * @package mod_loop
 * @author  Marc Vorreiter <marc.vorreiter@th-luebeck.de>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

$id = optional_param('id',0,PARAM_INT);    // Course Module ID, or
$l = optional_param('l',0,PARAM_INT);     // loop ID

if ($id) {
    $PAGE->set_url('/mod/loop/index.php', array('id'=>$id));
    if (! $cm = get_coursemodule_from_id('loop', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
        print_error('coursemisconf');
    }

    if (! $loop = $DB->get_record("loop", array("id"=>$cm->instance))) {
        print_error('invalidcoursemodule');
    }

} else {
    $PAGE->set_url('/mod/loop/index.php', array('l'=>$l));
    if (! $loop = $DB->get_record("loop", array("id"=>$l))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id"=>$loop->course)) ){
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance("loop", $loop->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
}

require_login($course, true, $cm);

//redirect("$CFG->wwwroot/course/view.php?id=$course->id");


$loop_url = str_replace ('https://','',$loop->url);
$linkurl = new moodle_url('/mod/loop/link.php', ['loop' => $loop->url, 'page' => $page, 'skin' => $loop->theme]);

redirect($linkurl);


