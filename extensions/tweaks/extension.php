<?php
/** extensions/tweaks/extension.php
 *  Adds cool features for everything
 */
class tweaks_extension extends GarageSale\Extension {
    
    
    /** Acts as a constructor for the extension and is used to set up
     *  things like scripts and styles
     */
    function load()
    {
        
    }
    
    
    /** Gets the number of unread items in your mailbox
     */
    function unread_count()
    {
        $model = $this->app->model( 'messages' );
        return $model->count_unread(
            $this->app->user->get_user_id()
        );
    }
    
    
    
}
?>
