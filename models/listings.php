<?php
/** models/listings.php
 *  The listings model is responsible for grabbing all data associated
 *  with listings available in the garage
 */
class listings_model extends GarageSale\Model {
    
    /** Gets all active listings from the
     *  @return sorted and active results from the listings table
     */
    function get_all()
    {
        // where the item is active
        $this->stmt->where('status', 'i', 
            GarageSale\BaseDatabase::STATUS_ACTIVE);
            
        // Order the results
        $this->stmt->order('date_created',
            GarageSale\MySQLStatement::DESC);
        
        return parent::get();
    }
    
    /** Gets active listings from the database based on the user
     *  provided search terms
     *  @param string $search_terms The user provided search params
     *  @return array sorted and active results from the listings table
     */
    function get_search( $search_terms )
    {
        
		/* ---------------------------------------------
		 * Some processing to make search terms more fun
		 */
		 
		// explode a new search array
		$keyword_searches = explode( ' ', $search_terms );
		
		//Find partial matches that might fit search terms
		$searching = '%'.$search_terms.'%';
        
        
		// Search through descriptions of every listing with 
		// similar/matching terms
		$this->stmt->
			where_like('description', 's', $searching,'AND',null,'(');
		$this->stmt->where_like('title', 'i', $searching,'OR');
		
		
		// add keyword searches
		foreach( $keyword_searches as $key => $search ){
			
		    // Use an OR LIKE for each of the keywords
		    $this->stmt->where_like( 'keywords', 
		        's', 
		        '%'.$search.'%',
		        'OR',
		        null,
		        ($key != count($keyword_searches)-1)? null : ')'
	        );
		}
        
        // get everything and return
        return $this->get_all();
    }
    
    /** Gets active litsings from the database based on the user
     *  provided category requirement
     *  @param array $category_list A list of categories to grab data on
     *         from the database
     *  @return array sorted and active results from the listings table
     */
    function get_categories( $category_list )
    {
        // clear wheres
        // $this->where_clear_all();
        
        // start a new queue
        $queue = array();
        
        // now loop and add all subcategories
        foreach( $category_list as $cat ){
            
            // add to queue
            $queue[] = $cat;
        }
        
        // check if already grouped
        $grouped = false;
        
        // hot fix for category with no children
        if( count($queue) == 1 && 
            count($queue[0]['child_item']) == 0 
        ){
            
            // get next
            $next = array_shift( $queue );
            
            // set up where
            $this->stmt->
                where( 'categoryid','i', (int)$next['id'],'OR' );
            
        }
        
        // now loop and add all where's
        while( count($queue) > 0 ){
            
            // get next
            $next = array_shift( $queue );
            
            // set up where
            $this->stmt->where(
                'categoryid','i', (int)$next['id'],'OR',
                null, // table
                
                // this is messy, if there is nothing else, close group
                (!$grouped) ? '(' :  
                    ((count($queue) == 0 && 
                    count($next['child_item']) == 0 ) 
                    ? ')' : null)
            );
            
            // now its grouped
            $grouped = true;
            
            // add all children to queue
            foreach( $next['child_item'] as $child ){
                
                // add child
                $queue[] = $child;
            }
            
        }
                
        return $this->get_all();
    }
    
    /** Gets a count of every active entry in the listings
     *  @return int number of active listing items in the database
     */
    function count_all()
    {
        // new count
        $this->stmt = $this->db->count('listings');
        
        // reset limits
        $this->limit(-1)->page(-1);
        
        // get the row
        $count = $this->get_all();
        
        // return count
        return (int)$count[0]['id'];
    }
    
    /** Gets a count of every active entry in the listings based on a
     *  search
     *  @return int number of active searched items in the database
     */
    function count_search($search_terms)
    {
        // new count
        $this->stmt = $this->db->count('listings');
        
        // reset limits
        $this->limit(-1)->page(-1);
        
        // get the row
        $count = $this->get_search($search_terms);
        
        // return count
        return (int)$count[0]['id'];
    }
    
    /** Gets a count of every active entry in the listings based on a
     *  list of category
     *  @param array $categories list of categories to count through
     *  @return int number of active items in the category
     */
    function count_categories($categories)
    {
        // new count
        $this->stmt = $this->db->count('listings');
        
        // reset limits
        $this->limit(-1)->page(-1);
        
        // get the row
        $count = $this->get_categories($categories);
        
        // return count
        return (int)$count[0]['id'];
    }
    
    /** Gets a count of every active entry in the listings based on a
     *  user
     *  @param int $user_id id of the user to count for
     *  @return int number of active items for the user
     */
    function count_user($user_id)
    {
        // new count
        $this->stmt = $this->db->count('listings');
        
        // reset limits
        $this->limit(-1)->page(-1);
        
        // get the row
        $count = $this->get_user($user_id);
        
        // return count
        return (int)$count[0]['id'];
    }
    
    /** Gets active listings that are associated with a given user
     *  @param int $userid The id number of the user to look up
     *  @return array sorted and active results from the listings table
     */
    function get_user( $userid )
    {
        // where the category id matches
        $this->stmt->where('userid', 'i', $userid);
        
        return $this->get_all();
    }
    
    
    /** Gets a single item from the listings
     *  @param int $postid The id number of the listing to look up
     *  @return array sorted and active results from the listings table
     */
    function get_item( $postid )
    {
        $this->stmt = $this->db->select('listings');
        
        // where the post id matches
        $this->stmt->where('id', 'i', $postid);
        
        return parent::get();
        //return $this->get_all();
    }
	
	/** Gets the most recent post the user added
     *  @param int $userid The id of user
     *  @return array sorted and active results from the listings table
     */
    function get_most_recent( $userid )
    {
		$this->stmt = $this->db->select('listings');
        // where the user id matches
        $this->stmt->where('userid', 'i', $userid);
		// Order the results
        $this->stmt->order('date_created',
            GarageSale\MySQLStatement::DESC);
        return parent::get();
    }
	
	/** Gets a single sold item from the listings
     *  @param int $postid The id number of the listing to look up
     *  @return array sorted and active results from the listings table
     */
    function get_sold_item( $postid )
    {
        // where the post id matches
        $this->stmt->where('id', 'i', $postid);
		
		// where the item is active
        $this->stmt->where('status', 'i', 
            GarageSale\BaseDatabase::STATUS_ACCEPTED);
            
        // Order the results
        $this->stmt->order('date_created',
            GarageSale\MySQLStatement::DESC);
        
        return parent::get();
    }
    
    /** Updates an entry in the listings table to the values specified
     *  in the input array
     *  @param int $postid the listing id to update
     *  @param array $values The values to update in the database
     *  @return bool true on success false on failure
     */
    function set_item( $postid, $values )
    {
        // start a new internal statement
        $this->stmt = $this->db->update('listings');
        
        // set where 
        $this->stmt->where('id','i',$postid);
                
        // get price request, round to hundredth
        $values['asking_price'] = 
            (float)round((float)$values['asking_price'], 2);
        
        // get categoryid
        $values['categoryid'] = (int)$values['categoryid'];
        
        // this array holds the values of everything to update
        $update_values = $this->make_values($values);
        
        $this->stmt->values( $update_values );
        
        return parent::set();
    }
    
    /** Creates a new listings post to the garage
     *  @param array $values The values to update in the database
     *  @return bool true on success false on failure
     */
    function new_item( $values, $userid )
    {
        // start a new internal statement
        $this->stmt = $this->db->insert('listings');
        
        // get price request, round to hundredth
        $values['asking_price'] = 
            (float)round((float)$values['asking_price'], 2);
        
        // get categoryid
        $values['categoryid'] = (int)$values['categoryid'];
        
        // this array holds the values of everything to update
        $update_values = $this->make_values($values);
        
        // the status of the post
        $update_values[] = array(
            'name' => 'status',
            'value'=> GarageSale\BaseDatabase::STATUS_ACTIVE,
            'type' => 'i'
        );
        // associate user with this post
        $update_values[] = array(
            'name' => 'userid',
            'value'=> $userid,
            'type' => 'i'
        );
        
        
        $this->stmt->values( $update_values );
        
        return parent::set();
    }
	/** Set status of listing to accepted
     *  @param int $listingid the listing id to be changed
     */
    function set_accepted( $listingid){
		$this->stmt = $this->db->update('listings');
                
                // where
                $this->stmt->where('id','i',$listingid);
                
                // values
               $this->stmt->values( array(
                   
                   // userlevel
                   array(
                       'name' => 'status',
                       'type' => 'i',
                       'value'=> GarageSale\BaseDatabase::STATUS_ACCEPTED
                   )
                ));
		 return parent::set();
    }
} 

?>
