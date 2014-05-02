<!-- display thread -->
<div class="forum_pad">

    <?php
    if(isset($action_message)){
        echo <<< MSG
        <div class="forum-admin-action-message">$action_message</div>
MSG;
    }
    ?>
    
    <!-- Thread creation form -->
    <form method="POST" action="<?php echo $submit_action; ?>">
    
        <!-- Thread name input -->
        <input type="text" 
            <?php echo $name_value; ?>
            name="name" placeholder="Thread name" />
        <br />
    
        <!-- Thread input content -->
        <textarea name="description" placeholder="Thread message."
            class="wysiwyg"
            ><?php echo $description_value; ?></textarea>
        <br />
        
        <!-- Submit -->
        <input type="submit" value="Submit &raquo;" />
        
    </form>

</div>
