<?php
/** extensions/forum/models/forum_groups.php
 *  The forum_groups model is responsible for accessing and adding data
 *  the the database with regards to the forum extension and the board
 *  groups
 */
class forum_groups_model extends GarageSale\Model {

    
    /** Inserts a new group into the forum tables
     *  @return bool true on success false on failure
     */
	function new_item( $values )
    {
        // new insert statement
        $this->stmt = $this->db->insert('forum_groups');
        
        // convert necessary to integers
        $values['group_order'] = (int)$values['group_order'];
        $values['status'] = (int)GarageSale\BaseDatabase::STATUS_ACTIVE; 
        
        // make the values
        $update_values = $this->make_values( $values );
        
        // set values to statement
        $this->stmt->values( $update_values );
        
        return parent::set();
    }
} 

?>
