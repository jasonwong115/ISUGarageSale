<?php
/** extensions/forum/models/forum_boards.php
 *  The blocks model is responsible for grabbing all data associated
 *  with blocks between users
 */
class forum_boards_model extends GarageSale\Model {
    
    
    /* Adds a new board into the database under a particular group
     * @param array $values associative data with key values matching 
     *        the columns of the forum_boards table of teh database.
     * @return bool true on success false on failure
     */
    function new_item( $values )
    {
        // insert into the forum boards
        $this->stmt = $this->db->insert('forum_boards');
        
        // set integers as needed
        $values['group_id'] = (int) $values['group_id'];
        $values['board_order'] = (int) $values['board_order'];
        $values['status'] = (int)GarageSale\BaseDatabase::STATUS_ACTIVE;
        
        // make values
        $update_values = $this->make_values( $values );
        
        // set values
        $this->stmt->values( $update_values );
        
        // attempt insert
        return parent::set();
    }
    
    
    /** Gets a single board from the database
     *  @param int $board_id Id of the board to grab from the database 
     */
    function get_item( $board_id )
    {
        $this->stmt = $this->db->select('forum_boards');
        
        // get where
        $this->stmt->where('id','i',$board_id);
        // limit to 1
        $this->limit(1);
        
        return parent::get();
    }
    
    /** Gets all the boards grouped under their respective parent boards
     *  @return array forum boards grouped by their parent group
     */
	function get_grouped( )
    {
        // select groups
        $this->stmt = $this->db->select('forum_groups');
        $this->stmt->where('status','i', 
            GarageSale\BaseDatabase::STATUS_ACTIVE )->
            // and order descending
            order('group_order', GarageSale\MySQLStatement::ASC);
        
        // get em all
        $result = parent::get();
        
        // add un categorized results
        $result[] = array(
            'id' => 0,
            'name' => 'Ungrouped',
            'description' => 'Boards without groups',
            'date_created' => 0,
            'group_order' => -1,
            'status' => 0
        );
        
        // because PHP is kinda dumb about some stuff, new array
        $grouped = array();
        
        // loop over result and get children
        foreach( $result as $row ){
            
            // select boards
            $this->stmt = $this->db->select('forum_boards');
            $this->stmt->where('status','i', 
                GarageSale\BaseDatabase::STATUS_ACTIVE )->
                // order descending
                order( 'board_order', GarageSale\MySQLStatement::ASC );
            
            // with parent id
            $this->stmt->where('group_id','i',$row['id']);
            
            $row['boards'] = parent::get();
            
            // get result into grouped array
            $grouped[] = $row;
        }
        
        return $grouped;
    }
    
    
    
    
    /** Update a given boards values
     *  @param int $board_id id of the board to update
     *  @param array values associative list of values to update
     *  @return bool true on success false on failure
     */
    function set_item( $board_id, $values )
    {
        $this->stmt = $this->db->update('forum_boards');
        
        // set where
        $this->stmt->where('id','i',(int)$board_id);
        
        
        if( isset($values['status']) ) 
            $values['status'] = (int)$values['status'];
        if( isset($values['thread_count']) ) 
            $values['status'] = (int)$values['thead_count'];
        if( isset($values['post_count']) ) 
            $values['status'] = (int)$values['post_count'];
        if( isset($values['group_id']) ) 
            $values['status'] = (int)$values['group_id'];
        
        // get value
        $update_values = $this->make_values($values);
        
        $this->stmt->values( $update_values );
        
        //
        return parent::set();
    }
    
    /** Updates the thread count by 1.
     *  @param int $board_id id of the board to update count of
     *  @return bool true on success false on failure
     */
    function increment_thread( $board_id )
    {
        
        // create a safe prepared statement
        $this->db->prepare('inc_thread_cnt', 
            "UPDATE ".$this->db->prefix().
            "forum_boards SET thread_count=thread_count+1 WHERE id=?"
        );
        
        // set id
        $this->db->bind_param('inc_thread_cnt','i',$board_id);
        
        return $this->db->prepped_execute('inc_thread_cnt');
    }
    
} 

?>
