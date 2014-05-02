<!-- display thread -->

<div class="forum_pad">

	<?php
	// action message
	if( $action_message != null ){
		echo '<div class="forum-admin-action-message">'.
			$action_message.
			'</div>';
	}
	?>
    
    <div class="forum_thread_box">
        <?php if( $thread_result != null ){ ?>
            <!-- Bread crumb -->
            <div class="forum_breadcrumb">
                <a href="<?php echo $index_action; ?>">Index</a> &raquo;
                <a href="<?php echo $board_action; ?>"
                    ><?php echo $board_name; ?></a> &raquo;
                <?php echo $thread_result['name']; ?>
                
            </div>
            
            
            <!-- Paginate results -->
            <div class="forum_pagination">
                <?php
                for( $i=0; $i<$page_info['page_count']; $i++ ){
                    $j = $i+1;
                    
                    // if this page move on
                    if( $page_info['this_page'] == $i ){
                        echo ($i+1).' ';
                        continue;
                    }
                    echo <<< PAGE
                <a href="$page_action$i">$j</a> 
PAGE;
                }
                ?>
            </div>
    
    
            <!-- Display Description -->
            <div class="forum-thread-display">
            
                <!-- by whom -->
                <div>
                <strong>
                	<?php echo $thread_result['name']; ?>
                </strong>
                by
                <?php echo $app->user->
                    name_from_id((int)$thread_result['creator_id']); ?>
                
                    on <?php echo $thread_result['date_created']; ?>
                    
                </div> <hr />
                
                <?php echo $thread_result['description']; ?>
            </div>
            
        <?php } ?> 
    
    
<?php
// check for threads
if( count($posts_result) > 0 ){ 

    foreach( $posts_result as $reply ){
?>

    <!-- Reply post here -->
    <div class="forum-reply-display">
        
        <strong><?php echo $reply['name']; ?></strong>
        
        <br />
        by <?php echo $app->user->name_from_id(
        	(int)$reply['creator_id']); ?>
        on <?php echo $reply['date_created']; ?>
        
        <hr />
        
        <div><?php echo $reply['post']; ?></div>
        
    </div>
    
<?php } } else { ?>
    <p>It looks like there are no posts for this thread yet.
    </p>    
<?php } ?>
    </div>
    <br />
    
    <?php if($app->user->is_logged_in() ) { ?>
        <!-- Submit reply -->
        <div>Reply to this thread</div>
        <form action="<?php echo $reply_action; ?>" method="POST">
        
            <input type="text" name="name" placeholder="Reply title" 
                <?php echo $name_value; ?> />
            <br />
            
            <textarea name="post" class="wysiwyg"
                ><?php echo $post_value; ?></textarea>
            
            <br />
            <input type="submit" value="Submit &raquo;" />
        </form>
    <?php } ?>
	<br />    
</div>
