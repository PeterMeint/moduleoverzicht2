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
 * moduleoverzicht block renderer
 *
 * @package    block_moduleoverzicht
 * @copyright  2016 Peter Meint Heida <peter.meint.heida@springinstituut.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once("{$CFG->libdir}/completionlib.php");

class block_moduleoverzicht_renderer extends plugin_renderer_base {

    /**
     * Construct main contents of moduleoverzicht block
     *
     * @return string html to be displayed in course_overview block
     */
    public function moduleoverzicht($courses, $show_backgroundimage) {
		global $DB, $CFG;

		$courselist = '';

		// In case of 1 or 2 courses the info is seperated in two columns
		// If more 3 columns are used to show the courseinfo.
		$nr_courses = count($courses);

		switch ($nr_courses) {
			case	1:
			case	2:	$class_courseinfo = 'courseinfo-2col';
						break;
			default	:	$class_courseinfo = 'courseinfo-3col';
						break;
		}

		if ($nr_courses > 0) {
            foreach ($courses as $c) {

                $clink = '';

                $bgcolor = ' background-color:#' . $this->get_course_color($c->id) . '; ';

                if ($show_backgroundimage) {
                    $courseimagecss = "background-image: url(" . $CFG->wwwroot . "/blocks/moduleoverzicht/pix/course_images/course" . $c->id . ".jpg); ";
                }

                $dynamicinfo = '<div data-courseid="' . $c->id . '" class="dynamicinfo"></div>';

                $progress = '';
                $progress_info = $this->course_completion_progress($c);
                $progress = $progress_info->progresshtml;

                $clink = '<div style="cursor:pointer; '.$courseimagecss.'" onclick="document.location=\''.$CFG->wwwroot.'/course/view.php?id='.$c->id.'#section-0\'" class="courseinfo '.$class_courseinfo.'" style="'.$courseimagecss.'">
				<div class="courseinfo-body"><h3>'.format_string($c->fullname).'</h3>'.$progress.'</div>
				</div>';

                $courselist .= $clink;
            }
		}
        return $courselist;
    }
	
    /**
     * Construct heading of moduleoverzicht block
     *
     * @return string html to be displayed in course_overview block
     */
    public function heading() {
        global $PAGE;

        $css_filename = '/blocks/moduleoverzicht/styles.css';

        $PAGE->requires->css($css_filename, true);

        $html = '';

		$html .= html_writer::start_tag('div', array('class' => 'my-module-overview'));
		$html .= html_writer::start_tag('section', array('id' => 'fixy-my-courses'));
		$html .= html_writer::start_tag('div', array('class' => 'clearfix'));
		$html .= html_writer::tag('h2', get_string('mycourses', 'block_moduleoverzicht'), array());

        return $html;
    }
	
    /**
     * Construct footer/finishing of moduleoverzicht block
     *
     * @return string html to be displayed in course_overview block
     */
    public function footer() {
        $html = '';
		$html .= html_writer::end_tag('div');
		$html .= html_writer::end_tag('section');
		$html .= html_writer::end_tag('div');

        return $html;
    }

     /**
     * get hex color based on hash of course id
     *
     * @return string
     */
    private function get_course_color($id) {
        $color = substr(md5($id), 0, 6);
        return $color;
    }

    /**
     * Get course completion progress for specific course.
     * NOTE: It is by design that even teachers get course completion progress, this is so that they see exactly the
     * same as a student would in the personal menu.
     *
     * @param $course
     * @return stdClass | null
     */
    private function course_completion_progress($course) {

        if (!isloggedin() || isguestuser()) {
            return null; // Can't get completion progress for users who aren't logged in.
        }

        // Security check - are they enrolled on course.
        $context = \context_course::instance($course->id);
        if (!is_enrolled($context, null, '', true)) {
            return null;
        }

        $completioninfo = new \completion_info($course);
        $trackcount = 0;
        $compcount = 0;

        if ($completioninfo->is_enabled()) {
            $modinfo = get_fast_modinfo($course);

            foreach ($modinfo->cms as $thismod) {
                if (!$thismod->uservisible) {
                    // Skip when mod is not user visible.
                    continue;
                }
                $completioninfo->get_data($thismod, true);

                if ($completioninfo->is_enabled($thismod) != COMPLETION_TRACKING_NONE) {
                    $trackcount++;
                    $completiondata = $completioninfo->get_data($thismod, true);
                    if ($completiondata->completionstate == COMPLETION_COMPLETE ||
                        $completiondata->completionstate == COMPLETION_COMPLETE_PASS) {
                        $compcount++;
                    }
                }
            }
        }

        $compobj = (object) array('complete' => $compcount, 'total' => $trackcount, 'progresshtml' => '');
        if ($trackcount > 0) {
            $progress = get_string('progresstotal', 'completion', $compobj);
            // TODO - we should be putting our HTML in a renderer.
            $progresspercent = ceil(($compcount/$trackcount)*100);
            $progressinfo = '<div class="completionstatus outoftotal">'.$progress.'<span class="pull-right">'.$progresspercent.'%</span></div>
            <div class="completion-line" style="width:'.$progresspercent.'%"></div>
            ';
            $compobj->progresshtml = $progressinfo;
        }

        return $compobj;
    }

}
