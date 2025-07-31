<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * @package mod_loop
 * @author  Marc Vorreiter <marc.vorreiter@th-luebeck.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = [
    'mod_loop_get_structure' => [
        'classname'   => 'mod_loop_external',
        'methodname'  => 'get_structure',
        'description' => 'get loop structure',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false,
    ],
    'mod_loop_get_themes' => [
            'classname'   => 'mod_loop_external',
            'methodname'  => 'get_themes',
            'description' => 'get allowed loop themes',
            'type' => 'read',
            'ajax' => true,
            'loginrequired' => false,
    ],
];

$services = [
    'get_structure' => [
            'functions' => ['mod_loop_get_structure'],
            'restrictedusers' => 0,
            'enabled' => 1,
    ],
    'get_themes' => [
            'functions' => ['mod_loop_get_themes'],
            'restrictedusers' => 0,
            'enabled' => 1,
    ],
];
