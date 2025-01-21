<?php

function display_new_edit_button( $entry, $form_data ) {
    $form_id = $entry->form_id;

    // Ensure this applies to the target form ID
    if ( absint( $form_id ) !== 14708 ) {
        return;
    }

    echo '<div style="margin-top: 20px;">
            <label>
                <input name="make-api-call" type="checkbox" value="true"> 
                ' . esc_html__( 'Update to WorkDay API', 'wpforms' ) . '
            </label>
          </div>';
}

if ( wpforms_is_admin_page( 'entries', 'edit' ) ) {
    add_action( 'wpforms_entry_details_sidebar_details_action', 'display_new_edit_button', 11, 2 );
}


function wpf_job_entry_admin_edit_submissions( $form_data, $response, $updated_entry, $entry ) {
    $form_id = $entry->form_id;
    if ( absint( $form_id ) !== 14708 ) {
        return $response;
    }

    $entry_id = $entry->entry_id;
    $make_api_call = isset( $_REQUEST['make-api-call'] );

    if ( ! $make_api_call ) {
        return $response;
    }

    // Decode the existing fields
    $existing_fields = json_decode( $entry->fields, true );

    // Ensure the updated fields retain all original fields if not updated
    $fields = array_replace_recursive( $existing_fields, $updated_entry );

    // Extract job ID and Candidate ID
    $job_id = $fields[67]['value'] ?? null;

    // Ensure job ID exists
    if ( empty( $job_id ) ) {
        return $response;
    }

    // Detect changes and log updates
    foreach ( $updated_entry as $field_id => $updated_value ) {
        if (
            isset( $existing_fields[ $field_id ] ) && 
            $existing_fields[ $field_id ]['value'] !== $updated_value['value']
        ) {
            error_log( "Field {$field_id} updated: " . print_r( $updated_value['value'], true ) );
        }
    }

    $city = $fields[78]['city'];
    $state = $fields[78]['state'];
    $country = $fields[78]['country'];
    $postal = $fields[78]['postal'];
    $start_date = $fields[90]['date'];
    $start_year = (new DateTime($start_date))->format('Y');
    $end_date = $fields[91]['date'];  
    $end_year = (new DateTime($end_date))->format('Y');
    $jobTitle = $fields[40]['value'];
    $employer = $fields[42]['value'];

    // Gather education information
    $education = [];
    if (!empty($fields[53]['value']) && !empty($fields[54]['value'])) {
        $education[] = [
            "institution" => $fields[53]['value'],
            "degree" => $fields[54]['value'],
        ];
    }

    $filename = $fields[9]['value_raw'][0]['name'];
    $filetype = $fields[9]['value_raw'][0]['type'];
    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['baseurl'] . '/wpforms/';
    $ch = curl_init($upload_path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $file_path = curl_exec($ch);
    curl_close($ch);
    $encoded_file_content = base64_encode($file_path);  

    $apply_job_post = [
        "personal" => [
            "firstName" => $fields[2]['value'],
            "lastName" => $fields[17]['value'],
            "email" => $fields[4]['value'],
            "phone" => $fields[6]['value']
        ],
        "address" => [
            "address_line1" => $fields[78]['value'],
            "country" => $country,
            "region" => $state,
            "city" => $city,
            "postal" => $postal,
            "start_year" => $start_year,
            "end_year" => $end_year,
        ],
        "education" => $education,
        "workExperience" => [
            "jobTitle" => $jobTitle,
            "employer" => $employer,

        ],        
        "resume" => [
            "fileName" => $filename, 
            "mimeType" => $filetype, 
            "fileContent" => $encoded_file_content,
        ],
        "how_hear_about_us" => $fields[65]['value'],
        "consent" => true,
    ];

    $api_response = apply_to_workday_api( $job_id, $apply_job_post );
    error_log( 'API Result: ' . print_r( $api_response, true ) );
    $fields[81]['value'] = json_encode( $api_response ); 
    $fields[82]['value'] = json_encode( $apply_job_post ); 
    $fields[83]['value'] = $api_response['error'] ?? 'Success';

    wpforms()->entry->update( $entry_id, [ 'fields' => json_encode( $fields ) ], '', '', [ 'cap' => false ] );

    return $response;
}

add_action( 'wpforms_pro_admin_entries_edit_submit_completed', 'wpf_job_entry_admin_edit_submissions', 10, 4 );

if ( wpforms_is_admin_page( 'entries', 'edit' ) ) {
    add_action( 'wpforms_entry_details_sidebar_details_action', 'display_new_edit_button', 11, 2 );
}
