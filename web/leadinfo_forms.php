<?php

include('config.php');

$leadinfo_post_json = file_get_contents('php://input');

if($leadinfo_post_json) {
    $leadinfo_post = json_decode($leadinfo_post_json);

    $leadinfo_fields_array = array(
        'company' => $leadinfo_post->company,
        'contact' => $leadinfo_post->contact,
        'on_url' => $leadinfo_post->on_url,
        'timestamp' => $leadinfo_post->timestamp,
        'type' => $leadinfo_post->type,
        'value' => $leadinfo_post->value
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
        $select_query = "SELECT * FROM `leadinfo_forms`
        WHERE `leadinfo_id` = '{$leadinfo_post->key}' 
        AND `bromotion_client_id` = " . Bromotion_client_id;

        if($select_result = $mysqli->query($select_query)) {
            if ($select_result->num_rows == 0){

                // Insert row in DB
                $insert_query = "INSERT INTO `leadinfo_forms` (`leadinfo_id`, `bromotion_client_id`";
                foreach(array_keys($leadinfo_fields_array) as $key){
                    $insert_query .= ", `" . $key . "`";
                }
                $insert_query .= ") VALUES ('{$leadinfo_post->key}', " . Bromotion_client_id;
                foreach($leadinfo_fields_array as $value){
                    $insert_query .= ", '" . mysqli_real_escape_string($mysqli, $value) . "'";
                }
                $insert_query .= ")";

                if ($mysqli->query($insert_query)) {
                    echo "Insert: " . $leadinfo_post->key;
                } else {
                    echo "Error: " . mysqli_error($mysqli);
                }

            } else {

                $updated_row = $select_result->fetch_assoc();
                $updated_id = $updated_row['id'];

                // Update row in DB
                $update_query = "UPDATE `leadinfo_forms` 
            SET `bromotion_client_id` = " . Bromotion_client_id;

                foreach($leadinfo_fields_array as $key => $value){
                    $update_query .= ", `" . $key . "` = '" . mysqli_real_escape_string($mysqli, $value) . "'";
                }

                $update_query .= "
            WHERE `leadinfo_id` = '{$leadinfo_post->key}'";
                
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