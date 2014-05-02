<h2> Testing stuff </h2>


<strong>Display name</strong>: 
    <?php echo $user_args->get_displayname(); ?>
<br />

<strong>Description</strong>: 
    <?php echo $user_args->get_description(); ?>
<br />
    
<strong>Given name</strong>: 
    <?php echo $user_args->get_givenname(); ?>
<br />
    
<strong>Middle Name</strong>: 
    <?php echo $user_args->get_middlename(); ?>
<br />
    
<strong>Last Name</strong>: 
    <?php echo $user_args->get_lastname(); ?>
<br />
    
<strong>Title</strong>: 
    <?php echo $user_args->get_title(); ?>
<br />
    
<strong>College</strong>: 
    <?php echo $user_args->get_college(); ?>
<br />
    
<strong>Status</strong>: 
    <?php echo $user_args->get_status(); ?>
<br />
    
<strong>User Class</strong>: 
    <?php echo $user_args->get_userclass(); ?>
<br />
    
<strong>Email</strong>: 
    <?php echo $user_args->get_email(); ?>
<br />
    
    
<strong>General get example: getting: 'homepostaladdress'</strong>: 
    <?php echo $user_args->get('homepostaladdress'); ?>
<br />
    
<br />
<br />
<?php
//var_dump($user_args);
?>
