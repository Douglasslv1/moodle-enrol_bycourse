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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
 * Conditional self-enrolment by course plugin.
 *
 * @package    enrol_bycourse
 * @copyright  2026 Douglas Silva <douglas.aarq95@gmail.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_bycourse;

defined('MOODLE_INTERNAL') || die();

use enrol_bycourse\local\eligibility;

final class eligibility_test extends \advanced_testcase {
    public function test_match_any_and_all_require_active_enrolments(): void {
        global $DB;

        $this->resetAfterTest();
        $courseone = $this->getDataGenerator()->create_course();
        $coursetwo = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $instanceone = $this->create_manual_enrolment($courseone, $user->id);

        $this->assertTrue(eligibility::user_is_eligible(
            $user->id, [$courseone->id, $coursetwo->id], eligibility::MATCH_ANY
        ));
        $this->assertFalse(eligibility::user_is_eligible(
            $user->id, [$courseone->id, $coursetwo->id], eligibility::MATCH_ALL
        ));

        $this->create_manual_enrolment($coursetwo, $user->id);
        $this->assertTrue(eligibility::user_is_eligible(
            $user->id, [$courseone->id, $coursetwo->id], eligibility::MATCH_ALL
        ));

        $DB->set_field('user_enrolments', 'status', ENROL_USER_SUSPENDED, [
            'enrolid' => $instanceone->id,
            'userid' => $user->id,
        ]);
        $this->assertFalse(eligibility::user_is_eligible(
            $user->id, [$courseone->id, $coursetwo->id], eligibility::MATCH_ALL
        ));
    }

    public function test_completed_course_qualifies_only_while_enrolment_is_active(): void {
        global $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $instance = $this->create_manual_enrolment($course, $user->id);
        $DB->insert_record('course_completions', (object)[
            'userid' => $user->id,
            'course' => $course->id,
            'timecompleted' => time(),
        ]);

        $this->assertTrue(eligibility::user_is_eligible($user->id, [$course->id], eligibility::MATCH_ANY));
        $DB->set_field('user_enrolments', 'status', ENROL_USER_SUSPENDED, [
            'enrolid' => $instance->id,
            'userid' => $user->id,
        ]);
        $this->assertFalse(eligibility::user_is_eligible($user->id, [$course->id], eligibility::MATCH_ANY));
    }

    public function test_expired_enrolment_is_not_eligible(): void {
        global $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $instance = $this->create_manual_enrolment($course, $user->id);
        $DB->set_field('user_enrolments', 'timeend', time() - 1, [
            'enrolid' => $instance->id,
            'userid' => $user->id,
        ]);

        $this->assertFalse(eligibility::user_is_eligible($user->id, [$course->id], eligibility::MATCH_ANY));
    }

    public function test_disabled_enrolment_instance_is_not_eligible(): void {
        global $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $instance = $this->create_manual_enrolment($course, $user->id);
        $DB->set_field('enrol', 'status', ENROL_INSTANCE_DISABLED, ['id' => $instance->id]);

        $this->assertFalse(eligibility::user_is_eligible($user->id, [$course->id], eligibility::MATCH_ANY));
    }

    private function create_manual_enrolment(\stdClass $course, int $userid): \stdClass {
        $plugin = enrol_get_plugin('manual');
        $instanceid = $plugin->add_instance($course);
        $instance = (object)['id' => $instanceid, 'courseid' => $course->id, 'enrol' => 'manual'];
        $plugin->enrol_user($instance, $userid, 0, 0, 0, ENROL_USER_ACTIVE);
        return $instance;
    }
}

