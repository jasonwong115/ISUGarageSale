<?php
/** extensions/blog/admin.php
 *  Administers the blog extension. Provides functions for configuring
 *  the blog.
 */
class blog_admin extends GarageSale\ExtensionAdmin 
{
    
    /** Get the blog adminstration tools to generate the links 
     *  @return array Array of tool links
     */
    function get_tools()
    {
        // get installed value
        $settings = $this->controller->app->settings('blog_extension');
        $installed = $settings->get('installed');
    
        // returns toolbuttons array
        $tools = array(
        
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
    
    /** Provides entry functino for admin section */
    function index()
    {
        $this->layout();
    }
    
    /** Provides an action for administering the blog layout.
     */
    function layout()
    {
        $this->controller->view->add_subview('blog/views/layout');
    }
    
    
    /** Admin action to install the forum to the system.
     */
    function install()
    {
        // check for installation confirmation
        if( isset($_GET['confirm']) && $_GET['confirm'] == 'yes' )
        {
            
            // install boards
            $success = $this->controller->app->db->exec_file
                ('extensions/blog/misc/install.categories.sql');
            
            // install threads script
            $success = $success && $this->controller->app->db->
                exec_file( 'extensions/blog/misc/install.posts.sql');
            
            // install posts script
            $success = $success && $this->controller->app->db->
                exec_file('extensions/blog/misc/install.comments.sql');
            
            
            
            // if success then redirect to base
            if( $success ){
                
                // load settings
                $settings = $this->controller->app->
                    settings('blog_extension');
                
                // set new setting
                $settings->set('installed','true');
                
                // save
                $settings->save('; Blog extension settings');
                
                // redir
                $this->controller->app->redirect(
                    'admin/extensions/'.$this->location 
                );
            }
            
        }
    
        // usual install message
        $install_message = "Install the blog to the applicaion.";
        
        // get installed value
        $settings = $this->controller->app->settings('blog_extension');
        $installed = $settings->get('installed');
        
        // check if the system is installed
        if( $installed != null && $installed == 'true' ){
        
            // the installation message
            $install_message = "It looks like your system is already 
                installed. If you already have it is recommended that
                you do not re run the scripts here.";
            
        } else if( isset($success) ) {
            
            // the installation message
            $install_message = "There was an error installing the forum.
                You may need to report this to the developer.";
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
