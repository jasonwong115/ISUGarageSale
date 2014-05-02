
<?php if( isset($confirm_action) ){ ?>
    <!-- A Couple of medium widgets -->
    <div class="admin-content-widget widget-medium">
        <h4>Confirm action</h4>
        
        <?php echo $confirm_message; ?>
    </div>

    <div class="admin-content-widget widget-medium">
        <a href="<?php echo $self_link.'/'.$confirm_action.'?u='.$_GET['u']; ?>"
            >Confirm</a>
        <a href="<?php echo $self_link; ?>">Cancel</a>
    </div>
<?php } ?>


<!-- A full content widget -->
<div class="admin-content-widget widget-full">
    <h4>Manage Users</h4>
    
    <?php if( isset($users_result) ){ ?>
    <!-- Table holding user data -->
    <table>
        <!-- Header row -->
        <tr>
            <th> ID </th>
            <th> Handle </th>
            <th> Name </th>
            <th> Email </th>
            <th> User Level </th>
            <th> Status </th>
        </tr>
        
        <!-- Now display all -->
        <?php
        // loop over all
        foreach( $users_result as $row ){
        echo <<< USERDATAS
        <!-- User row -->
        <tr>
            <td>${row['userid']}</td>
            <td>${row['handle']}</td>
            <td>${row['name']}</td>
            <td>${row['email']}</td>
            <td>${row['userlevel']}
                <div style="float:right;">
USERDATAS;
            if( $row['userlevel'] < GarageSale\User::USER_ADMIN ){
            echo <<< ADVUSR
                <!-- advance user -->
                <a href="${self_link}/advanceuser?u=${row['id']}"
                    >[+]</a>
ADVUSR;
            }
            
            if( $row['userlevel'] > GarageSale\User::USER_STANDARD ){
            echo <<< DWNUSR
                <!-- down user -->
                <a href="${self_link}/devanceuser?u=${row['id']}"
                    >[-]</a>
DWNUSR;
            }
            
        echo <<< USERDATAS
                </div>
            </td>
            <td>${row['status']}
                <a href="$self_link/statuschange?u=${row['id']}"
                    style="float:right;">
                    (Change)
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
