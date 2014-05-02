
<div class="sidebar_area">
<!-- Amazon Results -->
<?php

    // use amazon result script
    //include( 'views/scripts/amazon_results.php');
    $app->script('amazon_results');
?>
</div>

<div class="main_gutter">

<?php echo $post_message; ?>
<br />

<?php
// loop through the profile data if its there
if( true ) { // $categories != null ){
?>


<form method="POST" action="<?php echo $form_path; ?>" enctype="multipart/form-data">

    <!-- Post title input -->
    Post title:
    <br />
    
    <?php 
    
        // test for update attempts and bad inputs
        if( $post_attempt && 
            (!isset($_POST['title']) || $_POST['title'] == null ) )
        {
        
            // alert use to bad input
            echo 'A title is required for this post<br />';
        }
        
    ?>
    
    <input type="text" name="title" placeholder="Post Title" <?php 
            // test if previous input
            echo $title_value;
        ?> />
    <br />
    
    <!-- Asking price input -->
    Asking price:
    
    <?php 
    
        // test for update attempts and bad inputs
        if( $post_attempt && 
            (!isset($_POST['asking_price']) || 
            $_POST['asking_price'] == null ) 
        ) {
        
            // alert use to bad input
            echo 'A price is required for this post.<br />';
        }
        
        // test for update attempts and bad inputs
        if( $post_attempt && isset($_POST['asking_price']) && 
            !is_numeric($_POST['asking_price']) 
        ) {
        
            // alert use to bad input
            echo 'A numeric value is required for the price.<br />';
        }
        
    ?>
     
    <br />
    $<input type="text" name="asking_price" placeholder="0.00" <?php
            // test if previous input
            echo $asking_price_value;
        ?> />
        
    <br />
    
    <!-- Other offers the user is willing to accept -->
    Other offer requirements:
    <br />
    
    <!-- And the text area -->    
    <textarea name="other_offer" 
        placeholder="Describe other offers you are willing to accept."
    ><?php echo $other_offer_value; ?></textarea>
    <br />


    <!-- A description of what is being sold -->
    Description:
    <br />
    
    <?php 
    
        // test for update attempts and bad inputs
        if( $post_attempt && 
            (!isset($_POST['description']) || 
            $_POST['description'] == null ) 
        ) {
        
            // alert use to bad input
            echo 'A description is required for this post<br />';
            
            // use null for description values
            $description_val = null;
        }
        
    ?>
    
    <textarea name="description" 
        placeholder="Describe what you'd like to like to sell."
    ><?php echo $description_value; ?></textarea>
    <br />
    
    
    <!-- Upload images -->
    Upload image:
    <br />
    <div id="image_upload_box">
        <input type="file" name="file" id="file">
    </div>
    
    
    <!-- Javascript makes this add more image fields -->
    <a href="#add" id="add_image">Add another image (up to 5)</a>
    <br /><br />
    
    
    
    <div id="newpost_category_selection" class="newpost_item category_selection">
        Choose Category:
        <br />
        
        
        <?php 
        
            // test for update attempts and bad inputs
            if( $post_attempt && 
                (!isset($_POST['categoryid']) || 
                $_POST['categoryid'] == null ) 
            ) {
            
                // alert use to bad input
                echo 'A category is required for this post<br />';
                
                // use null for category values
                $categoryid_val = -1;
                
            // must be a number
            } elseif( $post_attempt && isset($_POST['categoryid']) &&
                 !is_numeric($_POST['categoryid']) 
            ){
                
                // alert use to bad input
                echo 'A valid category must be chosen.<br />';
            } 
            
        ?>
        
        <!-- New list for the categories -->
        <ul>
<?php
    // start a new stack
    $stack = array();
    
    
    // start the stack up
    $count = count($categories);
    for( $i = 0; $i < $count; $i++ ){
        $stack[] = &$categories[$i];
    }// end of for each
    
    // loop over stack and output in time
    while( count($stack) > 0 ){
        
        // get next
        $next = array_pop($stack);
        
        // set default selected value:
        $selected = '';
        
        // check for category id match to select this category
        if( $categoryid_value == $next['id'] ){
            
            // select this on
            $selected = ' checked ';
        } 
        
        
        // output then push to stack
        echo '<li><input type="radio" name="categoryid" value="'.
            $next['id'] . '"' . $selected . '>' . $next['name'] . 
            '</li>';
        
        // if there are children push them
        if( $next['child_item'] != null ){
        
            // loop over and add
            $c = count( $next['child_item'] );
            for( $j = 0; $j < $c; $j++ ){
                $stack[] = &$next['child_item'][$j];
            }
        }
    }
?>

        </ul>
    </div>
    
    <br />
    Keywords:
    <br />
    <input type="text" name="keywords" 
        <?php echo $keywords_value; ?>
        placeholder="Separate keywords by a space" />
    
    
    
    <br />
    
    <!-- And of course, submit it -->
    <input type="submit" value="<?php echo $button_text; ?> &raquo;" />
    
    
</form>
<?php

// no reviews were found
} else {
    if( $subject < 0 ){
        echo "Error: user not found";
    }else{
        echo "You have no unreviewed transactions with user $subject";
    }
}
?>

</div>
