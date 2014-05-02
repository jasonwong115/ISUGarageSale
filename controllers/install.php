<?php
/** controllers/install.php
 *  The install controller holds all things necessary to install the website
 *  for distribution purposes.
 *
 */
class install extends GarageSale\Controller {


    /** The before function is used to pass information on the user into
     *  the view for every action in this controller.
     */
    function before($args){
        $this->view->new_view('layout');
        
        $this->userid = -1;
        $this->username = null;
		
		// set default page title
        $this->page_title = "Install the Garage...";
        $this->view->add("page_title",$this->page_title);
		
    }
	function database($args){
	}	
}

?>
