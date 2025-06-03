--------------------- functions.php --------------------- 

add_action('wp_enqueue_scripts', 'my_ajax_script_enqueue'); function my_ajax_script_enqueue() { wp_enqueue_script('my-ajax-script', get_template_directory_uri() . '/js/my-ajax.js', array('jquery'), null, true); wp_localize_script('my-ajax-script', 'my_ajax_object', array( 'ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('my_ajax_nonce') )); } add_action('wp_ajax_my_custom_action', 'my_custom_ajax_handler'); add_action('wp_ajax_nopriv_my_custom_action', 'my_custom_ajax_handler'); function my_custom_ajax_handler() { check_ajax_referer('my_ajax_nonce', 'security'); $response = array( 'message' => 'Hello from PHP via AJAX!' ); wp_send_json_success($response); } 


--------------------- my-ajax.js --------------------- 

jQuery(document).ready(function ($) { $('#my-ajax-button').on('click', function () { $.ajax({ url: my_ajax_object.ajax_url, type: 'POST', data: { action: 'my_custom_action', security: my_ajax_object.nonce }, success: function (response) { if (response.success) { $('#my-ajax-result').html(response.data.message); } else { $('#my-ajax-result').html('Error occurred.'); } }, error: function () { $('#my-ajax-result').html('AJAX failed.'); } }); }); }); 


--------------------- test-template.php --------------------- 


<?php /* Template Name: Testing pages */ get_header(); ?> <div id="my-ajax-container"> <button id="my-ajax-button">Click Me</button> <div id="my-ajax-result"></div> </div> <?php get_footer();?>