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

$string['pluginname'] = 'Conditional self enrolment by course';
$string['pluginname_desc'] = 'Allows self enrolment only when the user has active enrolments in selected prerequisite courses.';
$string['bycourse:config'] = 'Configure conditional self enrolment';
$string['bycourse:enrolself'] = 'Self enrol when course prerequisites are met';
$string['bycourse:manage'] = 'Manage conditional self enrolments';
$string['bycourse:unenrol'] = 'Unenrol users enrolled by this method';
$string['requiredcourses'] = 'Prerequisite courses';
$string['requiredcourses_help'] = 'Select the courses in which the user must have a current, active and non-suspended enrolment.';
$string['requiredcourseserror'] = 'Select at least one prerequisite course.';
$string['invalidrequiredcourse'] = 'One or more selected prerequisite courses no longer exist.';
$string['targetcoursenotallowed'] = 'The destination course cannot be its own prerequisite.';
$string['matchmode'] = 'Requirement matching';
$string['matchany'] = 'Any selected course';
$string['matchall'] = 'All selected courses';
$string['instructions'] = 'Enrolment instructions';
$string['instructions_help'] = 'Text shown to users on the enrolment page. Use it to explain the prerequisites and how to meet them.';
$string['role'] = 'Assigned role';
$string['defaultrole_desc'] = 'Role assigned when a user enrols through this method.';
$string['maxenrolled'] = 'Maximum enrolled users';
$string['maxenrolled_help'] = 'Maximum number of enrolments created by this instance. Zero means unlimited.';
$string['allownewenrolments'] = 'Allow new enrolments';
$string['enrolme'] = 'Enrol me';
$string['eligible'] = 'You meet the enrolment requirements.';
$string['noteligible'] = 'You cannot enrol because you do not have the required active course enrolment(s).';
$string['existinginactiveenrolment'] = 'You already have an inactive enrolment through this method. Contact the course administrator.';
$string['coursecardicon'] = 'Conditional self enrolment is available';
$string['enrolmentclosed'] = 'New enrolments are currently closed.';
$string['enrolmentnotstarted'] = 'Enrolment opens on {$a}.';
$string['enrolmentended'] = 'Enrolment closed on {$a}.';
$string['maxenrolledreached'] = 'The enrolment limit has been reached.';
$string['privacy:metadata'] = 'The conditional self enrolment plugin does not store additional personal data.';

