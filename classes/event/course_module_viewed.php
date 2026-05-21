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

namespace mod_loop\event;

use coding_exception;

/**
 * The mod_loop course module viewed event class.
 *
 * @package    mod_loop
 * @copyright  2026 oncampus GmbH <support@oncampus.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_viewed extends \core\event\course_module_viewed {
    /**
     * Initializes the event.
     *
     * @return void
     */
    protected function init(): void {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'loop';
    }

    /**
     * Returns a description of the event.
     *
     * @return string
     * @throws coding_exception
     */
    public function get_description(): string {
        $data = [
            'userid' => $this->userid,
            'contextinstanceid' => $this->contextinstanceid,
            'link' => $this->other['link'],
        ];

        return get_string('event:loop_viewed', 'mod_loop', $data);
    }

    /**
     * Returns the object ID mapping for backup/restore.
     * @return array
     */
    public static function get_objectid_mapping(): array {
        return ['db' => 'loop', 'restore' => 'loop'];
    }
}
