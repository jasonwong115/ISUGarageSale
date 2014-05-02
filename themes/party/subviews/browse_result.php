<div class="sidebar_area">
    Sidebar content
</div>

<div class="main_gutter">
<?php
	
	//Loop through each row of the result
	if( $listing_results != null){
		
		// browse each listing
		foreach( $listing_results as $row ){
?>
    <div class="item_result list">
    
        
        <?php if( isset($userid) && $userid == $row['userid'] ){  ?>
            <!-- Display edit post button -->
            <a href="<?php echo $app->form_path('listings/editpost/'.$row['id']); ?>"
                style="float: right; display: block; text-align: right;
                    padding-right: 5px;">
                Edit post
            </a>
        <?php } ?>
        
        <!-- Link to the item page -->
        <a href="<?php echo $app->form_path('browse/item/'.$row['id']); ?>"
            class="item_link">
            
            
            <div class="image_holder">
            </div>
        
            <!-- What do you call this product? -->
            <h3>
                <?php echo $row['title']; ?>
            </h3>
        
        <!-- List price details -->
            <div class="item_content">
                <strong>Price:</strong> $<?php echo $row['asking_price']; ?> <br />
                <strong>Other:</strong> <?php echo $row['other_offer']; ?>
            </div>
            
            
            <!-- Keywords and stuff -->
            <small>
            <?php
            
                // split the keywords into an array of words 
                $keywords = explode( ' ', $row['keywords'] );
                
                // output each keyword
                foreach( $keywords as $word ){
                    echo $word . ', '; 
                }
            ?>
            </small>
            <br />
    
        </a> <!-- end item link -->
        
        <!-- Creation date -->
        <small> Posted on: <?php echo $row['date_created']; ?>
        
            <!-- Poster data -->
            by 
            <a href="<?php echo $app->form_path('user/profile/'.$row['userid']); ?>">
            <?php echo $app->user->name_from_id( (int)$row['userid']); ?>
            </a>
        </small>
        <br />
        
        
    </div>
<?php
        }    
    
} else {
    echo "No listings match what you were looking for.";
}
?>

</div>
