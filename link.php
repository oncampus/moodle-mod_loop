<?php
/**
 * @package mod_loop
 * @author  Marc Vorreiter <marc.vorreiter@th-luebeck.de>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

Global $CFG, $USER;
require_login();

$loop = required_param('loop', PARAM_HOST);
$page = optional_param('page', '', PARAM_RAW);
$skin = optional_param('skin', '', PARAM_TEXT);

$wikiroot='https://'.$loop.'/mediawiki/';

$username=$USER->username;
$wgeMoodleLoopToken = get_config('mod_loop', 'token');

$token = md5($username.$wgeMoodleLoopToken);

try {
	$sid = $DB->get_field('sessions', 'sid', array ('userid'=>$USER->id), IGNORE_MULTIPLE );
} catch (Exception $e) {
	die();
}

$sid_encrypted = openssl_encrypt($sid, 'AES-128-ECB', $wgeMoodleLoopToken);
$sid_encrypted_encoded = urlencode($sid_encrypted);

$moodle_url = str_replace('https://','',$CFG->wwwroot);

$output =  '<html><head><meta http-equiv="refresh" content="0; URL='.$wikiroot.'index.php/'.$page.'?auth=moodle&moodle='.$moodle_url.'&loop='.$loop.'&skin='.$skin.'&u='.$username.'&t='.$token.'&p='.$page. '&sid=' . $sid_encrypted_encoded . '"></head></html>';


echo $output;

