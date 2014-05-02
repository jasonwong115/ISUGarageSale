<?php
/** extensions/forum/models/forum_threads.php
 *  The threads model updates and retrieves data on threads in the forum
 */
class forum_threads_model extends GarageSale\Model {
    
    /** Flag for a sticky thread */
    const FLAG_SOLVED = 1;
    
    /** Flag for closed thread */
    const FLAG_REVIEW = 2;
    
    /** Flag for solved thread */
    const FLAG_CLOSED = 4;
    
    /** Flag for moderator review */
    const FLAG_STICKY = 8;
    
    
    /** Adds a new board into the database under a particular group
     *  @param int $user_id id of the submitting user
     *  @param int $board_id id of the board to insert to
     *  @param array $values associative data with key values matching 
     *         the columns of the forum_boards table of teh database.
     *  @return bool true on success false on failure
     */
    function new_item( $user_id, $board_id, $values )
    {
        // insert into the forum boards
        $this->stmt = $this->db->insert('forum_threads');
        
        // set integers as needed
        $values['creator_id'] = (int) $user_id;
        $values['board_id'] = (int) $board_id;
        $values['date_created'] = null;
        $values['date_edited'] = null;
        $values['status'] = (int)GarageSale\BaseDatabase::STATUS_ACTIVE;
        
        // make values
        $update_values = $this->make_values( $values );
        
        // set values
        $this->stmt->values( $update_values );
        
        // attempt insert
        return parent::set();
    }
    
    
    /** Gets all the threads under a given board
     *  @param int board_id Id of the board to get threads from
     *  @return array list of forum threads for a board
     */
	function get_threads( $board_id )
    {
        // select groups
        $this->stmt = $this->db->select('forum_threads');
        $this->stmt->where('status','i', 
            GarageSale\BaseDatabase::STATUS_ACTIVE )->
            // for the given board
            where('board_id','i',$board_id)->
            // where the flags are sticky
            where_gte('flags','i',
                forum_threads_model::FLAG_STICKY )->
            // and order descending
            order('date_edited', GarageSale\MySQLStatement::DESC);
        
        // get em all
        $result = parent::get();
        
        // select groups
        $this->stmt = $this->db->select('forum_threads');
        $this->stmt->where('status','i', 
            GarageSale\BaseDatabase::STATUS_ACTIVE )->
            // for the given board
            where('board_id','i',$board_id)->
            // where the flags are not sticky
            where_lt('flags','i',
                forum_threads_model::FLAG_STICKY )->
            // and order descending
            order('date_edited', GarageSale\MySQLStatement::DESC);
            
        // merge result with everything else
        return array_merge($result,parent::get());
    }
    
    
    /** Get a single thread from the database
     *  @param int $thread_id Id of the thread to fetch
     *  @return array single row of the result from the database
     */
    function get_item( $thread_id )
    {
        $this->stmt = $this->db->select('forum_threads');
        
        // where
        $this->stmt->where('id','i',$thread_id);
        
        // limit to 1
        $this->stmt->limit(1);
    
        return parent::get();
    }
    
    /** Gets the very last item inserted into database
     *  @return array 1 row from the database
     */
    function last_thread()
    {
        $this->stmt = $this->db->select('forum_threads');
        
        // get it
        $this->stmt->limit(1)->order('id',
            GarageSale\MySQLStatement::DESC);
        
        // return it
        $thread = parent::get();
        return $thread[0];
    }
    
    /** Updates the reply count by 1.
     *  @param int $thread_id id of the thread to update count of
     *  @return bool true on success false on failure
     */
    function increment_reply( $thread_id )
    {
        
        // create a safe prepared statement
        $this->db->prepare('inc_thrd_reply_cnt', 
            "UPDATE ".$this->db->prefix().
            "forum_threads SET reply_count=reply_count+1 WHERE id=?"
        );
        
        // set id
        $this->db->bind_param('inc_thrd_reply_cnt','i',$thread_id);
        
        // then select the board and increment the reply count there
        $this->stmt = $this->db->select('forum_threads');
        
        // where thread is this thread
        $this->stmt->where('id','i',$thread_id)->limit(1);
        
        // get result
        $thread = parent::get();
        
        // get board id
        if( count($thread) > 0 ){
            
            // create a safe prepared statement
            $this->db->prepare('inc_brd_reply_cnt', 
                "UPDATE ".$this->db->prefix().
                "forum_boards SET post_count=post_count+1 WHERE id=?"
            );
            
            // set id
            $this->db->bind_param('inc_brd_reply_cnt','i',
                $thread[0]['board_id']);
            
        }
        
        // execute both
        return $this->db->prepped_execute('inc_brd_reply_cnt') &&
               $this->db->prepped_execute('inc_thrd_reply_cnt');
    }
} 

?>
