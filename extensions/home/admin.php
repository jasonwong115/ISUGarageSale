<?php
/** extensions/blog/admin.php
 *  Administers the blog extension. Provides functions for configuring
 *  the blog.
 */
class home_admin extends GarageSale\ExtensionAdmin 
{
    
    /** Get the blog adminstration tools to generate the links 
     *  @return array Array of tool links
     */
    function get_tools()
    {
        // returns toolbuttons array
        return array(
            
            // sets up the layout action link
            array(
                'link' => '?action=layout',
                'text' => 'Manage Layout'
            )
        );
        
    }
    
    
    /** Provides an action for administering the blog layout.
     */
    function layout()
    {
        $this->controller->view->add('err_msg','Configure home pages by 
            editing the views in the extension directory.');
        $this->controller->view->add('subviews',
            array('err_msg') );
    }
}
?>
