<?php
/** models/reviews.php
 *  The reviews model is responsible for grabbing all data associated
 *  with reviews of an associated user
 */
class reviews_model extends GarageSale\Model {
	
	
    /** Gets all active reviews from the database
     *  @param int $order ascending or descending order specifier
     *  @return array sorted and active results from the reviews table
     */
    function get_all( $order = GarageSale\MySQLStatement::DESC )
    {
        // order by the order asc
        $this->stmt->order( 'date_created', $order );
        
        // look at active entries
        $this->stmt->where( 'status', 'i', 
            GarageSale\BaseDatabase::STATUS_ACTIVE );
        
        return parent::get();
    }
	
	/** Get count of all active reviews
     *  @param int $userid id of user to count reviews for
	 *  @return count of reviews
     */
	function get_count($userid)
    {
        $this->stmt = $this->db->count('reviews');
    
        // where listingid matches
        $this->stmt->where('userid','i',$userid);
		$this->stmt->where('status','i',GarageSale\BaseDatabase::STATUS_ACTIVE);
        
        return parent::get();
    }
	
	/** Get all reviews for given user
     *  @param int $userid id of user to get reviews for
	 *  @param int $how_many number of reviews per page
	 *  @param int $offset is the page currently on
     */
	function get_reviews($user_id, $how_many, $offset){
		//****SQL****
        // use fancy system to select
        $this->stmt = $this->db->select('reviews');
        // set where values
        $this->stmt->where('userid','i',$user_id,'AND','reviews');
		$this->stmt->where('status','i',GarageSale\BaseDatabase::STATUS_ACTIVE,'AND','reviews');
        // set the limit
        $this->stmt->limit($how_many, $offset);
		$this->stmt->inner_join(
            array(
                'table' => 'listings',
                'other' => 'id',
                'this'  => 'listingid'
            )
        );
		$this->stmt->alias( 'reviews', 'description', 'reviewdescription' );
		$this->stmt->alias( 'listings', 'status', 'listingstatus' );
		$this->stmt->alias( 'reviews', 'id', 'reviewid' );
		$this->stmt->alias( 'listings', 'id', 'listingid' );
		return parent::get();
	}
	
	/** Get average of all reviews
     *  @param int $userid id of user to get average for
	 *  @return int average of all reviews
     */  
	function get_avg_reviews($user_id){
		$this->stmt = $this->db->select('reviews');
        $this->stmt->average('rating','rating_average')->
           where('userid','i',$user_id)->where('status','i',
               GarageSale\BaseDatabase::STATUS_ACTIVE)->
           limit(1);
		   return parent::get();
	}
	
	/** Insert a review for the given user
     *  @param string $message review message
	 *  @param int $reviewer id number of reviewer
	 *  @param int $reviewee id of seller
	 *  @param int $rating number of rating (1-5)
	 *  @param int $listingid is the id of the listing
     */
	function insert_review($message,$reviewer,$reviewee,$rating,$listingid){
		 // new insert statement
			$this->stmt = $this->db->insert('reviews');
		
			// set insert values
			$this->stmt->values( array(
			
				// description of review
				array(
					'name' => 'description',
					'value' => $message,
					'type' => 's',
					'table' => null
				),
				 
				// the reviewer id
				array(
					'name' => 'reviewerid',
					'value' => $reviewer,
					'type' => 'i',
					'table' => null
				),
				
				// the subject id
				array(
					'name' => 'userid',
					'value' => $reviewee,
					'type' => 'i',
					'table' => null
				),
				
				// and finally the rating
				array(
					'name' => 'rating',
					'value' => $rating,
					'type' => 'i',
					'table' => null
				),
				array(
					'name' => 'listingid',
					'value' => $listingid,
					'type' => 'i',
					'table' => null
				)
			
			) );
			return parent::set();
	}
	
	/** Updates review_submitted status to resolved
	 *  @param int $userid of user who submitted offer
	 *  @param int $listingid listing id of transaction
     */
	function update_offer($userid,$listingid){
		$this->stmt = $this->db->update('offers');
            
		// set up where
		$this->stmt->where( 'userid', 'i', $userid,'AND','offers');
		$this->stmt->where('listingid','i',$listingid,'AND','offers');
		// choose values
		$this->stmt->values( array(
				// set best offer flag to resolved
				array(
					'name'  => 'review_submitted',
					'value' => GarageSale\BaseDatabase::STATUS_RESOLVED,
					'type'  => 'i'
				)
			)
		);
		return parent::set();
	}
	
	/** Gets the offers that still need to be reviewed
	 *  @param int $userid id of user who needs to submit the review
	 *  @param int $how_many number of offers to retrieve
	 *  @return information for the offer
     */
	function get_offers($userid,$how_many){
		$this->stmt = $this->db->select('offers');
		// set the where value of the statement
		$this->stmt->where('userid','i',$userid,'AND','offers');
		$this->stmt ->where('review_submitted','i',GarageSale\BaseDatabase::STATUS_ACTIVE,'AND','offers');
		$this->stmt ->where('accepted','i',GarageSale\BaseDatabase::STATUS_ACCEPTED,'AND','offers');
		
		// set the statements limit
		$this->stmt->inner_join(
			array(
				'table' => 'listings',
				'other' => 'id',
				'this'  => 'listingid'
			)
		); //end of stmt join
		
		$this->stmt->alias('offers', 'status', 'offersstatus');
		$this->stmt->alias('listings','userid','sellerid');
		$this->stmt->limit($how_many,0);
		
		return parent::get();
	}
	
} 

?>
