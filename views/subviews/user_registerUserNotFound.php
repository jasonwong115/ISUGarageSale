<?php echo "<font color='red'>Either this userid does not exist or this email is not associated with the univeristy. Please try again and remember to use your actual netid and university email." ?>
<div id="stylized" class="myform">
<IMG src="<?php echo $app->inner_path('images/titleBasic.png'); ?>"> 

<form id="form1" action="input" method="POST">
    
    <label>netID
    <span class="small">Enter your ISU netID</span>
    </label>
    <input type="text" name="netID">
    
    <label>Password
        <span class="small">Enter your password</span>
    </label>
    <input type="password" name="password">
    
    <label>Retype Password
        <span class="small">Retype your password</span>
    </label>
		<input type="password" name="retypePassword">


<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<span class="normal">
   <b>By clicking the submit button, I state that I have read and agree to the Terms of Use and Privacy
   Notice. I am aware that if I violate any of these terms, I will be reported to the university's 
   dean of students.<b> <br><br>
</span>

<button type="submit" value="Send" style="margin-top:15px;">Submit</button>
<div class="spacer"></div>

</form>
</div> <!-- end of form class -->

<script>
	registrationValidator.addValidation("username","req","Please enter you netID!");
	registrationValidator.addValidation("password","req","Please enter your password!");
	registrationValidator.addValidation("retypePassword","req","Please reenter your password!");
</script><!--End of validator script used to ensure all required fields are provided -->
