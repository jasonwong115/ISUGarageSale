<?php
//User must be logged in to return any data
if (isset($_POST['userloggedin'])) {
	//Connect to the database
	require_once('db_connect.php');
	$db = new DB_CONNECT();
	$data = $db->connect();
	
	
	$userloggedin = $_POST['userloggedin'];
	$status = GarageSale\BaseDatabase::STATUS_ACTIVE;
	
	// mysql retrieve listings that are active
	$result = mysqli_query($data,"SELECT *
	FROM gs_listings l 
	WHERE l.userid = '$userloggedin' 
	AND l.status='$status'");
	 
	// check for empty result
	if (mysqli_num_rows($result) > 0) {
		// looping through all results
		$response["listings"] = array();
	 
		while ($row = mysqli_fetch_array($result)) {
			// temp user array
			$listing = array();
			$userID = $row["userid"];
			$listing["userid"] = $userID;
			$listing["listingid"] = $row["id"];
			$listing["productName"] = $row["title"];
			$listing["description"] = $row["description"];
			$listing["asking_price"] = $row["asking_price"];
			$listing["image_paths"] = $row["image_paths"];
			
			//Get the users handle from their user id
			$result2 = mysqli_query($data,"SELECT handle
				FROM gs_users u
				WHERE u.id ='$userID'");
			$row2 = mysqli_fetch_array($result2);
			$listing["handle"] = $row2["handle"];
			// push single product into final response array
			array_push($response["listings"], $listing);
		}
		// success
		$response["success"] = 1;
	 
		// echoing JSON response
		echo json_encode($response);
	} else {
		// no listings found
		$response["success"] = 0;
		$response["message"] = "No listings found";
	 
		// echo no users JSON
		echo json_encode($response);
	}
}else{
	$response["success"] = 0;
	$response["message"] = "User not logged in";
 
	// echo no users JSON
	echo json_encode($response);
}
?>