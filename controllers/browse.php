<?php
/** controllers/browse.php
 *  The browse controller will allow users to look through listings on 
 *  the site in order to find products.
 *
 *  This controller should only display content. All content updates
 *  should go throught the listings controller.
 */
class browse extends GarageSale\Controller {
    
    /** This before function is used to provide the browse views info
     *  details on the currently logged in users.
     *  @param $args The standard controller arguments.
     */
    function before( $args )
    {
        // initiate conenction to databse if need be
        $this->app->db->connect();
        
        // explicitly set the view
        $this->view->new_view('layout');
        
        // set default page title
        $this->page_title = "Browse the Garage";
        $this->view->add("page_title",$this->page_title);
        
        // if logged in, tell the view
        if( $this->app->user->is_logged_in() ){
            // add it to the view
            $this->view->add(
                'username',
                $this->app->user->get_user_name()
            );
            // add id to the view
            $this->view->add(
                'userid',
                $this->app->user->get_user_id()
            );
        }
        
        // get settings for the browse controller
        $this->settings = $this->app->settings('browse');
    }
    
    
    /** all() is the default function to be called for when navigating
     *  to this contorller. It will look through every active posting
     *  in the Garage Sale database and make it available for listing.
     *  @param array $args The array containing page arguments. all() 
     *         will only require the 'page' argument.
     */
    function all( $args )
    {   
        // set the appropriate view
		$subview = array('browse_result');
        $this->view->add('subviews',$subview);
        
        
        /* ====================== 
         * Get the listings model 
         */
        $model = $this->app->model('listings');
        
        // get from settings, 10 as default
        $limit = $this->settings->get('per_page');
        $limit = ( $limit != null ) ? $limit : 10;
        
        // find if user has decided on some number already
        if( isset( $_SESSION['listings_per_page'] )){
            $limit = (int)$_SESSION['listings_per_page'];
            
        }
        
        // get the page number
        $page = 0;
        // convert to integer
        if( $args['page'] != null && is_numeric($args['page']) ){
            $page = ((int) $args['page'])-1;
        }
        
        
        // get result
        $result = $model->limit($limit)->page($page)->get_all();
        
        
        $count = $model->count_all();
        // set up counts
        $paginate = array(
            'page_count' => ((int)ceil($count/$limit)),
            'this_page'  => $page+1
        );
        
        // add page count
        $this->view->add('paginate',$paginate);
        
        // add teh page action to the view
        $this->view->add('page_action',
            $this->app->form_path('browse/all') );
        
        // add the result to the view
        $this->view->add('listing_results', $result);
    }
        
    /** search() returns records which have a simple pattern match to
     *  the search parameters passed in from the search bar.
     *  @param array $args The array containing page arguments. all() 
     *         will only require the 'page' argument.
     */
    function search( $args )
    {
		//Load the browse_result subview to present search results
		$subview = array('browse_result');
        $this->view->add('subviews',$subview);
		
		//Set the term $searching as the users search parameters if set
		if(isset($_GET['item-search'])){
			$searching = $_GET['item-search'];
			
			// set the page_title for a search
			$this->view->add('page_title','Searching the Garage for : '. 
			    $_GET['item-search']);
		}else{
			$searching = null;
		}
		
		//If the user doesn't input anything, notify the view
		if($searching=='' || $searching==null || trim($searching)==''){
			$result=null;
			$this->view->add('result',$result);
			
		}else{
			// Add it to the view to display the users search terms back 
			// to them
			$this->view->add('searching',$searching);
			
			
            /* ====================== 
             * Get the listings model 
             */
            $model = $this->app->model('listings');
					
			
            // get from settings, 10 as default
            $limit = $this->settings->get('per_page');
            $limit = ( $limit != null ) ? $limit : 10;
			// find if user has decided on some number already
			if( isset( $_SESSION['listings_per_page'] )){
				$limit = (int)$_SESSION['listings_per_page'];
			}
			
			// get the page number
			$page = 0;
			// convert to integer
			if( $args['page'] != null && is_numeric($args['page']) ){
				$page = ((int) $args['page'])-1;
			}
			
			
			//Produce the result of the search given the searching terms
			$result = $model->limit($limit)->page($page)->
			    get_search( $searching );
			
			$count = $model->count_search($searching);
			// set up counts
			$paginate = array(
				'page_count' => ((int)ceil($count/$limit)),
				'this_page'  => $page+1
			);
			
			// add page count
			$this->view->add('paginate',$paginate);
			
			// add teh page action to the view
			$this->view->add('page_action',
				$this->app->form_path('browse/search') );
			
			// add action extra
			if( isset($_GET['item-search']) ){
				$this->view->add('action_extra','?item-search='.
					$_GET['item-search']);
			}
			
			// add the result to the view
			$this->view->add('listing_results', $result);
        }
		
    }
    
    /** This action will browse the garage given a specified category
     *  @param $args An array of the arguments provided in the url path.
     *         This controller is interested in both $args['page'] and
     *         $args['category'] but they show up out of order( i.e.
     *         page is provided in the path first, action should handle)
     */
    function category( $args )
    {
        // set the subview, all, category, and user should use the same
        $subview = array('browse_result');
        $this->view->add('subviews',$subview);
    
        // do some fancy pantsy switching to get the right args right
        $tmp = $args['page'];
        $args['page'] = $args['category'];
        $args['category'] = $tmp;
        
        // check if a category is provided
        if( $args['category'] == null ){
            // just use all because there is no category
            $this->all( $args );
            
            // do nothing else
            return;
        }
		
		
        $category_model = $this->app->model('categories');
		$exists = $category_model->category_exists($args['category']);
        if($exists != null){
			// get from settings, 10 as default
			$limit = $this->settings->get('per_page');
			$limit = ( $limit != null ) ? $limit : 10;
			
			// find if user has decided on some number already
			if( isset( $_SESSION['listings_per_page'] )){
				$limit = $_SESSION['listings_per_page'];
			}
			
			// init page number
			$page = 0;
			// find page
			if( $args['page'] != null && is_numeric($args['page'])  ){
				$page = ((int) $args['page'])-1;
			}
			
			
			// initialize category id to 0
			$category_id = 0;
			
			// initialize category name
			$category = 'Not found';
			
			
			/* ==================
			 * Get category model
			 */
			
			// get category return results
			$cat_result = $category_model->limit(1)->
				get_category( $args['category']);
					
			// check for results
			if( count($cat_result) > 0 ){
			
				// get the id
				$category_id = (int)$cat_result[0]['id'];
				
				// get the display name
				$category = $cat_result[0]['display_name'];         
			}
			
			// set page title
			$this->view->add('page_title',"Browsing category '$category'");
			
			// Ok. Now get all the children and stuff
			$cat_result[0]['child_item'] = $category_model->limit(-1)->
				get_category_children($category_id);
			
			/* ======================
			 * Get the Listings model
			 */
			$listing_model = $this->app->model('listings');
			
			// get categories result
			$result = $listing_model->limit($limit)->page($page)->
				get_categories( $cat_result );
			
			
			// add category name to the view
			$this->view->add('category', $category);
			
			// add category's display name to the view
			$this->view->add('category_display_name', $args['category']);
			
			
			
			$count = $listing_model->count_categories($cat_result);
			// set up counts
			$paginate = array(
				'page_count' => ((int)ceil($count/$limit)),
				'this_page'  => $page+1
			);
			
			// add page count
			$this->view->add('paginate',$paginate);
			
			// add teh page action to the view
			$this->view->add('page_action', $this->app->form_path(
				'browse/category/'.$args['category']) 
			);
			
			
			// add results to the view
			$this->view->add('listing_results', $result);
		}else{
			$this->view->add('page_title','Category "' . $args['category'] . '" not found');
		}
    }
    
    
    
    /** This action will browse the garage given a specified user
     *  @param array $args the arguments provided in the url path.
     *         This controller is interested in poth $args['page'] and
     *         $args['category'] but they show up out of order( i.e.
     *         page is provided in the path first, action should handle)
     *         Finally, category argument works as the user id
     */
    function user( $args )
    {
        // set the subview, all, category, and user should use the same
        $subview = array('browse_result');
        $this->view->add('subviews',$subview);
        
        // do some fancy pantsy switching to get the right args right
        $tmp = $args['page'];
        $args['page'] = $args['category'];
        $args['category'] = $tmp;
        
        
        // check if a user is provided
        if( $args['category'] == null ){
            // just use all because there is no user
            $this->all( $args );
            
            // do nothing else
            return;
        }
		$users_model = $this->app->model('users');
		$result = $users_model->user_exists($args['category']);
        //Check if id is valid
        if($result!=null){
			/* -------------------------------------------------------------
			 * Distinguish these two variables from userid and username
			 * which identify the logged in user while user_id and user_name
			 * is for this searched user.
			 */
			
			// user the user class to convert a name to an id, or just get
			// id if it is already a number, -1 on fail
			$user_id = $this->app->user->id_from_name($args['category']);
					
			// user the user class to convert a name to an id, or just get
			// id if it is already a number, -1 on fail
			$user_name = $this->app->user->name_from_id($user_id);
			
			// set page title
			$this->view->add('page_title','Listings by ' . $user_name);
			
			
			// get from settings, 10 as default
			$limit = $this->settings->get('per_page');
			$limit = ( $limit != null ) ? $limit : 10;
			
			// find if user has decided on some number already
			if( isset( $_SESSION['listings_per_page'] )){
				$limit = $_SESSION['listings_per_page'];
			}
			
			// init page number
			$page = 0;
			// find page
			if( $args['page'] != null && is_numeric($args['page'])  ){
				$page = ((int) $args['page'])-1;
			}
			
			// initialize results
			$result = null;
			
			
			// initialize category name
			$user_name = 'Not found';
			
			
			/* ===============================
			 * Get the listings model for this
			 */
			
			$model = $this->app->model('listings');
			
			$result = $model->limit($limit)->page($page)->
				get_user( $user_id );
			
			
			$count = $model->count_user($user_id);
			
			// set up counts
			$paginate = array(
				'page_count' => ((int)ceil($count/$limit)),
				'this_page'  => $page+1
			);
			
			// add page count
			$this->view->add('paginate',$paginate);
			
			// add teh page action to the view
			$this->view->add('page_action', $this->app->form_path(
				'browse/user/'.$user_id) 
			);
			
			
			
			// add category name to the view
			$this->view->add('user_name', $user_name);
			
			// add user id to view
			$this->view->add('user_id', $user_id);
			
			// add results to the view
			$this->view->add('listing_results', $result);
		}else{
			$this->view->add('listing_results', null);
		}
    }
    
    
    
    
    /** Display a particular record in our database
     *  @param array $args Contains the argument information passed in
     *         from the uri through the router. Will need id which will
     *         take the place of page
     */
    function item( $args )
    {
        
        // if post id is null just display all
        if( $args['page'] === null || !is_numeric($args['page']) ){
        
            // redirect to browse all
            $this->app->redirect( 'browse/all' );
        }
             
        // get the post id from the page number
        $postid = (int)$args['page'];
        
        
        // initialize results
        $result = null;
        
        
        /* ======================
         * Get the listings model
         */
        $listing_model = $this->app->model('listings');
        
        
        // get the result
        $result = $listing_model->limit(1)->get_item( $postid );
        
                
        // add results to the view
        $this->view->add('listing_result', $result);
        
        /* Check for listing result */
        if( count($result) < 1 ){
            
            // item is not available, update title
            $this->view->add('page_title','Item Unavailable');
            
            // add error message
            $this->view->add('err_msg',
                'It does not appear that this item is in our garage...'
            );
            
            // add err_msg subview to include
            $this->view->add('subviews',array('err_msg'));
            
            // end 
            return;
        }
        
        // update the page title
        $this->view->add('page_title',
            $result[0]['title']
        );
        
        // set the self referencing links
        $this->view->add('self_link',
            $this->app->form_path('browse/item/'.$postid)
        );
        
        
        
        // init accepted to false
        $accepted = false;
        
        /* ===========================================
         * Load offers model to get offers for listing
         */
        
        // start the selection
        $offer_model = $this->app->model('offers');
		$limit = 10;
		$offer_page = 0;
        
        // add a test if this listing has already been accepted
        if( $result[0]['status'] ==
            GarageSale\BaseDatabase::STATUS_ACCEPTED 
        ){
            
            // get offer result
            $offers_result = $offer_model->limit(1)->get_accepted( 
                (int) $result[0]['id']
            );
                
            // set accepted flag to true
            $accepted = true;
            
        } else {
            
            
            // limit to 10 displayed at a time
            $limit = 10;
            
            // default offer_page to 0
            $offer_page = 0;
            
            // set up offers page
            if( isset( $_GET['offerpage'] ) && 
                is_numeric($_GET['offerpage']) 
            ){
                
                // set offer_page to the user provided value
                $offer_page = ((int) $_GET['offerpage'] ) - 1;
            }
            
            $offers_result = $offer_model->limit($limit)->
                page($offer_page)->
                get_item_any_offer( (int) $result[0]['id'] );
        }
        
        
        // add to page
        $this->view->add( 'offers_result', $offers_result );
        
        
        
        /* =======================================
         * SQL Query to count the number of offers
         */
        
        if( $accepted === true ){
        
            // where accepted
            $offer_count_res = $offer_model->get_count($postid,
                GarageSale\BaseDatabase::STATUS_ACCEPTED );
        } else {
            
            // and are active
            $offer_count_res = $offer_model->get_count($postid,
                GarageSale\BaseDatabase::STATUS_ACTIVE );
        }
        
        // get the count
        $offer_count = (int)$offer_count_res[0]['id'];
        
        // calc offset
        $offset = $limit * $offer_page;
        
        // add comment count info to view
        $this->view->add( 'offer_count',
            array(
                // total numbr of offers that have been made
                'total' => $offer_count,
                
                // where this set of offers starts
                'begin' => ($offer_count > 0 ) ? $offset+1 : 0,
                
                // where this set of offers end
                'end'   => $offset + count($offers_result),
                
                // how many are selected per set
                'per'   => $limit
            )
        );
        
        
        
        /* ======================
         * Use the comments model
         */
        $comment_model = $this->app->model('comments');
        
        
        // limit to 10 for now, option for more later
        $limit = 10;
        
        // default comment page number is 0
        $comment_page = 0;
        // get comment page value
        if( isset($_GET['commentpage']) && 
            is_numeric( $_GET['commentpage'])
        ){
            // convert to int and is one less than displayed.
            $comment_page = ((int) $_GET['commentpage']) - 1;
        }
        
        
        // get comment results
        $comment_result = $comment_model->limit($limit)->
            page($comment_page)->get_item( $postid );
        
        
        // add comment results to page
        if( count($comment_result) > 0 ){
        
            // add comment result response
            $this->view->add( 'listing_comments', $comment_result );
        } else {
            
            // add null for comment listings
            $this->view->add( 'listing_comments', null );
        }
        
        
        
        
        /* =========================================
         * SQL Query to count the number of comments
         */
        $comment_count_res = $comment_model->get_count($postid);
        
        // get the count
        $comment_count = (int)$comment_count_res[0]['id'];
                
        // calc offset
        $offset = $limit * $comment_page;
        
        // add comment count info to view
        $this->view->add( 'comment_count',
            array(
                // total numbr of comments that have been made
                'total' => $comment_count,
                
                // where this set of comments starts
                'begin' => ($comment_count > 0) ? $offset+1 : 0,
                
                // where this set of comments end
                'end'   => $offset + count($comment_result),
                
                // how many are comments per set
                'per'   => $limit
            )
        );
        
        
	    // get wysiwyg extension
	    $wysiwyg = $this->app->extension('wysiwyg');
	    
	    // load comment form
	    $comment_form = $this->view->
	        form('comment','listings/postcomment/'.$postid);
	    $this->view->add('comment_form',$comment_form);
	    
	    
        /* -----------------------
         * Some neat Amazon stuff.
         */
         
        // require needed libraries
        $this->app->library('AmazonIntegration');
        
        $this->amazon = new \AmazonFetcher();
        
        // get response from amazon
        $response = $this->amazon->medium($result[0]['title']);
            
        // add amazon's response to our view
        $this->view->add('amazon_response',$response);
		//Get users ID
		$row = $result[0];
		$sellerid = $row['userid'];
		
		//Get rating and number of reviews for retrieved user id
		$reviews_model = $this->app->model('reviews');
		$rating = $reviews_model->get_avg_reviews($sellerid);
		$review_count_res = $reviews_model->get_count($sellerid);
		$review_count = $review_count_res[0]['id'];
		
		//Add information to view
		$this->view->add('rating_count',$review_count);
		//$this->view->add('scripts',array('star-review'));
		if($rating[0]['rating_average'] != null){
			$this->view->add('rating',$rating[0]['rating_average']);
		}else{
			$this->view->add('rating',0);
		}
        
    }
	
	/** Display a particular record in our database that has been sold
     *  @param array $args Contains the argument information passed in
     *         from the uri through the router. Will need id which will
     *         take the place of page
     */
    function sold( $args )
    {
        
        
        // if post id is null just display all
        if( $args['page'] === null || !is_numeric($args['page']) ){
        
            // redirect to browse all
            $this->app->redirect( 'browse/all' );
        }
             
        // get the post id from the page number
        $postid = (int)$args['page'];
        
        
        // initialize results
        $result = null;
        
        
        /* ======================
         * Get the listings model
         */
        $listing_model = $this->app->model('listings');
        
        
        // get the result
        $result = $listing_model->limit(1)->get_sold_item( $postid );
        
                
        // add results to the view
        $this->view->add('listing_result', $result);
        
        /* Check for listing result */
        if( count($result) < 1 ){
            
            // item is not available, update title
            $this->view->add('page_title','Item Unavailable');
            
            // add error message
            $this->view->add('err_msg',
                'It does not appear that this item is in our garage...'
            );
            
            // add err_msg subview to include
            $this->view->add('subviews',array('err_msg'));
            
            // end 
            return;
        }
        
        // update the page title
        $this->view->add('page_title',
            $result[0]['title']
        );
        
        // set the self referencing links
        $this->view->add('self_link',
            $this->app->form_path('browse/item/'.$postid)
        );
        
        
        
        // init accepted to false
        $accepted = false;
        
        /* ===========================================
         * Load offers model to get offers for listing
         */
        
        // start the selection
        $offer_model = $this->app->model('offers');
        
        
            
            
		// limit to 10 displayed at a time
		$limit = 10;
		
		// default offer_page to 0
		$offer_page = 0;
		
		// set up offers page
		if( isset( $_GET['offerpage'] ) && 
			is_numeric($_GET['offerpage']) 
		){
			
			// set offer_page to the user provided value
			$offer_page = ((int) $_GET['offerpage'] ) - 1;
		}
		
		$offers_result = $offer_model->limit($limit)->
			page($offer_page)->
			get_item_any_offer( (int) $result[0]['id'] );
        
        
        
        // add to page
        $this->view->add( 'offers_result', $offers_result );
        
        
        
        /* =======================================
         * SQL Query to count the number of offers
         */
        
        if( $accepted === true ){
        
            // where accepted
            $offer_count_res = $offer_model->get_count($postid,
                GarageSale\BaseDatabase::STATUS_ACCEPTED );
        } else {
            
            // and are active
            $offer_count_res = $offer_model->get_count($postid,
                GarageSale\BaseDatabase::STATUS_ACTIVE );
        }
        
        // get the count
        $offer_count = (int)$offer_count_res[0]['id'];
        
        // calc offset
        $offset = $limit * $offer_page;
        
        // add comment count info to view
        $this->view->add( 'offer_count',
            array(
                // total numbr of offers that have been made
                'total' => $offer_count,
                
                // where this set of offers starts
                'begin' => ($offer_count > 0 ) ? $offset+1 : 0,
                
                // where this set of offers end
                'end'   => $offset + count($offers_result),
                
                // how many are selected per set
                'per'   => $limit
            )
        );
        
        
        
        /* ======================
         * Use the comments model
         */
        $comment_model = $this->app->model('comments');
        
        
        // limit to 10 for now, option for more later
        $limit = 10;
        
        // default comment page number is 0
        $comment_page = 0;
        // get comment page value
        if( isset($_GET['commentpage']) && 
            is_numeric( $_GET['commentpage'])
        ){
            // convert to int and is one less than displayed.
            $comment_page = ((int) $_GET['commentpage']) - 1;
        }
        
        
        // get comment results
        $comment_result = $comment_model->limit($limit)->
            page($comment_page)->get_item( $postid );
        
        
        // add comment results to page
        if( count($comment_result) > 0 ){
        
            // add comment result response
            $this->view->add( 'listing_comments', $comment_result );
        } else {
            
            // add null for comment listings
            $this->view->add( 'listing_comments', null );
        }
        
        
        
        
        /* =========================================
         * SQL Query to count the number of comments
         */
        $comment_count_res = $comment_model->get_count($postid);
        
        // get the count
        $comment_count = (int)$comment_count_res[0]['id'];
                
        // calc offset
        $offset = $limit * $comment_page;
        
        // add comment count info to view
        $this->view->add( 'comment_count',
            array(
                // total numbr of comments that have been made
                'total' => $comment_count,
                
                // where this set of comments starts
                'begin' => ($comment_count > 0) ? $offset+1 : 0,
                
                // where this set of comments end
                'end'   => $offset + count($comment_result),
                
                // how many are comments per set
                'per'   => $limit
            )
        );
        
        
        /* -----------------------
         * Some neat Amazon stuff.
         */
         
        // require needed libraries
        $this->app->library('AmazonIntegration');
        
        $this->amazon = new \AmazonFetcher();
        
        // get response from amazon
        $response = $this->amazon->medium($result[0]['title']);
            
        // add amazon's response to our view
        $this->view->add('amazon_response',$response);
		
		//Get users ID
		$row = $result[0];
		$sellerid = $row['userid'];
		
		//Get rating and number of reviews for retrieved user id
		$reviews_model = $this->app->model('reviews');
		$rating = $reviews_model->get_avg_reviews($sellerid);
		$review_count_res = $reviews_model->get_count($sellerid);
		$review_count = $review_count_res[0]['id'];
		
		//Add information to view
		$this->view->add('rating_count',$review_count);
		//$this->view->add('scripts',array('star-review'));
		if($rating[0]['rating_average'] != null){
			$this->view->add('rating',$rating[0]['rating_average']);
		}else{
			$this->view->add('rating',0);
		}
        
    }
}

?>
