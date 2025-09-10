<?php
require_once("$CFG->libdir/externallib.php"); //This includes Moodle’s core library for external API support.
//externallib.php contains classes and helpers for defining web service functions.

class block_logged_user_external extends external_api {

    public static function get_user_data_parameters() {
        return new external_function_parameters([]);
    }

    public static function get_user_data() {
        global $USER, $DB, $CFG;

        $user_initials = strtoupper(substr($USER->firstname, 0, 1) . substr($USER->lastname, 0, 1));
        $profile_image_url = $CFG->wwwroot . "/user/pix.php/{$USER->id}/f1.jpg";
        $email = $USER->email;

        $courses = $DB->get_records_sql("
            SELECT c.id, c.fullname
            FROM {course} c
            JOIN {user_enrolments} ue ON ue.userid = ?
            JOIN {enrol} e ON e.id = ue.enrolid AND e.courseid = c.id
        ", [$USER->id]);

        $course_names = [];
        foreach ($courses as $course) {
            $course_names[] = $course->fullname;
        }

        return [
            'user_initials' => $user_initials,
            'profile_image' => $profile_image_url,
            'email' => $email,
            'courses' => $course_names
        ];
    }

    public static function get_user_data_returns() {
        return new external_single_structure([
            'user_initials' => new external_value(PARAM_TEXT, 'User initials'),
            'profile_image' => new external_value(PARAM_URL, 'Profile image URL'),
            'email' => new external_value(PARAM_EMAIL, 'User email'),
            'courses' => new external_multiple_structure(new external_value(PARAM_TEXT, 'Course name'))
        ]);
    }
}
