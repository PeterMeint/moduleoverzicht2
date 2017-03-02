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
 * moduleoverzicht block
 *
 * @package   block_moduleoverzicht
 * @copyright 2106 onwards Peter Meint Heida  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_moduleoverzicht extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_moduleoverzicht');
    }

    function has_config() {
        return true;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function specialization() {
        $this->title = '';//isset($this->config->title) ? format_string($this->config->title) : format_string(get_string('pluginname', 'block_moduleoverzicht'));
    }

    function instance_allow_multiple() {
        return true;
    }

    function get_content() {

	if($this->config->showtitle) {
			$this->title = get_string('pluginname', 'block_moduleoverzicht');
		} else {
			$this->title = '';
		}
		
		$this->content = new stdClass();
		$this->content->text  = '';
		
        $renderer = $this->page->get_renderer('block_moduleoverzicht');

		$this->content->text  .= $renderer->heading();

		// Ordering of courses based on sortorder ascending
		$mycourses = enrol_get_my_courses(null, 'visible DESC, sortorder ASC');

		// Default text when no courses.
		if (!$mycourses) {
			$this->content->text  .= html_writer::tag('p', get_string('coursefixydefaulttext', 'block_moduleoverzicht'), array());
		} else {
			$this->content->text .= $renderer->moduleoverzicht($mycourses, $this->config->showbackgroundimage);
		}
		
		$this->content->text  .= $renderer->footer();

		$this->content->footer = '';

		return $this->content;
		
    }
}


