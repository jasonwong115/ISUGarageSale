<?php

/*
 * Following code will insert a contact into the database
 */

// array for JSON response
$response = array();

// check for required fields
if (isset($_POST['userloggedin']) && isset($_POST['description']) && isset($_POST['asking_price']) && isset($_POST['other_offer']) && isset($_POST['title']) &&
	!empty($_POST['userloggedin']) && !empty($_POST['description']) && !empty($_POST['asking_price']) && !empty($_POST['other_offer']) && !empty($_POST['title'])) {
	
	//Connect to database
	require_once('db_connect.php');
    $db = new DB_CONNECT();
    $data = $db->connect();

    // mysql inserting a new row
    //$result = mysqli_query($data,"INSERT INTO gs_listings(userid,description,asking_price,other_offer,title) VALUES('$userloggedin','$description','$askingPrice','$otherOffer','$title')");
	$result = mysqli_prepare($data, "INSERT INTO gs_listings(userid,description,asking_price,other_offer,title) VALUES (?, ?, ?, ?, ?)");
	mysqli_stmt_bind_param($result, 'sssss', $userloggedin, $description, $askingPrice,$otherOffer,$title);
	
	//Get form information
	$userloggedin = $_POST['userloggedin'];
    $description = $_POST['description'];
    $askingPrice = $_POST['asking_price'];
	$otherOffer = $_POST['other_offer'];
	$title = $_POST['title'];
	
	mysqli_stmt_execute($result);
	
    // check if row inserted or not
    if ($result) {
        // successfully inserted into database
        $response["success"] = 1;
        $response["message"] = "Listing successfully created";

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
	$testing = "Hey \"Jason\"";
    $response["success"] = 0;
    $response["message"] = 'Required field(s)' . $testing . 'is missing';

    // echoing JSON response
    echo json_encode($response);
}
?>