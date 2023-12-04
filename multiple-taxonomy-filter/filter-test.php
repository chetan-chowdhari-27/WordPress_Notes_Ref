<?php /* Template Name: Filter Testing */ ?>

<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="GET" id="myfilter">
                <?php
                    $taxonomies = array(
                        'cities',
                        'districts',
                        'county_service_area',
                        'cycle',
                    );
                    foreach ($taxonomies as $taxonomy) :
                            if ($terms = get_terms(array('taxonomy' => $taxonomy, 'orderby' => 'name'))) :
                                echo '<select name="' . $taxonomy . '"><option value="">' . ucfirst(str_replace('_', ' ', $taxonomy)) . '</option>';
                                    foreach ($terms as $term) :
                                        $selected = (isset($_GET[$taxonomy]) && $_GET[$taxonomy] == $term->term_id) ? 'selected' : '';
                                        echo '<option value="' . $term->term_id . '" ' . $selected . '>' . $term->name . '</option>';
                                    endforeach;
                                echo '</select>';
                            endif;
                    endforeach;
                ?>
        <button type="submit">Apply filter</button>
            <a href="?cities=">Reset </a>
        <input type="hidden" name="action" value="myfilter">
</form>


<div id="myfilter-results">
    <?php
        $args = array(
            'post_type'      => 'msf_ocl',
            'posts_per_page' => -1,
            'orderby' => 'ASC',
        );

        foreach ($taxonomies as $taxonomy) {
            if (isset($_GET[$taxonomy]) && $_GET[$taxonomy] != '') {
                $args['tax_query'][] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $_GET[$taxonomy],
                );
            }
        }

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                ?>
                

               <?php
                    $file = get_field('file_upload');

                    if ($file) {
                        ?>
                        <a href="<?php echo $file['url']; ?>">
                            <h2><?php the_title(); ?></h2>
                        </a>
                    <?php } else { ?>
                        <h2><?php the_title(); ?></h2>
                    <?php } ?>


                <?php
            }
        }

        wp_reset_postdata();
    ?>
</div>



