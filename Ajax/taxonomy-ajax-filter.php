==============================================================================================================================
											Template filter-ajax.php
==============================================================================================================================

<?php $categories = get_terms('movies_taxonomy'); ?>
<ul class="cat-list">
  <li><a class="cat-list_item active" href="#!" data-slug="">All projects</a></li>

  <?php foreach ($categories as $category) : ?>
    <li>
      <a class="cat-list_item" href="#!" data-slug="<?= $category->slug; ?>">
        <?= $category->name; ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<?php
  $projects = new WP_Query([
    'post_type' => 'movies',
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'desc',
  ]);
?>

<?php if ($projects->have_posts()) : ?>
  <ul class="project-tiles">
    <?php while ($projects->have_posts()) : $projects->the_post(); ?>
      <li>
        <h4><?php the_title(); ?></h4>
      </li>
    <?php endwhile; ?>
  </ul>
  <?php wp_reset_postdata(); ?>
<?php endif; ?>

==============================================================================================================================
											Functions.php
==============================================================================================================================

function load_js_for_ajax() {
    wp_register_script('ajax-user', get_template_directory_uri() . '/assets/js/posts/loadmore.js', array('jquery'), null, true);
    wp_enqueue_script('ajax-user');
    wp_localize_script('ajax-user', 'wp_Ajax', array('ajax_Url' => admin_url('admin-ajax.php')));
}

add_action('wp_enqueue_scripts','load_js_for_ajax');


function enqueue_jquery() {
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'enqueue_jquery');



function filter_ajax() {
  $taxonomy = $_POST['taxonomy'];
  $term = $_POST['term'];

  $args = array(
    'post_type' => 'movies', 
    'posts_per_page' => -1,
    'tax_query' => array(
      array(
        'taxonomy' => $taxonomy,
        'field' => 'slug',
        'terms' => $term,
      ),
    ),
  );

  $query = new WP_Query($args);

  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
      // Display your custom post type posts here as desired
      the_title('<h4>', '</h4>');
    }
    wp_reset_postdata();
  }

  die();
}

add_action('wp_ajax_nopriv_filter_projects', 'filter_ajax');
add_action('wp_ajax_filter_projects', 'filter_ajax');



==============================================================================================================================
											Js for Ajax 
==============================================================================================================================
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($) {
  $('.cat-list_item').on('click', function(e) {
    e.preventDefault();
    $('.cat-list_item').removeClass('active');
    $(this).addClass('active');

    $.ajax({
      type: 'POST',
      url: '<?php echo admin_url('admin-ajax.php'); ?>',
      dataType: 'html',
      data: {
        action: 'filter_projects',
        taxonomy: 'movies_taxonomy', 
        term: $(this).data('slug'),
      },
      success: function(res) {
        $('.project-tiles').html(res);
      }
    });
  });
});
</script>
