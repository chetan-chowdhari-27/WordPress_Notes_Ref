<?php 

// Function to send Email Notification when 'workday_jobs_import_from_api' fails

function custom_cron_schedules($schedules) {
    // Schedule to check for failures every 10 minutes
    // $schedules['every_ten_minutes'] = array(
    //     'interval' => 600, // 10 minutes
    //     'display'  => esc_html__('Every 10 Minutes'),
    // );
    $schedules['twice_day_twelve_hrs'] = array(
        'interval' => 43200, // 10 minutes
        'display'  => esc_html__('Every 12 Hrs'),
    );
    return $schedules;
}
add_filter('cron_schedules', 'custom_cron_schedules');

function send_custom_failure_notification() {
    if (get_transient('workday_jobs_import_failed')) {
        $error_details = get_transient('workday_jobs_import_error');
        $to = 'chasedevteam@gmail.com';
        $subject = 'Alert: workday_jobs_import_from_api Cron Not Scheduled';
        $message = 'The cron job "workday_jobs_import_from_api" is not scheduled. It has now been scheduled automatically.<br><br>';
        $message .= 'Error Details : ';
        $message .= nl2br(sanitize_text_field($error_details));
        $headers = array('Content-Type: text/html; charset=UTF-8', 'From: chasedevteam@gmail.com');

        wp_mail($to, $subject, $message, $headers);
        delete_transient('workday_jobs_import_failed');
        delete_transient('workday_jobs_import_error'); 
    }
}

if (!wp_next_scheduled('send_email_notification_twelve_hrs')) {
    wp_schedule_event(time(), 'twice_day_twelve_hrs', 'send_email_notification_twelve_hrs');
}
add_action('send_email_notification_twelve_hrs', 'send_custom_failure_notification');
?>