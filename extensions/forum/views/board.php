<!-- display thread -->

<div class="forum_pad">

    <!-- User buttons for forum board -->
    <div class="forum_board_buttons">
    <?php if( $app->user->is_logged_in() ){ ?>
        <a href="<?php echo $add_action; ?>">New Thread</a>
    <?php } ?>
        <a href="<?php echo $search_action; ?>">Search</a>
    </div>
    
    <div class="forum_board_box">
    <hr />
        
        <!-- Bread crumb -->
        <div class="forum_breadcrumb">
            <a href="<?php echo $index_action; ?>">Index</a> &raquo;
            <?php echo $board_name; ?>
        </div>
        
        <!-- Display Description -->
        <h3><?php echo $board_name; ?>
            <small><?php echo $board_description; ?></small> 
        </h4>
        
        <!-- Paginate results -->
        <div class="forum_pagination">
            
        </div>
<?php

// check for threads
if( count($threads_result) == 0 ){
?>
    <p>It looks like there are no threads for this message board yet.
    </p>    
<?php
}

// loop over boards here
foreach( $threads_result as $thread )
{

    // get creator name
    $creator = $app->user->name_from_id((int)$thread['creator_id']);

    // print thread
    echo <<< THREAD
    
        <div class="forum_thread">
            
            <a href="$thread_action/${thread['id']}">
                <div class="forum-count">
                    ${thread['reply_count']} replies
                </div>
            <span class="forum-biggish">${thread['name']}</span>
            by $creator
            </a>
        </div>
THREAD;
        
}
?>
    </div>
</div>
