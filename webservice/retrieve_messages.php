<?php
if (true) {
	require_once('db_connect.php');
	$db = new DB_CONNECT();
	$data = $db->connect();
	
	// mysql get messages
	$result = mysqli_query($data,"SELECT *
	FROM gs_messages m
	WHERE m.toid = 2 OR m.fromid = 2");
	 
	// check for empty result
	if (mysqli_num_rows($result) > 0) {
		// looping through all results
		$response["messages"] = array();
	 
		while ($row = mysqli_fetch_array($result)) {
			// temp user array
			$message = array();
			$message["fromid"] = $row["fromid"];
			$message["toid"] = $row["toid"];
			$message["date_created"] = $row["date_created"];
			$message["status"] = $row["status"];
			$message["message"] = $row["message"];
			$message["subject"] = $row["subject"];
	 
			// push single message into final response array
			array_push($response["messages"], $message);
		}
		// success
		$response["success"] = 1;
	 
		// echoing JSON response
		echo json_encode($response);
	} else {
		// no products found
		$response["success"] = 0;
		$response["message"] = "No messages found";
	 
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