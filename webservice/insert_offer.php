<?php

/*
 * Following code will insert a offer into the database
 */

// array for JSON response
$response = array();

// check for required fields
if (isset($_POST['userid']) && isset($_POST['listingid']) && isset($_POST['offer_price']) && isset($_POST['offer_other']) && isset($_POST['comment']) &&
	!empty($_POST['userid']) && !empty($_POST['listingid']) && !empty($_POST['offer_price']) && !empty($_POST['offer_other']) && !empty($_POST['comment'])) {
	
	//Connect to database
	require_once('db_connect.php');
    $db = new DB_CONNECT();
    $data = $db->connect();
    
    //Get form information
    $userid = $_POST['userid'];
    $listingid = $_POST['listingid'];
    $offer_price = $_POST['offer_price'];
	$offer_other = $_POST['offer_other'];
	$comment = $_POST['comment'];
	$uid = $_POST['uid'];

    // mysql inserting a new row
    $result = mysqli_query($data,"INSERT INTO gs_offers(userid,listingid,offer_price,offer_other,accepted,comment,status,best_offer,review_submitted) VALUES('$userid', '$listingid', '$offer_price','$offer_other','NULL','$comment',0,0,0)");

    // check if row inserted or not
    if ($result) {
        // successfully inserted into database
        $response["success"] = 1;
        $response["message"] = "Offer successfully created";

        // echoing JSON response
        echo json_encode($response);
    } else {
        // failed to insert row
        $response["success"] = 0;
        $response["message"] = "Oops! An error occurred.";
		$response["query"] = "INSERT INTO gs_offers(userid,listingid,offer_price,offer_other,accepted,comment,status,best_offer,review_submitted) VALUES('$userid', '$listingid', '$offer_price','$offer_other','NULL','$comment',0,0,0)";
        
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