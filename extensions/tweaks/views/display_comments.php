<h3 style="margin-top: 0;">
    Recent Comments
</h3>
<?php
foreach( $result as $row ){
    
    // make profile link
    $who = '<a href="'.
        $this->app->form_path('user/profile/'.$row['userid']).
        '">' .
        $this->app->user->name_from_id((int) $row['userid']) .
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
    $content = substr(strip_tags($row['comment']), 0, 450); 
    
    // get title of this thing
    $item_name = $listings_found[$row['listingid']]['title'];
    
    $item_link = '<a href="'. 
        $this->app->form_path('browse/item/'.$row['listingid'])
        .'">'. $item_name . '</a>';
    

    // echo data
    echo <<< ROW

    <strong>${row['title']}</strong>
    <br />
    via $item_link
    <br />
    <small> by $who on ${row['date_created']}</small>
    <br />
    
    $content
    
    <br /><br />
    
ROW;
    
}
?>
