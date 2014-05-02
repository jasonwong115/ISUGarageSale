<?php
/** extensions/forum/models/forum_posts.php
 *  The posts model updates and retrieves data on replies to threads in 
 *  the forum
 */
class forum_posts_model extends GarageSale\Model {
    
    
    /** Adds a new reply into the database under a particular thread
     *  @param int $user_id id of the submitting user
     *  @param int $thread_id id of the board to insert to
     *  @param array $values associative data with key values matching 
     *         the columns of the forum_posts table of teh database.
     *  @return bool true on success false on failure
     */
    function new_item( $user_id, $thread_id, $values )
    {
        // insert into the forum boards
        $this->stmt = $this->db->insert('forum_posts');
        
        // set integers as needed
        $values['creator_id'] = (int) $user_id;
        $values['thread_id'] = (int) $thread_id;
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
    
    
    /** Gets all the posts under a given thraed
     *  @param int $thread_id Id of the thread to get replies from
     *  @return array list of forum replies to a thread
     */
	function get_all( $thread_id )
    {
        // select groups
        $this->stmt = $this->db->select('forum_posts');
        $this->stmt->where('status','i', 
            GarageSale\BaseDatabase::STATUS_ACTIVE )->
            // for the given board
            where('thread_id','i',$thread_id)->
            order('date_created', GarageSale\MySQLStatement::ASC);
        
        
        return parent::get();
    }
    
    
    /** Get a single thread from the database
     *  @param int $post_id Id of the reply to fetch
     *  @return array single row of the result from the database
     */
    function get_item( $post_id )
    {
        $this->stmt = $this->db->select('forum_posts');
        
        // where
        $this->stmt->where('id','i',$post_id);
        
        // limit to 1
        $this->stmt->limit(1);
    
        return parent::get();
    }
    
    /** Gets the very last item inserted into database
     *  @return array 1 row from the database
     */
    function last_post()
    {
        $this->stmt = $this->db->select('forum_posts');
        
        // get it
        $this->stmt->limit(1)->order('id',
            GarageSale\MySQLStatement::DESC);
        
        // return it
        $post = parent::get();
        return $post[0];
    }
} 

?>
