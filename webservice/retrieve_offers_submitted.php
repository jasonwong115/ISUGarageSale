<?php
//User must be logged in before anything is returned
if (isset($_POST['userloggedin'])) {
	//Connect to the database
	require_once('db_connect.php');
	$db = new DB_CONNECT();
	$data = $db->connect();
	
	//Get user id
	$userid = $_POST['userloggedin'];
	
	// mysql inserting a new row
	$result = mysqli_query($data,"SELECT o.*,o.userid AS buyer,o.status AS offerstatus,l.*
	FROM gs_offers o, gs_listings l 
	WHERE o.listingid = l.id AND o.userid =" . $userid);
	 
	// check for empty result
	if (mysqli_num_rows($result) > 0) {
		// looping through all results
		$response["offers"] = array();
	 
		while ($row = mysqli_fetch_array($result)) {
			// temp user array
			$offer = array();
			$offer["sellerID"] = $row["userid"];
			$offer["buyerID"] = $row["buyer"];
			$offer["listingID"] = $row["listingid"];
			$offer["productName"] = $row["title"];
			$offer["offerStatus"] = $row["offerstatus"];
			$offer["offerComment"] = $row["comment"];
			$offer["offerPrice"] = $row["offer_price"];
			$offer["otherOffer"] = $row["offer_other"];
			$offer["imagePath"] = $row["image_paths"];
			$offer["accepted"] = $row["accepted"];
			$offer["best_offer"] = $row["best_offer"];
	 
			// push single product into final response array
			array_push($response["offers"], $offer);
		}
		// success
		$response["success"] = 1;
	 
		// echoing JSON response
		echo json_encode($response);
	} else {
		// no offers found
		$response["success"] = 0;
		$response["message"] = "No offers found";
	 
		// echo no users JSON
		echo json_encode($response);
	}
}else{
	// no products found
	$response["success"] = 0;
	$response["message"] = "User not logged in";
 
	// echo no users JSON
	echo json_encode($response);
}
?>