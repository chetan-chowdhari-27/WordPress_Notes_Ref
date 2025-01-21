<?php

function apply_to_workday_api($job_id, $payload) {
    $username = 'ISU_RaaS_Job_Postings@mgahomecare2';
    $password = '5z8hvnea$rikQU)NpIQxou';

    $dateTime = new DateTime();
    $dateTime->setTimezone(new DateTimeZone('UTC'));
    $formatted_date = $dateTime->format('Y-m-d');

    // Build SOAP Envelope with Payload Data
    $xml_payload = <<<XML
    <?xml version="1.0" ?>
    <env:Envelope xmlns:env="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"
        xmlns:bsvc="urn:com.workday/bsvc">
        <env:Header>
            <wsse:Security env:mustUnderstand="1">
                <wsse:UsernameToken>
                    <wsse:Username>$username</wsse:Username>
                    <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">$password</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>
        </env:Header>
        <env:Body>
            <wd:Put_Candidate_Request xmlns:wd="urn:com.workday/bsvc" wd:Add_Only="true">
                <wd:Candidate_Data>
                    <wd:Name_Data>
                        <wd:Legal_Name>
                            <wd:Name_Detail_Data>
                                <wd:First_Name>{$payload['personal']['firstName']}</wd:First_Name>
                                <wd:Last_Name>{$payload['personal']['lastName']}</wd:Last_Name>
                            </wd:Name_Detail_Data>
                        </wd:Legal_Name>
                    </wd:Name_Data>
                    <wd:Contact_Data>
                        <wd:Location_Data>
                            <wd:Country_Reference>
                                <wd:ID wd:type="ISO_3166-1_Alpha-3_Code">USA</wd:ID>
                            </wd:Country_Reference>
                            <wd:Address_Line_1>{$payload['address']['address_line1']}</wd:Address_Line_1>
                            <wd:City>{$payload['address']['city']}</wd:City>
                            <wd:Postal_Code>{$payload['address']['postal']}</wd:Postal_Code>
                        </wd:Location_Data>
                        <wd:Phone_Device_Type_Reference>
                            <wd:ID wd:type="Phone_Device_Type_ID">Mobile</wd:ID>
                        </wd:Phone_Device_Type_Reference>
                        <wd:Country_Phone_Code_Reference>
                            <wd:ID wd:type="Country_Phone_Code_ID">USA_1</wd:ID>
                        </wd:Country_Phone_Code_Reference>
                        <wd:Phone_Number>{$payload['personal']['phone']}</wd:Phone_Number>
                        <wd:Email_Address>{$payload['personal']['email']}</wd:Email_Address>
                    </wd:Contact_Data>
                    <wd:Job_Application_Data>
                        <wd:Job_Applied_To_Data>
                            <wd:Job_Requisition_Reference>
                                <wd:ID bsvc:type="Job_Requisition_ID">$job_id</wd:ID>
                            </wd:Job_Requisition_Reference>
                            <wd:Stage_Reference>
                                <wd:ID bsvc:type="Recruiting_Stage_ID">REVIEW</wd:ID>
                            </wd:Stage_Reference>
                            <wd:Job_Application_Date>$formatted_date</wd:Job_Application_Date>
                        </wd:Job_Applied_To_Data>
                        <wd:Resume_Data>
                            <wd:Experience_Data>    
                                <wd:Company_Name>{$payload['workExperience']["jobTitle"]}</wd:Company_Name>
                                <wd:Title>{$payload['workExperience']["jobTitle"]}</wd:Title>   
                                <wd:Start_Year>{$payload['address']['start_year']}</wd:Start_Year>
                                <wd:End_Year>{$payload['address']['end_year']}</wd:End_Year>
                            </wd:Experience_Data>
                            <wd:Education_Data>
                                <wd:School_Name>{$payload['education'][0]}</wd:School_Name>
                                <wd:Degree_Reference>{$payload['education'][1]}</wd:Degree_Reference>
                            </wd:Education_Data>     
                        </wd:Resume_Data>
                        <wd:Resume_Attachment_Data>
                            <wd:Filename>{$payload['resume']['fileName']}</wd:Filename>
                            <wd:File_Content>{$payload['resume']['fileContent']}</wd:File_Content>
                            <wd:Mime_Type_Reference>{$payload['resume']['mimeType']}</wd:Mime_Type_Reference>
                            <wd:Comment>{$payload['how_hear_about_us']}</wd:Comment>
                        </wd:Resume_Attachment_Data>   
                    </wd:Job_Application_Data>
                </wd:Candidate_Data>
            </wd:Put_Candidate_Request>
        </env:Body>
    </env:Envelope>
    XML;

    // Send cURL Request
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL            => 'https://impl-services1.wd12.myworkday.com/ccx/service/mgahomecare2/Recruiting/v43.0',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $xml_payload,
        CURLOPT_HTTPHEADER     => array('Content-Type: application/xml'),
        CURLOPT_USERPWD        => "$username:$password",
        CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
    ));

    // Execute the request and obtain the response
    $response = curl_exec($curl);

    // Check for cURL errors
    if (curl_errno($curl)) {
        $error_message = 'cURL Error: ' . curl_error($curl);
        return ['error' => $error_message, 'xml_payload' => $xml_payload];
    }

    // Log HTTP Information
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    $response_xml = new SimpleXMLElement($response);
    $error_details = new SimpleXMLElement($response);

    // Parse the SOAP response only if HTTP code is 200
    if ($httpCode == 200) {
        $response_xml->registerXPathNamespace('wd', 'urn:com.workday/bsvc');
        $candidate_wid = $response_xml->xpath('//wd:Candidate_Reference/wd:ID[@wd:type="WID"]');
        $candidate_id = $response_xml->xpath('//wd:Candidate_Reference/wd:ID[@wd:type="Candidate_ID"]');
        $job_application_id = $response_xml->xpath('//wd:Job_Application_Reference/wd:ID[@wd:type="Job_Application_ID"]');
        
        return [
            'status' => 'Success',
            //'http_code' => $httpCode,
            'candidate_wid' => $candidate_wid ? (string)$candidate_wid[0] : null,
            'candidate_id' => $candidate_id ? (string)$candidate_id[0] : null,
            'job_application_id' => $job_application_id ? (string)$job_application_id[0] : null,
            //'api_response' => $response,
        ];
    } else {
        return [
            'status' => 'Fail',
            //'http_code' => $httpCode,
            'error' => $response,
        ];
    }
}


/**
 * WPForms Process Completion Hook
 */
add_action('wpforms_process_complete', 'wpf_apply_process_complete', 10, 4);
function wpf_apply_process_complete($fields, $entry, $form_data, $entry_id) {
    // Restrict to specific form ID
    if (absint($form_data['id']) !== 14708) {
        return;
    }
    $job_id = $fields[67]['value'];
    // Prepare payload only if job_id is present
    if (empty($job_id)) {
        return;
    }
    // Extracting address information
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
    
    // Retrieve the file details from WPForms
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
    // Call the Workday API
    $response_data = apply_to_workday_api($job_id, $apply_job_post);
    
    // Get the current entry data
    $entry_data = wpforms()->entry->get($entry_id);
    $entry_fields = json_decode($entry_data->fields, true);
    // Store the API response and payload
    $entry_fields[81]['value'] = json_encode($response_data); // Raw API Response
    $entry_fields[82]['value'] = json_encode($apply_job_post); // JSON Payload
    $entry_fields[83]['value'] = $response_data['status'] === 'Success' ? 'Success' : ($response_data['status'] ?? 'Fail');
    // Update the entry with new field data
    wpforms()->entry->update($entry_id, ['fields' => json_encode($entry_fields)], '', '', ['cap' => false]);
}