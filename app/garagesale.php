<?php
namespace GarageSale;

global $app;


/* INCLUDES */
// config contains necessary application configuration
require_once('app/config.php');

// router, part of the core application
require_once('app/router.php');

// session handling class
// require_once('app/session.php');

// database accessing classes
require_once('app/database.php');

// Model class for accessing data
require_once('app/model.php');

// view class for rendering page elements
require_once('app/view.php');

// user handling class
require_once('app/user.php');

// garage sale controller class
require_once('app/controller.php');

// extensions base class
require_once('app/extension.php');

// settings class
require_once('app/settings.php');

// utility class for major recurring requests that don't fall elsewhere
require_once('app/utility.php');

include('variables/variables.php');



/** app/garagesale.php
 *  The main application class. Runs the whole application. Also 
 *  basic utility functions for internal use within the application
 *  such as link/path formatting.
 */
class App {
    
    /** The router controls the loading of controllers based on input
     *  path.
     */ 
    public $router;
    
    /** Database class for the app to use. Configured before being
     *  set here.
     */
    public $db;
    
    /** Configuration for the entire garage sale site
     */
    public $config;
    
    /** The instance of the current running controller */
    private $controller = null;
    
    /** The GarageSale constructor which initializes the application
     */
    function __construct()
    {
        
    }
    
    /** The run funcion causes the entire application to start, loading
     *  the appropriate controllers and actions.
     */
    function run()
    {
        // first load extensions
        $file_handle = fopen("extensions/manifest", "r");
        // get line by line
        while ($file_handle && !feof($file_handle)) {
            
            // get appropriate name
            $line = trim(fgets($file_handle));
            
            // commented, skip
            if( substr($line,0,1) == ';' )
            {
                continue;
            }
            
            // load router if its in the manifest
            if( file_exists('extensions/'.$line.'/route.php')){
                include( 'extensions/'.$line.'/route.php' );
            }
        }
    
        // get the necessary controller to run:
        $controls = $this->router->get_controls();
        
        // check if found a controller
        if( $controls != null ){
                        
            // attempt to load the controller specified.
            require('controllers/'. $controls['controller'] . '.php' );
            
            
            // see if class and action exist
            if( !class_exists( $controls['controller'] ) || 
                !method_exists(
                    $controls['controller'], 
                    $controls['action']
                )
            ){
                // if not we need to use a different class
                $controls = $this->router->get_route404();
                
                // so load the 404 controller
                require('controllers/'.$controls['controller'].'.php');
            }
        } else {
            
            // try extensions
            $controls = $this->router->get_extensions();
            
            // if still no good, send 404
            if( $controls == null ){
                $controls = $this->router->get_route404();
                
                // so load the 404 controller
                require('controllers/'.$controls['controller'].'.php');
            // otherwise have a good extension
            } else {
                
                // ok now do stuff       
                require('extensions/'.$controls['controller'].
                    '/controller.php');
                
                
                // see if class and action exist
                if( !class_exists( $controls['controller'] ) || 
                    !method_exists(
                        $controls['controller'], 
                        $controls['action']
                    )
                ){
                    // if not we need to use a different class
                    $controls = $this->router->get_route404();
                    
                    // so load the 404 controller
                    require('controllers/'.
                        $controls['controller'].'.php');
                }
            }
            
        }
        
        // get controller and action name
        $controller_name    = $controls['controller'];
        $action_name        = $controls['action'];
        
        // load themes
        $theme_path = $this->settings('theme')->get('theme').'/';
        
        // start new controller instance
        $controller = new $controller_name('layout',$theme_path);
        
        $controller->app = $this;
        
        // save controller for later
        $this->controller = $controller;
        
        // add default subviews
        $this->controller->view->
            add_subview($controller_name.'_'.$action_name);
        
        // add controller style
        $this->controller->view->
            add_style($controller_name);
        
        // see if controller has a before method and run it
        if( method_exists( $controller, 'before') ){
            $controller->before( $controls['args'] );
        }
        
        // run action
        $controller->$action_name( $controls['args'] );
        
        // see if controller has an after method and run it
        if( method_exists( $controller, 'after') ){
            $controller->after( $controls['args'] );
        }
        
        // close everything down
        // TODO Let this back in once implemented
        $this->db->disconnect();
    }
    
    /** Redirects the page to another controller/action location
     *  @param string $destination the new destination in the route to 
     *         move to
     */
    function redirect( $destination )
    {
        header('Location: ' . $this->config->base_path . $destination);
        die();
    }
    
    /** Creates a form path for controllers to easily route
     *  @param string $destination the new destination in the route to
     *         move to
     */
    function form_path( $destination )
    {
         return $this->config->base_path . "$destination";
    }
    
    
    /** Creates a root path for controllers to easily navigate outside
     *  of the routing system
     *  @param string $destination the new destination in the app to
     *         move to
     */
    function root_path( $destination )
    {
         return $this->config->root_path . "$destination";
    }
    
    /** Creates an internal path for controllers to easily find internal
     *  files
     *  @param string $destination the desired location of a file
     */
    function inner_path( $destination )
    {
        return $this->config->internal_path . "$destination";
    }
    
    /** Includes a specified library into the application if it follows
     *  the appropriate location conventions.
     *  @param string $library_name the name of the library in the 
     *  app/libraries/ directory
     */
    function library( $library_name )
    {
        // require the source
        require_once( 'libraries/' . $library_name . '.php');
    }
    
    
    /** Loads a model from the available application models as long as
     *  they follow the proper conventions for being loaded.
     *  @param string $model_name the name of the model in the models/
     *         directory
     *  @param string $path Path to look for the model in
     *  @return object the new model object
     */
    function model( $model_name, $path = null )
    {
        // look for model in different extensions path
        if( $path != null ){
            
            // chck if exists
            if(file_exists('extensions/'.$path.'/'.$model_name.'.php')){
                
                // include file
                require_once(
                    'extensions/'.$path.'/'.$model_name.'.php'
                );
                
                // make new model
                $model = $model_name . '_model';
                
                return new $model($model_name, $this->db);
                
            } else {
                // failed, return null
                return null;
            }
            
        }
        
    
        // require the source
        require_once( 'models/' . $model_name . '.php');
        
        $model = $model_name . "_model";
        
        return new $model( $model_name, $this->db );
    }
    
    
    /** Checks to see if extension exists 
     *  @param string $extension_name name of extension to check
     *  @return true on found false on not found
     */
    function has_extension( $extension_name )
    {
        // look for extension folder
        if(file_exists(
            'extensions/'.$extension_name
        )){
            return true;
        }
        
        // didn't have it
        return false;
    }
    
    /** Loads an extension from the available application extensions
     *  they follow the proper conventions for being loaded.
     *  @param string $extension_name the name of the extension folder
     *         in the extensions/ directory
     *  @return object the new extension object
     */
    function extension( $extension_name )
    {
        if( !$this->has_extension($extension_name) ){
            return null;
        }
    
        // require the source
        require_once( 'extensions/'.$extension_name.'/extension.php');
        
        // new extension
        $ext = $extension_name . "_extension";
        $extension = new $ext( $this );
        
        // load
        $extension->load();
        
        // load scripts and styles
        $this->controller->view->add_styles( $extension->styles() );
        $this->controller->view->add_scripts( $extension->scripts() );
        
        return $extension;
    }
    
    /** Loads a helper from the available application helpers given
     *  they follow the proper conventions for being loaded.
     *  @param string $helper the name of the helper to load
     *  @return object the new helper object
     */
    function helper( $helper_name )
    {
        // check for existance
        if( !file_exists('app/helpers/'.$helper_name.'.php')){
            return null;
        }
        
        require_once( 'app/helpers/'.$helper_name.'.php');
        
        // helper name 
        $helper_class = 'GarageSale\\'.$helper_name.'_helper';
        
        
        return new $helper_class($this);
    }
    
    
    /** Loads a settings object from a setting in the app/settings dir
     *  @param string $setting_file Name of the settings file in the
     *         app/settings dir
     *  @return object the new settings object
     */
    function settings( $setting_file )
    {
        // new extension
        return new Settings($setting_file);
    }
    
    /** Renders a view script to the current page
     *  @param string $script_name The name of the script ot include
     */
    function script( $script_name )
    {    
        // include the script to the page
        include( 'views/scripts/' . $script_name . '.php' );
    }
    
    /** Check if an install is still needed. If it is, just display
     *  install page and abandon everything else.
     */
    function install_check()
    {
        $setup = $this->settings('setup');

		// (TOP) DELETE what's in between after install has been done
		if( ($setup == null || $setup->get('complete') != 'true') && 
		    file_exists('install.php') ){
			echo '<p><strong>Database Setup: </strong></p>';
			include('install.php');
			die();
		}
		
    }
    
}


/** $app is the application variable, an instance of the GarageSale
 *  class. It runs the show here. 
 */
$app = new App();

/* Config class contains all application configuration */
$app->config = new Config();

/* Utility class provides useful globally used methods */
$app->utility = new Utility( $app );

// set the GarageSale Router
$app->router = new Router();

// application database using mysql
// PDO seems safer and more convenient in my opinion
$app->db = new PDOMySQLDatabase( $app->config->databases['mysql'] );


/* Start a new session that can be used throughout the application
 */
//$session = new Session( $app->db, $app->config->salt, true );
// Session::start('user_session');
session_start();

// This all has to happen after sessions have started
$app->user = new User( $app->db, true );

// check if the install file is still present
$app->install_check();

?>
