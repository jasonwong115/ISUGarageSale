
<?php
if( $action_message != null ){
    echo <<< ACT
    <div class="submit_message">
        $action_message
    </div>
ACT;
}
?>

<!-- Form for new posting -->
<?php $post_form->print_self(); ?>
