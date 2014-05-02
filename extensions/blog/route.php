<?php
global $app;
// Set the route to the admin page
$app->router->add_extension('/blog', 
    array( 'controller' => 'blog',
        'action' => 'browse',
        'args' => array(
            'year' => null,
            'month' => null,
            'day' => null,
            'post' => null 
        )
    )
);
?>
