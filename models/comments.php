<?php
/** models/comments.php
 *  The comments model is responsible for grabbing all data associated
 *  with comments on items in the garage
 */
class comments_model extends GarageSale\Model {
    
    
    /** Gets all active offers from the database
     *  @param int $order ascending or descending order specifier
     *  @return array sorted and active results from the offers table
     */
    function get_all( $order = GarageSale\MySQLStatement::ASC )
    {
        // order by the order asc
        $this->stmt->order( 'date_created', $order );
        
        // look at active entries
        $this->stmt->where( 'status', 'i', 
            GarageSale\BaseDatabase::STATUS_ACTIVE );
        
        return parent::get();
    }
    
    /** Gets active comments on a particular item from the database
     *  @param int $postid ID of the listing to match comments for
     *  @return array sorted and active results from the comments table
     */
    function get_item( $postid )
    {
        // where listingid matches
        $this->stmt->where('listingid','i',$postid);
        
        return $this->get_all();
    }
    
    /** Gets a count on offers on a particular item from the database
     *  @param int $postid ID of the listing to match offers for
     *  @return array A single row with the count in result[0]['id']
     */
    function get_count( $postid )
    {
        $this->stmt = $this->db->count('comments');
    
        // where listingid matches
        $this->stmt->where('listingid','i',$postid);
        
        return parent::get();
    }
    
    
    /** Inserts a new comment item into the database by the specified
     *  user
     *  @param array $values The values to insert in the database
     *  @param int $listingid the listing for this comment post
     *  @param int $userid the user for this comment post
     *  @param string $default_title The default title to use if one is
     *         not provided in the input
     *  @return bool true on success false on failure
     */
    function new_item( $values, $listingid, $userid, 
        $default_title ='RE:' )
    {
        
        // check for title, if not just respond to post title
        if( !isset( $values['title']) || $values['title'] == null ){
            
            // choose title for them
            $values['title'] = $default_title;
        }
         
        // new statement
        $this->stmt = $this->db->insert('comments');
        
        // set up ids
        $values['listingid'] = (int)$listingid;
        $values['userid'] = (int)$userid;
        
        
        // generate update values
        $update_values = $this->make_values( $values );
        
        // update
        $this->stmt->values($update_values);
        
        return parent::set();
    }
} 

?>
