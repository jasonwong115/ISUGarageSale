<!-- display boards -->

<div class="admin-content-widget widget-full">

    <?php if( $action_message != null ){ ?>
    <!-- action message box -->
    <div class="forum-admin-action-message">
        <?php echo $action_message; ?>
    </div>
    <?php } ?>

<?php
// loop over boards here
foreach( $grouped_boards as $group )
{
    echo <<< GROUP
    <div class="board_group">
    <h3>${group['name']}
        <a href="#form_${group['id']}" class="reveal_form"
            >Add Board</a>
            
        <small>${group['description']}</small>
    </h3>
    
        <!-- Form for adding board to group -->
        <div class="hidden_form" id="form_${group['id']}">
            <!-- Form now -->
            <strong>Create a new board in this group</strong><br />
            <form method="POST" action="${add_action}">
                <input type="hidden" name="group_id" 
                    value="${group['id']}" />
GROUP;
    ?>                
                <!-- Name input -->
                <input type="text" name="name" 
                    placeholder="Board name"/>
                <br />
                
                <!-- Description input -->
                <textarea placeholder="Board description" 
                    name="description"></textarea>
                <br />
                
                <!-- group order -->
                <input type="number" name="board_order" 
                    placeholder="Group order" />
                <br />
                
                <!-- Submit -->
                <input type="submit" value="Submit &raquo;" />
                
            </form>
        </div>
    <div>
<?php
    
    if( count($group['boards']) == 0 ){
        echo "<p>No boards have been assigned to this group</p>";
    }

    // now loop over board in group
    foreach( $group['boards'] as $board ){
        
        echo <<< BOARD
        
        <h4>${board['name']}</h4>
        <p>${board['description']}</p>
BOARD;
        
    }

    echo <<< GROUP
    </div>
    </div>
GROUP;
}
?>

<strong> Add a group to the forum </strong>
<!-- New group form -->
<form method="POST" action="<?php echo $add_group_action; ?>">
    
    <!-- Name of the group to add -->
    <input type="text" name="name" placeholder="Group name" />
    <br />
    
    <!-- description of group to add -->
    <textarea name="description" placeholder="Group description"
        ></textarea>
    <br />
    
    <!-- group order -->
    <input type="number" name="group_order" placeholder="Group order" />
    <br />
    
    <!-- Submit -->
    <input type="submit" value="Submit &raquo;" />
    
</form>

</div>
