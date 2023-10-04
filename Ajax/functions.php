<?php
/**
 * a_theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package a_theme
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function a_theme_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on a_theme, use a find and replace
		* to change 'a_theme' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'a_theme', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'a_theme' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'a_theme_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'a_theme_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function a_theme_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'a_theme_content_width', 640 );
}
add_action( 'after_setup_theme', 'a_theme_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function a_theme_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'a_theme' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'a_theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'a_theme_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function a_theme_scripts() {
	wp_enqueue_style( 'a_theme-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'a_theme-style', 'rtl', 'replace' );

	wp_enqueue_script( 'a_theme-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'a_theme_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

add_image_size('custom_size_thumbnail',300,100, true);
// custom-size-thumbnail


require get_template_directory() . '/custom.php';


function myfilter_2() {
    $category_filter = $_POST['categoryfilter'];
    $taxonomy_filter = $_POST['taxonomyfilter'];
    $director_filter = $_POST['directorfilter'];
	$paged = get_query_var('paged');

    $args = array(
        'post_type' => 'movies',
        'posts_per_page' => 2,
        'paged' => $paged,
        'tax_query' => array(),

    );

    if (!empty($category_filter)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'movies_taxonomy',
            'field' => 'id',
            'terms' => $category_filter,
        );
    }

    if (!empty($taxonomy_filter)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'location_taxonomy',
            'field' => 'id',
            'terms' => $taxonomy_filter,
            'include_children' => true,
        );
    }

    if (!empty($director_filter)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'director_taxonomy',
            'field' => 'id',
            'terms' => $director_filter,
        );
    }    
    if (isset($_POST['keyword']) && !empty($_POST['keyword'])) {
        $args['s'] = sanitize_text_field($_POST['keyword']);
    }
    $the_query = new WP_Query($args);
    if ($the_query->have_posts()) :
        while ($the_query->have_posts()) : $the_query->the_post(); ?>
            <h2><a href="<?php echo get_permalink(); ?>" target="_blank"><?php the_title(); ?></a></h2>
           <?php  echo the_post_thumbnail('custom_size_thumbnail');
            $terms = get_the_terms(get_the_ID(), 'location_taxonomy');
	             if ($terms && !is_wp_error($terms)) {
	                 foreach ($terms as $term) {
	                     echo ' '.$term->name.'';
	                 }
	             }
	        $terms = get_the_terms(get_the_ID(), 'movies_taxonomy');
                 if ($terms && !is_wp_error($terms)) {
                     foreach ($terms as $term) {
                         echo ' '.$term->name.' <br>';
                     }
                 }
        endwhile;

        echo paginate_links(array(
            'total' => $the_query->max_num_pages      
     	));
        wp_reset_postdata();
    else :
        echo '<p>No posts found</p>';
    endif;

    die();
}

add_action('wp_ajax_myfilter_2', 'myfilter_2');
add_action('wp_ajax_nopriv_myfilter_2', 'myfilter_2');



function custom_search_action() {
    $category_filter = $_POST['categoryfilter'];
    $taxonomy_filter = $_POST['taxonomyfilter'];
    $director_filter = $_POST['directorfilter'];
    $paged = get_query_var('paged');

    $args = array(
        'post_type' => 'movies',
        'posts_per_page' => 2,
        'paged' => $paged,
        'tax_query' => array(),
    );

    if (!empty($category_filter)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'movies_taxonomy',
            'field' => 'id',
            'terms' => $category_filter,
        );
    }

    if (!empty($taxonomy_filter)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'location_taxonomy',
            'field' => 'id',
            'terms' => $taxonomy_filter,
            'include_children' => true,
        );
    }

    if (!empty($director_filter)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'director_taxonomy',
            'field' => 'id',
            'terms' => $director_filter,
        );
    }    
    if (isset($_POST['keyword']) && !empty($_POST['keyword'])) {
        $args['s'] = sanitize_text_field($_POST['keyword']);
    }
    $the_query = new WP_Query($args);
    if ($the_query->have_posts()) :
        while ($the_query->have_posts()) : $the_query->the_post(); ?>
           <h2><a href="<?php echo get_permalink(); ?>" target="_blank"><?php the_title(); ?></a></h2>
        
           <?php echo the_post_thumbnail('custom_size_thumbnail');
            $terms = get_the_terms(get_the_ID(), 'location_taxonomy');
	             if ($terms && !is_wp_error($terms)) {
	                 foreach ($terms as $term) {
	                     echo ' '.$term->name.'';
	                 }
	             }
	        $terms = get_the_terms(get_the_ID(), 'movies_taxonomy');
                 if ($terms && !is_wp_error($terms)) {
                     foreach ($terms as $term) {
                         echo ' '.$term->name.' <br>';
                     }
                 }

        endwhile;

        echo paginate_links(array(
            'total' => $the_query->max_num_pages      
     	));
        wp_reset_postdata();
    else :
        echo '<p>No posts found</p>';
    endif;

    die();
}
add_action('wp_ajax_search_action', 'custom_search_action');
add_action('wp_ajax_nopriv_search_action', 'custom_search_action');



function your_themes_pagination() {
	global $wp_query;
	echo paginate_links();
}
