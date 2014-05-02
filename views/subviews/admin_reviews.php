
<?php if( isset($confirm_action) ){ ?>
    <!-- A Couple of medium widgets -->
    <div class="admin-content-widget widget-medium">
        <h4>Confirm action</h4>
        
        <?php echo $confirm_message; ?>
    </div>

    <div class="admin-content-widget widget-medium">
        <a href="<?php echo $self_link.'/'.$confirm_action.'?r='.$_GET['r']; ?>"
            >Confirm</a>
        <a href="<?php echo $self_link; ?>">Cancel</a>
    </div>
<?php } ?>


<!-- A full content widget -->
<div class="admin-content-widget widget-full">
    <h4>Manage Reviews</h4>
    
    <?php if( isset($reviews_result) ){ ?>
    <!-- Table holding review data -->
    <table>
        <!-- Header row -->
        <tr>
            <th> Reviewee </th>
            <th> Reviewer </th>
            <th> Description </th>
            <th> Rating </th>
            <th> ListingID </th>
            <th> Status </th>
        </tr>
        
        <!-- Display all reviews-->
        <?php
        foreach( $reviews_result as $row ){
        echo <<< USERDATAS
        <!-- User row -->
        <tr>
            <td>${row['userid']}</td>
            <td>${row['reviewerid']}</td>
            <td>${row['description']}</td>
            <td>${row['rating']}</td>
            <td>${row['listingid']}</td>
            <td>
                <a href="$self_link/deleteconfirm?r=${row['id']}"
                    style="float:right;">
                    (DELETE)
                </a>
            </td>
        </tr>
USERDATAS;
        }
        ?>
        
    </table>
    <?php } else {
        echo "no data";
    } ?>
    
</div>
