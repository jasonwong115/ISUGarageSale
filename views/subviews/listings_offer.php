<div class="sidebar_area">

<!-- Amazon Results -->
<?php

    // use amazon result script
    //include( 'views/scripts/amazon_results.php');
    $app->script('amazon_results');
?>
</div>

<div class="main_gutter">

<?php echo $offer_message; ?>


<form action="<?php echo $form_path; ?>" method="POST">

    <!-- Post title input -->
    Your offer:
    <br />
    
    <?php 
    
        // test for update attempts and bad inputs
        if( $offer_attempt && 
            (!isset($_POST['offer_price']) || 
            $_POST['offer_price'] == null  ||
            !is_numeric($_POST['offer_price']) ) )
        {
        
            // alert use to bad input
            echo 'A valid price offer is required (0 for none)<br />';
        }
        
    ?>
    
    $<input type="text" name="offer_price" placeholder="0.00" <?php 
            // test if previous input
            echo $offer_price_value;
        ?> />
    <br />
        
    <!-- Other offers the user is willing to accept -->
    Other offer:
    <br />
    
    <!-- And the text area -->    
    <textarea name="offer_other" 
        placeholder="Describe another offer."
    ><?php echo $offer_other_value; ?></textarea>
    <br />


    <!-- A description of what is being sold -->
    Comment:
    <br />
    
    <textarea name="comment" 
        placeholder="Comment on your offer."
    ><?php echo $comment_value; ?></textarea>
    <br />
    
        
    <br />
    
    <!-- And of course, submit it -->
    <input type="submit" value="<?php echo $button_text; ?> &raquo;" />
    
    
</form>
<hr />
<strong> For Product </strong>

<!-- Display listing result also -->
<?php
	// listing is a row
	$row = $listing_result[0];
?>
    <!-- What do you call this product? -->
    <h3><?php echo $row['title']; ?></h3>
    
    <!-- Creation date -->
    <small> Posted on: <?php echo $row['date_created']; ?></small>
    
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


</div>
