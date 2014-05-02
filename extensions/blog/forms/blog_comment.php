<?php
/** blog/forms/blog_comment.php
 *  Creates a form for comments
 */
class blog_comment_form extends GarageSale\Form {
    
    /** Sets up the comment form
     *  @param int $post_id id of the post to comment on 
     */
    function create(){
        
        
        // add text input
        $this->text( 
            'name',
            'Comment title', // input title
            null, // no default value
            true, // required
            array('placeholder'=>'Comment Title')
        );
        
        // add text area
        $this->textarea(
            'comment',
            'Comment', // input title
            null, // default value
            true, // required
            array('class'=>'wysiwyg blog-comment') // attributes
        );
        
        
    }
    
    /** Prints the layout for this form
     */
    function print_self(){
        
        $this->open();
        
        $text_input = $this->get('name');
        
        $this->print_input( $text_input );
        echo "<br />\n";
        
        $post_input = $this->get('comment');
        $this->print_input( $post_input );
        echo "<br />\n";
        
        /*
        $id_input = $this->get('post_id');
        $this->print_input( $id_input );
        echo "\n";
        */
        
        echo <<< SUBMIT
        
        <input type="submit" value="Submit &raquo;" />
        <br />
SUBMIT;
        
        $this->close();
    }
    
}
?>
