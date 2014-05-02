<?php
if (isset($_POST['listingid']) && isset($_POST['offerid'])) {
	require_once('db_connect.php');
	$db = new DB_CONNECT();
	$data = $db->connect();

	$listingid = $_POST['listingid'];
	$offerid = $_POST['offerid'];
	$status = GarageSale\BaseDatabase::STATUS_DECLINED;
	if(isset($_POST['accept'])){
		$status = GarageSale\BaseDatabase::STATUS_ACCEPTED;
		// mysql inserting a new row
		$result = mysqli_query($data,"update gs_offers o set o.accepted = '$status' where o.id = '$offerid'");
		$result = mysqli_query($data,"update gs_listings l set l.status = '$status' where l.id = '$listingid'");
	}else if($_POST['best']){
		$status = GarageSale\BaseDatabase::STATUS_BEST;
		$result = mysqli_query($data,"update gs_offers o set o.status = '$status' where o.id = '$offerid'");
	}else{
		$result = mysqli_query($data,"update gs_offers o set o.status = '$status' where o.id = '$offerid'");
	}
	
	
	 
	// check for empty result
	if ($result) {
		$response["success"] = 1;
		// echoing JSON response
		echo json_encode($response);
	}else{
		// no products found
		$response["success"] = 0;
		$response["message"] = "Update Failed";
	 
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