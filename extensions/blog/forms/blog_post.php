<?php
/** views/forms/comment.php
 *  Creates a form for comments
 */
class blog_post_form extends GarageSale\Form {
    
    /** Sets up the comment form */
    function create(){
        
        // add text input
        $this->text( 
            'name',
            'Post title', // input title
            null, // no default value
            true, // required
            array('placeholder'=>'Post Title')
        );
        
        // add text area
        $this->textarea(
            'post',
            'Post content', // input title
            null, // default value
            true, // required
            array('class'=>'wysiwyg') // attributes
        );
        
        // add tags
        $this->text('tags');
        
    }
    
    /** Prints the layout for this form
     */
    function print_self(){
        
        $this->open();
        
        $text_input = $this->get('name');
        
        $this->print_input( $text_input );
        echo "<br />\n";
        
        $post_input = $this->get('post');
        $this->print_input( $post_input );
        echo "<br />\n";
        
        
        $tags_input = $this->get('tags');
        echo <<< TAGSTEXT
        
        <div>Enter tags separated by spaces</div>
TAGSTEXT;
        $this->print_input( $tags_input );
        echo <<< SUBMIT
        
        <br />
        
        <input type="submit" value="Submit &raquo;" />
        <br />
SUBMIT;
        
        $this->close();
    }
    
}
?>
