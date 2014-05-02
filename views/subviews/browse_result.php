
<?php if( $app->has_extension('sidebar') ) { ?>
<div class="sidebar_area">
    
    <?php
        $sidebar = $app->extension('sidebar');
        $sidebar->render_recent_reviews( );
        $sidebar->render_recent_comments( );
    ?>
    
</div>

<div class="main_gutter">
<?php } else { ?>
<div class="full-content-provider">
<?php
}
	//Loop through each row of the result
	if( isset($listing_results) && $listing_results != null && !isset($result)){
		
		// browse each listing
		foreach( $listing_results as $row ){
?>
    <div class="item_result grid">
    
  

        <!-- Link to the item page -->
        <a href="<?php echo $app->form_path('browse/item/'.$row['id']); ?>"
            class="item_link">


            <div class="image_holder">
                <?php echo '<img src="'.$app->inner_path($row['image_paths']).'" width="227" height="150" />'; ?>
            </div>

            <!-- What do you call this product? -->
            <h3>
                <?php echo $row['title']; ?>


            </h3>

        <!-- List price details -->
            <div class="item_content">
        <?php if( isset($userid) && $userid == $row['userid'] ){  ?>
            <!-- Display edit post button -->
            <a href="<?php echo $app->form_path('listings/editpost/'.$row['id']); ?>"
                style="float: right; text-align: right;
                    margin-left: 6px;
                    position:absolute;
                    padding-right: 5px;">
                Edit post
            </a>
	    	<!-- Display delete post button -->
            <a href="<?php echo $app->form_path('listings/deletePost/'.$row['id']); ?>"
                style="float: left; text-align: right; 
                    position:absolute;
                    margin-left: 140px;
                    padding-right: 5px;">
                Delete post
	    </a>
        <?php } ?>
        <br /></br>
                <strong>Price:</strong> $<?php echo $row['asking_price']; ?> <br />
                <strong>Other Acceptable Offer:</strong> <?php echo $row['other_offer']; ?> <br />
            </div>

          <strong> Item Description: </strong> <?php echo $row['description']; ?> <br />

            <!-- Keywords and stuff -->
            <strong>Keywords for item search:</strong>
            <small>
            <?php

                // split the keywords into an array of words
                $keywords = explode( ' ', $row['keywords'] );
                // output each keyword
                $kword = '';
                foreach( $keywords as $word ){
                    $kword .= $word . ', ';
                }
                $kword = rtrim($kword, ", "); // cut trailing commas.
                echo $kword; 
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
        </small> </br></br>        
    </div>
<?php }  // foreach ?>
    <div class="pagination listings-pages">
    Page: 
<?php
    // check for action extra
    if( !isset($action_extra) ){
        $action_extra = '';
    }
    
    // pagination
    for($i=1; $i<=$paginate['page_count']; $i++ ){
    
        // just echo page number
        if( $i == $paginate['this_page'] ){
            echo $i . ' ';
            continue;
        }
        
        // else echo link
        echo <<< PAGE
        <a href="$page_action/$i$action_extra">$i</a> 
PAGE;
    } 
?>
    </div>
<?php    
} else {
    echo "No listings match what you were looking for.";
}

?>

</div>
