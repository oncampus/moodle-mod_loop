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
 * Strings for component 'loop', language 'de'
 *
 * @package     mod_loop
 * @copyright   2025 oncampus GmbH
 * @author      Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['error_loops_server_connection'] = 'Verbindung zum Server für die LOOPs konnte nicht hergestellt werden: {$a}';
$string['error_loops_server_response'] = 'Der Server für die LOOPs hat keine gültige Antwort geliefert ({$a}).';
$string['event:loop_viewed'] = 'Der Nutzende mit der ID "{$a->userid}" sah die Loop-Aktivität mit der Kursmodul ID "{$a->contextinstanceid}" ein. (URL: {$a->link}).';

$string['get_allowed_loops_task'] = 'Synchronisieren der erlaubten LOOPs';

$string['loop:addinstance'] = 'Add a loop instance.';
$string['loop:view'] = 'LOOP anzeigen';

$string['loop_placeholder'] = 'Wählen Sie ein LOOP';

$string['loopinstance_chapter'] = 'Kapitel';
$string['loopinstance_introduction'] = 'Intro';
$string['loopinstance_name'] = 'Name';
$string['loopinstance_page'] = 'Seite';
$string['loopinstance_theme'] = 'Theme';
$string['loopinstance_url'] = 'LOOP';

$string['modulename'] = 'LOOP';
$string['modulename_help'] = 'Link zu einem LOOP';
$string['modulename_link'] = 'mod/loop/view';

$string['modulenameplural'] = 'LOOPS';
$string['noselection'] = 'Kein LOOP ausgewählt';

$string['pluginname'] = 'LOOP';

$string['privacy:metadata'] = 'Das Plugin LOOP speichert keine personenbezogenen Daten.';

$string['settings:course_loops'] = 'Kurs-LOOPs';
$string['settings:course_loops_desc'] = 'Der Zugang zu den LOOPS ist kursbasiert statt systembasiert geregelt';
$string['settings:default_theme'] = 'Standard Design';
$string['settings:default_theme_desc'] = 'Standard Design für LOOPs';
$string['settings:loop_token'] = 'Loop Token';
$string['settings:loop_token_desc'] = 'Dies ist der Token zum Abrufen der erlaubten LOOPs und zum Abrufen der Struktur eines LOOPs';
