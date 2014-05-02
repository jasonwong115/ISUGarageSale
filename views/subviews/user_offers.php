<!-- Set up sidebar for being on the left -->
<div class="sidebar_area left">
	<p>Submitted</p>
    <ul>
		<li><a href = "<?php echo $app->form_path('user/offers'); ?>">Offers/Bids</a></li>
	</ul>
	<br />
	<p>Received</p>
	<ul>
		<li><a href = "<?php echo $app->form_path('user/offersReceived'); ?>">Offers/Bids</a></li>
	</ul>
</div><!--End of left side-bar-->

<!-- Set up main gutter for being on the right side -->
<div class="main_gutter right">
    <!-- List offers user submitted -->
<?php
	// check if any offers exist
	if( isset($offer_results) && count( $offer_results) > 0){
		$row = $offer_results[0];
		foreach( $offer_results as $offer ){
			// do some lookups to determine styles
			$offer_class = '';
			// check for best offer or accepted
			if( $offer['accepted'] == GarageSale\BaseDatabase::STATUS_ACCEPTED
			) {
				$offer_class = 'class="offer_accepted"';
			} elseif( (int)$offer['best_offer'] == GarageSale\BaseDatabase::STATUS_BEST ) {
				$offer_class = 'class="offer_best"';
			}
?>
		<!-- List offers -->
		<?php 
			echo "<div $offer_class>";
			$upperTitle = strtoupper($offer['title']);
			
			//Listing is still active so provide link	
			if(!$offer['listingstatus']){ 
				echo"<a href= " . $app->form_path('browse/item/' . $offer['listingid']) . "><h4>" . $upperTitle . " </h4></a>";
			//The listing is no longer active
			}else{ 
				echo"<a href= " . $app->form_path('browse/sold/' . $offer['listingid']) . "><h4>" . $upperTitle . " </h4></a>";
			}//End of else
			
			//If we are on the offers the user submitted page don't show offer author
			if($offerpage=='Submitted'){
				echo "Offer submitted on:";
			}else{ //Provide User ID of user who submitted offer
				echo "Offer submitted by ".
				"<a href=" . $app->form_path('user/profile/'. $offer['userid'] ) . ">" .
				$app->user->name_from_id( (int)$offer['userid'] ) . "</a> on: ";
			} //End of else
		?>
			<small>
				<?php echo $offer['offersdate']; ?>
			</small>
			<br />
			
			<!-- Offer Status information -->
			<div>
				<strong>
				<?php 
				// test if this offer has been accepted
				if( $offer['accepted'] == GarageSale\BaseDatabase::STATUS_ACCEPTED){ ?>
				
					This offer has been accepted by the seller.
				
				<?php } elseif( (int) $offer['best_offer'] == GarageSale\BaseDatabase::STATUS_BEST) { ?>
				
					This offer has been marked as a best offer!
					
				<?php } elseif( (int) $offer['offersstatus'] === GarageSale\BaseDatabase::STATUS_DECLINED){?>
				
					This offer has been declined!
					
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
				//If on the offersReceived page, let user update offer status
				if($offerpage=='Received'){
					if($offer['accepted'] != GarageSale\BaseDatabase::STATUS_ACCEPTED && (int)$offer['listingstatus']===GarageSale\BaseDatabase::STATUS_ACTIVE
						&&(int)$offer['offersstatus']!=GarageSale\BaseDatabase::STATUS_DECLINED) { 
			?>
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
			<?php } //End of isset if
				//On the offers submitted page so show review options
				}else{
					if((int) $offer['accepted'] && !$offer['review_submitted']){
						echo "<a href =  " .  $app->form_path('user/newreview') . " >Submit review</a>";
					}else if((int) $offer['accepted'] && (int)$offer['review_submitted']){
						echo 'Review Already Submitted';
					}
				}
			?>

			</div>
			<?php
				} // end of for each
				// Offers per page select redirects to offers submitted page
				if($offerpage=='Submitted'){ 
			?>
					<form name = "drop-down" method = "POST" action = "<?php echo $app->form_path('user/offers'); ?>">
			<?php 
				}else{ //Redirect to right page 
			?>
					<form name = "drop-down" method = "POST" action = "<?php echo $app->form_path('user/offersReceived'); ?>">
			<?php	
				} //End of else to redirect to offersReceived 
			?>
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
				<p>Offers per page
					<!--User can select result size -->
					<select name="num-results" onchange='this.form.submit()'>
						<option></option>
						<option>2</option>
						<option>4</option>
						<option>6</option>
						<option>8</option>
						<option>10</option>
					</select><!--End of result size select-->
					<noscript><input type="submit" value="Submit"></noscript>
					</form><!--End of result size form-->
				</p><!--End of container holding results size-->
<?php
	} else {
		echo "No offers to be shown!";
	}
?>
</div><!--End of container of main-gutter-right-->