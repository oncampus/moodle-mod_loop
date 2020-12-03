<?php
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
	$userid = $DB->get_field('sessions', 'userid', array ('sid'=>$sid), MUST_EXIST);	
} catch (Exception $e) {
	die();
}

try {
	$session_username = $DB->get_field('user', 'username', array ('id'=>$userid), MUST_EXIST);
} catch (Exception $e) {
	die();	
}

if ($username == $session_username) {
	echo 1;
} else {
	die();
}

