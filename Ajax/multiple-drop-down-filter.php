====================================================================================================
									functions.php
==================================================================================================

function myfilter_2() {
    $category_filter = $_POST['categoryfilter'];
    $taxonomy_filter = $_POST['taxonomyfilter'];
    $director_filter = $_POST['directorfilter'];

    $args = array(
        'post_type' => 'movies',
        'posts_per_page' => -1,
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
        );
    }

    if (!empty($director_filter)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'director_taxonomy',
            'field' => 'id',
            'terms' => $director_filter,
        );
    }

    $the_query = new WP_Query($args);

    if ($the_query->have_posts()) :
        while ($the_query->have_posts()) : $the_query->the_post();
            echo '<h2>' . get_the_title() . '</h2>';
        endwhile;
        wp_reset_postdata();
    else :
        echo '<p>No posts found</p>';
    endif;

    die();
}

add_action('wp_ajax_myfilter_2', 'myfilter_2');
add_action('wp_ajax_nopriv_myfilter_2', 'myfilter_2');


====================================================================================================
									template-page.php
==================================================================================================

<form action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST" id="filter-3">
	<?php
		if( $terms = get_terms( array( 'taxonomy' => 'movies_taxonomy', 'orderby' => 'name' ) ) ) : 
 			echo '<select name="categoryfilter"><option value="">Select Category...</option>';
			foreach ( $terms as $term ) :
				echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';
			endforeach;
			echo '</select>';
		endif;
		if( $terms_2 = get_terms( array( 'taxonomy' => 'location_taxonomy', 'orderby' => 'name' ) ) ) :   
			echo '<select name="taxonomyfilter"><option value="">Select Taxonomy...</option>';
			foreach ( $terms_2 as $term ) :
				echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';
			endforeach;
			echo '</select>';
		endif;
		if( $terms_3 = get_terms( array( 'taxonomy' => 'director_taxonomy', 'orderby' => 'name' ) ) ) :   
			echo '<select name="directorfilter"><option value="">Select Director ...</option>';
			foreach ( $terms_3 as $term ) :
				echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';
			endforeach;
			echo '</select>';
		endif;
	?>
	<button>Apply filter</button>
	<input type="hidden" name="action" value="myfilter_2">
</form>
<div id="response">    
	<?php   
	$args = array(
	    'post_type' => 'movies',
	    'posts_per_page' => -1
	);   
	$the_query = new WP_Query( $args ); ?>   
	    <?php if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); ?>   
	          <h2><?php the_title(); ?></h2>  
	    <?php endwhile; endif; ?>   
	<?php wp_reset_postdata(); ?>
</div>

====================================================================================================
									js file 
==================================================================================================

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function($) {
    $('#filter-3').submit(function(){
        var filter = $('#filter-3');
        $.ajax({
            url: filter.attr('action'),
            data: filter.serialize(),
            type: filter.attr('method'),
            success:function(data){
                $('#response').html(data);
            }
        });
        return false;
    });
});

</script>
