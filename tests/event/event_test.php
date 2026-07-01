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

use advanced_testcase;
use coding_exception;
use context_module;
use core\event\base;
use core\exception\moodle_exception;
use dml_exception;
use moodle_url;

/**
 * Contains the event tests for the module loop
 *
 * @package   mod_loop
 * @category  test
 * @copyright 2026 oncampus GmbH <support@oncampus.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_loop\event\course_module_viewed
 */
final class event_test extends advanced_testcase {
    /**
     * The URL of the test loop system.
     */
    private const string LOOP_URL = 'https://loop.oncampus.de';

    /**
     * Sets up the test environment.
     * @return void
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Inserts a loop_systems record and creates a loop module instance.
     *
     * loop_add_instance() queries loop_systems by URL, so the system record
     * must exist before create_module() is called.
     *
     * @return object The loop module record (with ->id and ->cmid).
     * @throws dml_exception
     */
    private function create_test_loop(): object {
        global $DB;

        $DB->insert_record('loop_systems', [
            'externalid' => 'test-system',
            'name' => 'Test LOOP System',
            'url' => self::LOOP_URL,
            'personalized_access' => 0,
            'student_role_allocation' => 'loop_role_student_no_edit',
            'teacher_role_allocation' => 'loop_role_teacher_no_edit',
            'timecreated' => time(),
            'timemodified' => time(),
        ]);

        $course = $this->getDataGenerator()->create_course();
        return $this->getDataGenerator()->create_module('loop', [
            'course' => $course->id,
            'url' => self::LOOP_URL,
        ]);
    }

    /**
     * Tests that the event is triggered, captured, and carries the correct base data.
     * @throws coding_exception|moodle_exception
     * @covers ::trigger
     */
    public function test_course_module_viewed_is_triggered(): void {
        $loop    = $this->create_test_loop();
        $context = context_module::instance($loop->cmid);
        $linkurl = (new moodle_url('/mod/loop/link.php', ['loop' => self::LOOP_URL]))->out(false);

        $event = course_module_viewed::create([
            'context' => $context,
            'objectid' => $loop->id,
            'other' => ['link' => $linkurl],
        ]);

        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $sink->close();

        $this->assertCount(1, $events);

        $captured = reset($events);

        $this->assertInstanceOf(course_module_viewed::class, $captured);
        $this->assertEquals($context, $captured->get_context());
        $this->assertEquals($loop->id, $captured->objectid);
        $this->assertEquals('r', $captured->crud);
        $this->assertEquals(base::LEVEL_PARTICIPATING, $captured->edulevel);
    }


    /**
     * Test that get_url() returns the correct URL.
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     * @covers ::get_url
     */
    public function test_course_module_viewed_get_url(): void {
        $loop = $this->create_test_loop();
        $context = context_module::instance($loop->cmid);

        $event = course_module_viewed::create([
            'context' => $context,
            'objectid' => $loop->id,
            'other' => ['link' => self::LOOP_URL],
        ]);

        $expected = new moodle_url('/mod/loop/view.php', ['id' => $loop->cmid]);
        $this->assertEquals($expected, $event->get_url());
    }

    /**
     * Tests that get_description() includes userid, course-module ID, and the LOOP link.
     * @throws dml_exception|coding_exception
     * @covers ::get_description
     */
    public function test_course_module_viewed_get_description(): void {
        global $USER;

        $loop = $this->create_test_loop();
        $context = context_module::instance($loop->cmid);
        $linkurl = self::LOOP_URL . '/some-chapter';

        $event = course_module_viewed::create([
            'context' => $context,
            'objectid' => $loop->id,
            'other' => ['link' => $linkurl],
        ]);

        $description = $event->get_description();

        $this->assertStringContainsString((string) $USER->id, $description);
        $this->assertStringContainsString((string) $loop->cmid, $description);
        $this->assertStringContainsString($linkurl, $description);
    }
}
