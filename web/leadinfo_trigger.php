<?php

include('config.php');

$leadinfo_post_json = file_get_contents('php://input');

if($leadinfo_post_json) {
    $leadinfo_post = json_decode($leadinfo_post_json);

    $leadinfo_fields_array = array(
        'address1' => $leadinfo_post->address1,
        'address2' => $leadinfo_post->address2,
        'address3' => $leadinfo_post->address3,
        'branch_code' => $leadinfo_post->branch_code,
        'branch_code_international' => $leadinfo_post->branch_code_international,
        'branch_international_type' => $leadinfo_post->branch_international_type,
        'branch_local_type' => $leadinfo_post->branch_local_type,
        'city' => $leadinfo_post->city,
        'click_id' => $leadinfo_post->click_id,
        'click_provider' => $leadinfo_post->click_provider,
        'coc_number' => $leadinfo_post->coc_number,
        'comment' => $leadinfo_post->comment,
        'country' => $leadinfo_post->country,
        'domain' => $leadinfo_post->domain,
        'dribbble' => $leadinfo_post->dribbble,
        'email_address' => $leadinfo_post->email_address,
        'employees' => $leadinfo_post->employees,
        'employees_total' => $leadinfo_post->employees_total,
        'facebook' => $leadinfo_post->facebook,
        'founding_date' => $leadinfo_post->founding_date,
        'github' => $leadinfo_post->github,
        'instagram' => $leadinfo_post->instagram,
        'leadinfo_link' => $leadinfo_post->leadinfo_link,
        'legal_form' => $leadinfo_post->legal_form,
        'linkedin' => $leadinfo_post->linkedin,
        'name' => $leadinfo_post->name,
        'page_url_first_visit' => $leadinfo_post->page_url_first_visit,
        'phone_number' => $leadinfo_post->phone_number,
        'pinterest' => $leadinfo_post->pinterest,
        'postal_code' => $leadinfo_post->postal_code,
        'province' => $leadinfo_post->province,
        'purpose' => $leadinfo_post->purpose,
        'sales_volume' => $leadinfo_post->sales_volume,
        'tags' => $leadinfo_post->tags,
        'timestamp' => $leadinfo_post->timestamp,
        'trigger' => $leadinfo_post->trigger,
        'trigger_name' => $leadinfo_post->trigger_name,
        'twitter' => $leadinfo_post->twitter,
        'vat_number' => $leadinfo_post->vat_number,
        'vimeo' => $leadinfo_post->vimeo,
        'whatsapp' => $leadinfo_post->whatsapp,
        'xing' => $leadinfo_post->xing,
        'youtube' => $leadinfo_post->youtube
    );

    $mysqli = new mysqli(DB_host, DB_user, DB_pass, DB_database);

    // Check connection
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    // Perform query
    if ($result = $mysqli->query("SELECT * FROM `bromotion_clients` WHERE `id` = " . Bromotion_client_id)) {

        // Check if row with leadinfo_id already exists
        $select_query = "SELECT * FROM `leadinfo_trigger`
        WHERE `leadinfo_id` = '{$leadinfo_post->id}' 
        AND `bromotion_client_id` = " . Bromotion_client_id;

        if($select_result = $mysqli->query($select_query)) {
            if ($select_result->num_rows == 0){

                // Insert row in DB
                $insert_query = "INSERT INTO `leadinfo_trigger` (`leadinfo_id`, `bromotion_client_id`";
                foreach(array_keys($leadinfo_fields_array) as $key){
                    $insert_query .= ", `" . $key . "`";
                }
                $insert_query .= ") VALUES ('{$leadinfo_post->id}', " . Bromotion_client_id;
                foreach($leadinfo_fields_array as $value){
                    $insert_query .= ", '" . mysqli_real_escape_string($mysqli, $value) . "'";
                }
                $insert_query .= ")";

                if ($mysqli->query($insert_query)) {
                    echo "Insert: " . $leadinfo_post->id;
                } else {
                    echo "Error: " . mysqli_error($mysqli);
                }

            } else {

                $updated_row = $select_result->fetch_assoc();
                $updated_id = $updated_row['id'];

                // Update row in DB
                $update_query = "UPDATE `leadinfo_trigger` 
            SET `bromotion_client_id` = " . Bromotion_client_id;

                foreach($leadinfo_fields_array as $key => $value){
                    $update_query .= ", `" . $key . "` = '" . mysqli_real_escape_string($mysqli, $value) . "'";
                }

                $update_query .= " WHERE `leadinfo_id` = '{$leadinfo_post->id}'";

                if ($mysqli->query($update_query)) {
                    echo "Update: " . $updated_id;
                } else {
                    echo "Error: " . mysqli_error($mysqli);
                }

            }
        }

        // Free result set
        $result->free_result();
    }

    $mysqli->close();

}