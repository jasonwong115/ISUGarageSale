<?php
/** models/users.php
 *  The users model is responsible for grabbing all data associated
 *  with users
 */
class users_model extends GarageSale\Model {

	/** Checks if a user exists and retrieves their information
     *  @param int $user_id id of the user to check
     *  @return users info
     */
	function user_exists($user_id){
		$this->stmt = $this->db->select('users');
		$this->stmt->where('id', 'i', $user_id);
		return parent::get();
	}
	
	/** Gets a count of every active entry in the listings based on a
     *  user
     *  @param int $user_id id of the user to count for
     *  @return int number of active items for the user
     */
    function count_user($user_id)
    {

        // new count
        $this->stmt = $this->db->count('users');
        
        // reset limits
        $this->limit(-1)->page(-1);
        
        // get the row
        $count = $this->user_exists($user_id);
		if($count ==null){
			return 0;
		}else{
			// return count
			return (int)$count[0]['id'];
		}
    }
	
} 

?>
