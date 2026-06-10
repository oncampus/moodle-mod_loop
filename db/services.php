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
 * Loop services.
 *
 * @package   mod_loop
 * @copyright 2025 oncampus GmbH
 * @author  Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'mod_loop_get_structure' => [
        'classname' => 'mod_loop\external\structure',
        'methodname' => 'execute',
        'description' => 'Get loop structure for a given URL',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false,
    ],
    'mod_loop_get_themes' => [
        'classname' => 'mod_loop\external\themes',
        'methodname' => 'execute',
        'description' => 'Get allowed loop themes for a given URL',
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
