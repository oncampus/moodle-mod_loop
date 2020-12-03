<?php
/**
 * @package mod_loop
 * @author  Marc Vorreiter <marc.vorreiter@th-luebeck.de>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once ($CFG->libdir . "/externallib.php");
require_once ($CFG->dirroot . '/mod/loop/locallib.php');

class mod_loop_external extends external_api {

	public static function get_structure_parameters() {
		return new external_function_parameters ( array (
				'url' => new external_value ( PARAM_TEXT, 'loop url' ) 
		) );
	}
	
	public static function get_structure($url) {
		global $DB, $CFG;
		
		$params = self::validate_parameters ( self::get_structure_parameters (), array (
				'url' => $url 
		)
		 );
		
		$return = array ();
		$return ['structure'] = json_encode ( get_loop_structure ( $url ) );
		
		return $return;
	}
	
	public static function get_structure_returns() {
		return new external_function_parameters ( array (
				'structure' => new external_value ( PARAM_RAW, 'loop structure' ) 
		) );
	}
}