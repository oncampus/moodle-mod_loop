<?php
/**
 * Add loop form
 *
 * @package mod_loop
 * @author  Marc Vorreiter <marc.vorreiter@th-luebeck.de>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once ($CFG->dirroot.'/mod/loop/lib.php');
require_once ($CFG->dirroot.'/mod/loop/locallib.php');

class mod_loop_mod_form extends moodleform_mod {

    function definition() {
        global $DB, $PAGE;

        $PAGE->force_settings_menu();
        
        $PAGE->requires->js_call_amd('mod_loop/loop', 'init', array());

        $mform = $this->_form;
        
        $mform->addElement('text', 'name', get_string('loopinstance_name', 'loop'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');        
        
        
        $this->standard_intro_elements(get_string('loopinstance_introduction', 'loop'));
        
        $loops = array(
        	/* 'none' => '---' */
        );
        $loop_systems = $DB->get_records('loop_systems');
        $first_url = '';
		if ($loop_systems) {
			foreach ($loop_systems as $loop_system) {
				if ($first_url == '') {
					$first_url = $loop_system->url;
				}
				$loops[$loop_system->url] = $loop_system->name. ' (' . $loop_system->url .')';
			}
		}
		$mform->addElement('select', 'url', get_string('loopinstance_url', 'loop'), $loops);		
		$mform->addRule('url', null, 'required', null, 'client');
		
		
		$chapters=array();
		$chapter_url = '';
		if ($this->current && isset($this->current->url)) {
			$chapter_url = $this->current->url;
		} else {
			$chapter_url = $first_url;
		}
		$structure = json_decode(get_loop_structure($chapter_url));
		foreach ($structure as $structureitem) {
			$chapters[$structureitem->key] = $structureitem->value;
		}		
		
		
		
		
		$chapterselect = $mform->addElement('select', 'chapter', get_string('loopinstance_chapter', 'loop'), $chapters);
		#$chapterselect->setSelected($loop_system->chapter);
		
		
		$mform->addElement('text', 'page', get_string('loopinstance_page', 'loop'), array('size'=>'255'));
		$mform->setType('name', PARAM_RAW);        
		
        
        $this->standard_coursemodule_elements();

        $this->add_action_buttons(true, false, null);

    }

    
}
