<?php
class block_logged_user extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_logged_user');
    }

    public function get_content() {
        global $USER, $OUTPUT, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        // Get user details
        $user_initials = strtoupper(substr($USER->firstname, 0, 1) . substr($USER->lastname, 0, 1));
        $profile_image = $OUTPUT->user_picture($USER, ['size' => 50]);
        $email = $USER->email;

        // Get enrolled courses
        $courses = $DB->get_records_sql("
            SELECT c.id, c.fullname 
            FROM {course} c
            JOIN {enrol} e ON e.courseid = c.id
            JOIN {user_enrolments} ue ON ue.enrolid = e.id
            WHERE ue.userid = ?
        ", [$USER->id]);

        // Prepare course list
        $course_list = "<ul>";
        foreach ($courses as $course) {
            $course_list .= "<li>{$course->fullname}</li>";
        }
        $course_list .= "</ul>";

        // Block content
        $this->content = new stdClass();
        $this->content->text = "
            <div>
                <p><strong>Username:</strong> $user_initials</p>
                <p>$profile_image</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Enrolled Courses:</strong> $course_list</p>
            </div>";

        return $this->content;
    }
     // ✅ Add this function to control delete permissions
     public function instance_can_be_deleted() {
        global $PAGE;

        // Allow deleting only on the user's Dashboard
        return $PAGE->pagetype === 'my-index';
    }
}
