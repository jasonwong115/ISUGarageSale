<?php

/*
 * Following code will insert a offer into the database
 */

// array for JSON response
$response = array();
//


// check for required fields
if (isset($_POST['handle']) && isset($_POST['password']) && !empty($_POST['handle']) && !empty($_POST['password'])) {

	//Connect to database
	require_once ('db_connect.php');
	require_once ('../controllers/passwordPHP.php');
	$db = new DB_CONNECT();
	$data = $db -> connect();

	//POST form information
	$handle = $_POST['handle'];;
	$password = $_POST['password'];

	// mysql inserting a new row
	$result = mysqli_query($data, "SELECT * FROM db30907.gs_users WHERE handle='" . $handle . "'");
	// check if row inserted or not
	$row = mysqli_fetch_array($result);
	
	if (mysqli_num_rows($result) > 0 && password_verify($password, $row['password'])) {
		// username and password are valid
		
		$id = $row['id'];

		//check if the user is already logged in

		$result = mysqli_query($data, "SELECT uid FROM db30907.gs_logged_in_users WHERE id='" . $id . "'");
		if (mysqli_num_rows($result) > 0) {
			//user is already logged in
			$row = mysqli_fetch_array($result);
			$uid = $row['uid'];
			$response["id"] = $id;
			$response["uid"] = $uid;
			$response["success"] = 1;
			$response["message"] = "User already logged in";

			//echoing JSON response
			echo json_encode($response);
		} else {
			//user is not already logged in
			
			$uid = uniqid();
			
			//insert uid into users_logged_in table
			$result = mysqli_query($data, "INSERT INTO db30907.gs_logged_in_users(id,uid) VALUES('$id', '$uid')");
			
			if ($result) {
				$response["id"] = $id;
				$response["uid"] = $uid;
				$response["success"] = 1;
				$response["message"] = "Login successful";
				// echoing JSON response
				echo json_encode($response);
			} else {
				$response["success"] = 0;
				$response["message"] = "Failed to insert uid into database";
				// echoing JSON response
				echo json_encode($response);
			}

		}

		
	} else {
		// username and password are invalid
		$response["success"] = 0;
		$response["message"] = "Invalid handle or password";

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