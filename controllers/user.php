<?php

    require "passwordPHP.php";

/** controllers/user.php
 *  Ther user controller handles all actions regarding front end access
 *  of user information. Search for user, read reviews, see posts, 
 *  comment histories, purchases, profile, etc.
 */

class user extends GarageSale\Controller {

    /** The before function is run before an action is called and is 
     *  usually used to set up a view.
     */
    function before($args){
        $this->app->db->connect();
        
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
        
        // add registration style
        $this->view->add_style('registration');
    }
   
    /** 
     * This function will hash the password of the loged in user
     */
	function hashPassword($args){
		$id = $this->app->user->get_user_id();	
		echo $id; 

  		$stmt0 = $this->app->db->select('users');
	        $stmt0->where('id', 'i', $id ); //id is a VARCHAR
	        $cat_result = $this->app->db->statement_result( $stmt0 );

		// new select statement
	        $stmt = $this->app->db->update('users');
		     
	        // set the where for category name
	        $stmt->where('id', 'i', $id ); //id is a VARCHAR
	        
	        // get category return results

		$pass = $cat_result[0]['password'];
		
   		$hash = password_hash($pass, PASSWORD_BCRYPT); //save the user's password and $hash

		$stmt->values( array (
                 	 array(
                    	'name'  => 'password',
                    	'value' => $hash,
                    	'type'  => 's'
                    	)
            	) );
            $success = $this->app->db->statement_execute($stmt);

	}
    
    
    /** 
     *	Login page. 
     *  @param $args Array containing argument information
     */
    function login( $args ){
        
        /* =============================================
         * Look for login request. Do login if present.
         */
        if($this->app->user->is_logged_in()){
			$this->app->redirect('user/profile');
		}
		$this->view->add('page_title','Login');
        // add the form path to the view
        $form_path = $this->app->form_path('user/login');
        $this->view->add('form_path',$form_path);
        $this->doLogin($args); 
        //redirect to doLogin to perform the actions of the form
    }

     /** 
     *	Login page. Called when either one of the inputs is incorrect. 
     *  @param $args Array containing argument information
     */
    function loginFail( $args ){
        
        /* =============================================
         * Look for login request. Do login if present.
         */
        
        // add the form path to the view
        $form_path = $this->app->form_path('user/login');
        $this->view->add('form_path',$form_path);
    }

   /** Action for login.
     *  
     *  @param $args Array containing argument information.
     */
    function doLogin( $args ){
        
        /* =============================================
         * Look for login request. Do login if present.
         */
	        
     if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
	        $username = $_POST['username'];
	        $password = $_POST['password'];
	            
	        // new select statement
	        $stmt = $this->app->db->select('users');
		     
	        // set the where for category name
	        $stmt->where('handle', 's', $username ); //id is a VARCHAR
	        
	        // get category return results
	        $cat_result = $this->app->db->statement_result( $stmt );
		     
	         if ($cat_result != null){
	            // get the user's password if the statement was found 
	            $passwordDB = $cat_result[0]['password'];
	            $isPasswordCorrect = password_verify ($password, $passwordDB);
	         }
        		
        		/*else if ($cat_result == null){
        			$this->printinRed( "Null result");
        		}*/
     		
        	
//            if(isset($passwordDB) && $password == $passwordDB){
            if($isPasswordCorrect){
				//Check if user is verified first
				$id = (int)$cat_result[0]['id'];
				$activation_model = $this->app->model('activation');
				$result_count_res= $activation_model->check_user((int)$id);
				$count =(int)$result_count_res[0]['id'];
				$hash = null;
				//User has not verified their account yet
				if($count != null && $count > 0){
					$result = $activation_model->get_all((int)$id);

					if($result !=null){
						$hash = $result[0]['hash'];
						//Redirect to activation page
						$this->app->redirect('user/activation?id=' . (int)$id . "&hash=" . $hash);
					}else{
						$this->app->redirect('user/activation?id=' . (int)$id);
					}
					
					
				//Log the user in
				}else{
				
					$this->app->user->user_login($username, $password);
					$this->app->redirect('user/profile');
				}
            }
            
             else{
		//Values do not match redirect back to login page
            	$this->app->redirect('user/loginFail');
		
            }
        }
        
    }

    /** Returns the last id of a given table.
     *
     *  ID is supposed to be auto-incremented.
     *
     *  @param $table_name table anme.
     *  @return the last id used.
     */
    function getLastID ($table_name) {
        $stmt = $this->app->db->select($table_name);
        $stmt->order('id',GarageSale\MySQLStatement::DESC);
        $stmt->limit( 1 );
        // get the last id entered
        $this->query_results = $this->app->db->statement_result($stmt);
        return $this->query_results[0]['id'];
    }

    
    /** Display the registration form to the user.
     *  @param $args Array containing argument information. This action
     *         should not need these.
     */
	function register( $args ){
		$this->view->add('page_title','Registration');
		// make action available
	}
    
   /** Display an error message to the user if their netid does not return any results.
     *  @param $args Array containing argument information. This action
     *  should not need these.
     */
	function registerUserNotFound($args){
		$styles = array('registration');
	        $this->view->add('stylesheets',$styles);
	        $subview = array('user_registerUserNotFound');
	        $this->view->add('subviews',$subview);
	}    
    
    
  /** Action for including a new user into the database.
     *
     *  @param $args not used.
     */ 
    function input ( $args ){

      if ($_POST['password'] == $_POST['retypePassword']){
		   // start a new sql update statement
		   $stmt = $this->app->db->insert('users');
		   $ldap = $this->app->extension('ldap');
		   $user =  $ldap->get_user($_POST['netID']);  //attempts to retrieve the user's information from the university's db
								       //if the user does not exit, it returns null
		
			$hash = password_hash($_POST['password'], PASSWORD_BCRYPT); //save the user's password and $hash
		
			if ($user == null){
			        $this->app->redirect('user/registerUserNotFound'); //user was not found, redirect to registration fail
			}
		
			$email = $user->get_email();
			$name = $user ->get_givenname();
			$userType = $user ->get_userclass();
			$major = $user ->get_major();
		        // insert a new user with the correct values
		           $stmt->values( array (
		               array(
		                    'name'  => 'name',
		                    'value' => $name,
		                    'type'  => 's'
		                    ),
		
		               array(
		                    'name'  => 'email',
		                    'value' => $email,
		                    'type'  => 's'
		                    ),
		
		               array(
		                    'name'  => 'handle',
		                    'value' => $_POST['netID'], //save their netID
		                    'type'  => 's'
		               ),
		
		               array(
		                    'name'  => 'password',
		                    'value' => $hash,
		                    'type'  => 's'
		                    ),
		
		               array(
		                    'name'  => 'usertype',
		                    'value' => $userType,
		                    'type'  => 's'
		                    )
		            ) );
					
		         // execute the statement
		         $success = $this->app->db->statement_execute($stmt);
		
		
		         $stmt = $this->app->db->select('users');
		         $stmt->order('id',GarageSale\MySQLStatement::DESC);
		         $stmt->limit( 1 );
		         //get the last id entered
		         $this->query_results = $this->app->db->
		                statement_result($stmt);
		            $last_id = $this->query_results[0]['id'];
					
			//Create activation steps
			$activation_model = $this->app->model('activation');
			$hash = md5( rand(0,1000));
			$code = rand(1000,5000);
			$activation_model->add_hash($last_id,$hash,$code);
			$sent = $this->app->utility->send_verification($last_id,$hash,$code,$email);
		
			 // start a new sql update statement
		         $stmt = $this->app->db->insert('profiles');
		
		         // insert a new user with the correct values
		            $stmt->values( array (
		               array(
		                    'name'  => 'description',
		                    'value' => "None at the moment.",
		                    'type'  => 's'
		                    ),
		
		               array(
		                'name'  => 'major', 
		                'value' => $major,
		                'type'  => 's'
		                ),
				array(
		                'name'  => 'userid', 
		                'value' => $this->getLastID ( 'users' ),
		                'type'  => 'i'
		                ),
		
				array(
		                    'name'  => 'image', 
		                    'value' => 'upload/troll2.png',
		                    'type'  => 's'
		                 )
		             ));
		
		            // execute the statement
		            $success = $this->app->db->statement_execute($stmt);
		
			    if ($success){
				    $subview = array('user_registerSuccess');  		
		        		$this->view->add('subviews',$subview);
			    }
	}
	
	else{
		$this->app->redirect('user/loginFail');
	}
        
        
    // yup, tried to update
    $update_attempt = true;
    }


    /** This page should automatically logout the current logged in user
     *  @param $args Array containing controller information
     */
    function logout( $args ){
        $this->app->user->do_logout();
        $this->app->redirect('');
    }

     /** Allow user to edit his/her profile image.
     *  @param $args not used
     */
    function editImage($args){
	    // register action
    }

     /** Adds the user's chosen image to the profiles database
     *  @param $args not used
     */
    function addProfileImage($args){
	    // new select statement
	    $stmt = $this->app->db->update('profiles');

	    //gets the logged in user's id
	    $user_id = $this->app->user->get_user_id();	     
	
	    // set the where value of the statement
            $stmt->where('userid','i',$user_id);

           // insert a new user with the correct values
           $stmt->values( array (
               array(
                    'name'  => 'image',
                    'value' => $this->app->user->uploadImage('u_'.strval($user_id)),
                    'type'  => 's'
                    )
		) );

            // execute the statement
	   // $this->getProfileImage($args);
            $success = $this->app->db->statement_execute($stmt);

	    if ($success){
            $this->app->redirect ('user/editImage');
	    }
    }

    
    /** View a users profile information. If this profile belongs to
     *  this user we should also indicate that to the view.
     *  @param $args An array containing the argument if they're
     *               available. Here it contains 'id'.
     */
    function profile( $args )
    {
        
        // user the user class to convert a name to an id, or just get
        // id if it is already a number, -1 on fail
        $user_id = $this->app->user->id_from_name($args['id']);
        
        // use logged in user if no other is available
        if( $user_id < 0 ){
            $user_id = $this->app->user->get_user_id();
        }
        
        // assume there is not a user match to a logged in user 
        $user_match = $this->app->user->is_user( $user_id );
        
        
        // add the user id to the view
        $this->view->add('user_id',$user_id);
        
        // indicate whether the users match
        $this->view->add('user_match',$user_match);
        
        
        // if the user id is not valid, can't send any records
        // send null pointer
        if( $user_id < 0 ){
            $this->view->add('profile_results',null);
            
            // change page title
            $this->view->add('page_title','Unknown Profile.');
            
            // and bail
            return;
        }
        
        
        /* ====================
         * Start SQL Selection
         */
        
        // create a new statement
        $stmt = $this->app->db->select('profiles');
        
        // set the where value of the statement
        $stmt->where('userid','i',$user_id);
        
        // inner join on the users table
        $stmt->inner_join(
            array(
                'table' => 'users',
                'other' => 'id',
                'this'  => 'userid'
            )
        );
        
        // set the statements limit
        $stmt->limit(1,0);
        
        // get the results fromt the statement
        $this->query_results = $this->app->db->statement_result($stmt);
        
        
        
        // start with default of no results
        $profile_results = null;
        
        // make sure there is a row    
        if( count($this->query_results) != 0 ){
                // get the array of the results from the first row
                $profile_results = $this->query_results[0];
        }
		$this->view->add('profile_results',$profile_results);
        
		
		//Get rating and number of reviews for retrieved user id
		$reviews_model = $this->app->model('reviews');
		$rating = $reviews_model->get_avg_reviews($user_id);
		$review_count_res = $reviews_model->get_count($user_id);
		$review_count = $review_count_res[0]['id'];
		
		//Add information to view
		$this->view->add('rating_count',$review_count);
		//$this->view->add('scripts',array('star-review'));
		if($rating[0]['rating_average'] != null){
			$this->view->add('rating',$rating[0]['rating_average']);
		}else{
			$this->view->add('rating',0);
		}
        
        // set teh page title
        $this->view->add('page_title',
            // if results not null
            ($profile_results != null ) ?
            // use user handle 
            "Garage Sale Profile: " . $profile_results['handle'] :
            // otherwise profile is not known
            'Unknown Profile..'
        );
    }
    
     /**
     * Prints the desired string, centralized in red 
     *
     *  @param $args desired string to be printed
     */
      function printinRed ($args){
    		echo '<p style="color: red; text-align: center"> '.$args. 
    		'</p>';
    }

     /**
     * Allows the user to change his previous username
     *
     *  @param $args not used.
     */
    function changeUsername($args){
	// identify user
        if( !$this->app->user->is_logged_in() ){
            
            // redirect, he/she doesn't belong here
            $this->app->redirect('user/login');
        }	

        $subview = array('user_newUsername');
        $this->view->add('subviews',$subview);
    }
    
     /**
     * Allows the user to change his previous password 
     *
     *  @param $args not used.
     */
    function changePassword($args){
	// identify user
        if( !$this->app->user->is_logged_in() ){
            
            // redirect, he/she doesn't belong here
            $this->app->redirect('user/login');
        }	
        $subview = array('user_newPassword');
        $this->view->add('subviews',$subview);
    }

     /**
     * Allows the user to change his previous email
     *
     *  @param $args not used.
     */
    function changeEmail($args){
	// identify user
        if( !$this->app->user->is_logged_in() ){
            
            // redirect, he/she doesn't belong here
            $this->app->redirect('user/login');
        }	
        $subview = array('user_newEmail');
        $this->view->add('subviews',$subview);
    
    }
    
     /**
     * Allows the user to change his previous phone
     *
     *  @param $args not used.
     */
    function changePhone($args){
	// identify user
        if( !$this->app->user->is_logged_in() ){
            
            // redirect, he/she doesn't belong here
            $this->app->redirect('user/login');
        }	
        $subview = array('user_newPhone');
        $this->view->add('subviews',$subview);
    }
  
    /**
     * Allows the user to change his previous username 
     *
     *  @param $args not used.
     */
    function changeUserAction($args){ 
	// identify user
        if( !$this->app->user->is_logged_in() ){
            
            // redirect, he/she doesn't belong here
            $this->app->redirect('user/login');
        }	
       $this->changeAction ( 'handle' );
    }

    /**
     * Allows the user to change his previous phone number 
     *
     *  @param $args not used.
     */
    function changePhoneAction($args){ 
	// identify user
        if( !$this->app->user->is_logged_in() ){
            
            // redirect, he/she doesn't belong here
            $this->app->redirect('user/login');
        }	
       $this->changeAction ( 'phone' );
    }

    /**
     * Allows the user to change his previous email adress 
     *
     *  @param $args not used.
     */
    function changeEmailAction($args){ 
	// identify user
        if( !$this->app->user->is_logged_in() ){
            
            // redirect, he/she doesn't belong here
            $this->app->redirect('user/login');
        }	
       $this->changeAction ( 'email' );
    }
    
 
    /**
     * Allows the user to change his previous password 
     * Separated from changeAction since this method deals with hashing and its easier to debug in a separate method
     *  @param $args not used.
     */
     function changePasswordAction ($args){ 
	// identify user
        if( !$this->app->user->is_logged_in() ){
            
            // redirect, he/she doesn't belong here
            $this->app->redirect('user/login');
        }

	if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
	        $netID = $_POST ['netID']; 
		$new = $_POST ['newValue'];
		$newR = $_POST ['newValueR'];
		$old = $_POST['oldValue'];

		// new select statement
		$stmt = $this->app->db->select('users');
			     
		// set the where for category name
		$stmt->where('handle', 's', $_POST['netID']); //id is a VARCHAR
		        
		// get category return results
		$cat_result = $this->app->db->statement_result( $stmt );
			   

  
		if ($cat_result != null){
			// get the user's password if the statement was found 
		        $passwordDB = $cat_result[0]['password'];
		        $isPasswordCorrectOld = password_verify ($old, $passwordDB);
		}
	
		if (!$isPasswordCorrectOld){
			$this->printinRed("Old values do not match! Try again.");
			$this->changePassword($args); 
			return;
		}
		
		if ($new != $newR) {
			$this->printinRed("New values do not match! Try again.");
			$this->changePassword($args); 
			return;
		}
	

	        // new select statement
	        $stmt = $this->app->db->update('users');
		     
	        // set the where for category name
	        $stmt->where('handle', 's', $netID ); 

		$hash = password_hash($new, PASSWORD_BCRYPT); //save the;

   		$stmt->values( array (
                	array(
                    	'name'  => 'password',
            	        'value' => $hash,
            	        'type'  => 's'
                      )
		      ));

           	// execute the statement
           	$success = $this->app->db->statement_execute($stmt);
      		if($success){
			$subview = array('user_valueChange');  		
    	    		$this->view->add('subviews',$subview);
     		
     		}
	}
	
     }
    
     /**
     * Action for the changing forms
     *  
     * @param args string representing the name of a database column
     */
    function changeAction($args){
	// identify user
        if( !$this->app->user->is_logged_in() ){
            
            // redirect, he/she doesn't belong here
            $this->app->redirect('user/login');
        }	

	if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
		// new select statement
        	$stmt = $this->app->db->select('users');
        	// set the where for category name
        	$stmt->where('handle', 's', $_POST['netID'] ); //id is a VARCHAR
        	// get category return results
        	$cat_result = $this->app->db->statement_result( $stmt );
        	// get the user's old input value
        	$oldVal = $cat_result[0][$args];

	        $netID = $_POST ['netID']; 
		$new = $_POST ['newValue'];
		$newR = $_POST ['newValueR'];
		$old = $_POST['oldValue'];

		if ($oldVal != $old){
			$this->printinRed("Old values do not match! Try again.");
			//Old values do not match redirect to renew attempt
			if ($args == 'username')
	     			$this->changeUsername($args); 
			if ($args == 'password')
	     			$this->changePassword($args); 
			if ($args == 'email')
	     			$this->changeEmail($args); 
			if ($args == 'phone')
	     			$this->changePhone($args); 
			return;
		}

	        if ($new != $newR) {
			$this->printinRed("New values do not match! Try again.");
	     		//New values do not match redirect to renew attempt
			if ($args == 'username')
	     			$this->changeUsername($args); 
			if ($args == 'password')
	     			$this->changePassword($args); 
			if ($args == 'email')
	     			$this->changeEmail($args); 
			if ($args == 'phone')
	     			$this->changePhone($args); 
	     		return;
	        }

	        // new select statement
	        $stmt = $this->app->db->update('users');
		     
	        // set the where for category name
	        $stmt->where('handle', 's', $netID ); 
	       
     		$stmt->values( array (
                	array(
                    	'name'  => $args,
            	        'value' => $new,
            	        'type'  => 's'
                   	)
			));

           	// execute the statement
           	$success = $this->app->db->statement_execute($stmt);
      		if($success){
			$subview = array('user_valueChange');  		
    	    		$this->view->add('subviews',$subview);
     		}

        }
     }
    
     /** Loads a page for retrieving the user's old password
     *  and sending it back to him via email.
     *
     *  @param $args not used.
     */
    function forgottenPassword($args) {
        $styles = array('registration');
        $this->view->add('stylesheets',$styles);
        $subview = array('user_forgottenPassword');
        $this->view->add('subviews',$subview);
    
    }

    /** Loads a page for retrieving the user's old username
     *  and sending it back to him via email.
     *
     *  @param $args not used.
     */
    function forgottenUsername($args) {
        $this->app->redirect('user/forgottenPassword'); 
        //the email sends both the username and password to the user
    }

    /** Action for generating a random string as the user's new password, hashing it,
     * saving it to the database,  
     * and sending it back to the user via email.
     * @param $args not used.
     */
    function forgottenPasswordAction($args) {
	(string)$code = sha1(time()); //generate random string
	$newPass = $code;
        $netID = $_POST ['netID']; //Retrieves the id from the form

        // new select statement
        $stmt = $this->app->db->select('users');
	     
        // set the where for category name
        $stmt->where('handle', 's', $netID ); //id is a VARCHAR
        // get category return results
        $cat_result = $this->app->db->statement_result( $stmt );
	
	if ($cat_result != null){
        // get the user's name
        $user = $cat_result[0]['name'];
        // get the user's email address
        $email = $cat_result[0]['email'];
        // get the user's username
        $username = $cat_result[0]['handle'];
        // get the user's password
        $password = $cat_result[0]['password'];
      
	// new select statement
	$stmt = $this->app->db->update('users');
		     
	// set the where for category name
	$stmt->where('handle', 's', $netID ); 
	$hash = password_hash($code, PASSWORD_BCRYPT); //save the;

	$stmt->values( array (
                	array(
                    	'name'  => 'password',
            	        'value' => $hash,
            	        'type'  => 's'
                      )
		      ));

        $success = $this->app->db->statement_execute($stmt); //execute the statement
		$from = 'ISUGarageSale'; 
		$headers = "From: $from\n";

        mail ($email,
            "ISU Garage Sale: Forgotten Password",
			"Hello $user,\n\n" . 
			"Your username is $username and your new temporary password is:\n\n" .
            "$newPass\n\n" . 
			"Please change your password at your earliest convenience.\n\n" .
			"Thank you for using our site.\n\n" . 
			"Greetings,\nfrom ISU Garage Sale",$headers);
	
        $subview = array('user_emailSent');
        $this->view->add('subviews',$subview);
	}
	else{
		$subview = array('unknown_id');
        	$this->view->add('subviews',$subview);
	}
    }
    
    
    /** Allow user to edit his/her own profile information.
     *  @param $args An array containing the argument if they're
     *  available. Here it contains 'id'.
     */
    function editprofile( $args )
    {
        
        // if user not logged in, redirect to login
        if( !$this->app->user->is_logged_in() ){
            $this->app->redirect('user/login');
        }
        
                
        // I forgot why I did this. Don't you hate when that happens? 
        $user_match = $this->app->user->is_user( $this->userid );
		
        $username = $this->app->user->get_user_name();
		
        // something has gone wrong, go to login page
        if( !$user_match ){
            	$this->app->redirect('user/login');
        }
        
        // default info
        $update_attempt = false;
        $success = false;
        
        
        /* ========================
         * Start SQL Profile Update
         * If post data is present
         */
         
        // look for post request
        if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
        
            // start a new sql update statement
            $stmt = $this->app->db->update('profiles');
            // update this user
            $stmt->where('userid','i',$this->userid);
            // with the correct profile values
            $stmt->values( array(
                array(
                    'name' => 'description',
                    'type' => 's',
                    'value' => $_POST['description']
                )
            ) );
            
            // execute the statement
            $success = $this->app->db->statement_execute($stmt);
            
            // yup, tried to update
            $update_attempt = true;
        }
        
        
        // add the user id to the view
        $this->view->add('user_id',$this->userid);
        
        // indicate whether the users match
        $this->view->add('user_match',$user_match);
        
        // indicate whether the user attempted to update profile
        $this->view->add('update_attempt',$update_attempt);
        
        // indicate whether the users update attempt was successful
        $this->view->add('success',$success);
        
        
        // the path to send the data to on form post
        $form_path= $this->app->form_path('user/editprofile');
        $this->view->add('form_path',$form_path);
        
        
        // if the user id is not valid, can't send any records
        // send null pointer
        if( $user_match < 0 ){
            $this->view->add('profile_results',null);
            
            // and bail
            return;
        }
        
        
        /* ====================
         * Start SQL Selection
         */
        
        // create a new statement
        $stmt = $this->app->db->select('profiles');
        // set the where value of the statement
        $stmt->where('userid','i',$this->userid);
        // set the statements limit
        $stmt->limit(1,0);
		
         // inner join on the users table
        $stmt->inner_join(
            array(
                'table' => 'users',
                'other' => 'id',
                'this'  => 'userid'
            )
        );
		
        // get the results fromt the statement
        $this->query_results = $this->app->db->statement_result($stmt);
        
        // start with default of no results
        $profile_results = null;
        
        // make sure there is a row    
        if( count($this->query_results) != 0 ){
            // get the array of the results from the first row
            $profile_results = $this->query_results[0];
        }
        
        // add the profile results to the view
        $this->view->add('profile_results',$profile_results);
        
        // set the page title
        $this->view->add('page_title',
            // if results not null
            ($profile_results != null ) ?
            // use user handle 
            "Edit Garage Sale Profile: " . $profile_results['handle'] :
            // otherwise profile is not known
            'Unknown Profile...'
        );
    }
    
	
    /**
    *Views the user's desired settings, allowing
    *the user to perform certain modifications to his account.
    *
    */
    function settings($args){
          if( !$this->app->user->is_logged_in() ){
            $this->app->redirect('user/login');
        }
    }

    function accountSettings($args){
	    if( !$this->app->user->is_logged_in() ){
            	$this->app->redirect('user/login');
            }

	    $subview = array('user_accountSettings');  		
    	    $this->view->add('subviews',$subview);
     }

     /**
    *Views the user's desired settings, allowing
    *the user to perform certain modifications to his account.
    *
    */
    function sellItem($args){
	if( !$this->app->user->is_logged_in() ){
            $this->app->redirect('user/login');
        }
        // add the form path to the view
        $form_path = $this->app->form_path('user/login');
        $this->view->add('form_path',$form_path);
    }


    /** Action for including a new item into the database.
     *
     *  @param $args not used.
     */
    function uploadItem ( $args ){

        // start a new sql update statement
           $stmt = $this->app->db->insert('listings');
		  
	// identify user
        if( !$this->app->user->is_logged_in() ){
            
            // redirect, he/she doesn't belong here
            $this->app->redirect('user/login');
        }	
	   
	//check to see if a form was actually submitted      
        if(!isset($_POST['submit'])){
            $this->app->redirect('browse/user/' . $this->userid );
      	}
		   

        // insert a new user with the correct values
           $stmt->values( array (
               array(
                    'name'  => 'title',
                    'value' => $_POST['name'],
                    'type'  => 's'
                    ),

               array(
                    'name'  => 'image_paths',
                    'value' => $this->app->user->uploadImage('l_'.strval($this->getLastID('listings')+1)),
                    'type'  => 's'
                    ),

               array(
                    'name'  => 'asking_price',
                    'value' => $_POST['price'],
                    'type'  => 's'
                    ),

	       array(
                    'name'  => 'other_offer',
                    'value' => $_POST['other'],
                    'type'  => 's'
                    ),

               array(
                    'name'  => 'description',
                    'value' => $_POST['description'],
                    'type'  => 's'
                    ),

               array(
                    'name'  => 'categoryid',
                    'value' => $_POST['itemType'],
                    'type'  => 'i'
                    ),

               array(
                    'name'  => 'userid',
                    'value' => $this->userid,
                    'type'  => 'i'
                    ),

               array(
                    'name'  => 'keywords',
                    'value' => $_POST['keywords'],
                    'type'  => 's'
               )
	       ) );

            // execute the statement
            $success = $this->app->db->statement_execute($stmt);
	    if($success){
			$listings_model = $this->app->model('listings');
			$listing_result = $listings_model->
			get_most_recent($this->userid);
			// finally redirect back to item
			$this->app->redirect('browse/item/' . $listing_result[0]['id'] );
	    }
    }

    
    /** View a users reviews. If this profile belongs to
     *  this user we should also indicate that to the view.
     *  @param $args An array containing the argument if they're
     *               available. Here it contains 'id' and may contains
     *               a page number.
     */
    function review( $args )
    {
		
	$user_id = $this->app->user->id_from_name($args['id']);
		
	// If user_id not provided in url, default to current user
	if( $user_id < 0 ){
            $user_id = $this->app->user->get_user_id();
        } // End of if    
        
		// If the user id is not valid, can't send any records
        if( $user_id < 0 ){ 
            $this->view->add('reviews_results',null);
            return;
        } // End of if
		
        //Make sure id corresponds to a user that exists
		$users_model = $this->app->model('users');
		if($users_model->count_user($user_id)==0){
			$this->view->add('reviews_results',null);
            return;
		} // End of if
		
		//Prepare title
		$user_handle= $this->app->user->name_from_id($user_id);
		$this->view->add('user_id',$user_id);
		$this->view->add('page_title',
		    'Garage Sale Reviews: ' . $user_handle);

		//Get count of reviews
		$reviews_model = $this->app->model('reviews');
		$reviews_count_res = $reviews_model->get_count($user_id);
		$reviews_count =(int)$reviews_count_res[0]['id'];
		$this->view->add('reviews_count',$reviews_count);

        // Calculate number of reviews to show
		if(isset($_POST['num-results'])){
			$num_results = $_POST['num-results'];
			$_SESSION['reviews_per_page'] = $num_results;
			$how_many = $num_results;
		}else if( ! isset($_SESSION['reviews_per_page']) ){ 
		    //Default number
            $how_many = 10;
        }else{ 
            // Last saved session to be used
            $how_many = $_SESSION['reviews_per_page'];
        } // End of else

        // default the page number to 0
        $pages = 0;
        // gather page info, make sure its a number
        if( $args['page'] != null && is_numeric($args['page']) ){
            $pages = (int) $args['page']-1;
        } // End of if
        
        // couldn't connect, abandon attempt
        if( !$this->app->db->is_connected() ){
            // add the reviews results to the view
            $this->view->add('reviews_results',null);
            return;
        } //End of if
        // calculating offset/start point for looking up records
        $offset = $pages * $how_many;

        //Get reviews information
        $reviews_results = $reviews_model->
            get_reviews($user_id,$how_many,$offset);
        $this->view->add('reviews_results',$reviews_results);
		
        // set up counts
		$paginate = array(
			'page_count' => ceil(($reviews_count/$how_many)),
			'this_page'  => $pages+1
		);
        // add page count
        $this->view->add('paginate',$paginate);
        // add teh page action to the view
        $this->view->add('page_action', $this->app->form_path(
            'user/review/'.$user_id) 
        );
		
    } // End of review


    /** Submit a new review based on a completed user transaction. This
     *  should only be accomplished if an offer has been accepted and 
     *  the review should be linked to the offer.
     *  @param $args An array containing the argument if they're
     *         available. Here it contains 'id' of the user to review.
     */
    function newreview( $args )
    {
        // verify user logged in, if not, redirect to login
        if( !$this->app->user->is_logged_in() ){
            $this->app->redirect('user/login');
        }               
        $this->view->add('page_title',
            'Submit a review: ' . $this->userid);

        $update_attempt = false;
        $update_success = false;

        $reviews_model = $this->app->model('reviews');

        // check for post data, insert review if so
        if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
			$insert_success = $reviews_model->insert_review(
		        $_POST['reviewmessage'],
		        $this->userid,
		        $_POST['reviewee-id'],
		        $_POST['rating'],
		        $_POST['listing-id']);
			    
			if($insert_success){ // Insert was successful
				$this->view->add('review_submitted',
				    'Thank you for your review! It is important to know 
				    how everything is going!');
				if(!$reviews_model->
				    update_offer($this->userid,$_POST['listing-id'])
				){
				    $this->view->add('review_submitted',
				        'Uh oh! Something went wrong. 
				        Please try again later!');
				} // End of if
			}else{
					$this->view->add('review_submitted',
					    'Uh oh! Something went wrong. 
					    Please try again later!'
					);
			} // End of else
        }else{
			$how_many = 10;
			$reviews_result = $reviews_model->
			    get_offers($this->userid,$how_many);
			$this->view->add('reviews_results',$reviews_result);
		}
    }


    /** The index function is the default to be called when
     *  accessing a controller
     */
    function search( $args )
    {   
        // load the search terms
        $terms = null;
        
        if( $args['terms'] != null ){
            $terms = $args['terms'];
        }
        
        // parse the query string values out of the 
        $query_vals = array();
        
        // parse the query string into query vals
        parse_str($_SERVER['QUERY_STRING'], $query_vals);
        
        $this->view->add('search_terms',$query_vals['s']);
    }
	
    /**Load My Offers Page for offers this user submitted*/
    function offers($args){
		//Prepare subview
		$this->view->add('offerpage','Submitted');
		$this->view->add_style('browse');
		$this->view->add('page_title',"Offers I've Submitted");

		//Ensure the user is logged in
		if( !$this->app->user->is_logged_in() ){
            $this->app->redirect('user/login');
        }else{
			$user_id = $this->app->user->id_from_name($args['id']);
		} // End of else

		//remember number of results to show
		if($_SERVER['REQUEST_METHOD'] === 'POST' ){ 
		    //Set number of reviews to show
			$num_results = $_POST['num-results'];
			$_SESSION['offers_per_page'] = $num_results;
			$how_many = $num_results;
        }else if( ! isset($_SESSION['offers_per_page']) ){ 
            //Default number
            $how_many = 10;
        }else{ // Last saved session to be used
            $how_many = $_SESSION['offers_per_page'];
        } // end of else

		//Get total number of offers
		$offer_model = $this->app->model('offers');
		$offer_count_res = $offer_model->get_offer_count($this->userid);
		$offer_count =(int)$offer_count_res[0]['id'];
		
        // default the page number to 0
        $pages = 0;
        // gather page info, make sure its a number
        if( $args['page'] != null && is_numeric($args['page']) ){
            $pages = (int) $args['page']-1;
        } // End of if
		$offset = $pages * $how_many;
		
		// Get offer information
        $offers_result = $offer_model->
            offers_submitted($this->userid,$how_many,$offset);
		$this->view->add('offer_results',$offers_result);
		
		// set up counts
		$paginate = array(
			'page_count' => ceil(($offer_count/$how_many)),
			'this_page'  => $pages+1
		);
        // add page count
        $this->view->add('paginate',$paginate);
        // add teh page action to the view
        $this->view->add('page_action', $this->app->form_path(
            'user/offers/'.$this->userid) 
        );
		
	} // End of offers
	
	/**Load My Offers Page for offers this user received
	*/
	function offersReceived($args){
		//Prepare subview
		$this->view->add('offerpage','Received');
        	$this->view->add('subviews',array('user_offers'));
		$this->view->add_style('browse');
		$this->view->add('page_title',"Offers I've Received");

		//Ensure the user is logged in
		if( !$this->app->user->is_logged_in() ){
            		$this->app->redirect('user/login');
        	}else{
			$user_id = $this->app->user->id_from_name($args['id']);
		} // End of else

		//Set number of offers to show
		if($_SERVER['REQUEST_METHOD'] === 'POST' ){
			$num_results = $_POST['num-results'];
			$_SESSION['offers_per_page'] = $num_results;
			$how_many = $num_results;
		}else if( ! isset($_SESSION['offers_per_page']) ){ 
		    	//Default number
            		$how_many = 10;
        	}else{ // Last saved session to be used
            		$how_many = $_SESSION['offers_per_page'];
        	} // end of else

		//Setup offers model
		$offer_model = $this->app->model('offers');
		//Get total number of offers
		$offer_count_res = $offer_model->
		    get_received_count($this->userid);
		$offer_count =(int)$offer_count_res[0]['id'];

		// default the page number to 0
        	$pages = 0;
        	// gather page info, make sure its a number
        	if( $args['page'] != null && is_numeric($args['page']) ){
            	$pages = (int) $args['page']-1;
        	} // End of if
		$offset = $pages * $how_many;
		
        	//Get offers information
        	$offers_result = $offer_model->
            	offers_received($this->userid,$how_many,$offset);
        	$this->view->add('offer_results',$offers_result);
		
        	// set up counts
		$paginate = array(
			'page_count' => ceil(($offer_count/$how_many)),
			'this_page'  => $pages+1
		);
        	// add page count
        	$this->view->add('paginate',$paginate);
        	// add the page action to the view
        	$this->view->add('page_action', $this->app->form_path(
            	'user/offersReceived/'.$this->userid) 
        	);
		
    	} // End of offersReceived
	
	function activation($args){
		//If user clicked on email
		if(isset($_GET['hash']) && isset($_GET['id']) && !isset($_POST['code'])){
			//Get user information
			$hash = $_GET['hash'];
			$id= $_GET['id'];
			
			//Prepare redirect url for form submission
			$this->view->add('hash',$hash);
			$this->view->add('id',$id);
			
			//Make sure url is right
			$activation_model = $this->app->model('activation');
			$result_count_res= $activation_model->check_hash($id,$hash,0,0);
			$count =(int)$result_count_res[0]['id'];
			if($count!= null && $count >0){
				$this->view->add('confirm',1);
				//Prepare title of the page
				$user_handle= $this->app->user->name_from_id((int)$id);
				$this->view->add('page_title',
					'Activate your account: ' . $user_handle);
			}else{
				$this->view->add('page_title', 'Activate your account: ');
			}
		// If user entered their confirmation code
		}else if(isset($_GET['hash']) && isset($_GET['id']) && isset($_POST['code'])){
			//Get user information and inputted code
			$hash = $_GET['hash'];
			$id= $_GET['id'];
			$code = $_POST['code'];
			$this->view->add('test',"HELLO");
			
			//Prepare redirect url
			$this->view->add('hash',$hash);
			$this->view->add('id',$id);
			
			//Prepare title
			$user_handle= $this->app->user->name_from_id((int)$id);
			$this->view->add('page_title',
				'Activate your account: ' . $user_handle);
			
			//Make sure url is right
			$activation_model = $this->app->model('activation');
			$result_count_res= $activation_model->check_hash($id,$hash,$code,1);
			$count =(int)$result_count_res[0]['id'];
			
			//If the user still needs to be activated, activate them
			if($count != null && $count > 0){
				$result  = $activation_model->activate_user($id);
				if($result){
					$this->view->add('activated',2);
					$activation_model->delete_hash($id,$hash);
				}
			// User does not have to be activated
			}else{
				$result_count_res= $activation_model->check_user_activated($id);
				$count =(int)$result_count_res[0]['id'];
				//The user has already been verified
				if($count > 0){
					$this->view->add('msg','Your account has already been verified');
				//The user has entered a wrong code
				}else{
					$this->view->add('confirm',1);
					$this->view->add('try_again',1);
				}
			}
		}else{
			$this->view->add('page_title','Activate your account: ');
		
		}
	} // End of activation
	
} // End of class


?>

