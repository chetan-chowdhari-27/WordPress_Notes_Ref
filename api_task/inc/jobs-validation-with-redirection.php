<?php

/* Redirect Function Start Here */

// add_action('template_redirect', 'custom_redirect_im_interested_careers_page');
// function custom_redirect_im_interested_careers_page() {
//     if (is_page('im-interested-careers')) {
//         $job_uuid = isset($_GET['jid']) ? sanitize_text_field($_GET['jid']) : '';
//         if (empty($job_uuid) ) {
//             wp_redirect(home_url('/careers/'), 301);
//         }
//     }
// }

// add_action('template_redirect', 'redirect_deleted_jobs_posts_to_homepage');
// function redirect_deleted_jobs_posts_to_homepage() {
//     global $wp_query;
//     if ($wp_query->is_404() && !is_admin() && !empty($_SERVER['REQUEST_URI'])) {
//         $post_type = get_query_var('post_type');
//         if ($post_type === 'jobs') {
//             wp_redirect(home_url('/careers/'), 301);
//         }
//     }
// }

// add_action('init', 'job_redirect');
// function job_redirect(){
//     if(isset($_GET['jid'])){
//         $args = array(
//             'posts_per_page' => -1,
//             'post_type' => 'jobs',
//             'post_status' => 'publish',
//             'meta_key' => 'smr_job_id',
//             'meta_query' => array(
//                 array(
//                     'key' => 'smr_job_id',
//                     'value' => $_GET['jid'],
//                     'compare' => '=',
//                 ),
//             ),
//         );
//         $category_posts = new WP_Query($args);
//         $post_count = $category_posts->post_count;
//         if($post_count == 0){
//             exit( wp_redirect( home_url( '/careers/' ) )); 
//         }
//     }
// }

// /* WpForms validation on Submit jobs is not Available */

// add_action( 'wpforms_process', 'custom_wpforms_validation', 10, 3 );
// function custom_wpforms_validation( $fields, $entry, $form_data ) {
//     // if ( !isset( $_GET['jid'] ) || empty( $_GET['jid'] ) ) {
//     //     return;
//     // }

//     $job_uuid = $fields[67]['value'];
//     if ( $form_data['id'] == 14708 ) { 
//         $args = array(
//             'posts_per_page' => -1,
//             'post_type' => 'jobs',
//             'post_status' => 'publish',
//             'meta_key' => 'smr_job_id',
//             'meta_query' => array(
//                 array(
//                     'key' => 'smr_job_id',
//                     'value' => $_GET['jid'],
//                     'compare' => '=',
//                 ),
//             ),
//         );
//         $job_query = new WP_Query( $args );
//         if ( $job_query->have_posts() ) {
//             $job_post = $job_query->posts[0];
//             $job_status = get_post_meta( $job_post->ID, 'job_status', true ); 
//             if ( $job_status == 'closed' ) {
//                 wpforms()->process->errors[ $form_data['id'] ]['footer'] = '<strong>This job has been closed. <a href="/careers/">Click here</a> to find more jobs.</strong>';
//                 return; 
//             }
//         } 
//     }
// }

/* Redirect Function End Here */


// Custom validation for WPForms
function custom_wpforms_validation( $fields, $entry, $form_data ) {
    $job_uuid = $fields[67]['value'];

    if ( $form_data['id'] == 14708 ) { 
        // Assuming job_uuid directly corresponds to smr_job_id
        $args = array(
            'posts_per_page' => 1, // We only need to check if it exists
            'post_type' => 'jobs',
            'post_status' => 'publish',
            'meta_key' => 'smr_job_id',
            'meta_value' => $job_uuid,
            'meta_compare' => '=',
        );

        $jobs_query = new WP_Query($args);

        if (!$jobs_query->have_posts()) {
            wpforms()->process->errors[$form_data['id']]['footer'] = '<strong>This job has been closed or does not exist. <a href="/careers/">Click here</a> to find more jobs.</strong>';
        }
        
        // Always reset post data after custom query
        wp_reset_postdata();
    }
}
add_action('wpforms_process', 'custom_wpforms_validation', 10, 3);

// Redirect from the "im-interested-careers" page if no job ID is present
function custom_redirect_im_interested_careers_page() {
    if (is_page('im-interested-careers')) {
        $job_uuid = isset($_GET['jid']) ? sanitize_text_field($_GET['jid']) : '';
        if (empty($job_uuid)) {
            wp_redirect(home_url('/careers/'), 301);
            exit; // Always call exit after wp_redirect
        }
    }
}
add_action('template_redirect', 'custom_redirect_im_interested_careers_page');

// Redirect for deleted job posts to homepage
function redirect_deleted_jobs_posts_to_homepage() {
    global $wp_query;
    if ($wp_query->is_404() && !is_admin()) {
        // Check if the request is for custom post type 'jobs'
        if (get_query_var('post_type') === 'jobs') {
            wp_redirect(home_url('/careers/'), 301);
            exit; // Always call exit after wp_redirect
        }
    }
}
add_action('template_redirect', 'redirect_deleted_jobs_posts_to_homepage');

// Check if job ID exists in the 'jobs' CPT during init
function job_redirect() {
    if (isset($_GET['jid'])) {
        $job_uuid = sanitize_text_field($_GET['jid']);
        
        $args = array(
            'posts_per_page' => 1, // We only need to check if it exists
            'post_type' => 'jobs',
            'post_status' => 'publish',
            'meta_key' => 'smr_job_id',
            'meta_value' => $job_uuid,
            'meta_compare' => '=',
        );

        $category_posts = new WP_Query($args);

        if ($category_posts->post_count == 0) {
            wp_redirect(home_url('/careers/'), 301);
            exit; // Always call exit after wp_redirect
        }
        
        // Always reset post data after custom query
        wp_reset_postdata();
    }
}
add_action('init', 'job_redirect');