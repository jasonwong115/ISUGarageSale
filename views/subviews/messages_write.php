<!-- Set up sidebar for being on the left -->
<div class="sidebar_area">
    Sidebar
</div>

<!-- Set up main gutter for being on the right side -->
<div class="main_gutter">

    <?php if( isset($write_message) && $write_message != '' ){ ?>
    
    <?php } ?>
    
    <?php
        // check for bad input
        if( $write_message != null ){
        echo <<< SM
            <div class="submit_message">
                $write_message
            </div>
SM;
        }
    ?>
    
    <!-- Reply form -->
    <?php $message_form->print_self(); ?>
    
</div>
