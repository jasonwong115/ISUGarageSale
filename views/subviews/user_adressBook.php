<?php
echo "Here you can modify your preffered trading locations.";
?>
</br>
<?php
echo "Select all that apply.";
?>
<form action="editLocations" method="post">
 
Which buildings do you want access to?<br />
<input type="checkbox" name="formLocation[]" value="UDCC" />UDCC Area<br />
<input type="checkbox" name="formLocation[]" value="Library" />Library<br />
<input type="checkbox" name="formLocation[]" value="Maple Willow Larch Area" />Maple Willow Larch Area<br />
<input type="checkbox" name="formLocation[]" value="Towers" />Towers<br />
<input type="checkbox" name="formLocation[]" value="MU" />Memorial Union
 
<input type="submit" name="formSubmit" value="Submit" />
 
</form>
<!--End of form for button to redirect-->

