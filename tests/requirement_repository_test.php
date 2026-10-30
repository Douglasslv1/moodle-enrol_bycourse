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

use enrol_bycourse\local\requirement_repository;

final class requirement_repository_test extends \advanced_testcase {
    public function test_replace_normalises_and_replaces_requirements(): void {
        $this->resetAfterTest();

        $target = $this->getDataGenerator()->create_course();
        $courseone = $this->getDataGenerator()->create_course();
        $coursetwo = $this->getDataGenerator()->create_course();
        $instanceid = enrol_get_plugin('manual')->add_instance($target);
        $repository = new requirement_repository();

        $repository->replace($instanceid, [$courseone->id, $coursetwo->id, $courseone->id]);
        $this->assertSame([$courseone->id, $coursetwo->id], $repository->get_course_ids($instanceid));

        $repository->replace($instanceid, [$coursetwo->id]);
        $this->assertSame([$coursetwo->id], $repository->get_course_ids($instanceid));

        $repository->delete($instanceid);
        $this->assertSame([], $repository->get_course_ids($instanceid));
    }
}
