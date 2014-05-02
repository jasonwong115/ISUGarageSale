<?php

/*
 * Following code will insert a contact into the database
 */

// array for JSON response
$response = array();

// check for required fields
if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['subject']) && isset($_POST['message']) && isset($_POST['reason']) &&
	!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['subject']) && !empty($_POST['message']) && !empty($_POST['reason'])) {
	
	//Connect to database
	require_once('db_connect.php');
    $db = new DB_CONNECT();
    $data = $db->connect();
    
    // mysql inserting a new row
    $result = mysqli_prepare($data,"INSERT INTO gs_contact(name,email,subject,message,reason,status) VALUES(?, ?, ?, ?, ?, ?)");
	mysqli_stmt_bind_param($result, 'sssssi', $name,$email,$subject,$message,$reason,$status);
	
	//Get form information
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
	$message = $_POST['message'];
	$reason = $_POST['reason'];
	$status = GarageSale\BaseDatabase::STATUS_ACTIVE;
	
	mysqli_stmt_execute($result);
	
    // check if row inserted or not
    if ($result) {
        // successfully inserted into database
        $response["success"] = 1;
        $response["message"] = "Contact successfully created";

        // echoing JSON response
        echo json_encode($response);
    } else {
        // failed to insert row
        $response["success"] = 0;
        $response["message"] = "Oops! An error occurred.";
        
        // echoing JSON response
        echo json_encode($response);
    }
} else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";

    // echoing JSON response
    echo json_encode($response);
}
?>