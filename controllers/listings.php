<?php
/** controllers/listings.php
 *  The listings controller is responsible for all updates on posts.
 *  It can be considered as the back end to user interaction with the
 *  website. 
 *
 *  This is the only controller that should allow users to add or remove
 *  content from the website. Distinguish from browse which will only
 *  display content.
 */
class listings extends GarageSale\Controller {


    /** The before function is used to pass information on the user into
     *  the view for every action in this controller.
     */
    function before($args){
        $this->app->db->connect();
        $this->view->new_view('layout');
        
        $this->userid = -1;
        $this->username = null;
        
        // if logged in, tell the view
        if( $this->app->user->is_logged_in() ){
            
            // this users id
            $this->userid = $this->app->user->get_user_id();
            
            // this users username
            $this->username = $this->app->user->get_user_name();
             
            // add them both to the view
            $this->view->add('username', $this->username );
            // add id to the view
            $this->view->add( 'userid', $this->userid );
        }
        
        // require needed libraries
        $this->app->library('AmazonIntegration');
        
        $this->amazon = new \AmazonFetcher();
    }

    /** Posts a new item to the Garage. Uses the current logged in users
     *  information to make the post.
     *  @param $args The array of arguments passed fromthe controller 
     *  we will need none of these arguments.
     */
    function newpost( $args )
    {
        // identify user
        if( !$this->app->user->is_logged_in() ){
            
            // redirect, he/she doesn't belong here
            $this->app->redirect('user/login');
        }
        
        // post success and attempt values, init both to false
        $post_attempt = false;
        $post_message = '';
        $post_success = false;
        
        /* -----------------------------------
         * Begin new submission post handling.
         */
        if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            
            // validate
            if( !isset($_POST['title'])         || 
                $_POST['title'] == null         ||
                !isset($_POST['asking_price'])  || 
                $_POST['asking_price'] == null  ||
                !isset($_POST['description'])   ||
                $_POST['description'] == null   ||
                !isset($_POST['categoryid'])    ||
                $_POST['categoryid'] == null 
            ){
            
            
                // post failed, bad inputs
                $post_success = false;
                
                // post failure message
                $post_message = 'One or more required fields are 
                    missing';
                
            }else if( !is_numeric($_POST['asking_price']) ||
                !is_numeric($_POST['categoryid'])
            ){
            
                // another bad input
                $post_success = false;
                
                // post failure message
                $post_message = 'A numeric value is malformed.';
                
            } else {
                
                /* ==================
                 * Get listings model
                 */
                $model = $this->app->model('listings');
                
                // and finally execute
                $post_success = $model->new_item($_POST,$this->userid);
                    
                // on success go to the listings
                if( $post_success ){
                
                    // redirect to this users browse page
                    $this->app->redirect(
                        'browse/user/' . $this->userid
                    );
                } else {
                    $post_message = "Uh, oh. There was an error 
                        accessing our database.";
                }
            }
                 
            
            // there was a post attempted 
            $post_attempt = true;
        }
        
        
        // new post subviews
        $subview = array('listings_post');
        $this->view->add('subviews',$subview);
        
        // tell the view what to display on the button
        $this->view->add('button_text','Submit new listing');
        
        
        /* -------------------------------------------------------------
         * These next few adds will add the default value to be included
         * for the various inputs in the post page.
         */
        
        // default value for the title
        $title_value = (isset($_POST['title']) ? 
            'value="'. $_POST['title'] .'"' : ''
        );
        
        // get default value for asking price
        $asking_price_value = (isset($_POST['asking_price']) ? 
            'value="'. $_POST['asking_price'] .'"' : ''
        );
        
        // get default value for other offer
        $other_offer_value = (isset($_POST['other_offer']) ? 
            $_POST['other_offer'] : ''
        );
        
        // get default value for description
        $description_value = (isset($_POST['description']) ? 
            $_POST['description'] : ''
        );
        
        
        // get default category for description
        $categoryid_value = ( (isset($_POST['categoryid']) && 
            is_numeric($_POST['categoryid'])) ? 
            (int)$_POST['categoryid']  : -1
        );
        
        // get default value for keywords
        $keywords_value = (isset($_POST['keywords']) ? 
            'value="'. $_POST['keywords'] .'"' : ''
        );
        
        
        // add all to view
        $this->view->add('title_value', $title_value );
        $this->view->add('asking_price_value', $asking_price_value );
        $this->view->add('other_offer_value', $other_offer_value );
        $this->view->add('description_value', $description_value );
        $this->view->add('categoryid_value', $categoryid_value );
        $this->view->add('keywords_value', $keywords_value );
        
        
        
        /* ------------------------------------------------------------
         * And then just th general post attempt information added next
         */
        
        // add insert attempt status
        $this->view->add( 'post_attempt', $post_attempt );
        
        // add insert successs status
        $this->view->add( 'post_success', $post_success );
        
        // add insert message
        $this->view->add( 'post_message', $post_message );
        
        
        
        // get the list of categories from the utility class
        $categories = $this->app->model('categories')->
            get_category_children(0);
        
        // pass this on to the view
        $this->view->add( 'categories', $categories );
        
        // pass form path in as well
        $form_path = $this->app->form_path('listings/newpost');
        $this->view->add( 'form_path', $form_path );
        
    }

    /** Edit a post that has been made to the garage. Select from other
     *  options such as Amazon/Google linkage and stuff.
     *  @param $args The array of arguments passed from the controller 
     *         This action will need the id.
     */
    function editpost( $args )
    {
        // identify user
        if( !$this->app->user->is_logged_in() ){
            
            // redirect, he/she doesn't belong here
            $this->app->redirect('user/login');
        }
        
        // get post id
        if( $args['id'] == null || !is_numeric($args['id']) ){
            
            // no id provided or malformed, redirect to listings page
            $this->app->redirect('browse/user/' . $this->userid );
        }
        
        // finally set post id
        $postid = (int) $args['id'];
        
        /* ---------------------------------------------------
         * Query to get old data and to check user association
         */
         
        /* =================
         * Get listings model
         */
        $model = $this->app->model('listings');
        
        // get result
        $result = $model->get_item($postid);
        
        // check if post exists and that it matches this user
        if( count($result) == 0 || 
            (int)$result[0]['userid'] !== $this->userid
        ){
            
            // redirect to users posts if no results
            $this->app->redirect('browse/user/' . $this->userid );
        }
        
        // post success and attempt values, init both to false
        $post_attempt = false;
        $post_message = '';
        $post_success = false;
        
        /* ------------------------------------
         * Begin edit submission post handling.
         */
        if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            
            // validate
            if( !isset($_POST['title'])         || 
                $_POST['title'] == null         ||
                !isset($_POST['asking_price'])  || 
                $_POST['asking_price'] == null  ||
                !isset($_POST['description'])   ||
                $_POST['description'] == null   ||
                !isset($_POST['categoryid'])    ||
                $_POST['categoryid'] == null 
            ){
            
            
                // post failed, bad inputs
                $post_success = false;
                
                // post failure message
                $post_message = 'One or more required fields are 
                    missing';
                
            }else if( !is_numeric($_POST['asking_price']) ||
                !is_numeric($_POST['categoryid'])
            ){
            
                // another bad input
                $post_success = false;
                
                // post failure message
                $post_message = 'A numeric value is malformed.';
                
            } else {
                                
                /* =====================
                 * Use model to set item
                 */
                $model->set_item($postid, $_POST);
                
                // and finally execute
                $post_success = $model->set();


                // new select statement
       	 	$stmt = $this->app->db->update('listings');

         	// set the where value of the statement
             	$stmt->where('id','i', $args['id']);
       	
		 $stmt->values( array (
                 	 array(
                    	'name'  => 'image_paths',
                    	'value' => $this->app->user->uploadImage('l_'.strval($args['id'])),
                    	'type'  => 'i'
                    	)
            	) );

		if ($_FILES["file"]["name"] != null){ //only execute this statement if the user actually selects an image
               		// execute the statement
            	$post_success = $this->app->db->statement_execute($stmt);
		}
                    
                // on success go to the listings
                if( $post_success ){
                    // redirect to this users browse page
                    $this->app->redirect(
                        'browse/user/' . $this->userid
                    );
                } else {
                    $post_message = "Uh, oh. There was an error 
                        accessing our database.";
                }
            }
                 
            
            // there was a post attempted 
            $post_attempt = true;
        }
        
       
        // new post subviews
        $subview = array('listings_post');
        $this->view->add('subviews',$subview);
        
        // tell the view what to display on the button
        $this->view->add('button_text','Update listing');
        
        /* -----------------------
         * Some neat Amazon stuff.
         */
        $response = $this->amazon->medium($result[0]['title']);
            
        // add amazon's response to our view
        $this->view->add('amazon_response',$response);
        
        /* -------------------------------------------------------------
         * These next few adds will add the default value to be included
         * for the various inputs in the post page.
         */
        
        // default value for the title
        $title_value = 'value="'. (isset($_POST['title']) ? 
            $_POST['title']  : $result[0]['title']
        ) .'"';
        
        // get default value for asking price
        $asking_price_value = 'value="'.(isset($_POST['asking_price']) ? 
            $_POST['asking_price'] : 
            $result[0]['asking_price']
        ) .'"';
        
        // get default value for other offer
        $other_offer_value = (isset($_POST['other_offer']) ? 
            $_POST['other_offer'] : $result[0]['other_offer']
        );
        
        // get default value for description
        $description_value = (isset($_POST['description']) ? 
            $_POST['description'] : $result[0]['description']
        );
                
        
        // get default category for description
        $categoryid_value = ( ( isset($_POST['categoryid']) && 
            is_numeric($_POST['categoryid'] ) ) ? 
            (int)$_POST['categoryid']  : (int)$result[0]['categoryid']
        );
        
        // get default value for keywords
        $keywords_value = 'value="'. (isset($_POST['keywords']) ? 
            $_POST['keywords'] : 
            $result[0]['keywords']
        ) .'"';
        
        
        // add all to view
        $this->view->add('title_value', $title_value );
        $this->view->add('asking_price_value', $asking_price_value );
        $this->view->add('other_offer_value', $other_offer_value );
        $this->view->add('description_value', $description_value );
        $this->view->add('categoryid_value', $categoryid_value );
        $this->view->add('keywords_value', $keywords_value );
        
        
        /* -------------------------------------------------------------
         * And then just the general post attempt information added next
         */
        
        // add insert attempt status
        $this->view->add( 'post_attempt', $post_attempt );
        
        // add insert successs status
        $this->view->add( 'post_success', $post_success );
        
        // add insert message
        $this->view->add( 'post_message', $post_message );
        
        // add database results to the view
        $this->view->add( 'listings_result', $result );
        
        
        // get the list of categories from the utility class
        $categories = $this->app->model('categories')->
            get_category_children(0);
        
        // pass this on to the view
        $this->view->add( 'categories', $categories );
        
        // pass form path in as well
        $form_path = $this->app->form_path(
            'listings/editpost/'.$args['id']
        );
        $this->view->add( 'form_path', $form_path );
        
    }


	 /*
         * Function to delete post
         */
	function deletePost($args){

		$subview = array('listings_deleteItem');
        $this->view->add('subviews',$subview);

        $postid = (int) $args['id'];

		 // pass form path in as well
        $form_path = $this->app->form_path(
            'listings/deletePost/'.$args['id']
        );
        $this->view->add( 'form_path', $form_path );

        /* ======================
         * Get the listings model
         */
        $listing_model = $this->app->model('listings');
        
        // get the result
        $result = $listing_model->limit(1)->get_item( $postid );
                
        // add results to the view
        $this->view->add('listing_result', $result);

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            if( $_POST['delete'] === 'yes' )  {
       		    // delete the item
                $stmt = $this->app->db->delete('listings');

                // set the where value of the statement
                $stmt->where('id', 'i', $postid);

               // $stmt->limit( 1 );
                echo $stmt->get_query();
                echo "Post id".$postid;

                $post_success = $this->app->db->statement_execute($stmt);

                // on success go to the listings
                if( $post_success ) {
                    // redirect to this users browse page
                    $this->app->redirect(
                        'browse/user/' . $this->userid
                    );
                } else {
                    $post_message = "Uh, oh. There was an error
                    accessing our database.";
                }
            } else {
                    // redirect to this users browse page
                    $this->app->redirect(
                        'browse/user/' . $this->userid
                    );
            }
        }
	}


    /** Displays form for a new offer for a given listing in the garage
     *  and processes the submission when made.
     *  @param array $args The uri provided arguments passed by the
     *         browser. This action is interested in 'id'.
     */
    function newoffer( $args )
    {
        // first ensure user is logged in, otherwise, bail
        if( !$this->app->user->is_logged_in() ){
            
            // redirect to login
            $this->app->redirect('user/login');
        }
        
        // check for valid id
        if( $args['id'] === null || !is_numeric($args['id'] ) ){
            
            // redirect to browse all
            $this->app->redirect('browse');
        }
        
        // ok get id
        $postid = (int) $args['id'];
        
        /* ==============
         * Listings model
         */
         
        // new selection
        $listings_model = $this->app->model('listings');
        
        // get result
        $result = $listings_model->get_item($postid);
        
        // if not results, go elsewhere
        if( count($result) < 1 ||
        
            // and can't offer on your own post 
            $result[0]['userid'] == $this->userid 
        ) {
            
            // send them back to their non existant item page 
            $this->app->redirect( 'browse/item/' . $args['id'] );
        }
        
        
        // add the listing result to the page
        $this->view->add('listing_result',$result);
        
        // init attempt variables
        $offer_attempt = false;
        $offer_success = false;
        
        $offer_message = '';
        
        /* -------------------------------------------------
         * Look for a post method to handle offer submision.
         */
        if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            
            
            /* ======================================================
             * Prevent spamming by making sure last 4 offers in a row
             * were not by this user.
             */
             
            // get upper limit
            $upper_limit = $this->app->config->consecutive_offers;
            
            // new selection
            $offers_model = $this->app->model('offers');
            
            // finally execute
            $offers = $offers_model->
                limit($upper_limit)->
                get_item($postid);
            
            // check for bounds
            if( !(count($offers) < $upper_limit) ){
                
                // check each against userid and count
                $found_other_user = -1;
                foreach( $offers as $offer ){
                
                    // if commenter does not match this user
                    if( $offer['userid'] != $this->userid ){
                        
                        // found another, we're good
                        $found_other_user = 1;
                        break;
                    } 
                }
                
                // send error message if bad commentor
                if( $found_other_user < 0 ){
                    // add error subview
                    $subview = array('err_msg');
					$this->view->add('subviews',$subview);
                
                    // send error message
                    $err_msg = 'In an effort to prevent spamming we have 
                        limitted the amount of consecutive comments you 
                        can make on a post. If you think this is a 
                        mistake please contact our staff immediatelly';
                    
                    // add message to page
                    $this->view->add('err_msg', $err_msg);
                    
                    return;
                }
            }
            
            // validate input
            if( !is_numeric( $_POST['offer_price']) ){
                // bad input
                $offer_success = false;
                
                // update message
                $offer_message = 'Offer price must be a numeric value';
            } else {
                
                
                
                /* =========================================
                 * Begin SQL to insert new offer to database
                 */
                
                
                // finally, execute
                $offer_success = $offers_model->
                    new_item($_POST, $postid, $this->userid);
                
                // test for success
                if( $offer_success ){
                    // redirect to item page
                    $this->app->redirect( 'browse/item/'.$args['id'] );
                }
            }
            
            // attempt happened
            $offer_attempt = true;
        }
        
        // add messages/ attmpt info
        $this->view->add('offer_attempt', $offer_attempt );
        $this->view->add('offer_success', $offer_success );
        $this->view->add('offer_message', $offer_message );
        
        
        /* -----------------------
         * Some neat Amazon stuff.
         */
        $response = $this->amazon->medium($result[0]['title']);
            
        // add amazon's response to our view
        $this->view->add('amazon_response',$response);
        
        
        /* -------------------------------------------------------------
         * These next few adds will add the default value to be included
         * for the various inputs in the post page.
         */
        
        
        // get default value for asking price
        $offer_price_value = (isset($_POST['offer_price']) ? 
            'value="'.$_POST['offer_price'] .'"' : 
            ''
        );
        
        // get default value for other offer
        $offer_other_value = (isset($_POST['offer_other']) ? 
            $_POST['offer_other'] : ''
        );
        
        // get default value for description
        $comment_value = (isset($_POST['comment']) ? 
            $_POST['comment'] : ''
        );
        
        
        
        // add all to view
        $this->view->add('offer_price_value', $offer_price_value );
        $this->view->add('offer_other_value', $offer_other_value );
        $this->view->add('comment_value', $comment_value );
        
        // add the subview to view
        $subview = array('listings_offer');
        $this->view->add('subviews',$subview);
        
        // set up form path
        $form_path = $this->app->form_path(
            'listings/newoffer/'.$args['id']
        );
        
        // add to view
        $this->view->add('form_path',$form_path);
        
        // finally set submitbutton text
        $this->view->add('button_text','Submit offer');
    }


    /** Post a new comment to a given listings post and redirects back
     *  to the browse item page upon success.
     *  @param array $args The uri provided arguments passed by the
     *         browser. This action is interested in 'id'.
     */
    function postcomment( $args )
    {
        // first ensure user is logged in, otherwise, bail
        if( !$this->app->user->is_logged_in() ){
            
            // redirect to login
            $this->app->redirect('user/login');
        }
        
        // make sure we're getting post data
        if( $_SERVER['REQUEST_METHOD'] !== 'POST' ){
            
            // redirect back to item page (if id not provided item
            // will redirect back to browse all
            $this->app->redirect('browse/item/'.$args['id']);
        }
        
        // check for valid id
        if( $args['id'] === null || !is_numeric($args['id'] ) ){
            
            // redirect to browse all
            $this->app->redirect('browse');
        }
        
        // ok get id
        $postid = (int) $args['id'];
        
        
        /* ========================================
         * SQL Query to check for post with this id
         */
         
        // new selection
        $listings_model = $this->app->model('listings');
        
        // get result
        $result = $listings_model->limit(1)->get_item($postid);
        
        // if not results, go elsewhere
        if( count($result) < 1 ) {
            
            // send them back to their non existant item page 
            $this->app->redirect( 'browse/item/' . $args['id'] );
        }
        
        
        /* ========================================================
         * Prevent spamming by making sure last 5 comments in a row
         * were not by this user.
         */
         
        // get upper limit
        $upper_limit = $this->app->config->consecutive_comments;
        
        /* ==============
         * Comments model
         */
        
        // new selection
        $comments_model = $this->app->model('comments');
        
        // finally execute
        $comments = $comments_model->
            limit($upper_limit)->
            get_item( $postid );
        
        // check for bounds
        if( !(count($comments) < $upper_limit) ){
            
            // check each against userid and count
            $found_other_user = -1;
            foreach( $comments as $comment ){
            
                // if commenter does not match this user
                if( $comment['userid'] != $this->userid ){
                    
                    // found another, we're good
                    $found_other_user = 1;
                    break;
                } 
            }
            
            // send error message if bad commentor
            if( $found_other_user < 0 ){
                // add error subview
                $subview = array('err_msg');
				$this->view->add('subviews',$subview);
            
                // send error message
                $err_msg = 'In an effort to prevent spamming we have 
                    limitted the amount of consecutive comments you can
                    make on a post. If you think this is a mistake 
                    please contact our staff immediatelly';
                     
                $this->view->add('err_msg', $err_msg);
                
                return;
            }
        }
        
        // load the comment form
	    // load comment form
	    $comment_form = $this->view->
	        form('comment','listings/postcomment/'.$postid);
        
        // ok begin processing
        // with validation
        if( !$comment_form->validate( $err_msg ) ){
            // just don't do it.
            // $this->app->redirect('browse/item/'.$args['id']);
            // add error subview
            $subview = array('err_msg');
            $this->view->add('subviews',$subview);
            $this->view->add('err_msg',$err_msg);
            return;
        }
        
        // insert new comment
        $success = $comments_model->new_item(
            $_POST,         // values 
            $postid,        // listingid
            $this->userid,  // userid
            // set default title
            'RE: ' . $result[0]['title']
        );
        
        
        // test for success
        if( !$success ){
        
            // add error subview
            $subview = array('err_msg');
            $this->view->add('subviews',$subview);
        
            // send error message
            $err_msg = 'There was an error adding your comment.
                 Please report this incident to our staff. Thank you';
                 
            $this->view->add('err_msg', $err_msg);
            
        } else {
        
            // all is well, send them back
            $this->app->redirect('browse/item/'.$args['id']);
        }
        
    }

    
    /** Marks the specified OFFER as one of the best offers for this
     *  post. 
     *  This action will first prompt the user to confirm that this is
     *  a best offer and they will have to accept, then a GET request
     *  is sent to this same page with confirmation. 
     *  If the specified offer is already a best offer the user will be
     *  prompted to unmark it.
     *  @param array $args The uri provided arguments passed by the
     *         browser. This action is interested in 'id'.
     */
    function bestoffer( $args ) {
        // first make sure the user is logged in
        if( !$this->app->user->is_logged_in() ){
            // get them to login page
            $this->app->redirect('user/login');
        }
        
        // define err_msg
        $err_msg = <<< ERRMSG
        Sorry, your request does not seem to match any active offers in 
        our database.
ERRMSG;
        
        // check valid input
        if( !isset($_GET['lid']) && !isset($_GET['oid']) ){
            
            // send them error message
            $this->view->add('subviews',array('err_msg'));
            $this->view->add('err_msg',$err_msg);
            
            // and end
            return;
        }
        
        // get offer id
        $listingid = $_GET['lid'];
		$this->view->add('listingid',$listingid);
		$offerid = $_GET['oid'];
		$this->view->add('offerid',$offerid);
        
        /* ===============================
         * SQL Query to get selected offer
         */
        
        // offer model
        $offers_model = $this->app->model('offers');
        $offer_result = $offers_model->limit(1)->
            get_item_any($listingid,$offerid);
        // check result
        if( count( $offer_result ) < 1 ){
            // send them error message
            $this->view->add('subviews',array('err_msg'));
            $this->view->add('err_msg',$err_msg);
            // and end
            return;
        }
        
        /* ==========================================
         * SQL Statement to verify the listing exists
         */
        $listings_model = $this->app->model('listings');
        $listing_result = $listings_model->limit(1)->
            get_item($listingid);
        
        // check
        if( count($listing_result) < 1 ||
            $listing_result[0]['userid'] != $this->userid){
            // send them error message
            $this->view->add('subviews',array('err_msg'));
            $this->view->add('err_msg',$err_msg);
            return;
        }
               
        /* NOW check for GET confirmation post */
        if( isset($_GET['confirm'])  ){
            // finally execute the statement and check for failure
			$offers_model->unset_best($listingid);
            if( !$offers_model->set_best( $listingid,$offerid) ){
                // update failed, give them an error message
                $this->view->add('subviews',array('err_msg'));
                // add err msg
                $this->view->add('err_msg','Uh oh. There was an error
                while updating our database. Please report this to our
                staff so we can fix this for you.');
                return;
            }
            
            // finally redirect back to item
            $this->app->redirect( 'browse/item/' . 
                $offer_result[0]['listingid'] 
            );
        }
        
        // this view
        $this->view->add('page_title','Mark Best Offer');        
        // add action message to view
        $this->view->add('action_message',
            'You are about to mark this offer as a best offer:'
        );        
        // add self link information
        $this->view->add('self_link',
            $this->app->form_path('listings/bestoffer/')
        );        
        // add cancel path
        $this->view->add('cancel_link',
            $this->app->form_path('browse/item/'.
                $offer_result[0]['listingid']
            )
        );        
        // add offer results to the view
        $this->view->add( 'offer_result', $offer_result );
        // add subview
        $this->view->add('subviews',array('listings_offeraction'));
    }
    
     
    /** Marks the specified OFFER as being declined
     *  This action will first prompt the user to confirm that this is
     *  an action they want and they will have to accept, then a GET 
     *  request is sent to this same page with confirmation.
     *  @param array $args The uri provided arguments passed by the
     *         browser. This action is interested in 'id'.
     */
    function declineoffer( $args ) {
        
        // first make sure the user is logged in
        if( !$this->app->user->is_logged_in() ){
            
            // get them to login page
            $this->app->redirect('user/login');
        }
        
        // define err_msg
        $err_msg = <<< ERRMSG
        Sorry, your request does not seem to match any active offers in 
        our database.
ERRMSG;
        
        // check valid input
        if( !isset($_GET['lid']) && !isset($_GET['oid'])){
            
            // send them error message
            $this->view->add('subviews',array('err_msg'));
            $this->view->add('err_msg',$err_msg);
            
            // and end
            return;
        }
        $this->view->add('page_title','Reject Offer');
        // get offer id
        $listingid = $_GET['lid'];
		$this->view->add('listingid',$listingid);
		$offerid = $_GET['oid'];
		$this->view->add('offerid',$offerid);
		
		
        /* ===============================
         * SQL Query to get selected offer
         */
        
        // offer model
        $offers_model = $this->app->model('offers');
        $offer_result = $offers_model->limit(1)->
            get_item_any($listingid,$offerid);
        // check result
        if( count( $offer_result ) < 1 ){
            // send them error message
            $this->view->add('subviews',array('err_msg'));
            $this->view->add('err_msg',$err_msg);
            // and end
            return;
        }
        
        /* ==========================================
         * SQL Statement to verify the listing exists
         */
        $listings_model = $this->app->model('listings');
        $listing_result = $listings_model->limit(1)->
            get_item($listingid);
        
        // check
        if( count($listing_result) < 1 ||
            $listing_result[0]['userid'] != $this->userid
        ){
            // send them error message
            $this->view->add('subviews',array('err_msg'));
            $this->view->add('err_msg',$err_msg);
            return;
        }
        
        /* NOW check for GET confirmation post */
        if( isset($_GET['confirm']) ){
            $offers_model->unset_single_best($offerid);
             // finally execute the statement and check for failure
            if( !$offers_model->set_item( $offerid, 
                GarageSale\BaseDatabase::STATUS_DECLINED) 
            ){
                
                // update failed, give them an error message
                $this->view->add('subviews',array('err_msg'));
                
                // add err msg
                $this->view->add('err_msg','Uh oh. There was an error
                while updating our database. Please report this to our
                staff so we can fix this for you.');
                return;
            }
            
            // finally redirect back to item
            $this->app->redirect( 'browse/item/' . 
                $offer_result[0]['listingid'] 
            );
        }
        
        // add action message to view
        $this->view->add('action_message',
            'You are about to decline this offer:'
        );
        // add self link information
        $this->view->add('self_link',
            $this->app->form_path('listings/declineoffer/')
        );
        // add cancel path
        $this->view->add('cancel_link',
            $this->app->form_path('browse/item/'.
                $offer_result[0]['listingid']
            )
        );
        // add offer results to the view
        $this->view->add( 'offer_result', $offer_result );
        // add subview
        $this->view->add('subviews',array('listings_offeraction'));
    }
    
     
    /** Marks the specified OFFER as being accepted
     *  This action will first prompt the user to confirm that this is
     *  an actio nthey want and they will have to accept, then a GET 
     *  request is sent to this same page with confirmation. 
     *  @param array $args The uri provided arguments passed by the
     *         browser. This action is interested in 'id'.
     */
    function acceptoffer( $args ) {
        
        // first make sure the user is logged in
        if( !$this->app->user->is_logged_in() ){
            // get them to login page
            $this->app->redirect('user/login');
        }
        
        // define err_msg
        $err_msg = <<< ERRMSG
        Sorry, your request does not seem to match any active offers in 
        our database.
ERRMSG;
        
        // check valid input
        if( !isset($_GET['lid']) && !isset($_GET['oid']) ){
            // send them error message
            $this->view->add('subviews',array('err_msg'));
            $this->view->add('err_msg',$err_msg);
            return;
        }
        
        $this->view->add('page_title','Accept Offer');
        // get offer id
        $listingid = $_GET['lid'];
		$this->view->add('listingid',$listingid);
		$offerid = $_GET['oid'];
		$this->view->add('offerid',$offerid);
		
        /* ===============================
         * SQL Query to get selected offer
         */
        
        // offer model
        $offers_model = $this->app->model('offers');
        $offer_result = $offers_model->limit(1)->
            get_item_any($listingid,$offerid);
        // check result
        if( count( $offer_result ) < 0 ){
            // send them error message
            $this->view->add('subviews',array('err_msg'));
            $this->view->add('err_msg',$err_msg);
            // and end
            return;
        }
        
        /* ==========================================
         * SQL Statement to verify the listing exists
         */
        $listings_model = $this->app->model('listings');
        $listing_result = $listings_model->limit(1)->get_item($listingid);
        
        // check
        if( count($listing_result) < 1 ||
            $listing_result[0]['userid'] != $this->userid
        ){
            // send them error message
            $this->view->add('subviews',array('err_msg'));
            $this->view->add('err_msg',$err_msg);
            return;
        }
               
        /* NOW check for GET confirmation post */
        if( isset($_GET['confirm']) ){
            
             // finally execute the statement and check for failure
            if( !$offers_model->set_accepted($offerid)){
                
                // update failed, give them an error message
                $this->view->add('subviews',array('err_msg'));
                
                // add err msg
                $this->view->add('err_msg','Uh oh. There was an error
                while updating our database. Please report this to our
                staff so we can fix this for you.');
                return;
            }
           $listings_model->set_accepted( $listingid );
            // finally redirect back to item
            $this->app->redirect( 'browse/sold/' . 
                $offer_result[0]['listingid'] 
            );
        }

        // add action message to view
        $this->view->add('action_message',
            'You are about to accept this offer:'
        );
        // add self link information
        $this->view->add('self_link',
            $this->app->form_path('listings/acceptoffer/'.$args['id'])
        );
        // add cancel path
        $this->view->add('cancel_link',
            $this->app->form_path('browse/item/'.
                $offer_result[0]['listingid']
            )
        );
        // add offer results to the view
        $this->view->add( 'offer_result', $offer_result );
        // add subview
        $this->view->add('subviews',array('listings_offeraction'));
    }
}

?>
