<?php
global $app;
// Set the route to the admin page
$app->router->add_extension('/ldap', 
    array( 'controller' => 'ldap',
        'action' => 'test',
        'args' => array(
            'name' => null 
        )
    )
);
?>
