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

if (empty($wgeMoodleLoopToken)) {
    throw new moodle_exception('error:notoken', 'mod_loop', '', null, 'Loop token is not configured in site administration');
}

$token = md5($username.$wgeMoodleLoopToken);

try {
    $sid = $DB->get_field('sessions', 'sid', array ('userid'=>$USER->id), IGNORE_MULTIPLE );
    if (!$sid) {
        throw new moodle_exception('error:nosession', 'mod_loop', '', null, 'No valid session found for user');
    }
} catch (Exception $e) {
    throw new moodle_exception('error:dberror', 'mod_loop', '', null, 'Database error: ' . $e->getMessage());
}

$sid_encrypted = openssl_encrypt($sid, 'AES-128-ECB', $wgeMoodleLoopToken);
if ($sid_encrypted === false) {
    throw new moodle_exception('error:encryption', 'mod_loop', '', null, 'Failed to encrypt session ID');
}
$sid_encrypted_encoded = urlencode($sid_encrypted);

$moodle_url = str_replace('https://','',$CFG->wwwroot);

$output =  '<html><head><meta http-equiv="refresh" content="0; URL='.$wikiroot.'index.php/'.urldecode($page).'?auth=moodle&moodle='.$moodle_url.'&loop='.$loop.'&skin='.$skin.'&u='.$username.'&t='.$token.'&p='.$page. '&sid=' . $sid_encrypted_encoded . '"></head></html>';

echo $output;

