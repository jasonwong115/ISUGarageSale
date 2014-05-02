<?php include('variables/variables.php');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	
	    <!-- Meta information -->
		<meta name="author" content="Flavia Roma Cavalcanti" >
		<meta content="<?php echo $search_engine_description; ?>" 
		    name="description">
		<meta name = "keywords" content= "I<?php echo $search_engine_keywords; ?>">
		<meta charset="UTF-8">
		
		
		<!-- Echo title properties -->
		<title><?php
		    // echo main title from variables page 
		    echo $title; 
		    
		    // check for other page titles
		    if( isset($page_title) ){
		        echo " : $page_title";
		    }
		    
		?></title>
		
		<!-- CSS/resource Linkins -->
		<link href='http://fonts.googleapis.com/css?family=Josefin Sans' 
		    rel='stylesheet' type='text/css' />
		<link rel="icon" type = "image/ico" 
			href ="<?php echo $app->inner_path('favicon.ico') ?>">
			    
	    
		
	    <!-- Load that stylesheet like a BOSS -->
		<?php 
		    // forces jquery here
		    // prevents the global scripts from using this
		    $this->script_force('jquery');
		
		    $this->script('gen_validatorv31');
			$this->script('star-review');
		    $this->style('home');
			$this->style('search-bar');
		    $this->style('category-bar');
			$this->style('menu-bar');
			$this->style('hacktastic');
			$this->style('footer'); 
		?>
		
		<?php
		    /*  Facilitate multiple stylesheets by using them as arrays
		     *  To add multiple stylesheets from your action do:
		     *  $styles = array();
		     *  $styles[] = "stylesheet1.css";
		     *  $styles[] = "stylesheet2.css";
		     *  $this->view->add('stylesheets',$styles);
		     */
		    
		    if( isset($stylesheets) ){
		        $this->print_styles( $stylesheets );
		    }// isset
		?>
	</head>
    <body>
    
        <?php $app->script('admin_panel'); ?>
    
        <!-- A persistent title area so browsing areas don't look so...
             blah.... -->
        <div id="persistent_title">
        
            <!-- Centering element -->
            <div class="center_area">
                
                <!-- User welcome info -->
                <div class="right_stuff">
                    <?php
						$logged_in = $app->user->is_logged_in();
						if($logged_in){
					?>
							<nav class = "special-menu">
								<ul class = "float-list">
								  <li class="tnav"><a class="internal" href="<?php echo $app->form_path('user/settings');?>">Settings</a></li>
								  <li class="cnav"><a class="internal" href="<?php echo $app->form_path('user/sellItem');?>">Sell Items</a></li>
								  <li class="wnav"><a class="internal" href="<?php echo $app->form_path('user/offers/'.$app->user->get_user_id());?>">My Offers</a></li>
								  <li class="inav"><a class="internal" href="<?php echo $app->form_path('browse/user/'.$app->user->get_user_id());?>">My Listings</a></li>
								  <li class="idnav"><a class="internal" href="<?php echo $app->form_path('messages');?>">My Messages
								  
                                <?php
                                // check for tweaks for message count
                                if( $app->has_extension('tweaks') ){
                                    $tweaks = $app->extension('tweaks');
                                    $count = $tweaks->unread_count();
                                    
                                    echo <<< COUNT
                                    
                                    ( $count )
COUNT;
                                }
                                ?>
								  
								  </a></li>
								  <li class="anav"><a class="internal" href="<?php echo $app->form_path('user/logout');?>">Logout</a></li>
								</ul>
							</nav>
					<?php
						}else{
					?>
						<nav class = "special-menu">
							<ul class = "float-list">
								<li class="tnav"><a class="internal" href="<?php echo $app->form_path('user/login');?>">Login</a></li>
								<li class="cnav"><a class="internal" href="<?php echo $app->form_path('user/register');?>">Register</a></li>
							</ul>
						</nav>
					<?php	} ?>
                    
                </div>
                
                <!-- Actual title announcement -->
                <div class="left_stuff">
                    <h1><a href="<?php echo $app->form_path(''); 
                            // blank goes to home ?>"> 
                        Party Hardy!
                    </a></h1>
					<?php
						if($logged_in){
							$username = $app->user->get_user_name();
							echo "<a href=" . $app->form_path('user/profile') . ">" . $username . "'s Garage</a>";
						}else{
							echo "<small> Sell some stuff </small>";
						}
					?>
                    <br />
                    <small>
                        This is a theme for partiers! Partyers? Both
                        of those look weird.
                    </small>
                </div>
                
                
            </div>
        </div>
    
		<div class = "center-wrapper">
		
			<?php include('includes/search-bar.php'); ?>
			<?php include('includes/hyperlink-bar.php'); ?>
			<div class = "main-container">
			    
			    <!-- Page Title -->
				<?php
				/* Include page title info */
				if( isset($page_title) ){
				    
				    // echo the page title as an h2
				    echo <<< PAGETITLE
				    <h2>$page_title</h2>
PAGETITLE;
				}
				
				/* Load subviews */
				$this->print_subviews($subviews);
				
				?>
			</div>
		</div>
		<?php include('includes/footer.php'); ?>
    </body>
    
	    <?php
	    
		    /*  Facilitate multiple scripts by using them as arrays
		     */
		    if( isset($scripts) ){
		        $this->print_scripts($scripts);
		    }
		    
		    // only include this script if it was not part of the 
		    // controller scripts
		    $this->script_once('jquery');
		?>
</html>
