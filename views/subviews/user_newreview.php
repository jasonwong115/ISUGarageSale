<?php
	if(isset($reviews_results) && count($reviews_results) > 0){
		foreach($reviews_results as $result_row){ // Go through each listing that needs a review
			$count = 0;
			$seller_id = -1;
			$listing_id = -1;
			foreach($result_row as $key => $val){ // Print out 
								if($count%2==0){
						// output the profile datas
						if($key == 'title'){
							echo "<strong>" . strtoupper($val) . "</strong><br/>";
						}else if($key == 'offer_price'){
							echo "<strong>Price paid: </strong>" . $val . "<br />";
						}else if($key == 'sellerid'){
							$seller_id = $val;
							echo '<strong>Seller: </strong>' .  $app->user->name_from_id((int)$seller_id);
						}
					}
					$count++;
					if($key == 'listingid'){
						$listing_id = $val;
					}
			}
?>
			<form method = "POST" name = "review" action = "<?php echo $app->form_path('user/newreview'); ?>">
				<input type = "hidden" name ="reviewee-id" value = "<?php echo $seller_id; ?>">
				<input type = "hidden" name ="listing-id" value = "<?php echo $listing_id; ?>">
				<label>Detailed Review (optional): <input type='text' name = 'reviewmessage' placeholder='Detailed review message' size = '80'></label>
				<label> Rating: <select name="rating">
					<option></option>
					<option>1</option>
					<option>2</option>
					<option>3</option>
					<option>4</option>
					<option>5</option>
				</select></label><!--End of result size select-->
				<input type="Submit" name ="submit">
			</form>
			<script>
				var contactValidator  = new Validator("review");
				contactValidator.addValidation("rating","req","Please provide a rating!");
			</script>
<?php
		} // End of foreach
	}else if(isset($review_submitted)){
		echo $review_submitted;
	}else{
		echo 'No reviews to submit! You better start buying some stuff!';
	} //End of else
?>