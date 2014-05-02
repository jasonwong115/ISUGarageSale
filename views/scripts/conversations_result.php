
<!-- Conversations -->
<strong> Conversations </strong>
<br />
<!-- List all of the conversations -->
<?php
    global $conversations_result;
    global $app;
    
    // check if convos exist
    if( isset($conversations_result) && 
        count($conversations_result) > 0 
    ){
        // array of already contained ids
        $already_seen = array();
        
        // loop over all conversations and display here
        foreach( $conversations_result as $next ){
?>
    <div class="convo_contact">
    
        <?php
            // make sure we are linking to another user and not this one
            if( $next['fromid'] != $app->user->get_user_id() &&
                !in_array($next['fromid'], $already_seen) 
            ){
                $already_seen[] = $next['fromid'];
        ?>
    
        <a href="<?php echo $app->
            form_path('messages/conversation/'.$next['fromid']); ?>">
            <?php echo $app->user->
                name_from_id( (int) $next['fromid'] ); ?>
        </a>
        
        <?php 
            } elseif( $next['toid'] != $app->user->get_user_id() &&
                !in_array($next['toid'], $already_seen) 
            ){ 
                $already_seen[] = $next['toid'];
        ?>
    
        <a href="<?php echo $app->
            form_path('messages/conversation/'.$next['toid']); ?>">
            <?php echo $app->user->
                name_from_id( (int) $next['toid'] ); ?>
        </a>
        
        <?php } ?>
        
    </div>
<?php
        } // end foreach
    } else {
    
        // nope, no conversations
        echo "No conversations";
    }
?>
