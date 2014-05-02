
</style>
</head>


<div id="stylized" class="myform">
<IMG src="<?php echo $app->inner_path('images/newUsername.png'); ?>"> 
<br><br>

<form id="form1" id="form1" action="changeUserAction" method="POST">
	 <label>Enter your netID
    </label>
    <input type="text" name="netID">
    
    <label>Old Username
    <span class="small">Forgot your 
            <a href="<?php echo $app->form_path('user/forgottenUsername'); ?>" target="_blank" class = "orange-links">Username</a>?
    </label>
	 <input type="text" name = "oldValue">


    <label>New Username
    </label>
	 <input type="text" name = "newValue">



    <label>Retype Username
    </label>
	 <input type="text" name = "newValueR">
	
	 
	 <button type="submit" value="Submit" style="margin-top:15px;">Submit</button>
<div class="spacer"></div>
<br />
<br />

</form>

</div> <!-- end of form class -->

</body>
</html>
<script>
	var registrationValidator  = new Validator("form1");
	registrationValidator.addValidation("id","req","Please provide your id!");
	registrationValidator.addValidation("oldValue","req","Please provide the old value!");
	registrationValidator.addValidation("newValue","req","Please provide the new value!");
	registrationValidator.addValidation("newValueR","req","Please repeat the new value!");
</script><!--End of validator script used to ensure all required fields are provided -->
