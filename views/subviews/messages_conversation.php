<!-- Set up sidebar for being on the left -->
<div class="sidebar_area left">
    
    <?php $app->script('conversations_result'); ?>

</div>

<!-- Set up main gutter for being on the right side -->
<div class="main_gutter right">

    
    <?php 
        if( $write_message !== '' ){
        echo <<< MESSG
        <!-- Submit message -->
        <div class="submit_message">
            $write_message
        </div>  
MESSG;
        }
    ?>

    <!-- List messages in this conversation -->
    <?php
        // check if messages exist
        if( count($message_result) < 1 ){
            echo "No messages between you and $othername";
        } else {
            
            // loop over all messages and display here
            foreach( $message_result as $next ){
    ?>
        <div class="convo_item<?php
        
            // set mine/ theirs
            if( $app->user->get_user_id() == $next['toid'] ){
                
                // message was to me, so class is theirs
                echo ' theirs';
            } else {
                
                // is mine
                echo ' mine';
            }
         ?>">
            <h4><?php echo $next['subject']; ?></h4>
            <br />
            From: 
            <a href="<?php echo $app->form_path('messages/conversation/'.$next['fromid']); ?>">
                <?php echo $app->user->
                    name_from_id( (int) $next['fromid'] ); ?>
            </a>
            on 
            <?php echo $next['date_created']; ?>
            <br />
            
            <p>
                <?php echo $next['message']; ?>
            </p>
        </div>
    <?php
            } // end foreach
        }
    ?>
    
    <!-- Paginate to older messages -->
    <div class="pageinate">
        Displaying messages <?php echo $message_count['begin']; ?>
        - <?php echo $message_count['end']; ?> of
        <?php echo $message_count['total']; ?>
        <br />
        
        <?php
            // calculate pages
            $num_pages = ceil( 
                $message_count['total'] / $message_count['per']  
            );
            
            // get this page
            $this_page = 1;
            
            // check
            if( isset($_GET['cpage']) ){
                $this_page = (int) $_GET['cpage'];
            }
            
            // iterate and display all
            for( $i=1; $i <= $num_pages; $i++ ){
                
                // don't need to link this 
                if( $i === $this_page ){
                    echo ' ' . $i . ' ';
                    continue;
                }
                
                // display link
                echo <<< LINKAGE
            <a href="$self_link?cpage=$i">$i</a>
LINKAGE;
            }
        ?>
        
    </div>
    
    <!-- Reply form -->
    <?php $message_form->print_self(); ?>
    
</div>
