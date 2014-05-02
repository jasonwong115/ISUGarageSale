
<?php
if( isset($action_message) && $action_message != null ){
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
    
    There are no updates from your GarageSale team yet.
    
NO;
}

// loop over all
foreach($result as $row ){
    
    // get user name
    $who = $app->user->name_from_id((int)$row['user_id']);
    
    echo <<< ROW
    
    <div class="blog-post">
        <h3>${row['name']}</h3>
        <small>by $who on ${row['date_created']}</small>
        <div>
            ${row['post']}
        </div>
    </div>
    
ROW;
    
}

   
// paging
if( $paging['pages'] > 0 ){
   
    echo "Page: ";
    
    
    
    // pages are 1 indexed
    for($i = 1; $i <= $paging['pages']; $i++ ){
   
        // check if this is current page
        if( $i == $paging['current'] ){
            echo <<< CUR
            
            $i 
CUR;
            continue;
        } 
   
        echo <<< PAGE
        
        <a href="$self_link?p=$i">$i</a>
PAGE;
   }
   
}
?>
