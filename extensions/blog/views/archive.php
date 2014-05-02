
<?php
if( $action_message != null ){
    echo <<< ACT
    <div class="submit_message">
        $action_message
    </div>
ACT;
}
?>

<!-- Display posts -->
<?php 

if( count($result) == 0 ){
    echo <<< NO
    
    There are no updates from your GarageSale team here.
    
NO;
}

// loop over all
foreach($result as $row ){
    
    // get user name
    $who = $app->user->name_from_id((int)$row['user_id']);
    
    echo <<< ROW
    
    <div class="blog-post">
        <small>by $who on ${row['date_created']}</small>
        <div>
            ${row['post']}
        </div>
    </div>
    
ROW;
    
}

// check for prev / next links
if( $prev_post != null ){
    echo <<< PREV
    
    <a href="$prev_post">&laquo; Previous post</a>
    &nbsp; &nbsp; &nbsp; &nbsp;
PREV;
}

if( $next_post != null ){
    echo <<< NEXT
    
    <a href="$next_post">Next post &raquo; </a>
    &nbsp; &nbsp; &nbsp; &nbsp;
NEXT;
    
}

?>
<br />
<h3>Comments</h3>
<?php
if( count($comment_result) == 0 ){
    
    echo <<< COM
    No comments yet.
COM;
} else {
    foreach( $comment_result as $comment ){
        
        // get the who
        $who = '<a href="' . 
            $app->form_path('user/profile/'.$comment['user_id']) . '">' .
            $app->user->name_from_id((int)$comment['user_id']) . '</a>';
    
        echo <<< COMMENT
        
        <strong>${comment['name']}</strong>
        <br />
        <small> by $who on ${comment['date_created']} </small>
        <br />
        ${comment['comment']}
        <br />
COMMENT;
    }
    
    // print paging
    echo '<br /> Page: ';
    
    
    for( $i=1; $i<=$comment_paging['pages']; $i++ ){
    
        // check this page
        if( $comment_paging['current'] == $i ){
            
            echo ' ' . $i . ' ';
            continue;
        }
    
        echo <<< PAGE
        
        <a href="$page_link$i">$i</a> 
PAGE;
    }
    
    echo '<br />';
}



// print form here
if( $app->user->is_logged_in() ){
    echo "<br /><br />";
    $comment_form->print_self();
}
?>
