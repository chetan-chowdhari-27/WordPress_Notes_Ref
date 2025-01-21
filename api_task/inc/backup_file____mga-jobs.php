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


	// echo '<pre>';
	// print_r($loop);
	// echo '</pre>'; die;
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



/* Custom Cron hook for Import jobs from API
** Date:16-03-2023
*/

function smartrecruiters_api_get_callback($get_url = '', $x_token= '', $offset = 0 , $limit = 50) {
    
   $curl = curl_init();

  curl_setopt_array($curl, array(
  CURLOPT_URL => $get_url.'?limit='.$limit.'&offset='.$offset,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'x-smarttoken: '.$x_token,
    'Cookie: AWSALB=U1pKeLtf+T48jeCWloi34+cFjHFK4glIIlrBJmeBlfU8rfEqmtjdaJpQSaxOJgEcBb7J9ATD9OAFVH6OeOrtaszeFnu1ZGMYiNULemQbXtcSYzfZsOcvwtmlhTXR; AWSALBCORS=U1pKeLtf+T48jeCWloi34+cFjHFK4glIIlrBJmeBlfU8rfEqmtjdaJpQSaxOJgEcBb7J9ATD9OAFVH6OeOrtaszeFnu1ZGMYiNULemQbXtcSYzfZsOcvwtmlhTXR'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return $response;

}

function __update_post_meta( $post_id, $field_name, $value = '' )
{
    if ( empty( $value ) OR ! $value )
    {
        delete_post_meta( $post_id, $field_name );
    }
    elseif ( ! get_post_meta( $post_id, $field_name ) )
    {
        add_post_meta( $post_id, $field_name, $value );
    }
    else
    {
        update_post_meta( $post_id, $field_name, $value );
    }
}

add_action( 'mga_custom_cron_1', 'mga_custom_cron_1_func' );
 
function mga_custom_cron_1_func() {
	
	/* smartrecruiters API Functions */
	$get_url = 'https://api.smartrecruiters.com/v1/companies/MGAHomecare/postings';
	$x_token = get_field('sm_x_token', 'option');
	

	global $wpdb;
	$cron_table_data = $wpdb->get_results( "SELECT * FROM wp_cron_info" );
	if(count($cron_table_data) == 0) {
		/* wp cron info custom table insert */

		$offset = 0;
		$limit = 50;
		$total_request = 1;

		$api_data = smartrecruiters_api_get_callback($get_url, $x_token, $offset, $limit);
		$careers_jobs_listing = json_decode($api_data, true);

		if($careers_jobs_listing['totalFound'] > 50){
		 $total_request = $careers_jobs_listing['totalFound'] / 50;	
		 if(is_float($total_request)){
			 $total_request=(int)$total_request;
		 }
		 $total_request = (int)$total_request + 1;
		}

				
				
		$tablecron = $wpdb->prefix.'cron_info';

		$insert = $wpdb->insert( $tablecron, array(
			'cron_offset' => $offset, 
			'cron_limit' => $limit,
			'cron_request' => $total_request, 
			'cron_total_records' => $careers_jobs_listing['totalFound'] ),
			array( '%d', '%d', '%d','%d' ) 
		);
		
		if(!$insert){
			echo 'Error in insert.</br>';
			echo $wpdb->last_error;
		} else {
			echo 'Insert Successful</br>';
			echo '</br>Cron Offset: '.$offset;
			echo '</br>Cron Limit: '.$limit;
			echo '</br>Total Cron Request: '.$total_request;
			echo 'Total Records: '.$careers_jobs_listing['totalFound'];
			
		}
	} else {
		echo "Job import cron in process";
	}
	
}




add_action( 'mga_custom_cron_2', 'mga_custom_cron_2_func' );
 
function mga_custom_cron_2_func() {
	
	/* smartrecruiters API Functions */
	$get_url = 'https://api.smartrecruiters.com/v1/companies/MGAHomecare/postings';
	$x_token = get_field('sm_x_token', 'option');
	

	global $wpdb;
	$cron_table_data = $wpdb->get_results( "SELECT * FROM wp_cron_info" );

	//print_r($cron_table_data);
	if(count($cron_table_data) > 0) {

		/* smartrecruiters API Functions call */
		$cron_info  = $wpdb->prefix."cron_info";
		$cron_table_id = $cron_table_data[0]->id;
		$offset = $cron_table_data[0]->cron_offset;
		$limit = $cron_table_data[0]->cron_limit;
		$total_count = $cron_table_data[0]->cron_total_records;
		$total_request = $cron_table_data[0]->cron_request;
		$cron_job_ids = $cron_table_data[0]->cron_job_ids;


		if($total_request > 0) {
			$api_data = smartrecruiters_api_get_callback($get_url, $x_token, $offset, $limit);
			$careers_jobs_listing = json_decode($api_data, true);
			
			
			if($careers_jobs_listing['totalFound'] != $total_count){
				
				if($careers_jobs_listing['totalFound'] < $total_count) {
					
					
					$updated_total_request = $careers_jobs_listing['totalFound'] / 50;	
					 if(is_float($updated_total_request)){
						 $updated_total_request=(int)$updated_total_request;
					 }
					$updated_total_request = (int)$updated_total_request + 1;
					
					if($total_request > $updated_total_request) {
						
						$wpdb->delete( $cron_info, array( 'id' => $cron_table_id ) );
					 
						exit; 
					} else {
						$update = $wpdb->update($cron_info,
						array(
						
							'cron_request'   => $updated_total_request,
							'cron_total_records'   => $careers_jobs_listing['totalFound']
						), 
						array(
							'id'    =>  $cron_table_id,
						)
						);
			
						if(!$update){
							echo 'Error in update total request and total.</br>';
							echo $wpdb->last_error;
						} else {
							echo 'Total request Update Successful</br>';
							echo '</br>Updated Total Cron Request: '.$updated_total_request;
						}
						
					}
					
				} elseif($careers_jobs_listing['totalFound'] > $total_count) {
					
					$updated_total_request = $careers_jobs_listing['totalFound'] / 50;	
					 if(is_float($updated_total_request)){
						 $updated_total_request=(int)$updated_total_request;
					 }
					$updated_total_request = (int)$updated_total_request + 1;
					 
					$update = $wpdb->update($cron_info,
						array(
						
							'cron_request'   => $updated_total_request,
							'cron_total_records'   => $careers_jobs_listing['totalFound']
						), 
						array(
							'id'    =>  $cron_table_id,
						)
					);
			
					if(!$update){
						echo 'Error in update total request and total.</br>';
						echo $wpdb->last_error;
					} else {
						echo 'Total request Update Successful</br>';
						echo '</br>Updated Total Cron Request: '.$updated_total_request;
					}
						
				} else {
					//
				}
			}

			$job_ids = array();
			$api_ids = array();
			$cnt=1;
			if(!empty($careers_jobs_listing['content'])) {
				foreach($careers_jobs_listing['content'] as $careers_job) {
					
					$api_ids[] = $careers_job['id'];
					
					$job_url = 'https://api.smartrecruiters.com/v1/companies/MGAHomecare/postings/'.$careers_job['id'];
					$single_job_data = smartrecruiters_api_get_callback($job_url, $x_token);
					$careers_job_details = json_decode($single_job_data, true);
					//echo "<pre>";print_r($careers_job_details);
					
							//check for job existence
							$check_job = get_posts(array(
								'posts_per_page'   => -1,
								'post_type'  => 'jobs',
								'meta_key'         => 'smr_job_id',
								'meta_value'       => $careers_job_details['id']
							));
						
						

						  $job_department_cat = trim($careers_job_details['department']['label']);
						  $job_department_state = trim($careers_job_details['location']['region']);
						  $job_department_city = trim($careers_job_details['location']['city']);

						  $brand='';
						  foreach($careers_job_details['customField'] as $job_brand){
							if($job_brand['fieldLabel'] == 'Brands'){
								$brand = $job_brand['valueLabel']; 
							}
						  }
						  handle_response_log($brand);

						  $job_post_meta = array(
							'smr_job_id'  => trim($careers_job_details['id']),
							'smr_job_uuid'  => trim($careers_job_details['uuid']),
							'smr_job_ids'  => trim($careers_job_details['jobId']),
							'smr_job_adid'  => trim($careers_job_details['jobAdId']),
							'refNumber'  => trim($careers_job_details['refNumber']),
							'jb_country'  => trim($careers_job_details['location']['country']),
							'job_remote_work'  => !empty($careers_job_details['location']['remote']) ? $careers_job_details['location']['remote'] : 0,
							'job_type'  => trim($careers_job_details['typeOfEmployment']['label']),
							'company_description_title'  => trim($careers_job_details['jobAd']['sections']['companyDescription']['title']),
							'brand'  => trim($brand),
							'company_description_mga'  => $careers_job_details['jobAd']['sections']['companyDescription']['text'],
							'job_description_title'  => trim($careers_job_details['jobAd']['sections']['jobDescription']['title']),
							'job_description_mga'  => $careers_job_details['jobAd']['sections']['jobDescription']['text'],
							'qualifications_title'  => trim($careers_job_details['jobAd']['sections']['qualifications']['title']),
							'qualifications_description'  => $careers_job_details['jobAd']['sections']['qualifications']['text'],
							'additional_information_title'  => trim($careers_job_details['jobAd']['sections']['additionalInformation']['title']),
							'additional_description'  => $careers_job_details['jobAd']['sections']['additionalInformation']['text'],
							'experience_level'  => trim($careers_job_details['experienceLevel']['label']),
							'language'  => trim($careers_job_details['language']['label']),
						  );
						  

							
							
							if(count($check_job) == 0) {
								
								// Create post object
								  $job_post = array(
									'post_type'  => 'jobs',
									'post_title'    => wp_strip_all_tags($careers_job_details['name']),
									'post_status'   => 'publish',
									'post_author'   => 1,
								  );
								//Insert the post into the database
								$the_post_id = wp_insert_post( $job_post );
								wp_set_object_terms( $the_post_id, $job_department_cat, 'job_department' );
								wp_set_object_terms( $the_post_id, $job_department_state, 'job_states' );
								wp_set_object_terms( $the_post_id, $job_department_city, 'job_cities' );

								foreach($job_post_meta as $key => $val){
								__update_post_meta( $the_post_id, $key, $val );
								}
							
							} else {
								foreach($job_post_meta as $key => $val){
								__update_post_meta( $check_job[0]->ID, $key, $val );
								}
								wp_set_object_terms( $check_job[0]->ID, $job_department_cat, 'job_department' );
								wp_set_object_terms( $check_job[0]->ID, $job_department_state, 'job_states' );
								wp_set_object_terms( $check_job[0]->ID, $job_department_city, 'job_cities' );
								handle_response_log($check_job[0]->ID);
								
							}
						
					$cnt++;
				}
			}

			echo "Total Jobs Inserted/Updated:".count($api_ids)."</br>";


			if($cron_job_ids != ''){
				$cron_job_ids .= ",".implode(",",$api_ids);
			} else {
				$cron_job_ids .= implode(",",$api_ids);
			}
			//echo $cron_job_ids;
			$offset = $offset + 50;
			$total_request = $total_request - 1;

			$update = $wpdb->update($cron_info,
				array(
					'cron_offset'    => $offset,
					'cron_request'   => $total_request,
					'cron_job_ids'   => $cron_job_ids
				), 
				array(
					'id'    =>  $cron_table_id,
				)
			);
			
			if(!$update){
					echo '</br>Error in update.</br>';
					echo $wpdb->last_error;
				} else {
					echo 'Update Successful</br>';
					echo '</br>Cron Offset: '.$offset;
					echo '</br>Final Total Cron Request: '.$total_request;
				}
			if($total_request == 0){
				$newarray = array();
				$newarray = explode(",",$cron_job_ids);
				
				echo "Total new ids: ".count($newarray)."</br>";
				//echo "<pre>";print_r($newarray);
					
					$jobs_args = array(
	                    'posts_per_page'   => -1,
	                    'post_type'  => 'jobs',
	                );
					$my_query = new WP_Query( $jobs_args );
					echo $count = $my_query->found_posts;
					
					$rcnt = 0;
					if ( $my_query->have_posts() ) {
						while ( $my_query->have_posts() ) {
							$my_query->the_post();
							$job_id = get_field( "smr_job_id", get_the_ID() );
							$id_exist = array_search($job_id,$newarray);
							
							if ($id_exist === false) 
							{
								$myvals = get_post_meta(get_the_ID());
								foreach($myvals as $key=>$val)  {
										delete_post_meta(get_the_ID(), $key);
								}
								wp_delete_post(get_the_ID(), true);
								$rcnt++;
							}

						}
					}
				echo "</br>Total Deleted Jobs:".$rcnt; 	
			}
				
		} else {
			
			$wpdb->delete( $cron_info, array( 'id' => $cron_table_id ) );

		}
	}
	
}

//wp_schedule_event( time(), 'minutely', 'mga_custom_cron' );
//wp_clear_scheduled_hook( 'mga_custom_cron' );