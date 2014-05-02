<h3 style="margin-top: 0;">
    Recent Reviews
</h3>
<?php
foreach( $result as $row ){
    
    // make profile link
    $who = '<a href="'.
        $this->app->form_path('user/profile/'.$row['userid']).
        '">' .
        $this->app->user->name_from_id((int) $row['userid']) .
        '</a>';
    
    $reviewer = '<a href="'.
        $this->app->form_path('user/profile/'.$row['reviewerid']).
        '">' .
        $this->app->user->name_from_id((int) $row['reviewerid']) .
        '</a>';
    
    if( !array_key_exists($row['listingid'], $listings_found) ){
        $result = $listings_model->limit(1)->
            get_item( $row['listingid'] );
        
        // check for values
        if( count($result) == 0 ){
            // nothing, skip
            continue;
        }
        
        // save row
        $listings_found[$row['listingid']] = $result[0];
    }
    
    
    // strip the content down to nothing
    $content = substr(strip_tags($row['description']), 0, 450); 
    
    // get title of this thing
    $item_name = $listings_found[$row['listingid']]['title'];
    
    $item_link = '<a href="'. 
        $this->app->form_path('browse/sold/'.$row['listingid'])
        .'">'. $item_name . '</a>';
    
    // echo data
    echo <<< ROW
    
    
    <strong>via $item_link</strong>
    <small>
    <br />
    on ${row['date_created']} by $reviewer for $who </small>
    <br />
    <div>
        <span class="stars">${row['rating']}</span>
    </div>
    
    $content
    
    <br /><br />
    
ROW;
    
}
?>
