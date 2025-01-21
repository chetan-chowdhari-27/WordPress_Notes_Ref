<?php
/**
 * The template for displaying all single jobs
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 */
//get_header();
?>
<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying all single posts
 *
 * Do not overload this file directly. Instead have a look at templates/single.php file in us-core plugin folder:
 * you should find all the needed hooks there.
 */

get_header(); ?>
	
<main id="page-content" class="l-main">
	<?php while ( have_posts() ) { the_post();
		
		$title = get_the_title();
		$term_list_states = wp_get_post_terms($post->ID, 'job_states', array("fields" => "all")); 
		$term_list_cities = wp_get_post_terms($post->ID, 'job_cities', array("fields" => "all"));
		
		$job_location = $term_list_cities[0]-> name .','.  $term_list_states[0]-> name .', USA';
		
		// acf fields
		//$job_uuid = get_field('smr_job_uuid');
		$job_uuid = get_field('smr_job_id');
		$job_type = get_field('job_type');
		$job_remote_work = get_field('job_remote_work');
		$company_description_title = get_field('company_description_title');
		$brand = get_field('brand');
		$brand_logo="";
		if($brand == 'MGA Homecare'){
			$brand_logo= '<img class="brand-logo" src="'.get_field('mga_homecare','option').'">';
		}elseif($brand == 'MGA Behavior Therapy'){
			$brand_logo= '<img class="brand-logo" src="'.get_field('mga_behavior_therapy','option').'">';
		}elseif($brand == 'Circle of Care Colorado'){
			$brand_logo= '<img class="brand-logo" src="'.get_field('circle_of_care_colorado','option').'">';
		}
		$company_description_mga = get_field('company_description_mga');
		$why_choose_mga_title = get_field('why_choose_mga_title');
		$why_choose_mga_homecare_description = get_field('why_choose_mga_homecare_description');
		$job_description_title = get_field('job_description_title');
		$job_description_mga = get_field('job_description_mga');
		$qualifications_title = get_field('qualifications_title');
		$qualifications_description = get_field('qualifications_description');
		$additional_information_title = get_field('additional_information_title');
		$additional_description = get_field('additional_description');
		$experience_level = get_field('experience_level');
		$language = get_field('language');

		?>

		<div class="detail-content l-section">
			<div class="l-section-h">
				<div class="job-details">
					<div class="job-content">
						<div class="cl-left">
							
							<?php if(!empty($title)){ 
								echo '<h1 class="job-title">'.$title.'</h1>'; 
							} ?>
							
							<?php echo '<div class="job-location-city-state"><span>'. $term_list_cities[0]-> name .','.  $term_list_states[0]-> name .', USA</span></div>'; 
							?>
							
							<?php if(!empty($job_remote_work)){ ?>
								<div class="job-work">
									<span>Employees can work remotely <img src="/wp-content/uploads/2023/03/mga-homecare-careers-world-01a.svg"></span>
								</div> 
							<?php } ?>
							
							<?php if(!empty($job_type) ){ ?>
								<div class="job-type">
									<span><?php echo $job_type; ?></span>
								</div>
							<?php } ?>

							<div class="job-sections">
								<div class="company-description">
									<?php if(!empty($company_description_title)) { 
										echo '<h4 class="company-description-title">'.$company_description_title.'</h4>'; 
									} ?>	
									
									<?php if(!empty($brand_logo)){
										echo $brand_logo;
									}
									?>

									<?php if(!empty($company_description_mga)) { 
										echo '<div class="company-description">'.$company_description_mga.'</div>';   
									} ?>
								</div>

								<div class="why-choose-mga">
									<?php if(!empty($why_choose_mga_title)) { 
										echo '<h4 class="why-choose-mga-title">'.$why_choose_mga_title.'</h4>'; 
									} ?>	
									
									<?php if(!empty($why_choose_mga_homecare_description)) { 
										echo '<div class="why-choose-mga-description">'.$why_choose_mga_homecare_description.'</div>';  
									} ?>
								</div>

								<div class="job-description">
									<?php if(!empty($job_description_title)) { 
										echo '<h4 class="job-description-title">'.$job_description_title.'</h4>'; 
									} ?>	
									
									<?php if(!empty($job_description_mga)) { 
										echo '<div class="job-description">'.$job_description_mga.'</div>';   
									} ?>
								</div>

								<div class="job-qualification">
									<?php if(!empty($qualifications_title)) { 
										echo '<h4 class="job-qualification-title">'.$qualifications_title.'</h4>'; 
									} ?>	
									
									<?php if(!empty($qualifications_description)) { 
										echo '<div class="job-qualification-description">'.$qualifications_description.'</div>';  
									} ?>
								</div>

								<div class="additional-info">
									<?php if(!empty($additional_information_title)) { 
										echo '<h4 class="job-additional-info-title">'.$additional_information_title.'</h4>'; 
									} ?>	
									
									<?php if(!empty($additional_description)) { 
										echo '<div class="job-additional-info-description">'.$additional_description.'</div>';   
									} ?>
								</div>
								<div class="w-btn-wrapper">
									<a class="w-btn us-btn-style_30 us_custom_879446cc" href="/im-interested-careers?jid=<?php echo $job_uuid;?>"><span class="w-btn-label">Apply Now</span></a>
								</div>
							</div>
							<?php } ?>
						</div>
						<div class="cl-right">
							<div class="jobad-buttons">
								<div class="btn-info">
									<a class="w-btn us-btn-style_30" title="" href="/im-interested-careers?jid=<?php echo $job_uuid;?>&jtitle=<?php echo $title;?>&jlocation=<?php echo $job_location;?>"><span class="w-btn-label">Apply Now</span></a>
								</div>
								<div class="btn-info">
									<a class="w-btn us-btn-style_30" title="" href="/refer-a-friend-form-careers/"><span class="w-btn-label">Refer a friend</span></a>
								</div>
							</div>
							<div class="widget socials print-hidden">
							    <h2 class="widget-title">share this job</h2>
							    <ul class="social-list js-socials">
							        <li class="social-item"><a class="social-link js-social" target="_blank" href=""><i class="fab fa-linkedin-in"></i></a></li>
							        <li class="social-item"><a class="social-link js-social" href=""><i class="fab fa-instagram"></i></a></li>
							        <li class="social-item"><a class="social-link js-social" href=""><i class="fab fa-facebook-f"></i></a></li>
							        <li class="social-item"><a class="social-link js-social" href=""><i class="fab fa-twitter"></i></a></li>
							        <li class="social-item"><a class="social-link" href=""><i class="fas fa-envelope"></i></a></li>
							        <li class="social-item"><a class="social-link js-social" href=""><i class="fab fa-xing"></i></a></li>
							        <li class="social-item"><a class="social-link" href="" target="_blank"><i class="fab fa-weixin"></i></a></li>
							    </ul>
							</div>

							<?php //get the taxonomy terms of custom post type
							
							$job_department = wp_get_object_terms( $post->ID, 'job_department', array('fields' => 'ids') );

							//query arguments
							$args = array(
							    'post_type' => 'jobs',
							    'post_status' => 'publish',
							    'posts_per_page' => 3,
							    'tax_query' => array(
							        array(
							            'taxonomy' => 'job_department',
							            'field' => 'id',
							            'terms' => $job_department
							        )
							    ),
							    'post__not_in' => array ($post->ID),
							);

							//the query
							$relatedPosts = new WP_Query( $args );

							//loop through query
							if($relatedPosts->have_posts()){
							    echo '<div class="widget print-hidden js-others jobs-info">
								    	<h2 class="widget-title">Other jobs at MGA Homecare</h2>
									    <ul class="widget-list">';
							    while($relatedPosts->have_posts()){ 
							        $relatedPosts->the_post();
							        $job_cities = wp_get_post_terms($post->ID, 'job_cities', array("fields" => "all"));
							        $job_states = wp_get_post_terms($post->ID, 'job_states', array("fields" => "all"));
							        $job_city_name = $job_cities[0]->name;
							        $job_state_name = $job_states[0]->name;

									?>
							        <li>
							        	<a class="details link--block"  href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><p class="details-title font--medium link--block-target truncate"><?php the_title(); ?></p></a>
										<p class="details-desc"><?php echo $job_city_name; ?>, <?php echo $job_state_name; ?></p>
									</li>
								<?php }

								echo '<li><a data-sr-track="other-all" href="'.get_site_url().'/careers/">Show all jobs</a></li></ul></div>';
							}
							wp_reset_postdata(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
</main>
<?php get_footer(); 
