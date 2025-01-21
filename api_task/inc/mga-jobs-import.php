<?php 

function custom_cron_schedules_five_mins($schedules) {
    $schedules['five_minutes'] = array(
        'interval' => 300, // 300 seconds = 5 minutes
        'display'  => __('Every 5 Minutes'),
    );
    return $schedules;
}
add_filter('cron_schedules', 'custom_cron_schedules_five_mins');

// Schedule the cron job upon activation
function custom_jobs_cron_activation() {
    if (!wp_next_scheduled('workday_jobs_import_from_api')) {
        wp_schedule_event(time(), 'five_minutes', 'workday_jobs_import_from_api');
    }
}
register_activation_hook(__FILE__, 'custom_jobs_cron_activation');

// Unschedule the cron job upon deactivation
function custom_jobs_cron_deactivation() {
    wp_clear_scheduled_hook('workday_jobs_import_from_api');
}
register_deactivation_hook(__FILE__, 'custom_jobs_cron_deactivation');

// Hook the cron job event to the function
add_action('workday_jobs_import_from_api', 'fetch_and_update_jobs');
add_action('init', 'custom_jobs_cron_activation');

function fetch_and_update_jobs() {
    error_log('Cron task triggered.');
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://impl-services1.wd12.myworkday.com/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic SVNVX1JhYVNfSm9iX1Bvc3RpbmdzOjV6OGh2bmVhJHJpa1FVKU5wSVF4b3U=',
        ),
    ));

    $response = curl_exec($curl);
    $curl_error = curl_error($curl); // Capture cURL errors
    curl_close($curl);

    if ($response === false) {
        error_log('cURL error: ' . $curl_error);
        mark_jobs_import_failed('cURL error: ' . $curl_error);
        return;
    }

    error_log('API Response: ' . $response);

    $jsonAllDecoded = json_decode($response);

    if ($jsonAllDecoded === null) {
        error_log('JSON decoding failed: ' . json_last_error_msg());
        mark_jobs_import_failed('JSON decoding failed: ' . json_last_error_msg());
        return;
    }

    if (isset($jsonAllDecoded->Report_Entry)) {
        $api_ids = [];

        // Update or insert jobs
        foreach ($jsonAllDecoded->Report_Entry as $jobdetails) {
            $jobRequisitionId = sanitize_text_field($jobdetails->jobRequisitionId);
            $api_ids[] = $jobRequisitionId;

            $existing_post = get_posts(array(
                'post_type'  => 'jobs',
                'meta_key'   => 'smr_job_id',
                'meta_value' => $jobRequisitionId,
                'numberposts' => 1,
            ));

            $post_data = array(
                'post_title'   => sanitize_text_field($jobdetails->title),
                'post_status'  => 'publish',
                'post_type'    => 'jobs',
            );

            if ($existing_post) {
                // Post exists, update it
                $post_id = $existing_post[0]->ID;
                $post_data['ID'] = $post_id;
                $result = wp_update_post($post_data);
                error_log('Updated job ID: ' . $post_id);
            } else {
                // Post doesn't exist, insert a new one
                $result = wp_insert_post($post_data);
                if (is_wp_error($result)) {
                    error_log('Error inserting job: ' . $result->get_error_message());
                    continue;
                }
                $post_id = $result;
                error_log('Inserted new job ID: ' . $post_id);
            }

            if ($post_id) {
                // Update post meta
                update_post_meta($post_id, 'smr_job_id', $jobRequisitionId);
                update_post_meta($post_id, 'job_description_mga', sanitize_text_field($jobdetails->jobDescription));
                update_post_meta($post_id, 'jb_country', 'us');
                update_post_meta($post_id, 'job_remote_work', sanitize_text_field($jobdetails->remoteType));

                // Process job type
                $jobTypeValue = $jobdetails->timeType;
                $jobtype = ($jobTypeValue == 'Full time') ? 'Full-time' : (($jobTypeValue == 'Part time') ? 'Part-time' : '');
                update_field('job_type', $jobtype, $post_id);
                error_log('Updated job type for post ID: ' . $post_id);
            }

            // Set terms (if available)
            if (!empty($jobdetails->department)) {
                wp_set_object_terms($post_id, sanitize_text_field($jobdetails->department), 'job_department', true);
            }
            if (!empty($jobdetails->primaryLocationCity)) {
                wp_set_object_terms($post_id, sanitize_text_field($jobdetails->primaryLocationCity), 'job_cities', true);
            }
            if (!empty($jobdetails->primaryLocationState)) {
                wp_set_object_terms($post_id, sanitize_text_field($jobdetails->primaryLocationState), 'job_states', true);
            }
        }

        // Delete outdated jobs
        $existing_jobs = get_posts(array(
            'post_type' => 'jobs',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ));

        foreach ($existing_jobs as $post_id) {
            $job_id = get_post_meta($post_id, 'smr_job_id', true);
            if (!in_array($job_id, $api_ids)) {
                wp_delete_post($post_id, true); // Delete permanently
                error_log('Deleted outdated job ID: ' . $post_id);
            }
        }

    delete_transient('workday_jobs_import_failed');

    } else {
        error_log('JSON missing "Report_Entry".');
        mark_jobs_import_failed('JSON missing "Report_Entry".');
    }
}

function mark_jobs_import_failed($error_details) {
    set_transient('workday_jobs_import_failed', true, 600);
    set_transient('workday_jobs_import_error', $error_details, 600);
}