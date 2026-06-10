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
 * Loop settings.
 *
 * @package   mod_loop
 * @copyright 2025 oncampus GmbH
 * @author    Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configpasswordunmask(
        'mod_loop/token',
        get_string('settings:loop_token', 'mod_loop'),
        get_string('settings:loop_token_desc', 'mod_loop'),
        '',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'mod_loop/default_theme',
        get_string('settings:default_theme', 'mod_loop'),
        get_string('settings:default_theme_desc', 'mod_loop'),
        '',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configcheckbox(
        'mod_loop/course_loops',
        get_string('settings:course_loops', 'mod_loop'),
        get_string('settings:course_loops_desc', 'mod_loop'),
        0,
    ));
}
