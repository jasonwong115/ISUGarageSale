<?php
	echo "<font color='red'>At least one of the inputs is incorrect. Please try again. </font>";;
?>
</br></br>

<div id="stylized" class="login_form">
<IMG src="<?php echo $app->inner_path('images/loginTitle.png'); ?>" </A>
<br><br>

<!-- Begin form information -->
<form id="form1" id="form1" action="doLogin" 
    method="POST">
    
    <!-- User name input label -->
	<label for="username_input">Username
        <span class="small">Forgot your 
            <a href="<?php echo $app->form_path('user/forgottenUsername'); ?>" target="_blank" class = "orange-links">Username</a>?
        </span> 
    </label>
    
    <!-- input for username -->
    <input type="text" id="username_input" name="username">
    
    <br><br>
    
    <!-- password input label -->
    <label>Password
        <span class="small">Forgot your 
            <a href="<?php echo $app->form_path('user/forgottenPassword'); ?>" target="_blank" class = "orange-links">Password</a>?
        </span> 
    </label>
    
    <!-- Input for password -->
	<input type="password" name="password">
	 
	 <!-- Submit it up -->
	 <button type="submit" value="Send" style="margin-top:15px;">Submit</button>
<div class="spacer"></div>
<br />
<br />

</form>

</div> <!-- end of form class -->
