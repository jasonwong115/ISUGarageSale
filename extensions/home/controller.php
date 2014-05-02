<?php
/** home.php
 *  The homepage controller which displays the welcome/ main page
 */
class home extends GarageSale\Controller {
    
    /** The index function is the default to be called when
     *  accessing a controller
     */
    function index( $args )
    {   
        // load the slider extension
        $slider = $this->app->extension('slider');
        
    
        // add slider to the view
        $this->view->add('slider',$slider);
    
		$subview = array('home/views/index');
        $this->view->add('subviews',$subview);
        
    }
	
	function terms(){
		$subview = array('home/views/terms');
        $this->view->add('subviews',$subview);
		$this->view->add('page_title','Terms of Service');
		
	}
	
	function about(){
		$subview = array('home/views/about');
        $this->view->add('subviews',$subview);
		$this->view->add('page_title', 'About ISU Garage Sale');
	}
	
	function contact(){
		$subview = array('home/views/contact');
		$this->view->add_style('contact');
		$this->view->add('subviews',$subview);
	}
	
	function contact_form_submitted(){
		//Load Subview
		$subview = array('home/views/contact_form');
		$this->view->add('subviews',$subview);
		
		//Email information
		if(isset($_POST['fullname'])){
			$errors = '';
			$name = $_POST['fullname']; 
			$email_address = $_POST['emailaddress'];
			$subject = $_POST['subject'];
			$message = $_POST['message'];
			$reason = $_POST['reason'];
			if(!filter_var($email_address, FILTER_VALIDATE_EMAIL)){
				$errors = 'INVALID EMAIL';
			}
			if( empty($errors))
			{
				//Send email to admins/moderators
				$sent = $this->app->utility->send_mail($name,$email_address,$subject,$message,$reason,"Contact");
				$messages_model = $this->app->model('messages');
				$result = $messages_model->insert_contact( $name, $email_address, $subject, $message,$reason);
				$this->view->add('sent',$sent);
			}else{
				$this->view->add('errors',$errors);
			}
		}
	}
	function report_submitted(){
		//Load Subview
		$subview = array('home/views/contact_form');
		$this->view->add('subviews',$subview);
		//Email information
		if(isset($_POST['fullname'])){
			$errors = '';
			$name = $_POST['fullname']; 
			$email_address = $_POST['emailaddress'];
			$subject = $_POST['subject'];
			$message = $_POST['message'];
			$reason = $_POST['reason'];
			if(!filter_var($email_address, FILTER_VALIDATE_EMAIL)){
				$errors = 'INVALID EMAIL';
			}
			if( empty($errors))
			{
				//Send email to admins/moderators
				$sent = $this->app->utility->send_mail($name,$email_address,$subject,$message,$reason,"Report User");
				$messages_model = $this->app->model('messages');
				$messages_model->insert_report($name,$email_address,$subject,$message,$reason);
				$this->view->add('sent',$sent);
			}else{
				$this->view->add('errors',$errors);
			}
		}
	}
	
	function webservice(){
		
	}
}

?>
