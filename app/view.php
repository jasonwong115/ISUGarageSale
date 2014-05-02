<?php
namespace GarageSale;
/** app/view.php
 *  This class provides useful functions for aiding in rendering pages.
 */
class View {
    
    /** The path the the view to load */
    private $view_path;
    
    /** Variables to be made available to the view as added with add() 
     */
    private $view_variables = array();
    
    /** Defines the path the the theme folder. Defaults to views/ if
     *  nothing is present
     */
    private $theme_path = null;
    
    /** Stylesheets to make available to the page. */
    private $stylesheets = array();
    
    /** Scripts to make available to the page */
    private $scripts = array();
    
    /** Scripts forced by the view that should not appear later */
    private $forced_scripts = array();
    
    /** Subviews to make available to the page */
    private $subviews = array();
    
    /** Construct a new View object
     */
    function __construct( $theme_path = 'views/' )
    {
        $this->theme_path = $theme_path;
        $this->view_path = null;
    }
    
    /** The new_view method saves the path to the layout/template file
     *  that is to be used for this particular view. It is not rendered
     *  until the render() method is called.
     *  @param $view_path the path to the view that should be rendered.
     *         This will be a path inside the views folder.
     */
    function new_view( $view_path )
    {
        $this->view_path = $view_path;
    }
    
    /** Adds a variable to the view to provide the view/subviews scope
     *  @param string $var_name the name to give the variable in the 
     *         views scope
     *  @param mixed $var_value value to associate with the variable
     */
    function add( $var_name, $var_value )
    {
        $this->view_variables[$var_name] = $var_value;
    }
    
    
    /** Adds a stylesheet to the list to make available to the view
     *  @param string $stylesheet name of stylesheet to include
     */
    function add_style( $stylesheet )
    {
        $this->stylesheets[] = $stylesheet;
    }
    
    /** Adds multiple stylesheets to the list of styles
     *  @param array $stylesheets list of stylesheets to include
     */
    function add_styles( $stylesheets )
    {
        // loop over and add
        foreach( $stylesheets as $stylesheet ){
            $this->add_style($stylesheet);
        }
    }
    
    /** Adds a script to the list to make available to the view
     *  @param string $script name of script to include
     */
    function add_script( $script )
    {
        // make sure its not already in there
        if( !in_array($script, $this->scripts) ){ 
            $this->scripts[] = $script;
        }
    }
    
    /** Adds multiple scripts to the list of scro[ts]
     *  @param array $scripts list of scripts to include
     */
    function add_scripts( $scripts )
    {
        // loop over and add
        foreach( $scripts as $script ){
            $this->add_script($script);
        }
    }
    
    /** Adds a subview to the list to make available to the view
     *  @param string $subview name of subview to include
     */
    function add_subview( $subview )
    {
        $this->subviews[] = $subview;
    }
    
    /** Adds multiple subviews to the list ofsubviews
     *  @param array $subviews list of subviews to include
     */
    function add_subviews( $subviews )
    {
        // loop over and add
        foreach( $subviews as $subview ){
            $this->add_subview($subview);
        }
    }
    
    /** Renders the current view if there is one ready and makes 
     *  bound variables available to the view.
     */
    function render()
    {
        // attempt to render view
        if( $this->view_path != null ){
        
            // provide stylesheets
            global $stylesheets;
            $stylesheets = $this->stylesheets;
            // provide scripts
            global $scripts;
            $scripts = $this->scripts;
            global $subviews;
            $subviews = $this->subviews;
        
            // attempt to create the variables for the view
            foreach( $this->view_variables as $key => $val ){
                global $$key;
                $$key = $val;
            }
            global $app;
        
            // include the view
            if( file_exists(
                $this->theme_path . $this->view_path . '.php' )
            ){
                // include theme path
                include( $this->theme_path . $this->view_path . '.php');
            } else {
                
                // include default path
                include( 'views/' . $this->view_path . '.php');
            }
        }
    }
    
    
    
    /** Prints a script to the view if it exists in either the core
     *  scripts or in the extension scripts
     *  @param string $script Path to script in scripts folders
     */
    function script( $script )
    {
        // make sure script has not been forced
        if( in_array($script, $this->forced_scripts) ) {
            return;
        }
        
        global $app;
        // ensure file existance
        // try theme path
        if( file_exists($this->theme_path . 'scripts/'.$script.'.js')){
            
            // set up style path
            $style_path = $app->inner_path(
                $this->theme_path . 'scripts/'.$script.'.js'
            );
            
        } elseif( file_exists('views/scripts/'.$script.'.js') ){
        
            // set up style path
            $style_path = $app->inner_path(
                'views/scripts/'.$script.'.js'
            );
        
        // check for extension
	    }elseif( file_exists('extensions/'.$script.'.js') ) {
	        
            // set up style path
            $style_path = $app->inner_path(
                'extensions/'.$script.'.js'
            );
            
	    } else {
	        return;
	    }
	    
        // print style path
        echo <<< SCRIPT
        
        <script type="text/javascript" src="$style_path"></script>
SCRIPT;
    }
    
    
    /** Include a script only if it isn't part of the controller 
     *  scripts
     *  @param string $script name of script to include
     */
    function script_once( $script ){
        if( !in_array($script, $this->scripts) ){
            $this->script( $script );
        }
    }
    
    
    /** Include a script and override it so it won't be included later
     *  @param string $script name of script to include
     */
    function script_force( $script ){
        if( !in_array($script, $this->forced_scripts) ){
            $this->script( $script );
            $this->forced_scripts[] = $script;
        }
    }
    
    /** Prints scripts to the view from a list of scripts
     *  @param array $scripts list of Paths to script in scripts folders
     */
    function print_scripts( $scripts )
    {
        /*  Facilitate multiple scripts by using them as arrays
         *  To add multiple scripts from your action do:
         *  $scripts = array();
         *  $scripts[] = "stylesheet1.css";
         *  $scripts[] = "stylesheet2.css";
         *  $this->view->add('scripts',$scripts);
         */
        if( isset($scripts) ){
            foreach( $scripts as $script ){
                $this->script( $script );
            }
	    }
	}
    
    
    /** Prints all stylesheets in a list of stylesheets to the view
     *  @param array $stylesheet stylesheets to inlcude
     */
    function print_styles( $stylesheets )
    {
    
	    /*  Facilitate multiple stylesheets by using them as arrays
	     *  To add multiple stylesheets from your action do:
	     *  $styles = array();
	     *  $styles[] = "stylesheet1.css";
	     *  $styles[] = "stylesheet2.css";
	     *  $this->view->add('stylesheets',$styles);
	     */
	    if( isset($stylesheets) ){
	    
	        // loop over all stylesheets and link them
	        foreach($stylesheets as $stylesheet ){
	        
	            // utility class will check this for us.
	        	$this->style( $stylesheet );
	        }// foreach
	    }// isset
    }
    
    
    /** Prints a stylesheet to the view if it exists in either the core
     *  styles or in the extension styles
     *  @param string $stylesheet Path to stylesheet in css folders
     */
    function style( $stylesheet )
    {
        global $app;
        
        // ensure file existance
        if( file_exists($this->theme_path .'css/'.$stylesheet.'.css') ){
        
            // set up style path
            $style_path = $app->inner_path(
                $this->theme_path .'css/'.$stylesheet.'.css'
            );
        
        // check for extension
	    } elseif( file_exists('views/css/'.$stylesheet.'.css') ){
        
            // set up style path
            $style_path = $app->inner_path(
                'views/css/'.$stylesheet.'.css'
            );
        
        // check for extension
	    }elseif( file_exists('extensions/'.$stylesheet.'.css')  ) {
	        
            // set up style path
            $style_path = $app->inner_path(
                'extensions/'.$stylesheet.'.css'
            );
            
        // one last extension check
	    }elseif( file_exists('extensions/'.$stylesheet.'/css/all.css')){
	        
	        // set up style path
            $style_path = $app->inner_path(
	            'extensions/'.$stylesheet.'/css/all.css'
            );
	        
	    } else {
	        return;
	    }
	    
        // print style path
        echo <<< STYLESHEET
        
        <link type="text/css" rel="stylesheet" href="$style_path" />
STYLESHEET;
    }
    
    /** Prints the subviews in the given subviews list to the view
     *  @param array $subviews list of subviews to include
     */
    function print_subviews( $subviews )
    {
        /* Make these globals availale to the subview scope */
        global $stylesheets;
        global $scripts;
        foreach( $this->view_variables as $key => $val ){
            global $$key;
        }
        // and the app, might as well
        global $app;
            
		/* Load subviews */
		foreach($subviews as $subview){
		
		    //Make sure every subview has a file
			if( file_exists( 
			        $this->theme_path.'subviews/'.$subview.'.php'
			    ) 
			 ){
			    // include it up
				include($this->theme_path.'subviews/'.$subview.'.php');
			
			// handle extensions
			} elseif( file_exists( 'extensions/'.$subview.'.php' ) ){
			
			    // include it up
				include('extensions/'.$subview.'.php');
			// otherwise, error out
			} elseif( file_exists( 'views/subviews/'.$subview.'.php' )){
			
			    // include it up
				include('views/subviews/'.$subview.'.php');
			
			// handle extensions
			} else {
			    
			    // Attempt to get a special case extension
			    $parts = explode('_', $subview, 2);
			    
			    if( count($parts) == 2 &&
			        file_exists( 'extensions/'.$parts[0].
			            '/views/'.$parts[1].'.php'
                    )
			    ){
			        
			        // include it up
				    include(
				        'extensions/'.$parts[0].
				        '/views/'.$parts[1].'.php'
			        );
			    }
			    
			}
		}
    }
    
    
    /** Loads a new form from the available forms
     *  @param string $form name of form in forms folder
     *  @param string $action destination of the form submission
     *  @param string $method method for the submission
     *  @param array $attributes attributes to give to the form 
     *  @return object Instance of the form object
     */
    function form( $form, $action )
    {
        
        global $app;
        // ensure file existance
        // try theme path
        if( file_exists($this->theme_path . 'forms/'.$form.'.php')){
            
            // set up style path
            $form_path = $this->theme_path . 'forms/'.$form.'.php';
            
        } elseif( file_exists('views/forms/'.$form.'.php') ){
        
            // set up style path
            $form_path = 'views/forms/'.$form.'.php';
        
        // check for extension
	    }elseif( file_exists('extensions/'.$form.'.php') ) {
	        
            // set up style path
            $form_path = 'extensions/'.$form.'.php';
            
	    } else {
	        return null;
	    }
	    
	    // make sure to get form name
	    $paths = explode('/',$form);
	    
	    // form name
	    $form_name = $paths[count($paths)-1];
	    
        // first get teh helper
        $helper = $app->helper('form');
	    
	    if( $helper == null )
	        return null;
	    
	    // require form class
        require_once( $form_path );
        
        // then get the form from the name of the form
        $form = $helper->create_from( $form_name, $action );
        
        // create form
        $form->create();
        
        return $form;
    }
}
?>
