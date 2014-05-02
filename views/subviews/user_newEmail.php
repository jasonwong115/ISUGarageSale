
</style>
</head>


<div id="stylized" class="myform">
<IMG src="<?php echo $app->inner_path('images/newEmail.png'); ?>"> 
<br><br>

<form id="form1" id="form1" action="changeEmailAction" method="POST">
	 <label>Enter your netID
    </label>
    <input type="text" name="netID">
    
    <label>Old Email
    <span class="small">Please write your current email adress</a>
    </label>
	 <input type="text" name = "oldValue">


    <label>New E-mail
    </label>
	 <input type="text" name = "newValue">



    <label>Retype E-mail
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
