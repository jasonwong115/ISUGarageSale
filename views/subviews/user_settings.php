<IMG src="<?php echo $app->inner_path('images/ISULogo.png'); ?>"> 
</br>

<?php echo "Here you can modify some of your personal information"?>
</br></br>

<?php echo "---------------------------------------------------------"?>

<br />
<br />

<form action="<?php echo $app->form_path('user/accountSettings');?>">
<input type="submit" value="View Account Settings&raquo;">
</form><!--End of form for button to redirect-->
<?php
echo "Name, e-mail, password, and mobile phone.";
?>
<br />
<br />

<?php echo "---------------------------------------------------------"?>

</br></br>
<form action="<?php echo $app->form_path('user/forgottenPassword');?>">
<input type="submit" value="Forgot your password?&raquo;">
</form><!--End of form for button to redirect-->
<?php
echo "Forgot your password?";
?>
<br />
<br />

<?php echo "---------------------------------------------------------"?>

<br />
<br />

</td>
