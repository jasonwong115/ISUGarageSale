<?php
namespace GarageSale;
/** app/extensionadmin.php
 *  Provides an interface for extensions be able to administer the admin
 *  sections of the extension
 */
class ExtensionAdmin {
    
    /** Instance of the primary application GarageSale class */
    protected $controller = null;
    
    /** The location of this particular extension (i.e. the dir) */
    protected $location = null;
    
    /** Constructs a new instance of the extensionadmin class and
     *  initializes with the application instance ready.
     *  @param object $controller An instance of the Controller class
     *  @param string $location Saves the location of this extension
     */
    function __construct( $controller, $location )
    {
        $this->controller = $controller;
        $this->location = $location;
    }
    
    
    /** Gets available tools for this extension
     *  @return array List of tools for this extensions administration
     *          array(
     *              'link' => $link_to_tool,
     *              'text' => $tool_text
     *          )
     */
    function get_tools()
    {
        return array();
    }
    
    
    /** Installs the extension if it has not already been installed
     *  @return true if installation was successful or already installed
     *  or false if otherwise 
     */
    function install()
    {
        return true;
    }
    
    
    /** Executes an action if it exists for the extension admin
     *  @param string $action name of the action to run
     */
    function action( $action )
    {
        // check existance
        if(method_exists($this,$action)){
            // run
            $this->$action();
        } else {
            // run default
            $this->index();
        }
    }
    
    /** Index action is the default action to run if nothing is found
     */
    function index()
    {
        // do nothing
    }
}

?>
