<?php
    // loop through the profile data
    if( $profile_results != null && $is_logged_in){
?>

    <form action="<?php echo $form_path; ?>" method="POST">
        <strong>Email:</strong> <?php echo $profile_results['email']; ?>
        <br />
		<strong>Description</strong>
		<br />
        <textarea name="description"><?php echo $profile_results['description']; ?></textarea>
        <br />
        <input type="submit" value="Submit" />
    </form>
<?php  
    } else { //The user is not properly logged in
		echo "Error: user not found! Please login to view your profile!";
	//Notice that else does not end until next php block
	//This is to ensure button form below only appears if not logged in
?>

	<form action="<?php echo $app->form_path('user/login');?>">
		<input type="submit" value="Login &raquo;">
	</form><!--End of form for button to redirect to edit profile-->
	<br />
<?php
	} //End of else statement
?>