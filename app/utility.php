<?php
namespace GarageSale;

/** app/utility.php
 *  This class provides major utility functions that are used in the
 *  shop at frequent times but do not fall technically under other 
 *  classes.
 */
class Utility {
    
    /** The reference to the application instance which contains the
     *  needed features like database access, etc.
     */
    private $app;
    
    /** The Utility class constructor, sets the private local app value
     *  for internal use
     */
    function __construct( $app )
    {
        // set internal app
        $this->app = $app;
    }
    
    
    
    /** Prints a list of the categories and their links(<li><a>) to
     *  the page.
     *  @param $parentid is an integer of the parent id to use as the 
     *         starting point for the categories.
     *  @return A nested array of database results for the categores.
     */
    function print_category_list( $parentid = 0 )
    {
        // start by gathering the categories
        $categories = $this->app->model('categories')->
            get_category_children( $parentid );
        
        // start a new stack
        $stack = array();
        
                
        $count = count($categories);
        // push everything into the queue
        for( $i=0; $i<$count; $i++ ){
            $stack[] = &$categories[$i];
        }
        
        // loop over stack and output in time
        while( count($stack) > 0 ){
            
            // get next
            $next = array_pop($stack);
            
            // output closing tag
            if( $next == null ){
                echo '</ul></li>';
                
                // then move on
                continue;
            }
            
            // output then push to stack
            echo '<li class = "cat-list"><a class = "innerstuff" href="'. 
                $this->app->form_path('browse/category/'.$next['name']).
                '">' . $next['display_name'] . '</a>';
            
            // if there are children push them
            if( $next['child_item'] != null ){
                
                // start new ul
                echo '<ul>';
                // insert null item into stack to indicate ul close
                $stack[] = null;
                                
                // count number of child items
                $c = count( $next['child_item'] );
                // loop over and add
                for( $j=0; $j<$c; $j++ ){
                    $stack[] = &$next['child_item'][$j];
                }
                
            } else {
                // echo the closing tag
                echo "</li>";
            }
        }
    }
	
	/** Sends a message to moderators via email.
	 *  @param string $name Name of the email sender
	 *  @param string $email_address address of the sender
	 *  @param string $subject Subject of the email
	 *  @param string $message Message to communicate via email
	 *  @param string $reason Reason for contacting moderators
	 *  @param string $form I'm honestly not sure
	 *  @return 1 if successful 0 if unsuccessful
	 */
	function send_mail( 
	    $name, $email_address, 
	    $subject, $message, $reason, $form 
	) {
		//Who the email will say it is from
		$from = 'ISUGarageSale'; 
		
		// subject of the email
		$email_subject = "$form form submission: $name";
		
		// body content of the email
		$email_body = "A new form was submitted ($form Form)" . 
		"Here are the details:\n".
		"Name: $name ($email_address)\n".
		"Reason: $reason\n".
		"Subject: $subject \n".
		"Message: \n\n".
		"$message\n\n\n".
		//"Do not reply to this automated email. 
		//    Any replies will not be seen!";
		    
		// headers
		$headers = "From: $from\n";
		
		// success count 
		$sent = 1;
		
		$stmt = $this->app->db->select('users');
        // set the where value of the statement
        $stmt->where_gte( 'userlevel', 'i', User::USER_MODERATOR,'AND', 'users');
		$stmt->field('email','users');
        // set the statements limit
		$moderatorsToSend = $this->app->db->statement_result($stmt);
		$modCount = 0;
		foreach($moderatorsToSend as $mod){
			foreach($mod as $to){
				if($modCount % 2 == 0){
					$sent =  mail($to,$email_subject,$email_body,$headers);
					if(!$sent){
						return $sent;
					}
				}
				$modCount++;
			}
		}
		
		//Mail was successfully sent and returns 1
		return $sent; 
	}
	
	function send_verification($last_id,$hash,$code,$email) {
		//Send email
		//Who the email will say it is from
		$from = 'ISUGarageSale'; 
		
		// subject of the email
		$email_subject = $from . ": activate your account";
		
		// body content of the email
		$email_body = "Thank you for registering with " . $from . ". " .
		"Follow the link below to enter the following code:\n\n".
		"Code: " . $code . "\n\n" .
		"http://proj-309-07.cs.iastate.edu/user/activation/?id=" . $last_id . "&hash=" . $hash;
			
		// headers
		$headers = "From: $from\n";
		
		$to = $email;
		$sent =  mail($to,$email_subject,$email_body,$headers);
		return $sent; 
	}
}
?>
