<?php
echo "Here you can modify your password, e-mail adress, and mobile phone number";
?>


</br>
</br>
<form action="<?php echo $app->form_path('user/changePassword');?>">
<input type="submit" value="Change Password  &raquo;">
</form><!--End of form for button to redirect-->
<?php
echo "I want to change my password";
?>

</br>
</br>
<form action="<?php echo $app->form_path('user/changeEmail');?>">
<input type="submit" value="Change Email Adress  &raquo;">
</form><!--End of form for button to redirect-->
<?php
echo "I want to change my e-mail adress";
?>


</br>
</br>
<form action="<?php echo $app->form_path('user/changePhone');?>">
<input type="submit" value="Change Phone  &raquo;">
</form><!--End of form for button to redirect-->
<?php
echo "I want to change my phone number";
?>
<br />
<br />

</td>
