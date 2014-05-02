<?php
/** models/blocks.php
 *  The blocks model is responsible for grabbing all data associated
 *  with blocks between users
 */
class blocks_model extends GarageSale\Model {

    
    /** Checks if a user is blocked by another user
     *  @param int $userid The id of the blocking user
     *  @param int $blockedid The id of the user who may be blocked
     *  @return bool true if user is blocked, false if not
     */
	function is_blocked( $userid, $blockedid )
    {
        // check for blocks from the destination 
        $this->stmt->where( 'userid', 'i', $userid );
        
        // check for blocks to the current user
        $this->stmt->where( 'blockedid', 'i', $blockedid );
        
        // set limit to 1
        $this->stmt->limit( 1 );
        
        // execute statement
        $blocked_result = parent::get();
        
        return ( count($blocked_result) > 0 );
    }
} 

?>
