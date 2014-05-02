<?php
echo "Here you can modify certain aspects of the site";
?>

</br>
</br>
<form id="form1" action="siteSettingsAction" method="POST">

   <label>Desired site theme 
    </label>

	<select name="theme" size="1">
	<option value="normal">Normal</option>
	<option value="party">Party</option>
	<option value="loca">Living la vida loca</option>
	<option value="loca">Eclipse</option>
	</select>

</br>
</br>
   <label>Language
    </label>

	<select name="language" size="1">
	<option value="english">English</option>
	<option value="portuguese">Português</option>
	<option value="spanish">Español</option>
	<option value="french">Français</option>
	<option value="chinese simplified">中文（简）</option>
	<option value="chinese traditional">中文（傳統）</option>
	</select>
</br>
</br>
<input type="submit" value="Submit &raquo;">
</form>
