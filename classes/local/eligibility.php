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

final class eligibility {
    public const MATCH_ANY = 0;
    public const MATCH_ALL = 1;

    /**
     * Checks active enrolments against the configured prerequisite courses.
     *
     * @param int $userid User being checked.
     * @param int[] $courseids Prerequisite course IDs.
     * @param int $matchmode MATCH_ANY or MATCH_ALL.
     * @return bool
     */
    public static function user_is_eligible(int $userid, array $courseids, int $matchmode): bool {
        global $DB;

        $courseids = self::normalise_course_ids($courseids);
        if (!$courseids) {
            return false;
        }
        $enabledplugins = array_keys(enrol_get_plugins(true));
        if (!$enabledplugins) {
            return false;
        }

        [$coursesql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'requiredcourse');
        [$pluginsql, $pluginparams] = $DB->get_in_or_equal($enabledplugins, SQL_PARAMS_NAMED, 'enabledplugin');
        $now = time();
        $params = [
            'userid' => $userid,
            'userenrolactive' => ENROL_USER_ACTIVE,
            'instanceenabled' => ENROL_INSTANCE_ENABLED,
            'nowstart' => $now,
            'nowend' => $now,
        ] + $courseparams + $pluginparams;

        $sql = "SELECT COUNT(DISTINCT e.courseid)
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON e.id = ue.enrolid
                 WHERE ue.userid = :userid
                   AND ue.status = :userenrolactive
                   AND e.status = :instanceenabled
                   AND e.enrol {$pluginsql}
                   AND e.courseid {$coursesql}
                   AND (ue.timestart = 0 OR ue.timestart <= :nowstart)
                   AND (ue.timeend = 0 OR ue.timeend > :nowend)";

        $matched = (int)$DB->count_records_sql($sql, $params);
        return $matchmode === self::MATCH_ALL ? $matched === count($courseids) : $matched > 0;
    }

    /**
     * Converts stored or submitted course IDs into a unique integer list.
     *
     * @param string|array|null $value
     * @return int[]
     */
    public static function decode_course_ids($value): array {
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        return self::normalise_course_ids((array)$value);
    }

    /** @param array $courseids @return int[] */
    private static function normalise_course_ids(array $courseids): array {
        $courseids = array_map('intval', $courseids);
        $courseids = array_filter($courseids, static fn(int $id): bool => $id > 0 && $id !== SITEID);
        return array_values(array_unique($courseids));
    }
}
