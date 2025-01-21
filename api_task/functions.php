<?php
//Allow shortcodes in widgets
add_filter ('widget_text', 'do_shortcode');

/* Enqueue scripts
** Date: 02-09-2022
*/
function load_scripts() {

    wp_enqueue_script('custom-ajax-script', get_stylesheet_directory_uri() . '/js/ajax-filtering.js', array(), true);

    wp_localize_script('custom-ajax-script' , 'wp_ajax',
        array('ajax_url' => admin_url('admin-ajax.php'))
        );

}
add_action( 'wp_enqueue_scripts', 'load_scripts');


/* include Custom Functions 
** Date:01-03-2023
*/

require_once get_stylesheet_directory() . '/inc/mga-jobs-import.php';
require_once get_stylesheet_directory() . '/inc/mga-jobs.php';
require_once get_stylesheet_directory() . '/inc/put-candidate.php';
require_once get_stylesheet_directory() . '/inc/send-email-notification.php';
require_once get_stylesheet_directory() . '/inc/wpform-edited-job-entry.php';
require_once get_stylesheet_directory() . '/inc/jobs-validation-with-redirection.php';

//Display current year
function currentYear( $atts ){
    return date('Y');
}
add_shortcode( 'year', 'currentYear' );

// map section content js for new homepage
function homepagenew_map_content_js() { ?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			if (jQuery(window).width() > 1024) {
				setTimeout(function() {
					jQuery(".location-box-content").clone().insertBefore(".mapsvg-filters-wrap");
				}, 4500);
			}
			setTimeout(function() {
				jQuery(".map-content-block").hide();
			}, 4600);
			
			let forceFocusFn = function() {
			  // Gets the search input of the opened select2
			  var searchInput = document.querySelector('.select2-container--open .select2-search__field');
			  // If exists
			  if (searchInput)
				searchInput.focus(); // focus
			};

			//Every time a select2 is opened
			jQuery(document).on('select2:open', () => {
			  // We use a timeout because when a select2 is already opened and you open a new one, it has to wait to find the appropiate
			  setTimeout(() => forceFocusFn(), 300);
			});
			
			jQuery(document).on('select2:open', () => {
				document.querySelector('.select2-search__field').focus();
				
				setTimeout(function () {
					document.querySelector('.select2-search__field').focus();
				},300);
				
			  });
			// document.querySelector('.select2-search__field').on( "focus", function() {
			// 	document.querySelector('.select2-search__field').trigger( "focus" );
			// } );

			 jQuery(document).on('select2:open', () => {
			    setTimeout(function () {
					document.querySelector('.select2-search__field').focus();
				},300);
			  });

		 	/** Locations dropdown **/

	    	jQuery('.download_btn a').css("pointer-events", "none");
	    	jQuery('.download_btn a').css("display", "none");
	    	

	    	jQuery('.family-tabs .w-tabs-item').on('click',function(e){
	    		jQuery('.select-dropdown-list').hide();
	    		jQuery('.download_btn a').css("display", "none");	
	    	});

			jQuery('.dropdown_button').on('click', function(e){
				jQuery('.select-dropdown-list').slideToggle();
				jQuery('.download_btn a').css("display", "none");
			});

			jQuery('.select-dropdown-list .list-item').on('click', function(e){
				var itemValue = jQuery(this).data('value');
				//console.log(itemValue);
				jQuery('.dropdown_button span').text(jQuery(this).text()).parent().attr('data-value', itemValue);
				jQuery('.select-dropdown-list').slideToggle('active');
		        jQuery('.download_btn a').attr('href',itemValue);
		        jQuery('.download_btn a').css("pointer-events", "auto");
				jQuery('.download_btn a').css("display", "flex");
			});

			jQuery('.family-tabs button').on('click',function(e){

		    	//jQuery('#location-dropdown').hide();
		    	jQuery('.download_btn a').attr('href','#');
		    	jQuery('.download_btn a').css("pointer-events", "none");
		    	jQuery('.download_btn a').css("display", "none");
		    	jQuery('.dropdown_button span').text('Locations');
		    
		    });
		});

	</script><?php
}
add_action( 'wp_footer', 'homepagenew_map_content_js' , 999999999);


if( function_exists('acf_add_options_page') ) {
    
    acf_add_options_page(array(
        'page_title'    => 'Location Options',
        'menu_title'    => 'Location Options',
        'menu_slug'     => 'location-options',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
	
	acf_add_options_page(array(
		'page_title'    => 'Jobs API Options',
		'menu_title'    => 'Jobs API Options',
		'menu_slug'     => 'jobs-api-options',
		'capability'    => 'edit_posts',
		'redirect'      => false
	));

}

/** Location dropdown on Family Resources page */

function locations_pdfs_upload($atts){

	$atts = shortcode_atts( [
	    "state" => $location_name,
	], $atts );

	$AllLocations = get_field('mga_location_fr_locations','options'); 
	$download_community_button = get_field('download_community_button','options'); 
	$download_community_button_icon = get_field('download_community_button_icon','options'); 
	$dropdown_title = get_field('dropdown_title','options'); ?>
	
	<div class="select-dropdown tab-select">
		<button href="#" role="button" data-value="" class="dropdown_button"><span><?php echo $dropdown_title; ?></span> <i class="zmdi zmdi-chevron-down"></i>
		</button>
		<ul class="select-dropdown-list close-tab" id="location-dropdown">
			<?php
				foreach ($AllLocations as $StateArr) {

					$StateName = $StateArr['mga_location_fr_parent_location_name'];
					$cityArr = $StateArr['mga_location_fr_parent_location'];
			
					if($StateName == $atts['state']){
						foreach($cityArr as $city){
							if($city['mga_location_fr_child_location_resources']['url'] == ""){
								echo '<li data-value="javascript:void(0);" class="list-item">'.$city['mga_location_fr_child_location_name'].'</li>';	
							} else {
								echo '<li data-value="'.$city['mga_location_fr_child_location_resources']['url'].'" class="list-item">'.$city['mga_location_fr_child_location_name'].'</li>';
							}
						}
					}
				}
			?>
		</ul>
	</div>
	<div class="download_btn">
		<a href="javascript:void(0);" class="dw-wrp" download>
		<div class="dw-ic"><img src="<?php echo $download_community_button_icon['url']; ?>" alt="<?php echo $download_community_button_icon['alt']; ?>"></div>
			<div class="dw-content"><?php echo $download_community_button; ?></div>
		</a>
	</div>
	<?php
}
add_shortcode('location_dropdown','locations_pdfs_upload');


/* Get Child posts of parent Locations ( USED ON: state pages )
** Date: 12/12/2022
*/
function mga_get_child_posts() { 
	
	global $post;
 	
 	$args = array(
	    'post_type'      => 'locations',
	    'posts_per_page' => -1,
	    'post_parent'    => $post->ID,
	    'order'          => 'ASC',
	    'orderby'        => 'title'
	 );

	$parent = new WP_Query( $args );
	if ( $parent->have_posts() ) : ?>
	    <?php while ( $parent->have_posts() ) : $parent->the_post(); ?>
	        <div id="parent-<?php the_ID(); ?>" class="parent-page">
	            	<h4><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4>
		        </div>
	    <?php endwhile; ?>
	<?php endif; 
	wp_reset_postdata();
}
add_shortcode('get_locations_child_posts', 'mga_get_child_posts');

/*
**	WPforms add new date format for admin option selection
*/
function wpf_add_date_field_formats( $formats ) {
  
 // Adds new format 24-07-2021
 $formats[ 'Y-m-d' ] = 'Y-m-d';
  
 return $formats;
}
add_filter( 'wpforms_datetime_date_formats', 'wpf_add_date_field_formats', 10, 1 );

/*
**	WPforms scrollTop hook function after submit success of Job Application
*/
function wpf_dev_event_after_submit() {
   ?>
    <script type="text/javascript">
            ( function() {
                jQuery( window ).on( 'load', function() {
					wpforms.scrollToError = function() {}; 
                    wpforms.animateScrollTop = function() {};
                    jQuery( '#wpforms-14708' ).on( 'wpformsAjaxSubmitSuccess', function( e, response ) {
							 var headerHeight = jQuery('header').outerHeight();
							jQuery('html,body').animate({
								scrollTop: jQuery('html,body').offset().top - headerHeight
							}, 800, function() {
							});
                    } );
                } )
            }() );
    </script>
   <?php
}
add_action( 'wpforms_wp_footer_end', 'wpf_dev_event_after_submit', 10 );

/* Preload resources on homepage for improving performance
** Date: 27-07-2023
*/
function mga_preload_resources() {
	if(is_front_page()){
	  echo '
	  <link rel="preload" as="image" href="/wp-content/uploads/2023/07/mga-homecare-banner-slider-01-gradient-right-02a.webp" />
	  <link rel="preload" as="image" href="/wp-content/uploads/2023/07/mga-homecare-banner-slider-02-gradient-02a.webp" />
	  ';
	}
}
add_action( 'wp_head', 'mga_preload_resources' );