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

class enrol_bycourse_plugin extends enrol_plugin {
    public function use_standard_editing_ui() {
        return true;
    }

    public function get_info_icons(array $instances) {
        foreach ($instances as $instance) {
            if ($this->is_enrolment_open($instance, false)) {
                return [new pix_icon('i/enrolusers', get_string('coursecardicon', 'enrol_bycourse'))];
            }
        }
        return [];
    }

    public function show_enrolme_link(stdClass $instance) {
        return $this->is_enrolment_open($instance);
    }

    public function get_instance_name($instance) {
        return empty($instance->name) ?
            get_string('pluginname', 'enrol_bycourse') : format_string($instance->name);
    }

    public function get_description_text($instance) {
        if (empty($instance->customtext2)) {
            return null;
        }
        $context = context_course::instance($instance->courseid);
        return format_text($instance->customtext2, FORMAT_MOODLE, ['context' => $context]);
    }

    public function can_add_instance($courseid) {
        $context = context_course::instance($courseid);
        return has_capability('moodle/course:enrolconfig', $context) &&
            has_capability('enrol/bycourse:config', $context);
    }

    public function can_edit_instance($instance) {
        return $this->can_add_instance($instance->courseid);
    }

    public function can_delete_instance($instance) {
        return $this->can_edit_instance($instance);
    }

    public function delete_instance($instance) {
        $this->requirements()->delete((int)$instance->id);
        parent::delete_instance($instance);
    }

    public function allow_manage(stdClass $instance) {
        return true;
    }

    public function allow_unenrol(stdClass $instance) {
        return true;
    }

    public function restore_instance(restore_enrolments_structure_step $step, stdClass $data, $course, $oldid) {
        global $DB;

        $courseids = \enrol_bycourse\local\eligibility::decode_course_ids($data->customtext1 ?? '');
        if ($step->get_task()->is_samesite() && $courseids) {
            $existing = $DB->get_records_list('course', 'id', $courseids, '', 'id');
            $courseids = array_values(array_filter(
                $courseids,
                static fn(int $courseid): bool => isset($existing[$courseid])
            ));
        } else if (!$step->get_task()->is_samesite()) {
            $courseids = [];
            $data->status = ENROL_INSTANCE_DISABLED;
        }
        $data->customtext1 = implode(',', $courseids);

        $instanceid = $this->add_instance($course, (array)$data);
        $step->set_mapping('enrol', $oldid, $instanceid);
    }

    public function add_instance($course, ?array $fields = null) {
        $fields = $fields ?? [];
        $courseids = $this->extract_course_ids($fields);
        $fields['roleid'] = empty($fields['roleid']) ? (int)get_config('enrol_bycourse', 'roleid') : $fields['roleid'];
        $fields['customint4'] = array_key_exists('customint4', $fields) ? $fields['customint4'] : 1;
        $instanceid = parent::add_instance($course, $fields);
        $this->requirements()->replace($instanceid, $courseids);
        return $instanceid;
    }

    public function update_instance($instance, $data) {
        $fields = (array)$data;
        $courseids = $this->extract_course_ids($fields);
        $updated = parent::update_instance($instance, (object)$fields);
        $this->requirements()->replace((int)$instance->id, $courseids);
        return $updated;
    }

    public function edit_instance_form($instance, MoodleQuickForm $mform, $context) {
        global $DB;

        $instance->customtext1 = $this->requirements()->get_course_ids((int)$instance->id);

        $mform->addElement('text', 'name', get_string('custominstancename', 'enrol'));
        $mform->setType('name', PARAM_TEXT);

        $courses = $DB->get_records_select_menu(
            'course',
            'id <> :siteid AND id <> :targetid',
            ['siteid' => SITEID, 'targetid' => $instance->courseid],
            'fullname ASC',
            "id, " . $DB->sql_concat('fullname', "' ['", 'shortname', "']'") . ' AS displayname'
        );
        $mform->addElement('autocomplete', 'customtext1', get_string('requiredcourses', 'enrol_bycourse'), $courses,
            ['multiple' => true]);
        $mform->addHelpButton('customtext1', 'requiredcourses', 'enrol_bycourse');
        $mform->addRule('customtext1', get_string('required'), 'required', null, 'client');

        $matchoptions = [
            \enrol_bycourse\local\eligibility::MATCH_ANY => get_string('matchany', 'enrol_bycourse'),
            \enrol_bycourse\local\eligibility::MATCH_ALL => get_string('matchall', 'enrol_bycourse'),
        ];
        $mform->addElement('select', 'customint1', get_string('matchmode', 'enrol_bycourse'), $matchoptions);

        $mform->addElement('textarea', 'customtext2', get_string('instructions', 'enrol_bycourse'),
            ['rows' => 5, 'cols' => 60]);
        $mform->setType('customtext2', PARAM_RAW);
        $mform->addHelpButton('customtext2', 'instructions', 'enrol_bycourse');

        $roles = get_default_enrol_roles($context);
        $mform->addElement('select', 'roleid', get_string('role', 'enrol_bycourse'), $roles);
        $mform->setDefault('roleid', get_config('enrol_bycourse', 'roleid'));

        $mform->addElement('duration', 'enrolperiod', get_string('enrolperiod', 'enrol'), ['optional' => true]);
        $mform->setDefault('enrolperiod', 0);
        $mform->addElement('date_time_selector', 'enrolstartdate', get_string('enroltimestart', 'enrol'), ['optional' => true]);
        $mform->addElement('date_time_selector', 'enrolenddate', get_string('enroltimeend', 'enrol'), ['optional' => true]);

        $mform->addElement('text', 'customint2', get_string('maxenrolled', 'enrol_bycourse'), ['size' => 8]);
        $mform->setType('customint2', PARAM_INT);
        $mform->setDefault('customint2', 0);
        $mform->addHelpButton('customint2', 'maxenrolled', 'enrol_bycourse');

        $mform->addElement('select', 'customint3', get_string('sendcoursewelcomemessage', 'enrol'),
            enrol_send_welcome_email_options());
        $mform->setDefault('customint3', ENROL_DO_NOT_SEND_EMAIL);
        $mform->addElement('textarea', 'customtext3', get_string('customwelcomemessage', 'enrol'),
            ['rows' => 5, 'cols' => 60]);
        $mform->setType('customtext3', PARAM_RAW);

        $mform->addElement('advcheckbox', 'customint4', get_string('allownewenrolments', 'enrol_bycourse'));
        $mform->setDefault('customint4', 1);

        $statusoptions = [
            ENROL_INSTANCE_ENABLED => get_string('enable'),
            ENROL_INSTANCE_DISABLED => get_string('disable'),
        ];
        $mform->addElement('select', 'status', get_string('status'), $statusoptions);
        $mform->setDefault('status', ENROL_INSTANCE_ENABLED);
    }

    public function edit_instance_validation($data, $files, $instance, $context) {
        global $DB;

        $errors = [];
        $courseids = \enrol_bycourse\local\eligibility::decode_course_ids($data['customtext1'] ?? []);
        if (!$courseids) {
            $errors['customtext1'] = get_string('requiredcourseserror', 'enrol_bycourse');
        } else if (in_array((int)$instance->courseid, $courseids, true)) {
            $errors['customtext1'] = get_string('targetcoursenotallowed', 'enrol_bycourse');
        } else {
            [$insql, $params] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
            if ($DB->count_records_select('course', "id {$insql}", $params) !== count($courseids)) {
                $errors['customtext1'] = get_string('invalidrequiredcourse', 'enrol_bycourse');
            }
        }
        if (!in_array((int)($data['customint1'] ?? -1), [0, 1], true)) {
            $errors['customint1'] = get_string('error');
        }
        $roles = get_default_enrol_roles($context);
        if (empty($data['roleid']) || !isset($roles[$data['roleid']])) {
            $errors['roleid'] = get_string('error');
        }
        if (!empty($data['enrolenddate']) && !empty($data['enrolstartdate']) &&
                $data['enrolenddate'] <= $data['enrolstartdate']) {
            $errors['enrolenddate'] = get_string('enroltimeendinvalid', 'enrol');
        }
        if (($data['customint2'] ?? 0) < 0) {
            $errors['customint2'] = get_string('invaliddata', 'error');
        }
        return $errors;
    }

    public function enrol_page_hook(stdClass $instance) {
        global $DB, $OUTPUT, $USER;

        if (isguestuser() || !isloggedin()) {
            return null;
        }
        $context = context_course::instance($instance->courseid);
        if (is_enrolled($context, $USER, '', true)) {
            return null;
        }
        if ($DB->record_exists('user_enrolments', ['enrolid' => $instance->id, 'userid' => $USER->id])) {
            return $this->render_notice(get_string('existinginactiveenrolment', 'enrol_bycourse'));
        }

        $content = '';
        if (!empty($instance->customtext2)) {
            $content .= $OUTPUT->box(format_text($instance->customtext2, FORMAT_MOODLE, ['context' => $context]), 'generalbox');
        }
        $availabilityerror = $this->get_availability_error($instance);
        if ($availabilityerror !== null) {
            return $content . $this->render_notice($availabilityerror);
        }
        if (!has_capability('enrol/bycourse:enrolself', $context)) {
            return $content . $this->render_notice(get_string('notenrollable', 'enrol'));
        }

        $courseids = $this->requirements()->get_course_ids((int)$instance->id);
        $eligible = \enrol_bycourse\local\eligibility::user_is_eligible(
            $USER->id,
            $courseids,
            (int)$instance->customint1
        );
        if (!$eligible) {
            return $content . $this->render_notice(get_string('noteligible', 'enrol_bycourse'));
        }

        $url = new moodle_url('/enrol/index.php', ['id' => $instance->courseid]);
        $form = new \enrol_bycourse\form\enrol_form($url, ['instanceid' => $instance->id]);
        if ($data = $form->get_data()) {
            $stillopen = $this->get_availability_error($instance) === null;
            $stilleligible = \enrol_bycourse\local\eligibility::user_is_eligible(
                $USER->id,
                $courseids,
                (int)$instance->customint1
            );
            if ((int)$data->instanceid === (int)$instance->id && $stillopen && $stilleligible) {
                $timestart = time();
                $timeend = $instance->enrolperiod ? $timestart + $instance->enrolperiod : 0;
                $this->enrol_user($instance, $USER->id, (int)$instance->roleid, $timestart, $timeend,
                    ENROL_USER_ACTIVE);
                if (!empty($instance->customint3)) {
                    $this->send_course_welcome_message_to_user(
                        $instance,
                        $USER->id,
                        (int)$instance->customint3,
                        $instance->customtext3,
                        (int)$instance->roleid
                    );
                }
                redirect(new moodle_url('/course/view.php', ['id' => $instance->courseid]),
                    get_string('youenrolledincourse', 'enrol'));
            }
        }

        return $content . $this->render_notice(get_string('eligible', 'enrol_bycourse'), 'success') . $form->render();
    }

    private function is_enrolment_open(stdClass $instance, bool $checkcapacity = true): bool {
        return $this->get_availability_error($instance, $checkcapacity) === null;
    }

    private function get_availability_error(stdClass $instance, bool $checkcapacity = true): ?string {
        global $DB;

        if ($instance->status != ENROL_INSTANCE_ENABLED || empty($instance->customint4)) {
            return get_string('enrolmentclosed', 'enrol_bycourse');
        }
        if ($instance->enrolstartdate && $instance->enrolstartdate > time()) {
            return get_string('enrolmentnotstarted', 'enrol_bycourse', userdate($instance->enrolstartdate));
        }
        if ($instance->enrolenddate && $instance->enrolenddate < time()) {
            return get_string('enrolmentended', 'enrol_bycourse', userdate($instance->enrolenddate));
        }
        if ($checkcapacity && $instance->customint2 > 0 &&
                $DB->count_records('user_enrolments', ['enrolid' => $instance->id]) >= $instance->customint2) {
            return get_string('maxenrolledreached', 'enrol_bycourse');
        }
        return null;
    }

    private function render_notice(string $message, string $type = 'warning'): string {
        $type = $type === 'success' ? 'success' : 'warning';
        return html_writer::div(
            s($message),
            "enrol-bycourse-notice enrol-bycourse-notice-{$type}",
            ['role' => $type === 'warning' ? 'alert' : 'status']
        );
    }

    /** @return int[] */
    private function extract_course_ids(array &$fields): array {
        $courseids = \enrol_bycourse\local\eligibility::decode_course_ids($fields['customtext1'] ?? []);
        // Keep a generated copy in the enrol record so core course backup can transport the configuration.
        $fields['customtext1'] = implode(',', $courseids);
        return $courseids;
    }

    private function requirements(): \enrol_bycourse\local\requirement_repository {
        return new \enrol_bycourse\local\requirement_repository();
    }
}
