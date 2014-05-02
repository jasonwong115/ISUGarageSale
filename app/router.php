<?php
namespace GarageSale;
/** app/router.php
 *  Interprets the query string/url input and chooses the appropriate
 *  controller to load.
 *  Example:
 *  Say I have a pages controller with an action called display and
 *  expects a variable called $id. I would set up a new route like:
 *
 *  router->add_route('/path',
 *      array('controller' => 'pages',      // controller for path  
 *          'action' => 'display',          // default action to prefer
 *          'args' => array( 'id' => null ) // list of arguments in 
 *      )                                   // order they appear path in
 *  );                                      // the path
 *
 *  All args should be initialized to null
 */
class Router {
    
    /** The $routes array store the information on routes to take.
     *  The format is 'path' => 'controller to load'. Or choose a 
     *  general controller with 'path/{controller}/{action}/{var}'.
     *  Each entry will have an array containing the controller, a 
     *  boolean value indicating if an action is specified and an array
     *  of expected arguments.
     */
    private $routes = array();
    
    /** The $extensions array store the information on routes to take
     *  for extensions in particular.
     */
    private $extensions = array();
    
    /** The $route404 array contains the default controller/actions to
     *  load if a requested path is not found in the routing table.
     */
    private $route404; 
        
    /** Construct a new router instance with a different 404 route
     *  @param $route404 the controller definition array for the 404
     *         error message.
     */
    function __construct( $route404 = array(
            'controller' => 'error404',
            'action'     => 'index',
            'args'       => array()
        ) 
    )
    {
        $this->set_route404( $route404 );
    }
    
    /** Sets the 404 error message controller route information for the
     *  route table.
     *  @param $route404 the controller definition array for the 404
     *         error message.
     */
    function set_route404( $route404 )
    {
        $this->route404 = $route404;
    }
    
    /** Gets the control array for a 404 route.
     *  @return an array specifying the controller information for a 404
     *          route.
     */
    function get_route404()
    {
        return $this->route404;
    }
    
    /** The add route adds the specified path to the routes array and
     *  saves the controls array to that paths details.
     *  TODO: add checks to ensure proper input
     *  @param $path the path to specify the controller
     *  @param $controls the array specifying the controls for this 
     *         controller such as controller class default action and
     *         required arguments 
     */
    function add_route( $path, $controls )
    {
        // simply add the path and the controls to the routes
        // array.
        $this->routes[$path] = $controls;
    }
    
    /** Adds a route to a the router that connects to an extension
     *  extensions are only checked if all other routes fall through.
     *  TODO: add checks to ensure proper input
     *  @param string $path the path to specify the controller
     *  @param array $controls specifying the controls for this 
     *         controller such as controller class default action and
     *         required arguments 
     */
    function add_extension( $path, $controls )
    {
        // simply add the path and the controls to the routes
        // array.
        $this->extensions[$path] = $controls;
    }
    
    /** Searches for controls in specific route tables
     *  @param array $search The route table to search
     *  @return The controls found or null if nothing
     */
    function get_controls_in( $search ){
            
        // get the uri path info segments 
        $path_parts = $this->getPathInfo();
        
        // number of items in the path parts
        $count = count($path_parts);
                
        // save the recreated path
        $recreated_path = '';
        $action_path = null;
                
        // keep track of what level we are evaluating
        $path_level = 3;
        
        // keep track of current controller
        $current_controls = null;
        
        // loop through path parts and check if there is a route for it
        for( $i=1; $i < $count; ++$i ){
            
            // looking at controller
            if( $path_level == 3 ){    
                // grow the recreated path
                $recreated_path .= '/' . $path_parts[$i];
                
                // check if this exists in $routes array
                if( array_key_exists( $recreated_path, $search ) )
                {
                    // if so this is our controller
                    $current_controls = $search[$recreated_path];
                    
                    // reduce path level
                    $path_level = $path_level - 1;
                }
                
            // looking for action
            }else if( $path_level == 2 ){
                // action will be next part of controls
                // but only if there is actually a string present
                if( strlen($path_parts[$i]) > 0 ){
                    $current_controls['action'] = $path_parts[$i];
                }
                
                // register th next part of the recreated path
                $action_path = $path_parts[$i];
                
                // reduce path level
                $path_level = $path_level - 1;
                
            // looking for args
            } else {
                // hijack the loop at this point and fill in the 
                // remaining variables
                
                // track the current offset from $i
                $offset = 0;
                
                // loop through expected args
                foreach( $current_controls['args'] as $arg => $av ){
                    
                    // if within path parts bounds
                    if( $i+$offset < $count ){
                        // save argument value
                        $current_controls['args'][$arg] = 
                            $path_parts[$i+$offset];
                            
                    // otherwise we've gathered all there is
                    }else{
                        break;
                    }
                    
                    // increase the offset
                    $offset++;
                }
                
                break;
            }
        }
        
        if( $current_controls == null ){
            // found nothing, run 404 message
            return null;
        }
        
        // update args with the path to this controller
        $current_controls['args']['controller_path'] = $recreated_path;
        
        // action path is equal to the action, best used in before/after
        $current_controls['args']['action_path'] = 
            $current_controls['action'];
        
        // return the fixed control information
        return $current_controls;
    }
    
    /** This function will get the appropriate controls array that is
     *  stored in its routes table.
     *  @return An array containing controller information for use by
     *          the app, or if not found null
     */
    function get_controls()
    {
        // get regular routes
        return $this->get_controls_in( $this->routes );
    }
    
    /** This function will get the appropriate controls array that is
     *  stored in its extensions table.
     *  @return An array containing controller information for use by
     *          the app, or if not found null
     */
    function get_extensions(){
        
        // get regular routes
        return $this->get_controls_in( $this->extensions );
    }
    
    /** Explode the path information (info that follows index.php)
     *  for use in identifying paths.
     *  @return an array of segments from the $_SERVER['PATH_INFO'] 
     */
    static private function getPathInfo() {
        $path_info = Router::PATH_INFO();
        
        if( strlen($path_info) == 0 ){
            $path_info = '/';
        }
        return explode( "/", $path_info );
    }
    
    static function PATH_INFO()
    {
        if (array_key_exists('PATH_INFO', $_SERVER) === true){
            return $_SERVER['PATH_INFO'];
        }

        //$whatToUse = basename(__FILE__); // see below
        $whatToUse = 'index.php';
        return substr($_SERVER['PHP_SELF'], 
            strpos($_SERVER['PHP_SELF'], $whatToUse) + 
                    strlen($whatToUse)
        );
    }
}
?>
