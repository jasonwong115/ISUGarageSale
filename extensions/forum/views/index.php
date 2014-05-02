<!-- display boards -->

<div class="forum_pad">

<?php
// loop over boards here
foreach( $grouped_boards as $group )
{
    echo <<< GROUP
    <div class="board_group">
    <h3>${group['name']}
            
        <small>${group['description']}</small>
    </h3>
GROUP;
    
    if( count($group['boards']) == 0 ){
        echo "<p>No boards have been assigned to this group</p>";
    }

    // now loop over board in group
    foreach( $group['boards'] as $board ){
        
        echo <<< BOARD
        <div class="forum_board">
            <a href="$board_action/${board['id']}">
            
                <div class="forum-count">
                    ${board['post_count']} replies
                </div>
                <div class="forum-count">
                    ${board['thread_count']} topics
                </div>
                
                <h4>${board['name']}</h4>
                <div>${board['description']}</div>
            
            
            </a>
        </div>
BOARD;
        
    }

    echo <<< GROUP
    </div>
GROUP;
}
?>

</div>
