<div class="main_gutter">

<?php
	$delete = true;
	//Loop through each row of the result
	if( $listing_result != null ){

 
	// listing is a row
	$row = $listing_result[0];
?>

<?php echo '<img src="'.$app->inner_path($app->user->getItemImage($row['id'])).'" width="'.GarageSale\User::IMAGE_WIDTH.'"
                                                                                  height="'.GarageSale\User::IMAGE_HEIGHT.'" />'; ?>
</br></br>

    
    <!-- Creation date -->
    <small> Posted on: <?php echo $row['date_created']; ?> 
    by <a href="<?php echo $app->form_path('user/profile/'.$row['userid']); ?>"> 
    <?php echo $app->user->name_from_id((int)$row['userid']); ?>
    </a>
    </small>
    
    
    <!-- List price details -->
    <p>
        <strong>Price:</strong> $<?php echo $row['asking_price']; ?> <br />
        <strong>Other:</strong> <?php echo $row['other_offer']; ?>
    </p>
    
    
    <!-- Item description -->
    <strong>Description</strong>
    <p>
        <?php echo $row['description']; ?>
    </p> <br />
<?php } ?>

</br></br>
<strong> Do you really wish to delete this item (yes or no)?</strong></br>
<form id="Accept" action="<?php echo $form_path; ?>" method="POST">
<input type="text" name="delete">
<button type="submit" value="Send" style="margin-top:15px;">Submit</button>
</form>

</div>
