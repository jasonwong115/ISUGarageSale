<?php
namespace GarageSale;
// view class for rendering page elements
require_once('view.php');
/** app/controller.php
 *  Controller is the base class for Garage Sale controllers.
 *  All other controllers should extend this so that they have access
 *  to all of Garage Sale's features and can be reached by the router
 *  and run by the application.
 */

class Controller {
    
    /** The view object to use for this Controller */
    public $view = null;
    
    /** Construct a new Garage Sale Controller instance by creating a
     *  new view for it. If a default view is created it can later be
     *  overriden by a call to new_view()
     *  @param string $default_view The default view should be a path to 
     *         a file from the views/ directory which will be 
     *         automatically loaded by the controller upon construction. 
     *  @param string $default_theme The default theme to use if one is
     *         not provided
     */
    function __construct( 
        $default_view = 'layout', 
        $default_theme = 'views/' 
    ){
        $this->view = new View( $default_theme );
        $this->view->new_view($default_view);
    }

    /** The after function sets the deffault behavior of the controller
     *  to render whatever view is loaded.
     */
    function after( )
    {
        $this->view->render();
    }
}

?>
