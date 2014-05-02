<?php
/** extensions/blog/admin.php
 *  Administers the blog extension. Provides functions for configuring
 *  the blog.
 */
class forum_admin extends GarageSale\ExtensionAdmin 
{
    
    /** Get the blog adminstration tools to generate the links 
     *  @return array Array of tool links
     */
    function get_tools()
    {
        // get installed value
        $settings = $this->controller->app->settings('forum_extension');
        $installed = $settings->get('installed');
    
        // returns toolbuttons array
        $tools = array(
        
            // manage forum boards
            array(
                'link' => '?action=boards',
                'text' => 'Manage Boards'
            ),
            
            array(
                'link' => '?action=moderators',
                'text' => 'Manage Moderators'
            ),
        
            // sets up the posts action link
            array(
                'link' => '?action=posts',
                'text' => 'Manage Posts'
            ),
            
            // sets up the layout action link
            array(
                'link' => '?action=layout',
                'text' => 'Manage Layout'
            )
        );
        
        // check if the system is installed
        if( $installed == null || $installed != 'true' ){
        
            // add install link
            $tools[] = array(
                'link' => '?action=install',
                'text' => 'Install'
            );
        }
        
        // return the tools
        return $tools;
    }
    
    
    /** Provides an action for adding and editing boards in the forum */
    function boards()
    {
        $action_message = null;
    
        /* --------------------------------------------------------
         * Check if a do request has been submitted for this action
         */
        if( isset($_GET['do']) ){
            
            // creating a new group
            if( $_GET['do'] == 'addgroup' && 
                $_SERVER['REQUEST_METHOD'] == 'POST' &&
                isset($_POST['name']) && $_POST['name'] != null 
            ){
                
                
                // get the groups model
                $groups_model = $this->controller->app->
                    model('forum_groups','forum/models');
                
                // get success
                $success = $groups_model->new_item( $_POST );
                
                // Check for success 
                if( $success ){
                
                    // good
                    $action_message = "Group added successfully.";
                } else {
                
                    // bad
                    $action_message = "There was an error adding your
                        group to our database.";
                }
            
            
            // do is to add a new board
            } else if( $_GET['do'] == 'add' && 
                $_SERVER['REQUEST_METHOD'] == 'POST' &&
                isset($_POST['name']) && $_POST['name'] != null 
            ){
                
                
                // get the boards model
                $boards_model = $this->controller->app->
                    model('forum_boards','forum/models');
                
                // get success
                $success = $boards_model->new_item( $_POST );
                
                // Check for success 
                if( $success ){
                
                    // good
                    $action_message = "Board added successfully.";
                } else {
                
                    // bad
                    $action_message = "There was an error adding your
                        board to our database.";
                }
            }
            
            
            // check for bad required fields
            if( $_SERVER['REQUEST_METHOD'] == 'POST' && 
                isset($_POST['name']) && $_POST['name'] == NULL
            ){
                // add action message
                $action_message = 'One or more required fields have not
                    been provided.';
            }
        }
        
        // add action message
        $this->controller->view->add('action_message',$action_message);
        
        
        /* -------------------
         * Use the forum model
         */
        $boards_model = $this->controller->
            app->model('forum_boards','forum/models');
        
        // get grouped boards
        $grouped_boards = $boards_model->get_grouped();
        
        // add to view
        $this->controller->view->add('grouped_boards',$grouped_boards);
        
        /* -----------------------------------
         * Define available actions for boards
         */
        $add_group_action = $this->controller->app->form_path(
            'admin/extensions/'.$this->location.
            '?action=boards&do=addgroup'
        );
        // add
        $this->controller->view->add( 'add_group_action', 
            $add_group_action );
        
        // action for editing a group
        $edit_group_action = $this->controller->app->form_path(
            'admin/extensions/'. $this->location.
            '?action=boards&do=editgroup'
        );
        // add
        $this->controller->view->add( 'edit_group_action', 
            $edit_group_action );
        
        //
        $delete_group_action = $this->controller->app->form_path(
            'admin/extensions/' . $this->location .
            '?action=boards&do=delgroup'
        );
        // action for deleting a group
        $this->controller->view->add( 'delete_group_action', 
            $delete_group_action );
        
        /* ---------------------------------------------
         * Define available user action links for boards
         */
        
        // action fro deleting board
        $delete_action = $this->controller->app->form_path(
            'admin/extensions/'.$this->location.'?action=boards&do=del'
        );
        $this->controller->view->add('delete_action',$delete_action);
        
        // action for adding board
        $add_action = $this->controller->app->form_path(
            'admin/extensions/'.$this->location.'?action=boards&do=add'
        );
        $this->controller->view->add('add_action',$add_action);
        
        // edit board action
        $edit_action = $this->controller->app->form_path(
            'admin/extensions/'.$this->location.'?action=boards&do=edit'
        );
        $this->controller->view->add('edit_action',$edit_action);
        
        
        
        
        // set page title
        $this->controller->view->add('page_title',
            'Manage Forum Boards');
        
        // add styles to the thingy
        $this->controller->view->add_style('forum/css/all');
        
        // add scripts
        $this->controller->view->add_scripts(
            array('jquery','forum/scripts/boards')
        );
        
        // set subview
        $this->controller->view->
            add_subview( 'forum/views/boards_admin' );
        
    }
    
    
    /** Provides an action for administering the blog layout.
     */
    function layout()
    {
        $this->controller->view->
            add_subview( 'forum/views/layout' );
    }
    
    
    /** Admin action to install the forum to the system.
     */
    function install()
    {
        // check for installation confirmation
        if( isset($_GET['confirm']) && $_GET['confirm'] == 'yes' )
        {
            // do the installation of the groups
            $success = $this->controller->app->db->exec_file(
                'extensions/forum/misc/install.groups.sql'
            );
            
            // install boards
            $success = $success && $this->controller->app->db->
                exec_file( 'extensions/forum/misc/install.boards.sql' );
            
            // install threads script
            $success = $success && $this->controller->app->db->
                exec_file( 'extensions/forum/misc/install.threads.sql');
            
            // install posts script
            $success = $success && $this->controller->app->db->
                exec_file( 'extensions/forum/misc/install.posts.sql');
            
            // install moderators script
            $success = $success && $this->controller->app->db->
                exec_file( 
                    'extensions/forum/misc/install.moderators.sql'
                );
            
            
            
            // if success then redirect to base
            if( $success ){
                
                // load settings
                $settings = $this->controller->app->
                    settings('forum_extension');
                
                // set new setting
                $settings->set('installed','true');
                
                // save
                $settings->save('; Forum extension settings');
                
                // redir
                $this->controller->app->redirect(
                    'admin/extensions/'.$this->location 
                );
            }
            
        }
    
        // usual install message
        $install_message = "Install the forum to the applicaion.";
        
        // get installed value
        $settings = $this->controller->app->settings('forum_extension');
        $installed = $settings->get('installed');
        
        // check if the system is installed
        if( $installed != null && $installed == 'true' ){
        
            // the installation message
            $install_message = "It looks like your system is already 
                isntalled. If you already have it is recommended that
                you do not re run the scripts here.";
            
        } else if( isset($success) ) {
            
            // the installation message
            $install_message = "There was an error installing the forum.
                You man need to report this to the developer.";
        }
        
        // add install message
        $this->controller->view->add('install_message',
            $install_message);
        
        // action to this page
        $install_action = $this->controller->app->form_path(
            'admin/extensions/'.$this->location.
            '?action=install'.
            '&confirm=yes'
        );
        
        // add the install action to the view
        $this->controller->view->add('install_action',$install_action);
        
        
        // choose the subview here
        $this->controller->view->add_subview('forum/views/install');
    }
}
?>
