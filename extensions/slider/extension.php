<?php
/** extensions/slider/extension.php
 *  Creates an extension for the application that provides methods for 
 *  printing a 'slider' to the view. The output of this slider can
 *  be controlled via the admin panel. 
 */
class slider_extension extends GarageSale\Extension {
    
    /** Acts as a constructor for the extension and is used to set up
     *  things like scripts and styles
     */
    function load()
    {
        $this->styles = array('slider/css/new_slideshow');
        $this->scripts = array(
            'jquery',
            'slider/scripts/jquery-scroller-1.0',
            'slider/scripts/run'
        );
    }
        
    /** Prints the slider to the view
     */
    function display()
    {
        include('slider_display.php');
    }
    
}
?>
