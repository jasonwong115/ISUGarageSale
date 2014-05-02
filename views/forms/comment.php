<?php
/** views/forms/comment.php
 *  Creates a form for comments
 */
class comment_form extends GarageSale\Form {
    
    /** Sets up the comment form */
    function create(){
        
        // add text input
        $this->text( 'title' );
        
        // add text area
        $this->textarea(
            'comment',
            'Comment',
            null, // default value
            true, // required
            array('class'=>'wysiwyg') // attributes
        );
        
    }
    
    /** Prints the layout for this form
     */
    function print_self(){
        
        $this->open();
        
        $text_input = $this->get('title');
        
        $this->print_input( $text_input );
        echo "<br />\n";
        
        $comment_input = $this->get('comment');
        $this->print_input( $comment_input );
        echo "<br />\n";
        
        echo <<< SUBMIT
        
        <input type="submit" value="Submit &raquo;" />
SUBMIT;
        
        $this->close();
    }
    
}
?>
