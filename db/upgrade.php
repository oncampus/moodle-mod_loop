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
 * Main file to view greetings
 *
 * @package     mod_loop
 * @copyright   2025 oncampus GmbH
 * @author      Alexander Mueller <alexander.mueller@oncampus.de>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define upgrade steps to be performed to upgrade the plugin from the old version to the current one.
 *
 * @param int $oldversion Version number the plugin is being upgraded from.
 * @return true
 * @throws ddl_exception
 * @throws moodle_exception
 */
function xmldb_loop_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025080414) {
        // Define table loop_systems to be created.
        $table = new xmldb_table('loop_systems');

        // Adding fields to table loop_systems.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('externalid', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
        $table->add_field('url', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL);
        $table->add_field('personalized_access', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('student_role_allocation', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'student_no_edit');
        $table->add_field('teacher_role_allocation', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'teacher_no_edit');
        $table->add_field('allowed_themes', XMLDB_TYPE_CHAR, '1024');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table loop_systems.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch creates a table for loop_systems.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Loop savepoint reached.
        upgrade_mod_savepoint(true, 2025082500, 'loop');
    }

    if ($oldversion < 2025091906) {
        $table = new xmldb_table('loop_systems');
        $field = new xmldb_field('url', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);

        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_type($table, $field);
        }

        upgrade_mod_savepoint(true, 2025091906, 'loop');
    }

    return true;
}
