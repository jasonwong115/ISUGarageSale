<?php
/** models/messages.php
 *  The messages model is responsible for grabbing all data associated
 *  with messages on items in the garage
 */
class messages_model extends GarageSale\Model {
    
    
    /** Gets all active offers from the database
     *  @return array sorted and active results from the offers table
     */
    function get_all()
    {
        // order by the order asc
        $this->stmt->order( 'date_created', 
            GarageSale\MySQLStatement::DESC );
        
        // look at active entries
        $this->stmt->where_ne('status','i',
                GarageSale\BaseDatabase::STATUS_CLOSED)->
            where_ne('status','i',
                GarageSale\BaseDatabase::STATUS_SUSPENDED);
        
        return parent::get();
    }
    
    
    /** Gets the messages in a conversation between two users.
     *  @param int $userid The current logged in user
     *  @param int $otherid the user id of the user whom is the other
     *         end of this conversation.
     *  @return array the associative result set of this conversation
     */
    function get_conversation( $userid, $otherid )
    {
        $this->stmt = $this->db->select('messages');
        
        // set up this statement
        $this->stmt->where('toid','i',$otherid)->
            where('fromid','i',$userid);
        
        // set up other stmt to get messages from the other
        $other_stmt = $this->db->select('messages');
        $other_stmt->where('toid','i',$userid)->
            where('fromid','i',$otherid)->
            where_ne('status','i',
                GarageSale\BaseDatabase::STATUS_CLOSED)->
            where_ne('status','i',
                GarageSale\BaseDatabase::STATUS_SUSPENDED);
        
        // union it with this statement
        $this->stmt->union( $other_stmt );
        
        // get all
        return $this->get_all();
    }
    
    /** Gets unread messages for the provided user
     *  @param int $userid The id of the user to get unread messages for
     *  @return array The result of the query for unread messages  
     */
    function get_unread( $userid )
    {
        $this->stmt = $this->db->select('messages');
        // return result
        return $this->unread( $userid );
    }
    
    /** Gets a count of unread messages for provided user
     *  @param int $userid The id of the user to get unread messages for
     *  @return int number of unread items for this user
     */
    function count_unread( $userid )
    {
        $this->stmt = $this->db->count('messages');
        
        // get count
        $count = $this->unread( $userid );
        
        
        // get number
        return (int)($count[0]['id']);
    }
    
    /** Perform a query for unread results
     *  @param int $userid The id of the user to get unread messages for
     *  @return array The result of the query for unread messages  
     */
    function unread( $userid )
    {
        // set up wheres
        $this->stmt->where('toid','i',$userid )->
            where('status','i',GarageSale\BaseDatabase::STATUS_UNREAD );
        
        // set ordering
        $this->stmt->order('date_created', 
            GarageSale\MySQLStatement::DESC );
        
        // return result
        return parent::get();
    }
    
    /** Gets a distinct list of persons who
     */
    function get_conversation_list( $userid ){
        
        // new select
        $this->stmt = $this->db->select('messages');
        
        // set up grouping
        $this->stmt->group('fromid')->group('toid');
        
        // set up wheres
        $this->stmt->where('toid','i',$userid)->
            where('fromid','i',$userid,'OR');
        
        // where not deleted or anything
        $this->stmt->where_ne('status','i',
            GarageSale\BaseDatabase::STATUS_SUSPENDED);
        
        // where not deleted or anything
        $this->stmt->where_ne('status','i',
            GarageSale\BaseDatabase::STATUS_CLOSED);
        
        
        // order them by the order ascending
        $this->stmt->order('date_created', 
            GarageSale\MySQLStatement::DESC);
        
        
        return parent::get();
    }
    
    
    
    /** Gets a count on messages between particular users (i.e. for a 
     *  conversation) from the db
     *  @param int $userid The current logged in user
     *  @param int $otherid the user id of the user whom is the other
     *         end of this conversation.
     *  @return int Count of messages in a given conversation
     */
    function get_count( $userid, $otherid )
    {
        $this->stmt = $this->db->count('messages');
    
        // where listingid matches
        $this->stmt->where('toid','i',$userid)->
            where('fromid','i',$otherid)->
            where_ne('status','i',
                GarageSale\BaseDatabase::STATUS_CLOSED)->
            where_ne('status','i',
                GarageSale\BaseDatabase::STATUS_SUSPENDED);
        
        // result of messages to other user
        $result = parent::get();
        
        // increment count
        $count = (int)$result[0]['id'];
        
        // set up other stmt to get messages from the other
        $this->stmt = $this->db->count('messages');
        $this->stmt->where('toid','i',$otherid)->
            where('fromid','i',$userid)->
            where_ne('status','i',
                GarageSale\BaseDatabase::STATUS_CLOSED)->
            where_ne('status','i',
                GarageSale\BaseDatabase::STATUS_SUSPENDED);
        
        // get result for messages to this user
        $result = parent::get();
        
        // increment count
        $count += (int)$result[0]['id'];
        
        // add to count
        return $count;
    }
    
    
    function set_read( $userid, $values )
    {
        
        // new update
        $this->stmt = $this->db->update('messages');
        
        // set values
        $this->stmt->values( array( 
            array(
                'name' => 'status',
                'type' => 'i',
                'value'=> GarageSale\BaseDatabase::STATUS_READ
            )
        ) );
        
        // count the items in the values
        $count = count( $values );
        
        // now loop over every read value and check that it
        // belongs to this user then add it to the statement
        for( $i=0; $i < $count; $i++ ){
            
            // and update stmt where
            $this->stmt->where( 
                'id', 'i', (int)$values[$i], 'OR' 
            );
            
        }
        
        // set this user
        $this->stmt->where('toid','i',$userid,'AND');
        
        // execute update
        return parent::set();
    }
    
    
    /** Inserts a message into the database from one user to another
     *  and sets it up as an unread message
     *  @param int $userid 
     */
    function new_message( $userid, $otherid, $values )
    {
        // make a new insert statement
        $this->stmt = $this->db->insert('messages');
        
        // values to set in database
        $values['status'] = (int)GarageSale\BaseDatabase::STATUS_UNREAD;
        $values['toid']   = (int)$otherid;
        $values['fromid'] = (int)$userid;
        
        // get values
        $update_values = $this->make_values( $values );
        
        // set up the values
        $this->stmt->values( $update_values );
        
        // return success
        return parent::set();
    }
	
	 /** Inserts a message into the database from one user to another
     *  and sets it up as an unread message
     *  @param int $userid 
     */
    function insert_contact( $name, $email_address, $subject, $message,$reason)
    {
        $this->stmt = $this->db->insert('contact');

				// insert a new contact with the correct values
			   $this->stmt->values( array (
				   array(
						'name'  => 'name',
						'value' => $name,
						'type'  => 'i'
						),

				   array(
						'name'  => 'email',
						'value' => $email_address,
						'type'  => 's'
						),

				   array(
						'name'  => 'subject',
						'value' => $subject,
						'type'  => 's'
						),

				   array(
						'name'  => 'message', 
						'value' => $message,
						'type'  => 's'
						),

				   array(
						'name'  => 'reason',
						'value' => $reason,
						'type'  => 's'
				   ),

				   array(
						'name'  => 'status',
						'value' => GarageSale\BaseDatabase::STATUS_ACTIVE,
						'type'  => 'i'
						)
				) );

				// execute the statement
				return parent::set();
    }
	
	function insert_report($name,$email_address,$subject,$message,$reason){
		$this->stmt = $this->db->insert('reports');

		// insert a new contact with the correct values
	   $this->stmt->values( array (
		   array(
				'name'  => 'name',
				'value' => $name,
				'type'  => 'i'
				),

		   array(
				'name'  => 'email',
				'value' => $email_address,
				'type'  => 's'
				),

		   array(
				'name'  => 'offender',
				'value' => $subject,
				'type'  => 's'
				),

		   array(
				'name'  => 'explanation', 
				'value' => $message,
				'type'  => 's'
				),

		   array(
				'name'  => 'reason',
				'value' => $reason,
				'type'  => 's'
		   ),

		   array(
				'name'  => 'status',
				'value' => GarageSale\BaseDatabase::STATUS_ACTIVE,
				'type'  => 'i'
				)
		) );

		// execute the statement
		return parent::set();
	}
    
} 

?>
