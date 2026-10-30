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

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_bycourse_upgrade($oldversion) {
    global $DB;

    if ($oldversion < 2026102901) {
        $instances = $DB->get_records('enrol', ['enrol' => 'bycourse']);
        foreach ($instances as $instance) {
            // Version 0.1 stored a category in customint1 and the role in customint2.
            $instance->roleid = $instance->customint2 ?: $instance->roleid;
            $instance->customint1 = 0;
            $instance->customint2 = 0;
            $instance->customint3 = ENROL_DO_NOT_SEND_EMAIL;
            $instance->customint4 = 1;
            $instance->customtext1 = '';
            $instance->status = ENROL_INSTANCE_DISABLED;
            $DB->update_record('enrol', $instance);
        }
        upgrade_plugin_savepoint(true, 2026102901, 'enrol', 'bycourse');
    }

    if ($oldversion < 2026103001) {
        $dbman = $DB->get_manager();
        $table = new xmldb_table('enrol_bycourse_req');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('enrolid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('enrolid', XMLDB_KEY_FOREIGN, ['enrolid'], 'enrol', ['id']);
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
        $table->add_index('enrolcourse', XMLDB_INDEX_UNIQUE, ['enrolid', 'courseid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $repository = new \enrol_bycourse\local\requirement_repository();
        $instances = $DB->get_records_select(
            'enrol',
            'enrol = :enrol AND customtext1 IS NOT NULL',
            ['enrol' => 'bycourse']
        );
        foreach ($instances as $instance) {
            $courseids = \enrol_bycourse\local\eligibility::decode_course_ids($instance->customtext1);
            $repository->replace((int)$instance->id, $courseids);
            $DB->set_field('enrol', 'customtext1', implode(',', $courseids), ['id' => $instance->id]);
        }

        upgrade_plugin_savepoint(true, 2026103001, 'enrol', 'bycourse');
    }

    return true;
}
