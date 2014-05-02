<?php
/** controllers/forum.php
 *  provides a message board for people to post discussions about
 *  acquiring things in a college town 
 */
class forum extends GarageSale\Controller {
    
    /** Index is the default action for the forum. It provides will
     *  display the available message boards currently open for
     *  discussion.
     *  @param $args The arguments available to the action. Index does
     *         not need any.
     */
    function index( $args )
    {
    
        /* =======================================
         * Begin Query database for message boards
         */
        $boards_model = $this->app->
            model('forum_boards','forum/models');
        
        // get boards
        $result = $boards_model->get_grouped();
        
        
        // add action for link to boards
        $board_action = $this->app->form_path('forum/board');
        $this->view->add('board_action',$board_action);
    
        // pass the results on to the view
        $this->view->add('grouped_boards',$result);
        
        
        // set page title
        $this->view->add('page_title', 'Garage Sale Message Boards');
    }
	
	/** The board action should display the available threads in a 
	 *  chosen board. 
	 *  @param $args The arguments available to the action. Board will
	 *         require only id
	 */
	function board( $args )
	{
	    // get board id
	    $board_id = (int) $args['id'];
	    
	    // get board info
	    $boards_model = $this->app->
	        model('forum_boards','forum/models');
	   
	    // get board item
	    $board = $boards_model->get_item( $board_id );
	    
	    // check if records found
	    if( count($board) == 0 ){
	        
	        // st page title to reflect
	        $this->view->add('page_title','Unknown board');
	        $this->view->add('board_description','None');
	        $this->view->add('board_name','Unknown');
	    } else {
	        
	        // set page title
	        $this->view->add('page_title','Viewing threads
	            in message board: ' . $board[0]['name'] );
	        $this->view->add('board_description',
	            $board[0]['description']);
	        $this->view->add('board_name',
	            $board[0]['name']);
	    }
	    
	    // get threads
	    $threads_model = $this->app->
	        model('forum_threads','forum/models');
	    
	    // add the threads to the page
	    $this->view->add('threads_result',
	        $threads_model->get_threads($board_id) );
	    
	    
	    /* -----------------------
	     * Add actions to the view
	     */
	    
	    // index action
	    $this->view->add('index_action',
	        $this->app->form_path('forum'));
	    
	    // create a new thread action
	    $this->view->add('add_action',
	        $this->app->form_path('forum/newthread/'.$board_id));
	    
	    // search the forum action
	    $this->view->add('search_action',
	        $this->app->form_path('forum/search'));
	    
	    // add thread action to view
	    $this->view->add('thread_action',
        $this->app->form_path('forum/thread'));
        	
	}
	
	/** View a thread in the forum
	 *  @param array $args Arguments from the url. Concerned with id.
	 */
	function thread( $args )
	{
	    $thread_id = (int)$args['id'];
	    
	    // check for existance, use threads model
	    $threads_model = $this->app->
	        model('forum_threads','forum/models');
	        
		// boards model
		$boards_model = $this->app->
		    model('forum_boards', 'forum/models');    
	            
	    // posts model
	    $posts_model = $this->app->
	        model('forum_posts','forum/models');
	    
	    // get item
	    $thread = $threads_model->get_item( $thread_id );
	    $thread = ( count($thread) > 0 ) ? $thread[0] : null;
	    
	    
	    // imprtant stuffs
	    if( $thread == null ){
	         
	    	// redirect
	    	$this->app->redirect('forum');
	    }
	    
	        
        // get board
        $board = $boards_model->get_item((int)$thread['board_id']);
        $board = $board[0];
        
        // set page title
        $this->view->add('page_title',
            'Viewing thread: ' . $thread['name'] );
        
        // set index action
        $this->view->add('index_action',
            $this->app->form_path('forum'));
            
        // set board action
        $this->view->add('board_action',
            $this->app->form_path('forum/board/'.$board['id']));
        
        // good
        $this->view->add('board_name', $board['name'] );
	    
	    // need an action message
	    $action_message = null;
	    
	    // init text area values
	    $name_value = '';
	    $post_value = '';
	    
	    
	    // check for post
	    if( $_SERVER['REQUEST_METHOD'] == 'POST' &&
	    	isset($_POST['name']) &&
	    	$_POST['name'] != null &&
	    	isset($_POST['post']) &&
	    	$_POST['name'] != null 
	    ){
	    	
	    	// add success
	    	$success = $posts_model->new_item( 
	    		$this->app->user->get_user_id(), $thread_id, $_POST );
	    	
	    	// test for success
	    	if( $success ){
	    	
	    		// increment reply count
	    		$threads_model->increment_reply( $thread_id );
	    			    	
	    		$action_message = 'Post successfully added.';
	    	} else {
	    		$action_message = 'There was an error adding your post';
	    	}
	    	
	    } elseif( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	    	
	    	$action_message = 'One or more required fields was not
	    		provided';
	    		
	    	// update values
			$name_value = 'value="'.$_POST['name'].'"';
			$post_value = $_POST['post'];
	    }
	    
	    
	    // add them
	    $this->view->add('name_value',$name_value);
	    $this->view->add('post_value',$post_value);
	    
	    // add actio message
	    $this->view->add('action_message',$action_message);
	    
	    // get limits
	    $limit = 10;
	    
	    // get page
	    $page = 0;
	    if( isset($_GET['p']) ){
	        $page = (int)$_GET['p'];
	    }
	    
	    // get replies
	    $posts_result = $posts_model->limit($limit)->page($page)->
	        get_all($thread_id);
	    
	    //
	    $this->view->add('posts_result',$posts_result);
	    
	    $reply_count = ($thread==null) ? 0 :(int)$thread['reply_count'];
	    
	    // add paging
	    $this->view->add('page_info',array(
	        // posts count
	        'page_count' => (int)(($reply_count) / $limit)+1,
	        'this_page' => $page
	    ));
	    
	    
	    // add actions
	    $this->view->add('page_action',
	        $this->app->form_path('forum/thread/'.$thread_id.'?p='));
	    
	    $this->view->add('reply_action',$this->app->
	        form_path('forum/thread/'.$thread_id.'?p='.$page));
	        
	    
	    // add to the view
	    $this->view->add('thread_result',$thread);
	    
	    
	    // get wysiwyg extension
	    $wysiwyg = $this->app->extension('wysiwyg');
	    
	}
	
	
	/** Add a new thread to a board.
	 *  @param array $args arguments provided by url. $args['id'] will
	 *         refer to the board to add the new thread to.
	 */
	function newthread( $args )
	{
	    // if not logged in redirect
	    if( !$this->app->user->is_logged_in() ){
	        $this->app->redirect('user/login');
	    }
	    
	    // get board id
	    $board_id = (int) $args['id'];
	    
	    // get board info
	    $boards_model = $this->app->
	        model('forum_boards','forum/models');
	   
	    // get board item
	    $board = $boards_model->get_item( $board_id );
	    
	    // check if board exists
	    if( count($board) == 0 ){
	        $this->app->redirect('forum');
	    }
	    
	    // action message
	    $action_message = null;
	    
	    
	    
	    // init failed submission values
	    $name_value = '';
	    $description_value = '';
	    
	    
	    /* -------------------------
	     * Check for form submission
	     */
	    if( $_SERVER['REQUEST_METHOD'] == 'POST' && 
	        isset( $_POST['name']) && 
	        $_POST['name'] != null &&
	        isset( $_POST['description']) && 
	        $_POST['description'] != null
	    ){
	        
	        // get threads model
	        $threads_model = $this->app->
	            model('forum_threads','forum/models');
	        
	        // add the new thread
	        $success = $threads_model->
	            new_item($this->app->user->get_user_id(), 
	                $board_id, $_POST );
	        
	        // test for success
	        if( $success ){
	        
	            // get most recent thread
	            $thread = $threads_model->last_thread();
	        
	            // increment thread count
	            $boards_model->increment_thread($board_id);
	        
	            // redirect to the new thread
	            $this->app->redirect('forum/thread/'.$thread['id']);
	        }
	    } elseif($_SERVER['REQUEST_METHOD'] == 'POST' ) {
	        $action_message = "One ore more required inputs were not 
	            provided.";
	           
	        $name_value = 'value="'.$_POST['name'].'"';
	        $description_value = $_POST['description'];
	       
	    }
	    
	    // add init values
	    $this->view->add('name_value',$name_value);
	    $this->view->add('description_value',$description_value);
	    
	    // add action message
	    $this->view->add('action_message',$action_message);
	    
	    
	    
	    /* -------------------------
	     * Action paths defined here
	     */
	    
	    // action on form submit
	    $this->view->add('submit_action',
	    $this->app->form_path('forum/newthread/'.$board_id));
	    
	    // get wysiwyg extension
	    $wysiwyg = $this->app->extension('wysiwyg');
	    
	    // set page title
	    $this->view->add('page_title','Create a new thread');
	    
	}
	
}

?>
