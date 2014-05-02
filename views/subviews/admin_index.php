
<?php
// check for action message
if( $action_message != null ){
    echo <<< ACTION
    <div class="admin-message">
        $action_message
    </div>
ACTION;
}
?>

    <!-- A small content widget -->
    <div class="admin-content-widget widget-small">
        <h4>Default listings per page:</h4>
        
        <!-- form to update settings for per page browsing -->
        <form action="<?php echo $per_page_action; ?>" method="POST">
            <input type="number" name="listings_per_page"
                value="<?php echo $listings_per_page_value; ?>" />
            <br />
            <input type="submit" value="Submit &raquo;" />
        </form>
    </div>
    
    <!-- A small content widget -->
    <div class="admin-content-widget widget-small">
        <h4>Another Header</h4>
        Lorem ipsum dolar sit amet.
    </div>
    <!-- A small content widget -->
    <div class="admin-content-widget widget-small">
        Small
    </div>
    
