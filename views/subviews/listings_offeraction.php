
<p>

<!-- Amazon Results -->
<?php $app->script('amazon_results'); 
    
    // set up
    $offer = $offer_result[0];
?>
    
    <?php echo $action_message; ?>
    
    <!-- Offer user info -->
    <br />
    <strong>Offer by: </strong>
    <a href="<?php echo $app->
        form_path('user/profile/'.$offer['userid']); ?>">
    <?php echo $app->user->name_from_id((int)$offer['userid']); ?>
    </a>
    on
    <?php echo $offer['date_created']; ?>
    <br />
    
    <!-- Offer price info -->
    <strong>Offer price: </strong>
    <?php echo $offer['offer_price']; ?>
    <br />
        
        
    <strong>Other offer: </strong>
    <?php echo $offer['offer_other']; ?>
    <br />
        
    <!-- Comment -->
    <strong>Comment: </strong>
    <?php echo $offer['comment']; ?>
    <br />

    <!-- Confirmation link -->
    <a href="<?php echo $self_link . '?confirm=yes&oid=' . $offerid . '&lid=' . $listingid; ?>"
        >Confirm</a>
    |
    
    <!-- Cancel -->
    <a href="<?php echo $cancel_link; ?>"
        >Cancel</a>
</p>
