<?php
    // loop through the reviews data
    if( isset($reviews_results) && $reviews_results != null ){
		$count = 0;
        foreach( $reviews_results as $review){
            echo "<a href =" . $app->form_path('browse/item/') . $review['listingid'] . ">" . $review['title'] . "</a>";
			echo '<br />';
			echo '<strong>Price:</strong> ' . $review['asking_price'];
			echo '<br />';
			echo '<strong>Other:</strong> ' . $review['other_offer'];
			echo '<br />';
			echo '<strong>Review Description:</strong> ' . $review['reviewdescription'];
			echo '<br />';
			echo '<span class="stars">' . $review['rating'] . '</span></a>';
			echo '<br />';
			echo '<br />';
        }// end of for each
?>
	<br />
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
	<br />
		<p>Reviews per page
        <form name = "drop-down" method = "POST" action = "<?php echo $app->form_path('user/review/'.$user_id); ?>">
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
        if( isset($user_id) && $user_id < 0 ){
            echo "Error: user not found";
        }else{
            echo "<br/>No records found!";
        }
    }
?>
