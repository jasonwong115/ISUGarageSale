<?php
/** controllers/error404.php
 *  The homepage controller which displays the welcome/ main page
 */
class error404 extends GarageSale\Controller {
    
    
    /** The index function is the default action to be called when
     *  accessing a controller
     */
    function index( $args )
    {
        $this->view->add('page_title','Error 404');
    }
    
}

?>
