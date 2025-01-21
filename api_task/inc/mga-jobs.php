<?php 

/* shortcode for job listing on carrer page
** Date: 24/02/2023
*/
function handle_response_log($error) {	
	$message = "Results: " . print_r( $error, true );	
	if(isset($error) && !empty($error)){	
		$file = fopen(get_template_directory()."/brand.log","a"); 	
		fwrite($file, "\n" . date('Y-m-d h:i:s') . " :: Message :: " . $message); 	
		fclose($file);	
	}	
}

function mga_job_listing_fun($atts) { 
	ob_start();
	global $post;
	$default = array(
		'state' => '',
		'city' => '',
		'department' => '',
		'type' => '',
		'remote' => '',
	);

	$states = shortcode_atts($default, $atts);
	if($states['state']){
		$tax_query[] = array(
			'taxonomy' => 'job_states',
			'field' => 'name',
			'terms' => $states['state'],
		);
		
		$args2 = array(
			'post_type'      => 'jobs',
			'posts_per_page' => -1,
			'orderby' 		 => 'date',
			'order'  		 => 'DESC',
			'tax_query'      => array(
				array(
					'taxonomy' => 'job_states',
					'field' => 'name',
					'terms' => $states['state'],
				),
			)
		);

		$query = new WP_Query($args2);
		$my_posts    = $query->posts;
		$my_post_ids = wp_list_pluck ($my_posts, 'ID');
		$city_terms    = wp_get_object_terms ($my_post_ids, 'job_cities');
	}
	if($states['city']){
		$tax_query[] = array(
			'taxonomy' => 'job_cities',
			'field' => 'name',
			'terms' => $states['city'],
		);
	}
	if($states['department']){
		$tax_query[] = array(
			'taxonomy' => 'job_department',
			'field' => 'name',
			'terms' => $states['department'],
		);
	}
	if ( $states['type']) {
		$meta_query[] = array(
			'key'     => 'job_type',
			'value'   => $states['type'],
		);
	}

	if ( $states['remote']) {
		if ( $states['remote'] == 'yes') {
			$meta_query[] = array(
				'key'     => 'job_remote_work',
				'value'   => true,
			);
		}
	}
	$args = array(
		'post_type'      => 'jobs',
		'posts_per_page' => -1,
		'orderby' 		 => 'date',
		'order'  		 => 'DESC',
		'meta_query'      => array( 'relation' => 'AND', $meta_query ),
		'tax_query'      => array( 'relation' => 'AND', $tax_query ),
	);
	
	$loop = new WP_Query($args); 
	$temAarry = []; 
	$ob_clean = ob_get_clean();
	$remote_work = $_POST['worklocation'];
	$job_states = get_terms([ 'taxonomy' => 'job_states', 'hide_empty' => true ]);
	$job_cities = get_terms([ 'taxonomy' => 'job_cities', 'hide_empty' => true, 'orderby' => 'name', 'order' => 'ASC', ]);
	$job_departments = get_terms([ 'taxonomy' => 'job_department', 'hide_empty' => true ]);
	?>

	<div class="search-filter-section">
		<div class="input-search">
			<input type="text" name="autocomplete" id="autocomplete" value="" placeholder="Filter by title, expertise" />
			<div id="et_top_search" class="careers-search"> <a class="search-button" href="javascript:void(0);">Search</a></div>
		</div>
		<div class="sortby-states">
			<select name="states-sort" class="state-sort" id="statessort" <?php echo ($states['state']) ? 'disabled="disabled"' : null;?>>
				<option selected data-sortbystates="" value="">States</option>
				<?php foreach ($job_states as $termstates){
					$select = ($states['state'] == $termstates->name) ? 'selected' : null;
				// {
				// 	$select = 'Selected';
				// }
					if($termstates->name == 'AZ'){
						echo '<option '.$select.' data-sortbystates="'. $termstates->name .'" >Arizona</option>';
					} elseif ($termstates->name == 'CO'){
						echo '<option '.$select.' data-sortbystates="'. $termstates->name .'">Colorado</option>';
					} elseif ($termstates->name == 'NC'){
						echo '<option '.$select.' data-sortbystates="'. $termstates->name .'">North Carolina</option>';
					} elseif ($termstates->name == 'SC'){
						echo '<option '.$select.' data-sortbystates="'. $termstates->name .'">South Carolina</option>';
					} elseif ($termstates->name == 'TN'){
						echo '<option '.$select.' data-sortbystates="'. $termstates->name .'">Tennessee</option>';
					} elseif ($termstates->name == 'TX'){
						echo '<option '.$select.' data-sortbystates="'. $termstates->name .'">Texas</option>';
					} elseif ($termstates->name == 'WA'){
						echo '<option '.$select.' data-sortbystates="'. $termstates->name .'">Washington</option>';
					} else {
						echo '<option '.$select.' data-sortbystates="'. $termstates->name .'">' . $termstates->name . '</option>';
					}
				} ?>
			</select>
		</div>
		<div class="sortby-location">
			<select name="cities-sort" class="city-sort" id="citysort">
				<option selected data-sortbycity="" value="">Cities</option>
				<?php if($states['state']){
					foreach($city_terms as $c_name){
						$select_city = ($states['city'] == $c_name->name) ? 'selected' : null;?>
						<option data-sortbycity='<?php echo $c_name->name; ?>' <?php echo $select_city;?>><?php echo $c_name->name; ?></option>
					<?php }
					
				} else{
					foreach ($job_cities as $termcities){
						$select_city = ($states['city'] == $termcities->name) ? 'selected' : null;
						echo '<option data-sortbycity="'. $termcities->name .'" '.$select_city.'>' . $termcities->name . '</option>';
					} 
				}?>
			</select>
		</div>
		<div class="sortby-department">
			<select name="department-sort" class="department-sort" id="departmentsort">
				<option selected data-sortbydepartment="" value="">Department</option>
				<?php foreach($job_departments as $termdept) {
					$select_dep = ($states['department'] == $termdept->name) ? 'selected' : null;
					if($termdept->name == 'Internal'){
						echo '<option data-sortbydepartment="'. $termdept->name .'" '.$select_dep.'>Office Staff</option>';
					} else {
						echo '<option data-sortbydepartment="'. $termdept->name .'" '.$select_dep.'>' . $termdept->name . '</option>';
					}
				} ?>
			</select>
		</div>
		<div class="sortby-jobtype"> 
			<select name="jobtype-sort" class="jobtype-sort" id="jobtypesort">
				<option selected data-sortbyjobtype="" value="">Job Type</option>
				<?php while ( $loop->have_posts() ) : $loop->the_post();
					$get_job_type = get_field('job_type', $post->ID);
					if($get_job_type){
						if(!in_array($get_job_type, $temAarry)){
							array_push($temAarry, $get_job_type); 
							$select_type = ($states['type'] == $get_job_type) ? 'selected' : null; ?>
							<option data-sortbyjobtype="<?php echo $get_job_type; ?>" <?php echo $select_type;?>><?php echo $get_job_type; ?></option><?php 
						}
					}  
				endwhile; ?> 
			</select>
		</div>
		<div class="employees-work-location">
			<?php $check_remote = ($states['remote']) ? 'checked' : null;?>
			<input type="checkbox" id="work-location" class="worklocation" name="worklocation" value="checked" <?php echo $check_remote;?>>
			<label for="work-location">Employees can work remotely <img src="/wp-content/uploads/2023/03/mga-homecare-careers-world-01a.svg"></label>
		</div>
	</div>
	<div class="jobs-section">
		<div class="job-listing">
			<table border="1" class="srJobList" style="">
				<tbody>
					<tr class="srJobListTitles">
						<th class="srJobListJobTitle">
							<nobr>Job Title</nobr>
						</th>
						<th class="srJobListLocation">
							<nobr>Location</nobr>
						</th>
					</tr>
					<?php 
					while ( $loop->have_posts() ) : $loop->the_post(); 

						$job_remote_work = get_field('job_remote_work');

						$term_list_states = wp_get_post_terms($post->ID, 'job_states', array("fields" => "all")); 
						$term_list_cities = wp_get_post_terms($post->ID, 'job_cities', array("fields" => "all")); 
						$term_list_department = wp_get_post_terms($post->ID, 'job_department', array("fields" => "all")); 

						?>
						<tr class="joblist">
							<td class="job-title">
								<a href='<?php the_permalink() ?>'><?php the_title(); ?></a>
							</td>
							<td class="job-location">
								<?php if( $job_remote_work == true) {
									echo $term_list_cities[0]-> name .','. $term_list_states[0]-> name . ' <img src="/wp-content/uploads/2023/03/mga-homecare-careers-world-01a.svg">' ; 
								} else {
									echo $term_list_cities[0]-> name .','. $term_list_states[0]-> name ; 
								} ?>
							</td>
						</tr>
					<?php endwhile;
					wp_reset_postdata(); ?>
				</tbody>
			</table>
		</div>
		</div><?php 
		return $ob_clean;
	}
	add_shortcode('Job_Listing', 'mga_job_listing_fun');

/* Ajax callback function > Careers page ( JOB LIST )
** Date: 03-03-2023
*/
add_action( 'wp_ajax_nopriv_orders_filter_sort', 'orders_filter_sort' );
add_action( 'wp_ajax_orders_filter_sort', 'orders_filter_sort' );

function orders_filter_sort(){ 

	global $post; 

	$sort_by_states = $_POST['sortbystates'];
	$sort_by_cities = $_POST['sortbycity'];
	$sort_by_departments = $_POST['sortbydepartment'];
	$sort_by_jobtype = $_POST['sortbyJobtype'];
	$search_keyword = $_POST['search_keyword'];
	$remote_work = $_POST['worklocation'];
	$meta_query = array('relation' => 'AND');

	if ( $remote_work != '') {
		if ( $remote_work == 'yes') {
			$meta_query[] = array(
				'key'     => 'job_remote_work',
				'value'   => true,
			);
		}
	}

	if ( $sort_by_jobtype != '') {
		$meta_query[] = array(
			'key'     => 'job_type',
			'value'   => $sort_by_jobtype,
		);
	}

	if ( $sort_by_states != '') {
		$tax_query[] = array(
			'taxonomy' => 'job_states',
			'field' => 'slug',
			'terms' => $sort_by_states,
		);
	}

	if ($sort_by_cities != '') {
		$tax_query[] = array(
			'taxonomy' => 'job_cities',
			'field' => 'slug',
			'terms' => $sort_by_cities,
		);
	}

	if ($sort_by_departments != '') {
		$tax_query[] = array (
			'taxonomy' => 'job_department',
			'field' => 'slug',
			'terms' => $sort_by_departments,
		);
	}

	$args = array(
		'post_type'      => 'jobs',
		'posts_per_page' => -1,
		'orderby'		 => 'date',
		'order' 		 => 'DESC',
		's' 			 => $search_keyword,
		'meta_query'	 => array( 'relation' => 'AND', $meta_query ),
		'tax_query'      => array( 'relation' => 'AND', $tax_query )
	);

	$loop = new WP_Query($args); 

	?>

	<div class="job-listing">
		<table border="1" class="srJobList" style="">
			<tbody>
				<tr class="srJobListTitles">
					<th class="srJobListJobTitle">
						<nobr>Job Title</nobr>
					</th>
					<th class="srJobListLocation">
						<nobr>Location</nobr>
					</th>
				</tr>
				<?php while ( $loop->have_posts() ) : $loop->the_post(); 

					$job_remote_work = get_field('job_remote_work');
					$term_list_states = wp_get_post_terms($post->ID, 'job_states', array("fields" => "all")); 
					$term_list_cities = wp_get_post_terms($post->ID, 'job_cities', array("fields" => "all")); 
					$term_list_department = wp_get_post_terms($post->ID, '	 ', array("fields" => "all"));
					?>
					<tr class="joblist">
						<td class="job-title">
							<a href='<?php the_permalink() ?>'><?php the_title(); ?></a>
						</td>
						<td class="job-location">
							<?php if( $job_remote_work == true) {
								echo $term_list_cities[0]-> name .','. $term_list_states[0]-> name . ' <img src="/wp-content/uploads/2023/03/mga-homecare-careers-world-01a.svg">' ; 
							} else {
								echo $term_list_cities[0]-> name .','. $term_list_states[0]-> name ; 
							} ?>
						</td>
					</tr>
				<?php endwhile;
				wp_reset_postdata(); ?>
			</tbody>
		</table>
	</div>

	<?php 
	die();
}

/* Ajax callback function > Careers page ( FILTER CITY )
** Date: 22-03-2023
*/
add_action( 'wp_ajax_nopriv_orders_filter_city_sort', 'orders_filter_city_sort' );
add_action( 'wp_ajax_orders_filter_city_sort', 'orders_filter_city_sort' );
function orders_filter_city_sort(){ 

	global $post; 

	$sort_by_states = $_POST['sortbystates'];
	$sort_by_cities = $_POST['sortbycity'];
	$sort_by_departments = $_POST['sortbydepartment'];
	$search_keyword = $_POST['search_keyword'];

	if ( $sort_by_states != '') {
		$tax_query[] = array(
			'taxonomy' => 'job_states',
			'field' => 'slug',
			'terms' => $sort_by_states,
		);
	}

	$args = array(
		'post_type'      => 'jobs',
		'posts_per_page' => -1,
		'orderby'		 => 'date',
		'order'  		 => 'ASC',
		's' 			 => $search_keyword,
		'meta_query'	 => array( $meta_query ),
		'tax_query'      => array( 'relationship' => 'AND', $tax_query )
	);

	$loop = new WP_Query($args);  
	$my_posts    = $loop->posts;
	$my_post_ids = wp_list_pluck ($my_posts, 'ID');
	$city_terms    = wp_get_object_terms ($my_post_ids, 'job_cities');
	$temAarry = [];  ?>

	<option data-sortbycity=''>Cities</option>
	<?php foreach($city_terms as $c_name){?>
		<option data-sortbycity='<?php echo $c_name->name; ?>'><?php echo $c_name->name; ?></option>
	<?php }
	die();
}

/* Ajax callback function > Careers page ( FILTER DEPT. )
** Date: 22-03-2023
*/
add_action( 'wp_ajax_nopriv_orders_filter_department_sort', 'orders_filter_department_sort' );
add_action( 'wp_ajax_orders_filter_department_sort', 'orders_filter_department_sort' );
function orders_filter_department_sort(){ 

	global $post; 

	$sort_by_states = $_POST['sortbystates'];
	$sort_by_cities = $_POST['sortbycity'];
	$sort_by_departments = $_POST['sortbydepartment'];

	if ( $sort_by_states != '') {
		$tax_query[] = array(
			'taxonomy' => 'job_states',
			'field' => 'slug',
			'terms' => $sort_by_states,
		);
	}

	if ($sort_by_cities != '') {
		$tax_query[] = array(
			'taxonomy' => 'job_cities',
			'field' => 'slug',
			'terms' => $sort_by_cities,
		);
	}

	if ($sort_by_departments != '') {
		$tax_query[] = array (
			'taxonomy' => 'job_department',
			'field' => 'slug',
			'terms' => $sort_by_departments,
		);
	}

	$args = array(
		'post_type'      => 'jobs',
		'posts_per_page' => -1,
		'orderby' 		 => 'date',
		'order'   		 => 'ASC',
		's' 			 => $search_keyword,
		'meta_query'	 => array( $meta_query ),
		'tax_query'      => array( 'relationship' => 'AND', $tax_query )
	);

	$loop = new WP_Query($args); 
	$temAarry = []; ?>

	<option data-sortbydepartment='null'>Department</option>

	<?php while ( $loop->have_posts() ) : $loop->the_post(); 

		$term_list_department = wp_get_post_terms($post->ID, 'job_department', array("fields" => "all")); 
		if($term_list_department){
			if(!in_array($term_list_department[0]->name, $temAarry)){
				array_push($temAarry, $term_list_department[0]->name); ?>
				<option data-sortbydepartment='<?php echo $term_list_department[0]->name; ?>'><?php echo $term_list_department[0]->name; ?></option><?php	
			}
		}
	endwhile;
	
	wp_reset_postdata(); 
	die();
}