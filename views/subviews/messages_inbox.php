<!-- Set up sidebar for being on the left -->
<div class="sidebar_area left">
    
    <?php $app->script('conversations_result'); ?>

</div>

<!-- Set up main gutter for being on the right side -->
<div class="main_gutter right">

    <!-- Message area -->
    <?php if( isset( $update_message ) ){ ?>
        <div class="write_message">
            <?php echo $update_message; ?>
        </div>
    <?php } ?>

    <h3 class="unread_title"> Unread messages </h3>

    <!-- List all of the unread messages -->
    <?php
        // check if messages exist
        if( count($unread_result) < 1 ){
            echo "No unread messages";
        } else {
    ?>
    
    <!-- Set up form -->
    <form method="post" action="<?php echo $self_link; ?>">
    
    <?php
            // loop over all messages and display here
            foreach( $unread_result as $next ){
    ?>
        <div class="unread_item">
            
            <!-- Mark items as read -->
            
        
            <!-- Actual datas -->
            <h4>    
                <input type="checkbox" name="read_items[]" 
                    value="<?php echo $next['id']; ?>" />
                <?php echo $next['subject']; ?>
                
                <a href="<?php echo $app->
                    form_path('messages/conversation/'.$next['fromid']); 
                    ?>" class="view_convo">
                        View full conversation
                    </a>
            </h4>
            <br />
            
            From: 
            <a href="<?php echo $app->form_path('user/profile/'.$next['fromid']); ?>">
                <?php echo $app->user->
                    name_from_id( (int) $next['fromid'] ); ?></a>
                    
            on 
            <?php echo $next['date_created']; ?>
            <br />
            
            <p>
                <?php echo $next['message']; ?>
            </p>
        </div>
    <?php
            } // end foreach
    ?>
    
        <!-- Submit button -->
        <input type="submit" value="Mark Selected as Read &raquo;" />
    
    </form>
    
    <?php
        }
    ?>
</div>
