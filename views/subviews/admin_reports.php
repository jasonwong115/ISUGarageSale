
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
    <h4>Manage Reports</h4>
    
    <?php if( isset($reports_result) ){ ?>
    <!-- Table holding reports data -->
    <table>
        <!-- Header row -->
        <tr>
            <th> Name </th>
            <th> Email</th>
            <th> Offender </th>
            <th> Explanation </th>
            <th> Reason </th>
            <th> Status </th>
			<th> Actions </th>
        </tr>
        
        <!-- Display all reports-->
        <?php
        foreach( $reports_result as $row ){
        echo <<< USERDATAS
        <!-- User row -->
        <tr>
            <td>${row['name']}</td>
            <td>${row['email']}</td>
            <td>${row['offender']}</td>
			<td>${row['explanation']}</td>
            <td>${row['reason']}</td>
            <td>${row['status']}</td>
            <td>
                <a href="$self_link/solvedconfirm?r=${row['id']}"
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
