
<!-- Item Information -->
<div class="sidebar_area">
    <?php // include the amazon script
    $app->script('amazon_results'); ?>
</div>

<div class="main_gutter">
<?php
	//Loop through each row of the result
	if( $listing_result != null ){
	
	// listing is a row
	$row = $listing_result[0];
?>
	
   <?php
	echo '<img src="'.$app->inner_path($app->user->getItemImage($row['id'])).'" width="235" height="170" />';?>
</br></br>
	
    <!-- Creation date -->
    <div class ="leftmost"><small> Posted on: <?php echo $row['date_created']; ?> 
    by <a href="<?php echo $app->form_path('user/profile/'.$row['userid']); ?>"> 
    <?php echo $app->user->name_from_id((int)$row['userid']) . "&nbsp"; ?>
	
    </a>
    </small></div>
	<div class ="leftmost"><a href ="<?php echo $app->form_path('user/review/'.$row['userid']);?>"><span class="stars"><?php echo $rating; ?></span></a></div>
	<a href ="<?php echo $app->form_path('user/review/'.$row['userid']);?>"><div class ="leftmost"><?php echo"(" . $rating_count . ")";?></div></a>
	<br />
	<br />
    
    <?php
        // if item has been sold
        if( $row['status'] == GarageSale\BaseDatabase::STATUS_ACCEPTED ){ 
    ?>
    <p class="item_sold">
        <strong>This item has been sold!</strong>
    </p>
    <?php } ?>
    
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
    
    
    <!-- Offers -->
    <hr />
    
    <h3>Offers</h3>
    
    
    <!-- List the comments pagination. -->
    <p class="item_ofers_pagination">
    Listing offers 
    <?php 
        echo $offer_count['begin'] . ' - ' . $offer_count['end'] .
            ' of ' . $offer_count['total'];
            
        /* Here is where things get kind of cool. We're going to 
         * generate our pagination here
         */
        
        // start by checking if we have more results than are displayed
        if( $offer_count['total'] > $offer_count['per'] ){
        
            // echo a break
            echo '<br />';
            
            // calculate number of pages
            $num_pages = ceil( 
                $offer_count['total'] / $offer_count['per'] 
            );
            
            // for checking if this is the particular page
            $this_page = (
                ($offer_count['begin']-1) / $offer_count['per']
            ) + 1;
            
            // display that many links
            for( $i = 1; $i <= $num_pages; $i++ ){
            
                // check if this page is the current page
                if( $this_page == $i ){
                
                    // just print page number and move on
                    echo " $i ";
                    continue;
                }
                
                // otherwise echo out link
                echo <<< LINK
                <a href="$self_link?offerpage=$i">$i</a> 
LINK;
            }
        }
        
    ?>
    </p>
    
    
    <?php 
    // check if user is logged in and if the listing is still open
    if( isset($userid) &&  $userid != $row['userid'] &&
        $row['status'] == GarageSale\BaseDatabase::STATUS_ACTIVE
    ){ 
    ?>
    <!-- Make an offer link -->
    <p>
        <a href="<?php echo $app->form_path('listings/newoffer/'
            .$row['id']); ?>">
            Make an offer &raquo;
        </a>
    </p>
    <?php } ?>
    
    
<?php if( count( $offers_result) > 0 ){ 

            // go over each offer in turn
            foreach( $offers_result as $offer ){
            
                // do some lookups to detirmine styles
                $offer_class = '';
                
                // check for best offer or accepted
                if( $offer['accepted'] == GarageSale\BaseDatabase::STATUS_ACCEPTED
                ) {
                    
                    // style is accepted
                    $offer_class = 'class="offer_accepted"';
                } elseif( (int)$offer['best_offer'] === GarageSale\BaseDatabase::STATUS_BEST ) {
                    
                    // style is best
                    $offer_class = 'class="offer_best"';
                }
        ?>
        
        <!-- List offers -->
        <div <?php echo $offer_class; ?> >
            Offer by
            <small>
                 <a href="<?php echo $app->form_path('user/profile/'.$offer['userid'] ); ?>"> 
                    
                    <?php echo $app->user->
                        name_from_id( (int)$offer['userid'] ); ?>
                </a>
                on
                <?php echo $offer['date_created']; ?>
            </small>
            <br />
            
            <!-- Offer Status infromation -->
            <div>
                <strong>
                <?php 
                // test if this listing has been accepted
                if( $offer['accepted'] == GarageSale\BaseDatabase::STATUS_ACCEPTED
                ){ ?>
                
                    This offer has been accepted by the seller.
                
                <?php } elseif( (int) $offer['best_offer'] > 0 ) { ?>
                    This offer has been marked as a best offer!
                <?php } ?>
                
                </strong>
            </div>
            
            <!-- Price -->
            <strong> Offer price: </strong> 
            $<?php echo $offer['offer_price']; ?>
            <br />
            
            <!-- Other -->
            <strong> Other offer: </strong> 
            <?php echo $offer['offer_other']; ?>
            <br />
            
            <!-- Comment -->
            <p>
            <?php echo $offer['comment']; ?>
            </p>
            
            <?php
                // let the logged in user accept reject or choose best
                if( isset($userid) && $userid == $row['userid'] &&
                    // and its still open
                    $row['status'] == GarageSale\BaseDatabase::
                        STATUS_ACTIVE
                ) { ?>
            
            <!-- User choice section -->
            <p class="user_offer_choices">
                <a href="<?php echo 
					$app->form_path('listings/bestoffer/' . '?lid=' .
					$offer['listingid']) ."&oid=" . $offer['id'];?>"
					class="best_offer">Mark best offer</a>
				|
				<!-- Accept offer -->
				<a href="<?php echo 
					$app->form_path('listings/acceptoffer/'  . '?lid=' .
					$offer['listingid']) ."&oid=" . $offer['id'];?>"
					class="accept_offer">Accept this offer</a>
				|
				<!-- Reject offer -->
				<a href="<?php echo 
					$app->form_path('listings/declineoffer/'. '?lid=' .
					$offer['listingid']) ."&oid=" . $offer['id']; ?>"
					class="reject_offer">Reject this offer</a>
            </p>
            <?php } ?>
            
        </div>
		<br />
<?php
            } // for each
         } else { 
?>
        
        <!-- No offers -->
        <p>
            No offers have been made on this post.<br />
            
            <?php if(isset( $userid ) && $userid != $row['userid'] ){ ?>
            <!-- Make an offer link -->
            <a href="<?php echo $app->form_path('listings/newoffer/'.
                $row['id']); ?>">
                
                Be the first &raquo;
            </a>
            <?php } ?>
        </p>
        
    <?php } ?>
    
    
    
    
    <!-- Comments stuff begins ! -->
    <hr />
    <h3>Comments</h3>
    
    <!-- List the comments pagination. -->
    <p class="item_comment_pagination">
    Listing comments 
    <?php 
        echo $comment_count['begin'] . ' - ' . $comment_count['end'] .
            ' of ' . $comment_count['total'];
            
              
        /* Here is where things get kind of cool. We're going to 
         * generate our pagination here
         */
        
        // start by checking if we have more results than are displayed
        if( $comment_count['total'] > $comment_count['per'] ){
        
            // echo a break
            echo '<br />';
            
            // calculate number of pages
            $num_pages = ceil( 
                $comment_count['total'] / $comment_count['per'] 
            );
            
            // for checking if this is the particular page
            $this_page = (
                ($comment_count['begin']-1) / $comment_count['per']
            ) + 1;
            
            // display that many links
            for( $i = 1; $i <= $num_pages; $i++ ){
            
                // check if this page is the current page
                if( $this_page == $i ){
                
                    // just print page number and move on
                    echo " $i ";
                    continue;
                }
                
                // otherwise echo out link
                echo <<< LINK
                <a href="$self_link?commentpage=$i">$i</a> 
LINK;
            }
        }
        
    ?>
    </p>
    
<?php
    // display comments here
    if( $listing_comments == null ){
        echo "There are no comments yet for this post.<br />";
        echo "Be the first: <br />";
        
    // yay we have comments
    } else {
        
        // loop over comments
        foreach( $listing_comments as $comment ){
?>
    <!-- Comment block -->
    <div class="listing_commment">
        
        
        <!-- Display user / post info -->
<?php echo '<img src="'.$app->inner_path($app->user->getProfileImage((int)$comment['userid'])).'" width="45" height="45" />';?>
        <!-- Display title -->
        <strong><?php echo $comment['title']; ?></strong> <br />
        <small>On <?php echo $comment['date_created']; ?>

            by <a href="<?php echo $app->form_path('user/profile/'.(int)$comment['userid']); ?>"> 
			
                <?php echo $app->user->
                    name_from_id( (int)$comment['userid'] ); ?> </br></br>
            </a>
        </small>
        
        <!-- display comment -->
        <p>
            <?php echo $comment['comment']; ?>
        </p>
        </br></br>
    </div>
<?php
        }
?>
    
      
<?php
    }
} else {
	echo '<br />';
	echo '<br />';
    echo "No more listings.";
}
?>

</div>
