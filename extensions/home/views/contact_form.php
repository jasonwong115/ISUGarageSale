<?php
	if(isset($sent)){
		if($sent){
			echo "<p>Thank you for contacting us! We'll get back you as soon as possible!</p>";
		}else{ //Mail was unsuccessful at sending
			echo "An error occurred, try again later! Sorry!";
		}
	}else if(isset($errors)){
		echo "<p>You must enter a valid email</p>";
	}else{//No form information was submitted, so redirect back to contact page
		header("Location: /home/contact");
		exit;
	}
?>