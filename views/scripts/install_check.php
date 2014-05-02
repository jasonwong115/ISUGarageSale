<?php
if( file_exists('views/subviews/install_database.php') ){
global $app;
?>
<!-- Administrator's panel -->
<div class="warning_panel">
    
    <!-- Cnter content -->
    <div class="center_admin">
        
        <!-- Admin links -->
        It looks as if you have installed your system already, however
        the install_database.php file still exists. For your own security, it
        is recommended that you delete this now. (Located at views/subviews/install_database.php)
        
        If you have not yet installed your system. Do so here:
        <a href="<?php echo $app->form_path('install'); ?>"
            >Install</a>
        
    </div>
    
</div>
<?php
}
?>



