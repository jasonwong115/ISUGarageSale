<?php
    // global app
    global $app;

    // if this user is an administrator or manager
    if( $app->user->get_user_level() >= 
        GarageSale\User::USER_ADMIN 
    ){
        
        // then display the admin panel
?>

<!-- Administrator's panel -->
<div class="admin_panel">
    
    <!-- Cnter content -->
    <div class="center_admin">
        
        <!-- Admin links -->
        Admin stuff here.
        
        <a href="<?php echo $app->form_path('admin'); ?>"
            class="right_link">
            Manage site &raquo;
        </a>
        
        <?php
        // test for blog existance
        if( $app->has_extension('blog') ){
        ?>
        <a href="<?php echo $app->form_path('blog/newpost'); ?>"
            class="right_link">
            New Blog Post &nbsp; &nbsp; &nbsp;
        </a>
        <?php } ?>
    </div>
    
</div>

<?php } ?>
