<?php
global $app;
// Set the route to the admin page
$app->router->add_extension('/', 
    array( 'controller' => 'home',
        'action' => 'index',
        'args' => array( 'id' => null )
    )
);
// Set the route to the admin page
$app->router->add_extension('/home', 
    array( 'controller' => 'home',
        'action' => 'index',
        'args' => array( 'id' => null )
    )
);
?>
