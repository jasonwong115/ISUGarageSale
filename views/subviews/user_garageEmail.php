<?php
echo "Do you wish to receive weekly emails from the garage related to high-selling items, discounts, and news?";
?>

</br>
</br>
<form action="<?php echo $app->form_path('');?>">
<Input type = 'checkbox' Name ='emailYes' value ="net"/>
<?php print "Yes"; ?>
<br />
<Input type = 'checkbox' Name ='emailNo' value ="net"/>
<?php print "No"; ?>
<br />
<br />
<input type="submit" value="Submit &raquo;">
</form><!--End of form for button to redirect-->

