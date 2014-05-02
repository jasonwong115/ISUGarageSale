<?php
/** models/activation.php
 *  The activation model is responsible for grabbing all data associated
 *  with activating/verifing new accounts
 */
class activation_model extends GarageSale\Model {

	/** Get activation of user to be activated
     *  @param int $userid information of user to be activated
     *  @return the users information
     */
	function get_all($userid){
		
		$this->stmt = $this->db->select('activation');
        // set where values
        $this->stmt->where('userid','i',$userid);
		return parent::get();
	}
	
	/** Add activation hash and code to table
     *  @param int $userid id of user that needs to be activated
	 *  @param int $hash id activation hash
	 *  @param int $code id code to activate account
     *  @return the users information
     */
	function add_hash($userid,$hash,$code){
		
		$this->stmt = $this->db->insert('activation');
		
			// set insert values
			$this->stmt->values( array(
			
				// description of review
				array(
					'name' => 'userid',
					'value' => $userid,
					'type' => 's',
					'table' => null
				),
				 
				// the reviewer id
				array(
					'name' => 'hash',
					'value' => $hash,
					'type' => 'i',
					'table' => null
				),
				
				// the subject id
				array(
					'name' => 'code',
					'value' => $code,
					'type' => 'i',
					'table' => null
				)
			) );
			return parent::set();
	}
	
	/** Check to make sure the activation information is correct
     *  @param int $user_id id of the user to check
	 *  @param int $hash id activation hash to check
	 *  @param int $code id code to activate account
	 *  @param int $pretest 0 if checking for right hash, 1 if checking for right code
     *  @return the users activation information
     */
	function check_hash($userid,$hash,$code,$pretest)
    {
        $this->stmt = $this->db->count('activation');
        // set where values
        $this->stmt->where('userid','i',$userid);
		$this->stmt->where('hash','i',$hash);
		if((int)$pretest > 0){
			$this->stmt->where('code','i',$code);
		}
		return parent::get();
    }
	
	/** Check to make sure the user is in fact not activate yet
     *  @param int $user_id id of the user to check
     *  @return the users information
     */
	function check_user($userid)
    {
        $this->stmt = $this->db->count('users');
        // set where values
        $this->stmt->where('id','i',$userid);
		$this->stmt->where_lte('userlevel','i',GarageSale\User::USER_UNVERIFIED);
		return parent::get();
    }
	
	/** Check to make sure the user is activated
     *  @param int $user_id id of the user to check
     *  @return the users information
     */
	function check_user_activated($userid)
    {
        $this->stmt = $this->db->count('users');
        // set where values
        $this->stmt->where('id','i',$userid);
		$this->stmt->where_gte('userlevel','i',GarageSale\User::USER_STANDARD);
		return parent::get();
    }
	
	/** Changes the status of a user to "standard user"
     *  @param int $user_id id of the user to activate
     */
	function activate_user($userid){
		$this->stmt = $this->db->update('users');
                
		// where
		$this->stmt->where('id','i',$userid);
		
		// values
	   $this->stmt->values( array(
		   
		   // userlevel
		   array(
			   'name' => 'userlevel',
			   'type' => 'i',
			   'value'=> GarageSale\User::USER_STANDARD
		   )
		));
		return parent::set();
	}
	
	/** Deletes a row in the hash table
     *  @param int $user_id id of the user to delete from the hash table
	 *	@param int $hash hash to delete to ensure right row is deleted
     */
	function delete_hash($userid,$hash){
		$this->stmt = $this->db->delete('activation');
        // set where values
        $this->stmt->where('userid','i',$userid);
		$this->stmt->where('hash','i',$hash);
		return parent::set();
	}
	
} 

?>
