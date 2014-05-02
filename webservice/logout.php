<?php

/*
 * Following code will logout the user
 */

// array for JSON response
$response = array();
//
// check for required fields
if (isset($_POST['uid']) && !empty($_POST['uid'])) {

	//Connect to database
	require_once ('db_connect.php');
	$db = new DB_CONNECT();
	$data = $db -> connect();

	//POST form information
	$uid = $_POST['uid'];

	// mysql inserting a new row
	$result = mysqli_query($data, "DELETE FROM db30907.gs_logged_in_users WHERE uid='" . $uid . "'");
	// check if row inserted or not
	if ($result) {

		$response["success"] = 1;
		$response["message"] = "Logout successful";

		//echoing JSON response
		echo json_encode($response);

	} else {
		// username and password are invalid
		$response["success"] = 0;
		$response["message"] = "Logout failed";

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