<?php
/** controllers/admin.php
 *  The admin controller which use used to manage the over all site 
 *  content and settings.
 */
class admin extends GarageSale\Controller {
    
    
    /** Prevent execution of default before actions */
    function before(){
    
        // validate user
        if( 
            // must be admin
            $this->app->user->get_user_level() < 
            GarageSale\User::USER_ADMIN 
        ){
            
            // send to login
            $this->app->redirect('user/login');
        }
                        
        // set the view to admin
        $this->view->new_view('admin');
        
        // set up admin self link
        $this->view->add('adminlink',
            $this->app->form_path('admin/'));
        
        
        // load the extension admin sections
        $directories = glob('extensions/*' , GLOB_ONLYDIR);
        $ext_list = array();
        foreach($directories as $dir){
        
            // name of the extension
            $name = $dir;
        
            // check dir for file
            if( file_exists($dir.'/name.txt') ){
                $file = fopen($dir.'/name.txt','r');
                while( !feof($file) ){
                    $name = fgets($file);
                    if( strlen(trim($name)) != 0 ){
                        break;
                    }
                }
            }
        
            // check for admin page
            if( file_exists($dir.'/admin.php') ){
                // extensions list
                $ext_list[] = array(
                    'location' => $this->app->form_path( 
                        'admin/'.$dir
                    ),
                    'name' => $name
                );
            }
        }
        
        // add to view
        $this->view->add('extension_list',$ext_list);
    }
    
    /* Constants */
    
    /** Defines a successful return from a toolbox function */
    const TOOL_SUCCESS = 0;
    
    /** Defines a failed return from a toolbox function */
    const TOOL_FAIL = -1;
    
    /* OK this is pretty cool: these are going to be a tool boxes of
     * various functions we can use to lighten the action load
     */
    
    /** users toolbox is the list of tools to use to manage the users
     *  registered with the garage and their access/user levels.
     *  @param string $tool the name of the tool to to select from the
     *         toolbox
     *  @return callable a function for th tool to use
     */
    public function users_toolbox( $tool, $obj ){
        
        // array of tools
        $arr = array(
            
            /* display all the users registered with garage sale */
            'display' => function($obj){
                
                /* ====================
                 * SQL select all users
                 */
                $stmt = $obj->app->db->select('users');
                
                // join with settings
                $stmt->inner_join( array(
                    'table' => 'profiles',
                    'other' => 'userid',
                    'this'  => 'id'
                ) );
                
                // get result
                $result = $obj->app->db->statement_result($stmt);
                
                // add to view
                $obj->view->add('users_result',$result);
                
                
                // succeed
                return admin::TOOL_SUCCESS;
            },
            
            
            /* Advance a users status within the Garage */
            'advanceuser' => function($obj){
                
                // validate get
                if( !isset($_GET['u']) ){
                    return admin::TOOL_FAIL;
                }
                
                // get the users result using toolbox
                $func = $obj->users_toolbox('user_result',$obj);
                $result = $func($obj);
                
                // add to view
                $obj->view->add('users_result',$result);
                
                // ask for confirmation
                $obj->view->add('confirm_action','promoteuser');
                
                
                $lvl_name = 'STANDARD';
                
                // output what the user will now be
                if( $result[0]['userlevel'] >= 20 ){
                    // is a manager, go to admin
                    $lvl_name = 'ADMIN';
                }else if( $result[0]['userlevel'] >= 10 ){
                
                    // is a moderator go to manager
                    $lvl_name = 'MANAGER';
                }else if( $result[0]['userlevel'] >= 0 ){
                
                    // is standard, go to moderator
                    $lvl_name = 'MODERATOR';
                }
                
                $message = <<< MSG
                You are about to promote this user to a(n): <br />
                $lvl_name
MSG;
                $obj->view->add('confirm_message',$message);
                
                // succeed
                return admin::TOOL_SUCCESS;
                
            },
            
            
            /* ----------------------
             * Perform user promotion
             */
            'promoteuser' => function($obj){
                
                // validate get
                if( !isset($_GET['u']) ){
                    return admin::TOOL_FAIL;
                }
                
                // get user result using toolbox
                $func = $obj->users_toolbox('user_result',$obj);
                $result = $func($obj);
                
                // perform promotion
                if( count($result) < 1 ){
                    return admin::TOOL_FAIL;
                }
                
                
                // get previous user level
                if( $result[0]['userlevel'] >= 30 ){
                    
                    // nope, faile it
                    return admin::TOOL_FAIL;
                } elseif( $result[0]['userlevel'] >= 20 ){
                    
                    // manager, go to admin
                    $usrlvl = 30;
                } elseif( $result[0]['userlevel'] >= 10 ){
                    
                    // moderator, go to manager
                    $usrlvl = 20;
                } else {
                    
                    // standard go to moderator
                    $usrlvl = 10;
                }
                
                
                
                /* ========================
                 * SQL to execute promotion
                 */
                
                $stmt = $obj->app->db->update('users');
                
                // where
                $stmt->where('id','i',$result[0]['id']);
                
                // values
                $stmt->values( array(
                   
                   // userlevel
                   array(
                       'name' => 'userlevel',
                       'type' => 'i',
                       'value'=> $usrlvl
                   )
                ));
                
                // attempt
                $success = $obj->app->db->statement_execute($stmt);
                
                if( !$success ){
                
                    // good to go
                    return admin::TOOL_FAIL;
                }
                
                $obj->app->redirect('admin/users');
            },
            
            
            /* ---------------------
             * Perform user demotion
             */
            'demoteuser' => function($obj){
                
                // validate get
                if( !isset($_GET['u']) ){
                    return admin::TOOL_FAIL;
                }
                
                // get user result using toolbox
                $func = $obj->users_toolbox('user_result',$obj);
                $result = $func($obj);
                
                // perform promotion
                if( count($result) < 1 ){
                    return admin::TOOL_FAIL;
                }
                
                
                // get previous user level
                if( $result[0]['userlevel'] >= 30 ){
                    
                    // admin to manager
                    $usrlvl = 20;
                } elseif( $result[0]['userlevel'] >= 20 ){
                    
                    // manager to moderator
                    $usrlvl = 10;
                } elseif( $result[0]['userlevel'] >= 10 ){
                    
                    // moderator to standard
                    $usrlvl = 0;
                } else {
                    
                    // nope, faile it
                    return admin::TOOL_FAIL;
                }
                
                
                
                /* ========================
                 * SQL to execute promotion
                 */
                
                $stmt = $obj->app->db->update('users');
                
                // where
                $stmt->where('id','i',$result[0]['id']);
                
                // values
                $stmt->values( array(
                   
                   // userlevel
                   array(
                       'name' => 'userlevel',
                       'type' => 'i',
                       'value'=> $usrlvl
                   )
                ));
                
                // attempt
                $success = $obj->app->db->statement_execute($stmt);
                
                if( !$success ){
                
                    // good to go
                    return admin::TOOL_FAIL;
                }
                
                $obj->app->redirect('admin/users');
            },
            
            
            /* --------------------------------------- 
             * Lowers a users status within the Garage 
             */
            'devanceuser' => function($obj){
                
                // validate get
                if( !isset($_GET['u']) ){
                    return admin::TOOL_FAIL;
                }
                
                // get the users result using toolbox
                $func = $obj->users_toolbox('user_result',$obj);
                $result = $func($obj);
                
                // add to view
                $obj->view->add('users_result',$result);
                
                // ask for confirmation
                $obj->view->add('confirm_action','demoteuser');
                
                            
                // level name
                $lvl_name = 'GUEST';
                
                // output what the user will now be
                if( $result[0]['userlevel'] > 20 ){
                
                    // is an admin, go to manager
                    $lvl_name = 'MANAGER';
                }else if( $result[0]['userlevel'] > 10 ){
                
                    // is a manager go to moderator
                    $lvl_name = 'MODERATOR';
                }else if( $result[0]['userlevel'] > 0 ){
                
                    // is moderator, go to standard
                    $lvl_name = 'STANDARD';
                }
                
                $message = <<< MSG
                You are about to demote this user to a(n): <br />
                $lvl_name
MSG;
                $obj->view->add('confirm_message',$message);
                
                // succeed
                return admin::TOOL_SUCCESS;
                
            },
            
            
            
            /* Get single user data as provided by url get */
            'user_result' => function($obj){
                
                /* =======================
                 * SQL select current user
                 */
                $stmt = $obj->app->db->select('users');
                
                // join with settings
                $stmt->inner_join( array(
                    'table' => 'profiles',
                    'other' => 'userid',
                    'this'  => 'id'
                ) );
                
                // set where
                $stmt->where('id','i',$_GET['u'],'AND','users');
                
                // get result
                return $obj->app->db->statement_result($stmt);
            }
            
        );
        
        // check if exists
        if( isset( $arr[$tool] ) ){
            return $arr[$tool];
        }
        
        // otherwise fail
        return null;
        
    }
    
    /** manage toolbox is a list of tools to use to use to manage
     *  the shop content and display like categories, etc.
     *  @param string $tool the name of the tool to return
     *  @return callable a callable function for the tool to use
     */
    private function manage_toolbox( $tool, $obj ){
        
        // create the list of our tools
        $arr = array(
        
            // the test function to demonstrate usability
            'test' => function($obj){
                echo 'test';
                
                // successful return
                return admin::TOOL_SUCCESS;
            },
            
            /* update category information from the manageshop area */
            'updatecategories' => function($obj){
                
                // make sure there is a category to get
                if( !isset($_GET['c']) ){
                    return admin::TOOL_FAIL;
                }
                
                
                /* Check for update request */
                if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
                    
                    
                    // validate
                    if(
                        !isset( $_POST['display_name'] ) ||
                        $_POST['display_name'] == null ||
                        !isset( $_POST['name'] ) ||
                        $_POST['name'] == null ||
                        !isset( $_POST['description'] ) ||
                        $_POST['description'] == null ||
                        (   isset($_POST['category_order']) && 
                            !is_numeric($_POST['category_order'])
                        )
                    ){
                        $obj->view->add('update_message',
                            'On or more required fields have not been
                            provided or are malformed.');
                    } else {
                    
                        
                        // set up default parent id
                        $parentid = 0;
                        $category_order = 99;
                        
                        // set up parentid
                        if( isset($_POST['parentid']) && 
                            $_POST['parentid'] != null 
                        ){
                            $parentid = (int) $_POST['parentid'];
                        }
                        
                        // set up category order
                        if( isset($_POST['category_order']) && 
                            $_POST['category_order'] != null 
                        ){
                            $category_order = 
                                (int)$_POST['category_order'];
                        }
                        
                        
                        /* =====================================
                         * SQL Insert new category into database
                         */
                        
                        $stmt = $obj->app->db->update('categories');
                        
                        // never forget to set the where
                        $stmt->where('id','i',$_GET['c']);
                        
                        // set up insert values
                        $stmt->values( array(
                            
                            // display name
                            array(
                                'name' => 'display_name',
                                'type' => 's',
                                'value'=> $_POST['display_name']
                            ),
                            
                            // name
                            array(
                                'name' => 'name',
                                'type' => 's',
                                'value'=> $_POST['name']
                            ),
                            
                            // description
                            array(
                                'name' => 'description',
                                'type' => 's',
                                'value'=> $_POST['description']
                            ),
                            
                            // category order
                            array(
                                'name' => 'category_order',
                                'type' => 'i',
                                'value'=> $category_order
                            ),
                            
                            
                            // parentid
                            array(
                                'name' => 'parentid',
                                'type' => 'i',
                                'value'=> $parentid
                            )
                            
                        ));
                        
                        
                        // finally execute update
                        $success = $obj->app->db->
                            statement_execute($stmt);
                        
                        if( !$success ){
                            
                            // print error message
                            $obj->view->add('update_message',
                                'There was an error updating our 
                                database');
                        } 
                        
                    }
                }
                
                /* ====================================
                 * SQL to select the category to update
                 */
                
                // select
                $stmt = $obj->app->db->select('categories');
                
                // set wheres
                $stmt->where( 'id', 'i' ,$_GET['c'] );
                
                // get results
                $result = $obj->app->db->statement_result($stmt);
                
                
                // check for valid result
                if( count($result) > 0 ){
                
                    // and add it
                    $obj->view->add('display_name_value',
                        $result[0]['display_name']);
                    $obj->view->add('name_value',
                        $result[0]['name']);
                    $obj->view->add('description_value',
                        $result[0]['description']);
                    $obj->view->add('category_order_value',
                        $result[0]['category_order']);
                    $obj->view->add('parentid_value',
                        $result[0]['parentid']);
                } else {
                
                    // else no results
                    $obj->view->add('display_name_value','');
                    $obj->view->add('name_value','');
                    $obj->view->add('description_value','');
                    $obj->view->add('category_order_value','');
                    $obj->view->add('parentid_value','');
                }
                
                // set correct value
                $obj->view->add('category_info', true);
                
                // successful return
                return admin::TOOL_SUCCESS;
            },
            
            
            /* Displays delete confirmation */
            'confirmdelete' => function($obj){
                
                // check for valid c
                if( !isset($_GET['c']) ){
                    
                    //fail
                    return admin::TOOL_FAIL;
                }
                
                /* ==================================
                 * SQL to select delete category info
                 */
                
                $stmt = $obj->app->db->select('categories');
                
                // where
                $stmt->where('id','i',$_GET['c']);
                
                // get results
                $result = $obj->app->db->statement_result($stmt);
                
                // check validity
                if( count($result) > 0 ){
                    
                    // add
                    $obj->view->add('delete_info',$result[0]);
                } else {
                    
                    // no results
                    $obj->view->add('delete_info',null);
                }
                
                // success
                return admin::TOOL_SUCCESS;
            },
            
            
            /* Deletes a category */
            'deletecategory' => function($obj) {
                
                // validate
                if( !isset( $_GET['c']) ){
                    return admin::TOOL_FAIL;
                }
                
                /* =========================================
                 * SQL to remove active status from category
                 */
                
                $stmt = $obj->app->db->update('categories');
                
                // set where
                $stmt->where('id','i',$_GET['r']);
                
                // execute the statement
                $success = $obj->app->db->statement_execute($stmt);
                
                // test success
                if( $success ){
                    
                    // redirect
                    $obj->app->redirect('admin/reviews');
                }
                
                // so something faily
                $obj->view->add('update_message',
                    'There was an error updating our database.');
                
                return admin::TOOL_FAIL;
            },
            
            /* Inserts a new category */
            'newcategory' => function($obj) {
                
                // set up attempt vars
                $obj->view->add('create_attempt',true);
                
                
                // check for appropriate post data
                if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
                    
                            
                /* =====================
                 * Set up default values
                 */
                
                $display_name_value = $_POST['display_name'];
                $name_value = $_POST['name'];
                $description_value = $_POST['description'];
                $category_order_value = $_POST['category_order'];
                $parentid_value = $_POST['parentid'];
                
                    
                // display name default
                $obj->view->add('display_name_value', 
                    $display_name_value );
                
                // name default
                $obj->view->add('name_value', $name_value );
                    
                // description default
                $obj->view->add('description_value', 
                    $description_value);
                    
                // order default
                $obj->view->add('category_order_value', 
                    $category_order_value);
                
                // parent id default
                $obj->view->add('parentid_value', $parentid_value);
                    
                    // validate
                    if(
                        !isset( $_POST['display_name'] ) ||
                        $_POST['display_name'] == null ||
                        !isset( $_POST['name'] ) ||
                        $_POST['name'] == null ||
                        !isset( $_POST['description'] ) ||
                        $_POST['description'] == null ||
                        (   isset($_POST['category_order']) && 
                            !is_numeric($_POST['category_order'])
                        )
                    ){
                        $obj->view->add('update_message',
                            'On or more required fields have not been
                            provided or are malformed.');
                        return admin::TOOL_FAIL;
                    }
                    
                    
                    // set up default parent id
                    $parentid = 0;
                    $category_order = 99;
                    
                    // set up parentid
                    if( isset($_POST['parentid']) && 
                        $_POST['parentid'] != null 
                    ){
                        $parentid = (int) $_POST['parentid'];
                    }
                    
                    // set up category order
                    if( isset($_POST['category_order']) && 
                        $_POST['category_order'] != null 
                    ){
                        $category_order = (int)$_POST['category_order'];
                    }
                    
                    
                    /* =====================================
                     * SQL Insert new category into database
                     */
                    
                    $stmt = $obj->app->db->insert('categories');
                    
                    // set up insert values
                    $stmt->values( array(
                        
                        // display name
                        array(
                            'name' => 'display_name',
                            'type' => 's',
                            'value'=> $_POST['display_name']
                        ),
                        
                        // name
                        array(
                            'name' => 'name',
                            'type' => 's',
                            'value'=> $_POST['name']
                        ),
                        
                        // description
                        array(
                            'name' => 'description',
                            'type' => 's',
                            'value'=> $_POST['description']
                        ),
                        
                        // category order
                        array(
                            'name' => 'category_order',
                            'type' => 'i',
                            'value'=> $category_order
                        ),
                        
                        
                        // parentid
                        array(
                            'name' => 'parentid',
                            'type' => 'i',
                            'value'=> $parentid
                        )
                        
                    ));
                    
                    
                    // finally execute insert
                    $success = $obj->app->db->statement_execute($stmt);
                    
                    if( !$success ){
                        
                        // print error message
                        $obj->view->add('update_message',
                            'There was an error updating our database');
                        
                        // and faile
                        return admin::TOOL_FAIL;
                        
                    } else {
                        
                        // redirect to main manage shop
                        $obj->app->redirect('admin/manageshop');
                    }
                    
                } else {
                
                    // FAILURE!!!!
                    return admin::TOOL_FAIL;
                }
                
                return admin::TOOL_SUCCESS;
            }
            
        );
        
        // if we have the tool
        if( isset($arr[$tool]) ){
            
            // send it back
            return $arr[$tool];
        }
        
        return false;
    }
    
    
    
    /** themes toolbox is the list of tools to use to manage the themes
     *  available to the garage.
     *  @param string $tool the name of the tool to to select from the
     *         toolbox
     *  @return callable a function for the tool to use
     */
    public function themes_toolbox( $tool, $obj ){
        
        // set up toolbox
        $arr = array(
            
            'usetheme' => function($obj)
            {
                // check for theme
                if( !isset($_GET['theme']) ){
                    return admin::TOOL_FAIL;
                }
                
                // get the path
                $theme_path = $_GET['theme'];
                
                // test if it exists
                if( file_exists($theme_path) && is_dir($theme_path) )
                {
                    // save settings
                    $obj->app->settings('theme')->
                        set('theme',$theme_path)->
                        save('; Configure Garage theme paths');
                    
                    return admin::TOOL_SUCCESS;
                }
                
                return admin::TOOL_FAIL;
            }
            
        );
        
        // if we have the tool
        if( isset($arr[$tool]) ){
            
            // send it back
            return $arr[$tool];
        }
        
        return false;
    }
	
	/** Toolbox used for reviews page
	*/
	public function reviews_toolbox( $tool, $obj ){
        
        // array of tools
        $arr = array(
            
            /* display all the reviews made in garage sale */
            'display' => function($obj){
                
                /* ====================
                 * SQL select all users
                 */
                $stmt = $obj->app->db->select('reviews');
				$stmt->where('status','i',0);
                
                // get result
                $result = $obj->app->db->statement_result($stmt);
                
                // add to view
                $obj->view->add('reviews_result',$result);
                
                
                // succeed
                return admin::TOOL_SUCCESS;
            },
		
			'deletereview' => function($obj) {
                
                // validate
                if( !isset( $_GET['r']) ){
                    return admin::TOOL_FAIL;
                }
                
                // get the users result using toolbox
				
                $stmt = $obj->app->db->update('reviews');
                
                // set where
                $stmt->where('id','i',$_GET['r']);
				
				// update status
				$stmt->values( array(
                   
                   // userlevel
                   array(
                       'name' => 'status',
                       'type' => 'i',
                       'value'=> 1
                   )
                ));
                // execute the statement
                $success = $obj->app->db->statement_execute($stmt);
                // test success
                if( $success ){
                    
                    // redirect
                    $obj->app->redirect('admin/reviews');
                }
                
                // so something failed
                $obj->view->add('update_message',
                    'There was an error updating our database.');
                
                return admin::TOOL_FAIL;
            },
			'deleteconfirm' => function($obj){
                
                // validate get
                if( !isset($_GET['r']) ){
                    return admin::TOOL_FAIL;
                }
                
                // get the reviews results using toolbox
                $func = $obj->reviews_toolbox('review_result',$obj);
                $result = $func($obj);
                
                // add to view
                $obj->view->add('reviews_result',$result);
                
                // ask for confirmation
                $obj->view->add('confirm_action','deletereview');

                $message = <<< MSG
                You are about to delete this review.
MSG;
                $obj->view->add('confirm_message',$message);
                
                // succeed
                return admin::TOOL_SUCCESS;
                
            },
			
			'review_result' => function($obj){
                
                /* =======================
                 * SQL select review
                 */
                $stmt = $obj->app->db->select('reviews');
                
                // set where
                $stmt->where('id','i',$_GET['r'],'AND','reviews');
                
                // get result
                return $obj->app->db->statement_result($stmt);
            }
		);
		 // if we have the tool
        if( isset($arr[$tool]) ){
            
            // send it back
            return $arr[$tool];
        }
        
        return false;
	}
	
	/** Toolbox used for reviews page
	*/
	public function reports_toolbox( $tool, $obj ){
        
        // array of tools
        $arr = array(
            
            /* display all the reviews made in garage sale */
            'display' => function($obj){
                
                /* ====================
                 * SQL select all users
                 */
                $stmt = $obj->app->db->select('reports');
				$stmt->where('status','i',0);
                
                // get result
                $result = $obj->app->db->statement_result($stmt);
                
                // add to view
                $obj->view->add('reports_result',$result);
                
                
                // succeed
                return admin::TOOL_SUCCESS;
            },
		
			'reportsolved' => function($obj) {
                
                // validate
                if( !isset( $_GET['r']) ){
                    return admin::TOOL_FAIL;
                }
                
                // get the users result using toolbox
				
                $stmt = $obj->app->db->update('reports');
                
                // set where
                $stmt->where('id','i',$_GET['r']);
				
				// update status
				$stmt->values( array(
                   
                   // userlevel
                   array(
                       'name' => 'status',
                       'type' => 'i',
                       'value'=> 1
                   )
                ));
                
                // execute the statement
                $success = $obj->app->db->statement_execute($stmt);
                
                // test success
                if( $success ){
                    
                    // redirect
                    $obj->app->redirect('admin/reports');
                }
                
                // so something failed
                $obj->view->add('update_message',
                    'There was an error updating our database.');
                
                return admin::TOOL_FAIL;
            },
			'solvedconfirm' => function($obj){
                
                // validate get
                if( !isset($_GET['r']) ){
                    return admin::TOOL_FAIL;
                }
                
                // get the reviews results using toolbox
                $func = $obj->reports_toolbox('report_result',$obj);
                $result = $func($obj);
                
                // add to view
                $obj->view->add('reports_result',$result);
                
                // ask for confirmation
                $obj->view->add('confirm_action','reportsolved');

                $message = <<< MSG
                You are about to resolve this issue.
MSG;
                $obj->view->add('confirm_message',$message);
                
                // succeed
                return admin::TOOL_SUCCESS;
                
            },
			
			'report_result' => function($obj){
                
                /* =======================
                 * SQL select review
                 */
                $stmt = $obj->app->db->select('reports');
                
                // set where
                $stmt->where('id','i',$_GET['r'],'AND','reports');
                
                // get result
                return $obj->app->db->statement_result($stmt);
            }
		);
		 // if we have the tool
        if( isset($arr[$tool]) ){
            
            // send it back
            return $arr[$tool];
        }
        
        return false;
	}
	
	/** Toolbox used for reviews page
	*/
	public function contacts_toolbox( $tool, $obj ){
        
        // array of tools
        $arr = array(
            
            /* display all the reviews made in garage sale */
            'display' => function($obj){
                
                /* ====================
                 * SQL select all users
                 */
                $stmt = $obj->app->db->select('contact');
				$stmt->where('status','i',0);
                
                // get result
                $result = $obj->app->db->statement_result($stmt);
                
                // add to view
                $obj->view->add('contacts_result',$result);
                
                
                // succeed
                return admin::TOOL_SUCCESS;
            },
		
			'contactsolved' => function($obj) {
                
                // validate
                if( !isset( $_GET['c']) ){
                    return admin::TOOL_FAIL;
                }
                
                // get the users result using toolbox
				
                $stmt = $obj->app->db->update('contact');
                
                // set where
                $stmt->where('id','i',$_GET['c']);
				
				// update status
				$stmt->values( array(
                   
                   // userlevel
                   array(
                       'name' => 'status',
                       'type' => 'i',
                       'value'=> 1
                   )
                ));
                
                // execute the statement
                $success = $obj->app->db->statement_execute($stmt);
                
                // test success
                if( $success ){
                    
                    // redirect
                    $obj->app->redirect('admin/contacts');
                }
                
                // so something failed
                $obj->view->add('update_message',
                    'There was an error updating our database.');
                
                return admin::TOOL_FAIL;
            },
			'solvedconfirm' => function($obj){
                
                // validate get
                if( !isset($_GET['c']) ){
                    return admin::TOOL_FAIL;
                }
                
                // get the contacts results using toolbox
                $func = $obj->contacts_toolbox('contact_result',$obj);
                $result = $func($obj);
                
                // add to view
                $obj->view->add('contacts_result',$result);
                
                // ask for confirmation
                $obj->view->add('confirm_action','contactsolved');

                $message = <<< MSG
                You are about to resolve this issue.
MSG;
                $obj->view->add('confirm_message',$message);
                
                // succeed
                return admin::TOOL_SUCCESS;
                
            },
			
			'contact_result' => function($obj){
                
                /* =======================
                 * SQL select review
                 */
                $stmt = $obj->app->db->select('contact');
                
                // set where
                $stmt->where('id','i',$_GET['c'],'AND','contact');
                
                // get result
                return $obj->app->db->statement_result($stmt);
            }
		);
		 // if we have the tool
        if( isset($arr[$tool]) ){
            
            // send it back
            return $arr[$tool];
        }
        
        return false;
	}
    
    
    /** The index function is the default action to be called when
     *  accessing a controller
     */
    function index( $args )
    {
        // attempt to get settings for number of posts per page
        $settings = $this->app->settings('browse');
        
        // default top no message
        $action_message = null;
        
        // check for post data
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
            if( isset($_POST['listings_per_page']) && 
                $_POST['listings_per_page'] != null &&
                is_numeric( $_POST['listings_per_page'] )
            ){
            
                // set values and save
                $settings->set('per_page',
                    (int)$_POST['listings_per_page']);
                $settings->save('; Browsing options');
                
                // set action message
                $action_message = 'Settings updated';
            } else {
            
                // failed message
                $action_message = 'Input must be a number.';
            }
        }
        
        // get value
        $listings_per_page_value = $settings->get('per_page');
        
        // check for set value
        if( $listings_per_page_value == null ){
        
            // let default be 10
            $listings_per_page_value = 10;
        }
        
        /* -----------------------------
         * Define action links for forms
         */
        $this->view->add('per_page_action', 
            $this->app->form_path('admin?action=per_page')
        );
        
        
        // add value to page
        $this->view->add('listings_per_page_value',
            $listings_per_page_value);
        
        // add action message
        $this->view->add('action_message', $action_message );
    }
    
    /** Manage shop setup content: categories, layout displays, etc.
     *  @param array $args the arguments passed in the users url request
     *         in this context we will be receiving a 'tool' param
     */
    function manageshop( $args )
    {
        
        // manage shop title, can be over ridden
        $this->view->add('page_title','Manage Shop');
        
        /* =====================
         * Set up default values
         */
        
        // display name default
        $this->view->add('display_name_value', '' );
        
        // name default
        $this->view->add('name_value', '' );
            
        // description default
        $this->view->add('description_value', '');
            
        // order default
        $this->view->add('category_order_value', '');
        
        // parent id default
        $this->view->add('parentid_value', 0);
        
        
        // if tool is not null
        if( $args['tool'] != null ){
            
            // use our fancy tool box
            if( $func = $this->manage_toolbox($args['tool'], $this) ) {
                
                // check fro success, default to no
                $success = false;
                
                // choose tool and execute
                $success = $func($this);
            }
        }
        
        
        
        /* ===========
         * Other stuff
         */
         
        // over ride admin link
        $this->view->add('self_link', 
            $this->app->form_path('admin/manageshop/') );
        
        // pass categories to the thing
        $categories = $this->app->model('categories')->
            get_category_children(0);
        
        // add categories to view
        $this->view->add( 'categories', $categories );
        
    }
    
    
    /** Manage user information and administration levels. Suspend,
     *  review, and recover user information.
     *  @param array $args The arguments provided by the user in the
     *         url path.
     */
    function users( $args ){
        
        // select toolbox
        if( $args['tool'] == null ){
            $tool = 'display';
        } else {
            $tool = $args['tool'];
        }
        
        // page title
        $this->view->add('page_title','Manage Garage Users');
        
        // use our fancy tool box
        if( $func = $this->users_toolbox($tool,$this) ) {
            
            // check fro success, default to no
            $success = false;
            
            // choose tool and execute
            $success = $func($this);
        }
        
        // add self link
        $this->view->add('self_link',
            $this->app->form_path('admin/users')
        );
        
    }
    
    
    /** Manage site themes and layouts
     *  @param array $args The arguments provided by the user in the
     *         url path. This action is concerned with the 'tool' arg
     */
    function themes( $args ){
        
        // select toolbox
        if( $args['tool'] == null ){
            $tool = 'display';
        } else {
            $tool = $args['tool'];
        }
        
        // page title
        $this->view->add('page_title','Manage Garage Themes');
        
        // use our fancy tool box
        if( $func = $this->themes_toolbox($tool,$this) ) {
            
            // check fro success, default to no
            $success = false;
            
            // choose tool and execute
            $success = $func($this);
        }
        
        // get the list of themes
        $theme_dirs = glob('themes/*', GLOB_ONLYDIR);
        
        // create theme list
        $theme_list = array();
        
        $theme_list[] = array( 'name' => 'Default', 'path' => 'views' );
        
        // build list
        foreach( $theme_dirs as $theme ){
            $theme_list[] = array(
                'name' => $theme,
                'path' => $theme
            );
        }
        
        // add to list
        $this->view->add('theme_list',$theme_list);
        
        // get current theme from settings
        $current_theme = $this->app->settings('theme')->get('theme');
        
        // add to view
        $this->view->add('current_theme',$current_theme);
        
        // add self link
        $this->view->add('self_link',
            $this->app->form_path('admin/themes')
        );
        
    }
	
	/**Controller for reports page
	*/
	function reports($args){
        $this->view->add('page_title','Reports');
        
		//Set tool box
		if( $args['tool'] == null ){
            $tool = 'display';
        } else {
            $tool = $args['tool'];
        }
		 if( $func = $this->reports_toolbox($tool,$this) ) {
            
            // check for success, default to no
            $success = false;
            
            // choose tool and execute
            $success = $func($this);
        }
        
        // add self link
        $this->view->add('self_link',
            $this->app->form_path('admin/reports')
        );
	}
	
	/**Controller for reviews page
	*/
	function reviews($args){
		$this->view->add('page_title','Reviews');
		
		//Set tool box
		if( $args['tool'] == null ){
            $tool = 'display';
        } else {
            $tool = $args['tool'];
        }
		 if( $func = $this->reviews_toolbox($tool,$this) ) {
            
            // check for success, default to no
            $success = false;
            
            // choose tool and execute
            $success = $func($this);
        }
        
        // add self link
        $this->view->add('self_link',
            $this->app->form_path('admin/reviews')
        );
	}
	
	/** Action for contacts page
	 *  @param array $args Arguments parsed by router from the url 
	 */
	function contacts($args){
		$this->view->add('page_title','Contacts');
		
		//Set tool box
		if( $args['tool'] == null ){
            $tool = 'display';
        } else {
            $tool = $args['tool'];
        }
		 if( $func = $this->contacts_toolbox($tool,$this) ) {
            
            // check for success, default to no
            $success = false;
            
            // choose tool and execute
            $success = $func($this);
        }
        
        // add self link
        $this->view->add('self_link',
            $this->app->form_path('admin/contacts')
        );
	}
	
	/** Loads an extension administration section into the view and 
	 *  displays its back end.
	 */
	function extensions( $args )
	{
	    // require admin extension
	    require_once('app/extensionadmin.php');
	    
	    // get tool/extension to admin
	    $tool = $args['tool'];
	    
	    // check if folder/file exists
	    if( !file_exists('extensions/'.$tool.'/admin.php') ){
	        return;
	    }
	    
	    // get the file into the scope
        require_once('extensions/'.$tool.'/admin.php' );
        
        // and get the admin object
        $admin_tool_name = $tool.'_admin';
        $admin_tool = new $admin_tool_name($this,$tool);
        
        
        $this->view->add('toolbuttons',$admin_tool->get_tools());
        
        
        // default to index action
        $action = 'index';
        
        // check if action is defined
        if( isset($_GET['action']) ){
            $action = $_GET['action'];
        }
        
        // execute the default action
        $admin_tool->action( $action );
	}
}

?>
