<?php
/** controllers/messages.php
 *  provides a message a method for users to send private messages to 
 *  each other 
 */
class messages extends GarageSale\Controller {
    
    

    /** The before function is used to pass information on the user into
     *  the view for every action in this controller.
     */
    function before($args){
        $this->app->db->connect();
        $this->view->new_view('layout');
        
        $this->userid = -1;
        
        // if logged in, tell the view
        if( $this->app->user->is_logged_in() ){
            // this users id
            $this->userid = $this->app->user->get_user_id();
        } else {
            
            // they don't belong here
            $this->app->redirect('user/login');
        }
        
    }
    
    /** Inbox is the default action for the messages. It displays the
     *  messages in this users inbox and links to old conversations.
     *  @param $args The arguments available to the action. Index does
     *         not need any but can accept the user id which assists
     *         admins in reviewing violations of ToS.
     */
    function inbox( $args )
    {
    
        // set page title
        $this->view->add('page_title','My Inbox');
        
        // mesages model
        $messages_model = $this->app->model('messages');
        
        /* ==================================
         * Begin mark messages as read update
         */
        if( $_SERVER['REQUEST_METHOD'] === 'POST' && 
            isset($_POST['read_items']) 
        ) {
            
            // check for updates
            $read_items = $_POST['read_items'];
            
            // count em up
            $count = count( $read_items ); 
            
            // check for values
            if( $count > 0 ){
                
                
                // finally execute statement
                $success = $messages_model->
                    set_read($this->userid, $_POST['read_items']);
                
                // if unsuccessful
                if( !$success ){
                    
                    // add an update message on failure
                    $this->view->add('update_message',
                        "There was an error marking messages as read."
                    ); 
                }
            }
        }
        
        
        /* ========================================
         * Begin Query database for unread messages
         */
        // calculate limits
        $inbox_page = 0;
        
        // set per_page default
        $per_page = 10;
        
        // check if page is set
        if( isset($_GET['ipage']) && is_numeric($_GET['ipage']) ){
            
            // get inbox page
            $inbox_page = ((int) $_GET['ipage']) - 1;
        }
        
        // calculate offsets
        $offset = $inbox_page * $per_page;
        
        // get the unread messages
        $result = $messages_model->//limit(1)->page($offset)->
            get_unread( $this->userid );;
        
        
        
        /* ====================================
         * SQL to select distinct conversations
         */
            
        // get conversations
        $conversations_result = $messages_model->limit(-1)->
            get_conversation_list( $this->userid );
        
        
	    // add self link
	    $this->view->add('self_link', 
	        $this->app->form_path('messages') 
	    );
	    
        // pass the results on to the view
        $this->view->add('unread_result',$result);
        
        // pass the results on to the view
        $this->view->add('conversations_result',$conversations_result);
    }
	
	
	
	/** Write a new message to another registered user. 
	 *  @param array $args The arguments available to the action. Will
	 *         only need to be concerned with the fromid, which will be
	 *         the destination user.
	 */
	function write( $args )
	{
	    $toid = $this->app->user->id_from_name($args['fromid']);
	    
	    // check for user being valid
	    if( $args['fromid'] == null || $toid < 1 ){
	        $err_msg = <<< ERRMSG
	        Hmm, it doesn't look like we have any record of this user.
	        Perhaps you have followed a bad link? If you believe this is
	        an error please report it to our staff.<br /> Thank you!
ERRMSG;

            // add view
            $this->view->add('subviews',array('err_msg'));
            
            // add errmsg
            $this->view->add('err_msg',$err_msg);
            
            // end
            return;
	    }
	    
	    // get user name
	    $name = $this->app->user->name_from_id( $toid );
	    
	    // add page title
	    $this->view->add('page_title','Message to ' . $name );
	    
	    
	    /* ======================================================
	     * SQL to Check to make sure that the user is not blocked 
	     */
	    $blocks_model = $this->app->model('blocks');
	    
	    // check for blocks
	    if( $blocks_model->is_blocked($toid, $this->userid) ){
	        
	        // set up err_msg
	        $err_msg = <<< ERRMSG
	        Uh, oh. It looks like this user has blocked messages from
	        you.
ERRMSG;

            // add view
            $this->view->add('subviews',array('err_msg'));
            
            // add errmsg
            $this->view->add('err_msg',$err_msg);
            
            // end
            return;
	    }
	    
	    
	    
	    $write_attempt = false;
	    $write_success = false;
	    $write_message = '';
	    
        // get form
        $message_form = $this->view->
            form('messages',
                'messages/write/'.$args['fromid']);
        
        // add form
        $this->view->add('message_form',$message_form);
        
	    /* Validate inputs */
	    if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
	        
	        // write attempt is true
	        $write_attempt = true;
	        
	        // check subject
            if( !$message_form->validate($write_message) ){
                
                // bad input not gonna fly
                $write_success = false;
                
            } else {
                
                /* =======================================
                 * SQL Insert statement to add new message
                 */
                $messages_model = $this->app->model('messages');
                
                // execute the statement
                if( !$messages_model->
                    new_message( $this->userid, $toid, $_POST )
                ){
                    
                    // fail
                    $write_message = 'Uh, oh there was an error sending
                        your message';
                } else {
                    
                    // redirect to users messages
                    $this->app->redirect(
                        'messages/conversation/' . $args['fromid']
                    );
                }
            }
	        
	    }
	    
	    
	    // add success and attempt and message stuff to view
	    $this->view->add('write_attempt',$write_attempt);
	    $this->view->add('write_message',$write_message);
	    
		
	    // get wysiwyg extension
	    $wysiwyg = $this->app->extension('wysiwyg');
	    
	}
	
	
	/** View and reply to a user who is part of your conversation 
	 *  @param array $args The arguments available to the action. Will
	 *         only need to be concerned with the fromid, which will be
	 *         the destination user.
	 */
	function conversation( $args )
	{
	    $toid = $this->app->user->id_from_name($args['fromid']);
	    
	    // messages model
	    $messages_model = $this->app->model('messages');
	    
	    // check for user being valid
	    if( $args['fromid'] == null || $toid < 1 ){
	        $err_msg = <<< ERRMSG
	        Hmm, it doesn't look like we have any record of this user.
	        Perhaps you have followed a bad link? If you believe this is
	        an error please report it to our staff.<br /> Thank you!
ERRMSG;

            // add view
            $this->view->add('subviews',array('err_msg'));
            
            // add errmsg
            $this->view->add('err_msg',$err_msg);
            
            // end
            return;
	    }
	    
	    // get user name
	    $name = $this->app->user->name_from_id( $toid );
	    
	    // add to view
	    $this->view->add('othername',$name);
	    
	    // add page title
	    $this->view->add('page_title','Conversation with ' . $name );
	    
	    	    
	    $write_attempt = false;
	    $write_success = false;
	    $write_message = '';
	    
        // get form
        $message_form = $this->view->
            form('messages',
                'messages/conversation/'.$args['fromid']);
        
        // add form
        $this->view->add('message_form',$message_form);
	        
	    /* Validate inputs */
	    if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
	        
	        
	        /* ======================================================
	         * SQL to Check to make sure that the user is not blocked 
	         */
	        $blocks_model = $this->app->model('blocks');
	        
	        // check for blocks against this user
	        if( $blocks_model->is_blocked($toid,$this->userid) ){
	            
	            // set up err_msg
	            $err_msg = <<< ERRMSG
	            Uh, oh. It looks like this user has blocked messages 
	            from you.
ERRMSG;

                // add view
                $this->view->add('subviews',array('err_msg'));
                
                // add errmsg
                $this->view->add('err_msg',$err_msg);
                
                // end
                return;
	        }
	        
	        
	        // write attempt is true
	        $write_attempt = true;
	        
	        
	        // check subject
            if( !$message_form->validate($write_message) ){
                
                // bad input not gonna fly
                $write_success = false;
            } else {
                
                /* =======================================
                 * SQL Insert statement to add new message
                 */
                
                // execute the statement
                if( !$messages_model->
                    new_message( $this->userid, $toid, $_POST )
                ){
                    
                    // fail
                    $write_message = 'Uh, oh there was an error sending
                        your message';
                } else {
                    
                    $write_message = "Message sent.";
                    
                    // reset form
                    $message_form->reset()->create();
                    
                    // clear stuff
                    $_POST['message'] = null;
                    $_POST['subject'] = null;
                }
            }
	        
	    }
	   
	   
	    // set up how many / pages
	    $conversation_page = 0;
	   
	    // check for get
	    if( isset( $_GET['cpage']) && is_numeric($_GET['cpage']) ){
	       
	       // update page
	       $conversation_page = ((int) $_GET['cpage'] ) - 1;
	    }
	   
	    // set how many
	    $how_many = 10;
	   
	    $offset = $conversation_page * $how_many;
	   
	    /* ===============================================
	     * SQL to count number of messages in conversation
	     */
	    
	    $count = $messages_model->get_count($this->userid, $toid );
	    
	    // get the results
	    if( $count > 0 ){
	        $message_count = array(
	            // total messages
	            'total' => (int) $count,
	            'per'   => $how_many,
	            'begin' => $offset + 1,
	            'end'   => $offset + (int) $count
	        );
	    } else {
	        // set zero results
	        $message_count = array(
	            // total messages
	            'total' => 0,
	            'per'   => $how_many,
	            'begin' => 0,
	            'end'   => 0
	        );
	    }
	   
	    // add message count to view
	    $this->view->add('message_count', $message_count );
	   
	   
	    /* ===========================
	     * SQL to load the convrsation
	     */
        
        // get messages
        $message_result = $messages_model->
            limit($how_many)->page($offset)->
            get_conversation( $this->userid, $toid );
        
        // add to view
        $this->view->add('message_result', $message_result );
	    
	    
        /* ====================================
         * SQL to select distinct conversations
         */
        
        // get conversations
        $conversations_result = $messages_model->
            get_conversation_list($this->userid);
        
        // add to view
        $this->view->add('conversations_result',$conversations_result);
	    
	    
	    // add success and attempt and message stuff to view
	    $this->view->add('write_attempt',$write_attempt);
	    $this->view->add('write_message',$write_message);
	    
	    // get wysiwyg extension
	    $wysiwyg = $this->app->extension('wysiwyg');
	    
	}
}

?>
