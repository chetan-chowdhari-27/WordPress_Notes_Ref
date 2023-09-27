<?php
$taxonomies = get_terms('movies_taxonomy'); // CPT taxonomy name 

if (!empty($taxonomies)) : ?>
    <ul>
        <?php
        foreach ($taxonomies as $taxonomy) {
            echo '<li>' . $taxonomy->name . '</li>';
        }
        ?>
    </ul>
<?php else : ?>
    <p>No terms found for this post.</p>
<?php endif; ?>
