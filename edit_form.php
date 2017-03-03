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

/**
 * Defines the form for editing Moduleoverview block instances.
 *
 * @package    block_moduleoverzicht
 * @copyright  2016 Peter Meint Heida <peter.meint.heida@springinstituut.nl>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Form for editing Moduleoverview block instances.
 *
 */
class block_moduleoverzicht_edit_form extends block_edit_form {
    /**
     * The definition of the fields to use.
     *
     * @param MoodleQuickForm $mform
     */
    protected function specific_definition($mform) {
        global $DB;

        // Fields for editing activity_results block title and contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('selectyesno', 'config_showtitle', get_string('config_show_title', 'block_moduleoverzicht'));

        $mform->addElement('selectyesno', 'config_showbackgroundimage', get_string('config_show_backgroundimage', 'block_moduleoverzicht'));

        // Short description of the module/course
        $courses = $DB->get_records_select('course', 'id > 1');
        foreach($courses as $course) {
            $mform->addElement('filepicker', 'config_backgroundimage'.$course->id, get_string('config_backgroundimage', 'block_moduleoverzicht').'<br>Course '.$course->id, null,
                array('maxbytes' => 1485760, 'accepted_types' => array('.png', '.jpg')));
        }

    }

    function set_data($default) {
        parent::set_data($default);
    }


}