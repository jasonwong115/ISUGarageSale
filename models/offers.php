<?php
/** models/offers.php
 *  The offers model is responsible for grabbing all data associated
 *  with offers on items in the garage
 */
class offers_model extends GarageSale\Model {
    
    
    /** Gets all active offers from the database
     *  @return array sorted and active results from the offers table
     */
    function get_all()
    {
        // order by the order asc
        $this->stmt->order( 'date_created', 
            GarageSale\MySQLStatement::ASC );
        
        // look at active entries
        $this->stmt->where( 'status', 'i', 
            GarageSale\BaseDatabase::STATUS_ACTIVE );
        
        return parent::get();
    }
    
    /** Gets a particular offer
     *  @param int $offerid ID of the offer to get
     *  @return array sorted and active results from the offers table
     */
    function get_item( $offerid )
    {
        $this->stmt = $this->db->select('offers');
        $this->stmt->where('id','i',$offerid);
        
        return $this->get_all();
    }
    
	/** Gets anyoffers on a particular item from the database
     *  @param int $postid ID of the listing to match offers for
     *  @return array sorted and active results from the offers table
     */
    function get_item_any( $postid,$offerid )
    {
    	$this->stmt = $this->db->select('offers');
        // where listingid matches
        $this->stmt->where('listingid','i',$postid);
		$this->stmt->where('id','i',$offerid); 
        
        return $this->get_all();
    }
	
	/** Gets anyoffers on a particular item from the database
     *  @param int $postid ID of the listing to match offers for
     *  @return array sorted and active results from the offers table
     */
    function get_item_any_offer( $listingid)
    {
    	$this->stmt = $this->db->select('offers');
        // where listingid matches
        $this->stmt->where('listingid','i',$listingid);
        
        return $this->get_all();
    }
	
    /** Gets a count on offers on a particular item from the database
     *  @param int $postid ID of the listing to match offers for
     *  @param int $status status to match in the database
     *  @return array A single row with the count in result[0]['id']
     */
    function get_count( $postid, $status )
    {
        $this->stmt = $this->db->count('offers');
    
        // where listingid matches
        $this->stmt->where('listingid','i',$postid);
        // where status matches
        $this->stmt->where('status','i',$status);
        
        return parent::get();
    }
	
	/**Get count of offers the user has submitted
	*/
	function get_offer_count( $userid)
    {
        $this->stmt = $this->db->count('offers');
    
        // where listingid matches
        $this->stmt->where('userid','i',$userid);
        
        return parent::get();
    }
	
	/**Get count of offers the user has received
	*/
	function get_received_count( $userid)
    {
        $this->stmt = $this->db->count('listings');
    
        $this->stmt->where('userid','i',$userid,'AND','listings');
        // set the statements limit
		$this->stmt->inner_join(
            array(
                'table' => 'offers',
                'other' => 'listingid',
                'this'  => 'id'
            )
        );
        
        return parent::get();
    }
    
    /** Gets an offer that has been accepted already by the user
     *  @param int $postid ID of the listing to match offers for
     *  @return array sorted and active results from the offers table
     */
    function get_accepted( $postid )
    {
    	$this->stmt = $this->db->select('offers');
        // by post id
        $this->stmt->where('listingid','i',$postid);
        
        // where accepted
        $this->stmt->where( 'status', 'i', 
            GarageSale\BaseDatabase::STATUS_ACCEPTED );
        
        return parent::get();
    }
    
    /** Gets active offers made by a particular user from the database
     *  @param int $userid ID of the user to match offers for
     *  @return array sorted and active results from the offers table
     */
    function get_user( $userid )
    {
        // get userid
        $this->stmt->where('userid','i',$userid);
        
        return $this->get_all();
    }
    
    
    
    /** Inserts a new offer item into the database by the specified
     *  user
     *  @param array $values The values to insert in the database
     *  @param int $listingid the listing for this offer post
     *  @param int $userid the user for this offer post
     *  @return bool true on success false on failure
     */
    function new_item( $values, $listingid, $userid )
    {
        $this->stmt = $this->db->insert('offers');
        
        // set userid
        $values['userid'] = (int) $userid;
        
        // set up listing id
        $values['listingid'] = (int) $listingid;
        
        // convert offer price
        $values['offer_price'] = round((float)$values['offer_price'],2);
        
        // genearte update values
        $update_values = $this->make_values( $values );
        
        // update
        $this->stmt->values($update_values);
        
        return parent::set();
    }
	
	/** Sets the status of an offer
     *  @param int $userid the user for this offer post
     *  @param int $status status to update in the database
     *  @return bool true on success false on failure
     */
    function set_item( $offerid, $status){
		$this->stmt = $this->db->update('offers');
                
        // where
        $this->stmt->where('id','i',$offerid);

        // values
        $this->stmt->values( array(
           
           // userlevel
           array(
               'name' => 'status',
               'type' => 'i',
               'value'=> $status
           )
        ));
        return parent::set();
    }
	
	/** Updates offer to be a best offer for a listing
     *  @param int $userid the user for this offer post
     *  @return bool true on success false on failure
     */
   function set_best( $listingid, $offerid){
		$this->stmt = $this->db->update('offers');
                
                // where
                $this->stmt->where('id','i',$offerid);
                
                // values
               $this->stmt->values( array(
                   
                   // userlevel
                   array(
                       'name' => 'best_offer',
                       'type' => 'i',
                       'value'=> GarageSale\BaseDatabase::STATUS_BEST
                   )
                ));
		 return parent::set();
    }
	
	/** Updates all offers to remove any best offer for a listing
     *  @param int $userid the user for this offer post
     *  @return bool true on success false on failure
     */
   function unset_best( $listingid){
		$this->stmt = $this->db->update('offers');
                
                // where
                $this->stmt->where('listingid','i',$listingid);
                
                // values
               $this->stmt->values( array(
                   
                   // userlevel
                   array(
                       'name' => 'best_offer',
                       'type' => 'i',
                       'value'=> GarageSale\BaseDatabase::STATUS_ACTIVE
                   )
                ));
		 return parent::set();
    }
	
	/** Updates particular offer to not be best offer
     *  @param int $userid the user for this offer post
     *  @return bool true on success false on failure
     */
   function unset_single_best($offerid){
		$this->stmt = $this->db->update('offers');
                
                // where
                $this->stmt->where('id','i',$offerid);
                
                // values
               $this->stmt->values( array(
                   
                   // userlevel
                   array(
                       'name' => 'best_offer',
                       'type' => 'i',
                       'value'=> GarageSale\BaseDatabase::STATUS_ACTIVE
                   )
                ));
		 return parent::set();
    }
	/**Gets the offers received by the user
	* @param int $userid user id to retrieve offers for
	* @param int $how_many number of offers to retrieve
	*/
	function offers_received($userid,$how_many,$offset){
		$this->stmt = $this->db->select('listings');
        // set the where value of the statement
        $this->stmt->where('userid','i',$userid,'AND','listings');
        // set the statements limit
		$this->stmt->inner_join(
            array(
                'table' => 'offers',
                'other' => 'listingid',
                'this'  => 'id'
            )
        ); //end of stmt join
		$this->stmt->alias( 'offers', 'status', 'offersstatus' );
		$this->stmt->alias( 'listings', 'status', 'listingstatus' );
		$this->stmt->alias( 'offers', 'date_created', 'offersdate' );
		$this->stmt->alias( 'listings', 'id', 'listingid' );
		$this->stmt->limit($how_many,$offset);
		// order by the order asc
        $this->stmt->order( 'offersdate', 
            GarageSale\MySQLStatement::DESC );
		return parent::get();
	}
	
	/**Gets the offers submitted by the user given
	* @param int $userid user id to retrieve offers for
	* @param int $how_many number of offers to retrieve
	*/
	function offers_submitted($userid,$how_many,$offset){
		// create a new statement
        $this->stmt = $this->db->select('offers');
        // set the where value of the statement
        $this->stmt->where('userid','i',$userid,'AND','offers');
        // set the statements limit
		$this->stmt->inner_join(
            array(
                'table' => 'listings',
                'other' => 'id',
                'this'  => 'listingid'
            )
        ); //End of stmt join
		$this->stmt->alias( 'offers', 'status', 'offersstatus' );
		$this->stmt->alias( 'offers', 'date_created', 'offersdate' );
		$this->stmt->alias( 'listings', 'status', 'listingstatus' );
		$this->stmt->limit($how_many,$offset);
		$this->stmt->order( 'offersdate', 
            GarageSale\MySQLStatement::DESC );
		return parent::get();
	}
	/** Accepts an offer
     *  @param int $offerid id of the offer to accept
     *  @return bool true on success false on failure
     */
    function set_accepted( $offerid){
		$this->stmt = $this->db->update('offers');
                
        // where
        $this->stmt->where('id','i',$offerid);

        // values
        $this->stmt->values( array(
           
           // userlevel
           array(
               'name' => 'accepted',
               'type' => 'i',
               'value'=> GarageSale\BaseDatabase::STATUS_ACCEPTED
           )
        ));
        return parent::set();
    }
} 

?>
