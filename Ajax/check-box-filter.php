<?php /* Template Name: Check-Box Filter 
*/
?>
<div class="container">
  <header class="header">
    <h1>Jobs Opening</h1> 
   <form action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST" id="search-form">
     <input type="text" name="keyword" placeholder="Search all jobs">
       <button type="submit">Search</button>
     <input type="hidden" name="action" value="search_action">
   </form>

<div id="search-results"></div>

  </header>
  <div class="content">
    <main class="main">
      <h2></h2>
       <div id="response">    
              <?php

                 $paged = get_query_var('paged');

                     $args = array(
                         'post_type' => 'movies',
                         'posts_per_page' => 2,
                         'paged' => $paged,
                     );
                     $the_query = new WP_Query($args);
                     ?>
                     <?php if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post(); ?>
                        <h2><a href="<?php echo get_permalink(); ?>" target="_blank"><?php the_title(); ?></a></h2>
                         <?php
                         $terms = get_the_terms(get_the_ID(), 'location_taxonomy');
                         if ($terms && !is_wp_error($terms)) {
                             foreach ($terms as $term) {
                                 echo ' '.$term->name.'';
                             }
                         }
                         ?>
                         <?php
                         $terms = get_the_terms(get_the_ID(), 'movies_taxonomy');
                         if ($terms && !is_wp_error($terms)) {
                             foreach ($terms as $term) {
                                 echo ' '.$term->name.' <br>';
                             }
                         }
                         ?>
                         <?= the_post_thumbnail('custom_size_thumbnail'); ?>
                     <?php endwhile; 

                     
                     echo paginate_links(array(
                            'total' => $the_query->max_num_pages      
                     ));

                     endif; ?>
                     <?php wp_reset_postdata(); 

                    ?>
       </div>
    </main>
    <aside class="aside">
      <h3>Sidebar</h3>
     <form action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST" id="filter-3">
       <?php
              if( $terms = get_terms( array( 'taxonomy' => 'movies_taxonomy', 'orderby' => 'name' ) ) ) : 
                     echo '<label><b>Department Taxonomy:</b></label><br>';
                     echo '<select name="categoryfilter"><option value="">Select Category...</option>';
                     foreach ( $terms as $term ) :
                            echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';
                     endforeach;
                     echo '</select><br>';
              endif;
              if( $terms_2 = get_terms( array( 'taxonomy' => 'location_taxonomy', 'orderby' => 'name' ) ) ) :   
                     echo '<label><b>Locations Taxonomy:</b></label><br>';
                     echo '<select name="taxonomyfilter"><option value="">Select Taxonomy...</option>';
                     foreach ( $terms_2 as $term ) :
                            echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';
                     endforeach;
                     echo '</select><br>';
              endif;
              if( $terms_3 = get_terms( array( 'taxonomy' => 'director_taxonomy', 'orderby' => 'name' ) ) ) :   
                     echo '<select name="directorfilter"><option value="">Select Director ...</option>';
                     foreach ( $terms_3 as $term ) :
                            echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';
                     endforeach;
                     echo '</select><br>';
              endif;
             
       ?>
       <button>Apply filter</button>
       <input type="hidden" name="action" value="myfilter_2">
</form>
   

    </aside>
  </div>
  <footer class="footer">
    &copy; Kudosintech
  </footer>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#filter-3').submit(function() {
        var filter = $('#filter-3');
        $.ajax({
            url: filter.attr('action'),
            data: filter.serialize(), 
            type: filter.attr('method'),
            success: function(data) {
                $('#response').html(data);
            }
        });
        return false;
    });

    $('#search-form').submit(function(e) {
        e.preventDefault(); // Prevent the form from actually submitting

        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: formData,
            success: function(response) {
                $('#response').html(response); // Display search results in the #search-results container
            }
        });
    });

});

</script>


<style type="text/css">
       body {
  margin: 0;
  padding: 0;
  font: 100%/1.5 "Noto Sans JP", sans-serif;
  background: lavender;
}
/* Layout Code */
.container {
  margin: 0 auto;
  min-height: 100vh;
  max-width: 1000px;
  display: flex;
  flex-direction: column;
  box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2);
}
.header {
  display: flex;
  height: 100px;
  padding: 0 20px;
  align-items: center;
  background: lightpink;
}
.content {
  flex: 1;
  display: flex;
}
.main {
  flex: 1;
  padding: 0 20px;
  background: snow;
}
.aside {
  width: 260px;
  padding: 0 20px;
  background: papayawhip;
}
.footer {
  display: flex;
  height: 50px;
  align-items: center;
  justify-content: center;
  background: lightsteelblue;
}

/* Content Code */
.header h1 {
  margin: 0;
  font-size: 1.75rem;
  text-transform: uppercase;
}
.main h2,
.aside h3 {
  font-size: 1.5rem;
  margin: 1.5rem 0;
}
.main p,
.aside p {
  margin: 0.75rem 0;
}
.aside,
.footer {
  font-size: 0.875rem;
}

</style>