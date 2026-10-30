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

namespace enrol_bycourse\local;

defined('MOODLE_INTERNAL') || die();

final class requirement_repository {
    /** @return int[] */
    public function get_course_ids(int $enrolid): array {
        global $DB;

        $courseids = $DB->get_fieldset_select(
            'enrol_bycourse_req',
            'courseid',
            'enrolid = :enrolid',
            ['enrolid' => $enrolid],
            'sortorder ASC, id ASC'
        );
        return array_map('intval', $courseids);
    }

    /** @param int[] $courseids */
    public function replace(int $enrolid, array $courseids): void {
        global $DB;

        $courseids = eligibility::decode_course_ids($courseids);
        $transaction = $DB->start_delegated_transaction();
        $DB->delete_records('enrol_bycourse_req', ['enrolid' => $enrolid]);

        if ($courseids) {
            $now = time();
            $records = [];
            foreach ($courseids as $sortorder => $courseid) {
                $records[] = (object)[
                    'enrolid' => $enrolid,
                    'courseid' => $courseid,
                    'sortorder' => $sortorder,
                    'timecreated' => $now,
                ];
            }
            $DB->insert_records('enrol_bycourse_req', $records);
        }

        $transaction->allow_commit();
    }

    public function delete(int $enrolid): void {
        global $DB;
        $DB->delete_records('enrol_bycourse_req', ['enrolid' => $enrolid]);
    }

    public function remove_course(int $courseid): void {
        global $DB;

        $enrolids = $DB->get_fieldset_sql(
            'SELECT DISTINCT enrolid FROM {enrol_bycourse_req} WHERE courseid = :courseid',
            ['courseid' => $courseid]
        );
        $DB->delete_records('enrol_bycourse_req', ['courseid' => $courseid]);
        foreach ($enrolids as $enrolid) {
            $courseids = $this->get_course_ids((int)$enrolid);
            $DB->set_field('enrol', 'customtext1', implode(',', $courseids), ['id' => $enrolid]);
        }
    }
}
