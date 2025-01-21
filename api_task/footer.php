<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying pages footers
 *
 * Do not overload this file directly. Instead have a look at templates/footer.php file in us-core plugin folder:
 * you should find all the needed hooks there.
 */

if ( function_exists( 'us_load_template' ) ) {

	us_load_template( 'templates/footer' );

} else {
	?>
		</div>
		<footer	class="l-footer">
			<section class="l-section color_footer-top">
				<div class="l-section-h i-cf align_center">
					<span><?php bloginfo( 'name' ); ?></span>
				</div>
			</section>
		</footer>
		<?php wp_footer(); ?>
	</body>
	</html>
	<?php
}

if(is_page(14713)){ ?>
	<script>
		jQuery(document).on('click',
			'.wpforms-field-repeater .wpforms-repeater-button.wpforms-repeater-add',
			function () { 
				console.log('click is triggered');
				WPFormsGeolocationInitGooglePlacesAPI()
			}
		);
	</script>
	<?php
} ?>

<?php if (!is_page(24364)) : ?>
    <script type='text/javascript'> 
    (function(o,l) {
        window.oliviaChatData = window.oliviaChatData || [];
        window.oliviaChatBaseUrl = o;
        window.oliviaChatData.push(['setKey', l]);
        window.oliviaChatData.push(['start']);
        var apply = document.createElement('script');
        apply.type = 'text/javascript';
        apply.async = true;
        apply.src = 'https://dokumfe7mps0i.cloudfront.net/static/site/js/widget-client.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(apply, s);
    })('https://olivia.paradox.ai', 'zlibtzmnhvnmwpavinli');
    </script>
<?php endif; ?>
