<?php
/** extensions/slider/extension.php
 *  Creates an extension for the application that provides methods for 
 *  printing a 'slider' to the view. The output of this slider can
 *  be controlled via the admin panel. 
 */
class wysiwyg_extension extends GarageSale\Extension {
    
    /** Acts as a constructor for the extension and is used to set up
     *  things like scripts and styles
     */
    function load()
    {
        $this->styles = array(
            'wysiwyg/wysiwyg',
            'wysiwyg/css/jqueryui',
            'wysiwyg/css/colpick'
            
        );
        $this->scripts = array(
            'jquery',
            'wysiwyg/scripts/jqueryui',
            'wysiwyg/scripts/colpick',
            'wysiwyg/wysiwyg',
            'wysiwyg/scripts/run'
        );
    }
        
    
}
?>
