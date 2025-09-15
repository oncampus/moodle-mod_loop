<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Add loop form.
 *
 * @package   mod_loop
 * @copyright 2025 oncampus GmbH
 * @author    Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/loop/lib.php');
require_once($CFG->dirroot . '/mod/loop/locallib.php');

/**
 * Loop module form.
 *
 * @package   mod_loop
 * @copyright 2025 oncampus GmbH
 * @author    Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_loop_mod_form extends moodleform_mod {
    /**
     * Form definition.
     */
    public function definition() {
        global $DB, $PAGE, $COURSE;

        $PAGE->force_settings_menu();

        $PAGE->requires->js_call_amd('mod_loop/loop', 'init', []);

        $mform = $this->_form;

        $mform->addElement('text', 'name', get_string('loopinstance_name', 'loop'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements(get_string('loopinstance_introduction', 'loop'));

        $loops = [
        ];
        $courseid = $PAGE->course->idnumber;
        $loopsystems = get_allowed_loops($courseid);
        $firsturl = '';
        foreach ($loopsystems as $loopsystem) {
            $loopsystemurl = $loopsystem->url;
            if ($firsturl == '') {
                $firsturl = $loopsystemurl;
            }
            $loops[$loopsystemurl] = $loopsystem->name . ' (' . $loopsystemurl . ')';
        }

        $mform->addElement('select', 'url', get_string('loopinstance_url', 'loop'), $loops);
        $mform->addRule('url', null, 'required', null, 'client');

        $chapters = [];
        $chapterurl = '';
        if ($this->current && isset($this->current->url)) {
            $chapterurl = $this->current->url;
        } else {
            $chapterurl = $firsturl;
        }

        // Add error handling for API calls.
        if ($chapterurl) {
            $structurejson = get_loop_structure($chapterurl);
            if ($structurejson !== false) {
                $structure = json_decode($structurejson);
                if ($structure) {
                    foreach ($structure as $structureitem) {
                        $chapters[$structureitem->key] = $structureitem->value;
                    }
                }
            }
        }

        $chapterselect = $mform->addElement('select', 'chapter', get_string('loopinstance_chapter', 'loop'), $chapters);
        if (isset($this->current->chapter)) {
            $chapterselect->setSelected($this->current->chapter);
        }

        $mform->addElement('text', 'page', get_string('loopinstance_page', 'loop'), ['size' => '255']);
        $mform->setType('page', PARAM_RAW);

        $themes = [];
        $themeurl = '';
        if ($this->current && isset($this->current->url)) {
            $themeurl = $this->current->url;
        } else {
            $themeurl = $firsturl;
        }

        // Add error handling for themes API call.
        if ($themeurl) {
            $themesjson = get_loop_themes($themeurl);
            if ($themesjson !== false) {
                $themelist = json_decode($themesjson);
                if ($themelist) {
                    foreach ($themelist as $themelistitem) {
                        $themes[$themelistitem->key] = $themelistitem->value;
                    }
                }
            }
        }

        $themeselect = $mform->addElement('select', 'theme', get_string('loopinstance_theme', 'loop'), $themes);
        if (isset($this->current->theme)) {
            $themeselect->setSelected($this->current->theme);
        }

        $this->standard_coursemodule_elements();

        $this->add_action_buttons(true, false, null);
    }


    /**
     * Get data.
     *
     * @return bool|stdClass
     */
    public function get_data() {

        $data = parent::get_data();

        if (!$data) {
            return false;
        }

        if (!empty($data)) {
            $mform =& $this->_form;

            if (!empty($mform->_submitValues['theme'])) {
                $data->theme = $mform->_submitValues['theme'];
            }
            if (!empty($mform->_submitValues['chapter'])) {
                $data->chapter = $mform->_submitValues['chapter'];
            }
        }

        return $data;
    }

    /**
     * Form validation
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate URL selection.
        if (empty($data['url'])) {
            $errors['url'] = get_string('required');
        }

        // Validate that page field is not too long if provided.
        if (!empty($data['page']) && strlen($data['page']) > 1024) {
            $errors['page'] = get_string('err_maxlength', 'form', 1024);
        }

        return $errors;
    }
}
