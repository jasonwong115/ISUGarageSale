
<?php if( isset($confirm_action) ){ ?>
    <!-- A Couple of medium widgets -->
    <div class="admin-content-widget widget-medium">
        <h4>Confirm action</h4>
        
        <?php echo $confirm_message; ?>
    </div>

    <div class="admin-content-widget widget-medium">
        <a href="<?php echo $self_link.'/'.$confirm_action.'?c='.$_GET['c']; ?>"
            >Confirm</a>
        <a href="<?php echo $self_link; ?>">Cancel</a>
    </div>
<?php } ?>


<!-- A full content widget -->
<div class="admin-content-widget widget-full">
    <h4>Manage Contacts</h4>
    
    <?php if( isset($contacts_result) ){ ?>
    <!-- Table holding reports data -->
    <table>
        <!-- Header row -->
        <tr>
            <th> Name </th>
            <th> Email</th>
            <th> Subject </th>
            <th> Message </th>
            <th> Reason </th>
            <th> Status </th>
			<th> Actions </th>
        </tr>
        
        <!-- Display all reports-->
        <?php
        foreach( $contacts_result as $row ){
        echo <<< USERDATAS
        <!-- User row -->
        <tr>
            <td>${row['name']}</td>
            <td>${row['email']}</td>
            <td>${row['subject']}</td>
			<td>${row['message']}</td>
            <td>${row['reason']}</td>
            <td>${row['status']}</td>
            <td>
                <a href="$self_link/solvedconfirm?c=${row['id']}"
                    style="float:right;">
                    (SOLVED)
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
