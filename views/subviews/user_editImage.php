<?php
echo "Select an image to be your new user profile image";
?>
</br></br>
<?php
echo '<img src="'.$app->inner_path($app->user->getProfileImage($app->user->get_user_id())).'" width="235" height="170" />';
?>
</br>
</br>

</br>
</br>
<form id="formImageProfile" id="formImageProfile" action="addProfileImage" method="POST" enctype="multipart/form-data">
 	<label>Upload an Image</label>
 	<input type="file" name="file" id="file">
</br>
 <button type="submit" name = "submit" value="Submit" style="margin-top:15px;">Submit</button>
</form><!--End of form for button to redirect-->

