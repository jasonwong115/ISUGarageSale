<?php

/*
 * Following code will add user to database
 */

// array for JSON response
$response = array();

// check for required fields
if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password']) &&
	!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['username']) && !empty($_POST['password'])) {
	
	//Connect to database
	require_once('db_connect.php');
	require_once('../controllers/passwordPHP.php');
    $db = new DB_CONNECT();
    $data = $db->connect();
	
    //Get form information
    $name= $_POST['name'];
	$phone= $_POST['phone'];
    $email= $_POST['email'] . "@iastate.edu";
    $username= $_POST['username'];
	$password = $_POST['password'];
	$hashedPassword = password_hash($password, PASSWORD_BCRYPT); //save the;
	$usertype = $_POST['usertype'];
	$major = $_POST['major'];

    // mysql inserting a new row
    $result = mysqli_query($data,"INSERT INTO gs_users(name, phone, handle, password, usertype)
		VALUES('$name','$phone','$username','$hashedPassword','$usertype')");

    // check if row inserted or not
    if ($result) {
		$result2 = mysqli_query($data,"SELECT u.id
			FROM gs_users u
			WHERE u.handle = '$username'");
			
		//Check if right userid retrieved
		if (mysqli_num_rows($result2) > 0) {
			$row = mysqli_fetch_array($result2);
			$userid = $row["id"];
			$result3 = mysqli_query($data,"INSERT INTO gs_profiles(userid,major)
				VALUES('$userid','$major')");
				
			//If profile successfully inserted create activation code
			if($result3){
				$hashActivation = md5( rand(0,1000));
				$code = rand(1000,5000);
				$result4 = mysqli_query($data,"INSERT INTO gs_activation(userid,hash,code)
					VALUES('$userid','$hashActivation','$code')");
				//If activation successfully inserted send email
				if($result4){
					//Send email
					//Who the email will say it is from
					$from = 'ISUGarageSale'; 
					
					// subject of the email
					$email_subject = $from . ": activate your account";
					
					// body content of the email
					$email_body = "Thank you for registering with " . $from . ". " .
					"Follow the link below to enter the following code:\n\n".
					"Code: " . $code . "\n\n" .
					"http://proj-309-07.cs.iastate.edu/user/activation/?id=" . $userid . "&hash=" . $hashActivation;
						
					// headers
					$headers = "From: $from\n";
					
					$to = $email;
					$sent =  mail($to,$email_subject,$email_body,$headers);
					// email successfully sent
					if($sent){
						$response["success"] = 1;
						$response["message"] = "Confirmation email sent";
						echo json_encode($response);
					// email failed to send
					}else{
						$response["success"] = 0;
						$response["message"] = "Could not send confirmation email";
						echo json_encode($response);
					}
				//Could not create activation code
				}else{
					$response["success"] = 0;
					$response["message"] = "Could not create activation code";
					echo json_encode($response);
				}
				// success
				$response["success"] = 1;
				// echoing JSON response
				echo json_encode($response);
			// Profile could not be created
			}else{
				$response["success"] = 0;
				$response["message"] = "Could not create profile";
			 
				// echoing JSON response
				echo json_encode($response);
			} // else for $result 3
		// Wrong user id given
		}else{
			$response["success"] = 0;
			$response["message"] = "Could not find the created user in the users table.";
		 
			// echoing JSON response
			echo json_encode($response);
		
		} // else for mysqli_num_rows($result2) > 0
        // successfully inserted into database
        $response["success"] = 1;
        $response["message"] = "User successfully created";

        // echoing JSON response
        echo json_encode($response);
    } else {
        // failed to insert row
        $response["success"] = 0;
        $response["message"] = "Oops! An error occurred. " . mysqli_error($data) ;
        
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