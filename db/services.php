<?php
/**
 * @package mod_loop
 * @author  Marc Vorreiter <marc.vorreiter@th-luebeck.de>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(
    'mod_loop_get_structure' => array(          
        'classname'   => 'mod_loop_external', 
        'methodname'  => 'get_structure', 
        'classpath' => 'mod/loop/externallib.php',
        'description' => 'get loop structure',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false,
    )
);

$services = array(
    'get_structure' => array(
            'functions' => array ('mod_loop_get_structure'), 
            'restrictedusers' => 0, 
            'enabled'=>1,
    )
);