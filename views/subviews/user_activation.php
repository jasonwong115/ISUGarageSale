<?php
	if(isset($confirm)){
		echo"To activate your account, input the code you received in the email: ";
?>
<br />
<br />
<form name ="inputCode" method="POST" action = "<?php echo $app->form_path('user/activation?id=' . $id . "&hash=" . $hash); ?>">
	<label>Code: <input type='text' name = 'code' placeholder = 'Input code here...'></label>
	<input type="submit" name="Submit">
</form>
<?php
		if(isset($try_again)){
			echo "<br /><p style ='color:red;'>The code you entered is incorrect! If you think this is a mistake please contact us immediately!</p>";
		}
	}else if(isset($activated)){
		echo 'Your account is now activated. Login to begin!';
	}else if(isset($msg)){
		echo $msg;
	}else{
		echo 'Uh oh! You have entered the wrong information! If this is a mistake, please contact us immediately!';
	}
?>