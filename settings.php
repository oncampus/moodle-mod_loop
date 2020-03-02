<?php
/**
 * @package mod_loop
 * @author  Marc Vorreiter <marc.vorreiter@th-luebeck.de>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add( new admin_setting_configpasswordunmask(
        'mod_loop/token',
        'Loop Token',
        'This is the token to get the allowed LOOPs and get the structe for a LOOP',
       '',
       PARAM_TEXT
   ) );    

}




    
	

 
