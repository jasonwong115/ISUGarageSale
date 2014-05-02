<!DOCTYPE html>
<html>
    <head>
        
        <!-- Load scripts -->
        
        
        <!-- Load CSS styles -->
        <?php if(isset($stylesheets)) {
            $this->print_styles($stylesheets);
        } ?>
        
        <!-- Jquery script -->
        <?php if(isset($scripts)) {
            $this->print_scripts($scripts);
        } ?>
        
        <!-- Page Title -->
        <title> Manage GarageSale:  </title>
		<script language="JavaScript" src="<?php echo $app->inner_path('views/scripts/gen_validatorv31.js'); ?>" type="text/javascript"></script>
    
    </head>
    <body>

        <!-- Very top of the page displays information on the
          -- current user and gives options for loging off,
          -- returning to the front end, etc.
          -->
        <div class="admin-top-nav">
             &nbsp;
            
            <!-- information for the left side of the top nav -->
            <span class="left">
                
                <a href="<?php echo $app->form_path(''); ?>" 
                    class="custom-link">&laquo; Return to site</a>
                                
            </span>
            
            <!-- information for teh right side of the top nav -->
            <span class="right">
                Admin (<a href="/"> logout  </a>) |
                messages ( <a href="#">0</a> )
            </span>
            
        </div> <!-- end div.admin-top-nav -->


        <!-- Contains all the management content to include headers
          -- sidebars, widgets, etc.
          -->
        <div class="admin-manage-content">
        
            <!-- Admin sidebar contains choices for which features to
              -- manage 
              -->
            <div class="admin-sidebar-data">
                
                
                <!-- ul.admin-sidebar-menu contains menu options
                  -- for navigating the admin backend 
                  -->
                <ul class="admin-sidebar-menu">
                    
                    <li><h3> Navigation </h3></li>
                    
                    <li><a 
                        href="<?php echo $adminlink . 'manageshop'; ?>">
                        Manage Shop
                    </a></li>
                    
                    <!--
                    <li><a href="<?php echo $adminlink . 'pages'; ?>">
                        Pages
                    </a></li>
                    
                    <li class="admin-active-menu">
                        <a href="<?php echo $adminlink . 'content'; ?>">
                            Content
                        </a>
                    </li>
                    
                    <li><a href="<?php echo $adminlink . 'posts'; ?>">
                        Posts
                    </a></li>
                    
                    <li><a href="<?php echo $adminlink. 'comments'; ?>">
                        Comments
                    </a></li>
                    -->
                    
                    <li><a href="<?php echo $adminlink . 'reviews'; ?>">
                        Reviews
                    </a></li>
                    
                    <li><a href="<?php echo $adminlink . 'users'; ?>">
                        Users
                    </a></li>
                    
					<li><a href="<?php echo $adminlink . 'contacts';?>">
                        Contacts
                    </a></li>
					
                    <li><a href="<?php echo $adminlink . 'reports'; ?>">
                        Reports
                    </a></li>
                    
                    <li><a href="<?php echo $adminlink . 'themes'; ?>">
                        Themes
                    </a></li>
                    
                </ul><!-- end ul.admin-sidebar-menu -->
                
                
                
                <!-- ul.admin-sidebar-menu contains menu options
                  -- for navigating the admin backend 
                  -->
                <ul class="admin-sidebar-menu">
                    
                    <li><h3> Extensions </h3></li>
                    
                    <?php
                    // loop over extension list
                    foreach( $extension_list as $ext ){
                    
                        // print out extension link
                        echo <<< EXTPRINT
                    <li>
                        <a href="${ext['location']}">${ext['name']}</a>
                    </li>
EXTPRINT;
                    }
                    ?>
                    
                </ul><!-- end ul.admin-sidebar-menu -->
                
            </div> <!-- end div.admin-sidebar-data -->
            
            
            <!-- Admin header data contains tools for managing
              -- the content of the current slected feature 
              -->
            <div class="admin-header-data">
                
                <!-- div.admin-header-title provides information on the
                  -- current page 
                  -->
                <div class="admin-header-title">
                    <h1>
                    
                    <?php
                    if( isset($page_title) ){
                        echo $page_title;
                    } else {
                        echo "Garage Sale Administration";
                    }
                    ?>
                    
                    </h1>
                </div> <!-- end div.admin-header-title -->
                
                <!-- ul.admin-header-menu contains tool options for
                  -- manageing the app content 
                  -->
                <ul class="admin-header-menu">
                    
                    <?php
                    // output tools list
                    if( isset($toolbuttons) ){
                        
                        // loop over
                        foreach( $toolbuttons as $tool ){
                            
                            // save vars
                            $toollink = $tool['link'];
                            $tooltext = $tool['text'];
                            
                            // output link
                            echo <<< LISTLINK
                        <li><a href="$toollink">$tooltext</a></li>
LISTLINK;
                        }
                    }
                    ?>
                    
                </ul> <!-- end ul.admin-header-menu -->
                
            </div> <!-- end div.admin-header-data -->
            
            
            <!-- Conatins management options and inputs -->
            <div class="admin-content-data">
                
                <?php
                // include views
                if( isset($subviews) && count($subviews) > 0 ){
                    
                    $this->print_subviews($subviews);
                    
                } else {
                    echo "No resources available";
                }
                ?>
                
            </div> <!-- end div.admin-content-data -->
        
        </div> <!-- end div.admin-manage-content -->

        <!-- Contains any concluding information -->
        <div class="admin-foot-info">
            Footer
        </div> <!-- end div.admin-foot-info -->

    </body>
</html>
