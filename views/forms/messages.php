<?php
/** views/forms/messages.php
 *  Creates a form for messages
 */
class messages_form extends GarageSale\Form {
    
    /** Sets up the comment form */
    function create(){
        
        // subject
        $this->text(
            'subject', // name
            'Subject', // title 
            null,
            true, // required
            array('placeholder'=>'Subject')
        );
        
        // add text area
        $this->textarea(
            'message',
            'Message',
            null, // default value
            true, // required
            array('class'=>'wysiwyg') // attributes
        );
        
    }
    
    /** Prints the layout for this form
     */
    function print_self(){
        
        $this->open();
        
        echo <<< SUBJ
        
        <strong>Subject</strong>
        <br />
SUBJ;
        
        // subject input
        $subject_input = $this->get('subject');
        $this->print_input($subject_input);
        
        echo <<< SUBJ
        
        <br /><br />
        <strong>Write Message</strong>
        <br />
SUBJ;
        
        // message
        $message_input = $this->get('message');
        $this->print_input( $message_input );
        echo "<br />\n";
        
        echo <<< SUBMIT
        
        <input type="submit" value="Submit &raquo;" />
SUBMIT;
        
        $this->close();
    }
    
}
?>
