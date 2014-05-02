<?php
	if( $app->user->is_logged_in() && 
	    $app->user->get_user_id() == $user_id 
	){
	//Notice that if statement does not end until next php block
	//This is to ensure button form below only appears if logged in
	
?>

	<?php
	//$image_path = user-> getProfileImage;
		echo '<img src="'.$app->inner_path($app->user->getProfileImage($app->user->get_user_id())).'" 
	width="'.GarageSale\User::IMAGE_WIDTH.'"height="'.GarageSale\User::IMAGE_HEIGHT.'" />'; 
	?>
	<br />
	<br />
	
	<div class ="leftmost"><a href ="<?php echo $app->form_path('user/review/'.$user_id);?>"><span class="stars"><?php echo $rating; ?></span></a></div>
	<a href ="<?php echo $app->form_path('user/review/'.$user_id);?>"><div class ="leftmost"><?php echo"(" . $rating_count . ")";?></div></a>
	<br />
	<br />
	
	<form action="<?php echo $app->form_path('user/editImage');?>">
		<input type="submit" value="Change User Image &raquo;">
	</form><!--End of form for button to redirect to edit profile-->
	<br />
	<form action="<?php echo $app->form_path('user/editprofile');?>">
		<input type="submit" value="Edit Profile &raquo;">
	</form><!--End of form for button to redirect to edit profile-->
	<br />

<?php
	}//End of if statement
	
	if( $app->user->get_user_id() != $user_id ){
	echo '<img src="'.$app->inner_path($app->user->getProfileImage($user_id)).'" width="'.GarageSale\User::IMAGE_WIDTH.'"
                                                                         height="'.GarageSale\User::IMAGE_HEIGHT.'" />'; 
?>
																 
	<br />
	<br />
	
	<div class ="leftmost"><a href ="<?php echo $app->form_path('user/review/'.$user_id);?>"><span class="stars"><?php echo $rating; ?></span></a></div>
	<a href ="<?php echo $app->form_path('user/review/'.$user_id);?>"><div class ="leftmost"><?php echo"(" . $rating_count . ")";?></div></a>
	<br />
	<br />


<!-- Send a message to this user-->
<div>
    <a href="<?php echo $app->form_path('messages/write/'.$user_id); ?>"
    >Message this user</a>
</div>

<?php
    } // user is not this user
	if( isset($profile_results) && $profile_results != null 
	/* && $logged_in                                                                                
	 * I disabled login requirement for viewing profiles so that users
	 * can see the links to send messages to each other
	 * -- tanner 
	 */
	){
		$count = 0; 
		echo "<strong>Username: </strong>" . $profile_results['handle']. 
		    "<br />";
		foreach( $profile_results as $key => $val ){

			if($key == 'description' && $count%2 == 0){
				echo "<strong>Description: </strong>" . $val;
				echo "<br />";
			}else if($key == 'email' && $count%2 == 0){
				echo "<strong>Email: </strong>" . $val;
				echo "<br />";
			}
			$count = $count + 1;
			// end of profile datas
		}// end of for each

	//Get some more information about this user from LDAP
	$ldap = $app->extension('ldap');
        $user =  $ldap->get_user($profile_results['handle']); 
	if( $user != null ){
	    $userType = $user->get_userclass();
	    $userMajor = $user->get_major();
	    if ($userType == null){
		 echo "<strong>I am a </strong>"."hidden user type."; 
	         echo "</br>";
	    }
	    else if ($userMajor  == null) echo "\n\n<strong>My major is: </strong>". "hidden";
	    else{
                echo "<strong>I am a </strong>".$userType; 
     	        echo "<br>"; 
                echo "\n\n<strong>My major is: </strong>".$userMajor;
	    }
	}

	} else { //The user is not properly logged in
		echo "This user has not configured their profile yet.";
	//Notice that else does not end until next php block
	//This is to ensure button form below only appears if not logged in
?>

	<form action="<?php echo $app->form_path('user/login');?>">
		<input type="submit" value="Login &raquo;">
	</form><!--End of form for button to redirect to login-->
	<br />

<?php
	} //End of else statement
?>
