
<!-- Amazon Results box -->
<div class="amazon-result-box">
<?php

global $amazon_response;

// there is a response from amazon
if( isset( $amazon_response ) ){
            
    if( $amazon_response->Items->TotalResults === 0 ){
        echo '<strong>No results from Amazon correspond to your post</strong>';
    } else {
    
        echo '<strong> Top Related Results from Amazon </strong>';
        
        
        // loop over items
        $i = 0;
        foreach( $amazon_response->Items->Item as $result ){
?>

<!-- Another Amazon request match -->
<div class="amazon-item-match" style="clear: both; font-size:14px;">
    
    <!-- Make ti all a link -->
    <?php if( isset($result->ItemLinks) ){ ?>
    <a href="<?php echo $result->ItemLinks->ItemLink[0]->URL; ?>"
    	target="_blank">
    <?php } ?>
    
		<div style="clear:both;">
		    <strong><?php echo $result->ItemAttributes->Title; ?></strong>
		</div>
		
		<!-- Display the item image -->
		<?php if( isset( $result->SmallImage) ){ ?>
		    <img src="<?php echo $result->SmallImage->URL; ?>" 
		        style="float:left;" />
		<?php } ?>
		
		
		<!-- Product Price information -->
		<div>
		<?php echo $result->ItemAttributes->ProductGroup; ?>
	   
		<!-- New Price -->
		<br />
		<?php if( isset($result->OfferSummary) ){ 
		    
		    // Only if there are new items, list the price
		    if( (int)$result->OfferSummary->TotalNew > 0 ){
		?>
		
		Lowest New Price: <?php echo $result->OfferSummary->
		    LowestNewPrice->FormattedPrice; ?>
		<br />
		
		<?php 
		    }// total new
		    
		    // only if htere are used items do this
		    if( (int)$result->OfferSummary->TotalUsed > 0 ) {
		?>
		
		<!-- Used price -->
		Lowest Used Price: <?php echo $result->OfferSummary->
		    LowestUsedPrice->FormattedPrice; ?>
		<br />
		
		
		<?php
		    } // total used
		} // offer summary ?>
		<hr style="clear:both;" />
		<br />
		</div>
		
	<?php if( isset($result->ItemLinks) ){ ?>
    </a>
    <?php } ?>
	
</div>

<?php
        if( ++$i > 3 ) break;
        }
    }
}
?>
</div> <!-- END Amazon results -->
