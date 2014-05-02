

</style>
</head>

<body>


<div id="stylized" class="myform">
<IMG src="<?php echo $app->inner_path('images/sellItem.png'); ?>"> 

<form id="form1" id="form1" action="uploadItem" method="POST" enctype="multipart/form-data">


    	<label>Name
        <span class="small">Add item name</span>
   	 </label>
	<input type="text" name="name">
	
	 <label>Asking Price
        <span class="small">Minimum price</span>
   	 </label>
	<input type="text" name="price">

	<label>Other offer
        <span class="small">What other offer would you accept?</span>
   	 </label>
	<input type="text" name="other">

   	 <label>Item type  
        <span class="small">Select one</span>
   	 </label>

	<select name="itemType" size="1">
	<option value="1">General</option>
	<option value="2">Books</option>
	<option value="3">Textbooks</option>
	<option value="6">Home & Decor</option>
	<option value="7">Furniture</option>
	<option value="8">Decoration</option>
	<option value="9">Art & Collectibles</option>
	<option value="10">Art Works</option>
	<option value="11">Art Supplies</option>
	<option value="12">Electronics</option>
	<option value="13">Sports</option>

	</select>
<br />
<br />
<br />
  
    	<label>Would you accept a trade?</label>

	<select name="trade" size="1">
	<option value="Yes">Yes</option>
	<option value="No">No</option>
</select>
<br />
<br />
<br />
    
	<label>Upload an Image</label>
 	<input type="file" name="file" id="file">
</BR>

<BR>
    	<label>Item Description
        <span class="small">Type a brief description </span>
    	</label>
	<textarea name="description" rows="6" cols="25"></textarea><br />
</BR>

    	<label>Keywords
        <span class="small">How to search for item</span>
   	 </label>
	<input type="text" name="keywords">
	
    <button type="submit" name = "submit" value="Submit" style="margin-top:15px;">Submit</button>

<div class="spacer"></div>

</form>

</div> <!-- end of form class -->

<script>
	var registrationValidator  = new Validator("form1");
	registrationValidator.addValidation("name","req","Please provide a name for the item!");
	registrationValidator.addValidation("price","req","Please provide a price for the item!");
	registrationValidator.addValidation("description","req","Please provide a description for the item!");
	registrationValidator.addValidation("keywords","req","Please provide keywords for the item search");
</script><!--End of validator script used to ensure all required fields are provided -->
</body>
</html>
