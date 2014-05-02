<?php
namespace GarageSale;
/** app/extension.php
 *  Provides an interface for extensions to the application in order
 *  to standardize methods and assist in loading the extension.
 */
class Extension {
    
    /** Instance of the primary application GarageSale class */
    protected $app = null;
    
    /** List of scripts to load for this extension */
    protected $scripts = array();
    
    /** List of styles to load for this extension */
    protected $styles = array();
    
    /** Constructs a new instance of the extension class and initializes
     *  with the application instance ready.
     *  @param object $app An instance of the GarageSale class
     */
    function __construct( $app )
    {
        $this->app = $app;
    }
    
    
    /** Acts as a constructor for the extension and is used to set up
     *  things like scripts and styles
     */
    function load()
    {
    }
    
    /** Gets the list of scripts associated with this extension
     *  @return array Paths to scripts for this extension 
     */
    function scripts()
    {
        return $this->scripts;
    }
    
    /** Gets the list of styles associated with this extension
     *  @return array Paths to style sheets for this extension 
     */
    function styles()
    {
        return $this->styles;
    }
    
}

?>
