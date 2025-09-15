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
        'Loop Token',
        'This is the token to get the allowed LOOPs and get the structe for a LOOP',
        '',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'mod_loop/default_theme',
        'Default Theme',
        'Default Theme for LOOPs',
        '',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configcheckbox(
        'mod_loop/moodalis_loops',
        'Moodalis LOOPs',
        'Is access to LOOPS managed by moodalis on course level',
        0,
    ));
}
