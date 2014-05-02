<?php
global $app;
// adds the forum controller to the application
$app->router->add_extension('/forum', 
    array( 'controller' => 'forum',
        'action' => 'index',
        'args' => array('id' => null,'page' => null )
    )
);
?>
