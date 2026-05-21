<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Loop module data generator class.
 *
 * @package    mod_loop
 * @category   test
 * @copyright  2026 oncampus GmbH <support@oncampus.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_loop_generator extends testing_module_generator {
    /**
     * Creates a new loop module instance.
     *
     * @param array|stdClass|null $record Module data. Must include 'course'.
     *     The 'url' field must match an existing loop_systems record.
     * @param array|null $options General course-module options.
     * @return stdClass Loop instance record with additional 'cmid' field.
     * @throws coding_exception
     */
    public function create_instance($record = null, ?array $options = null): stdClass {
        $record = (object)(array)$record;

        if (!isset($record->url)) {
            $record->url = 'https://loop.oncampus.de';
        }

        return parent::create_instance($record, $options);
    }
}
