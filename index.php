<?php
/** index.php
 *  This page is THE front end server. The index will serve nearly every
 *  request to the site by accepting and parsing queries. If mod_rewrite
 *  is enabled the routing class will direct all traffic onto this page
 *  and will interpret the path into controls and actions.
 *
 *  To use the router add a route as explaind in app/router.php then
 *  create a controller in the controllers/ directory.
 *  To access the controller on the page you can go to:
 *  http://localhost:4444/index.php/path-to-controller/<action>/<args>
 *  The action is the method in the controller that will be called and
 *  the args are loaded and passed to the action.
 */
// this loads garagesale and spawns an instanc of the GarageSale classes
// called $app 
require('app/garagesale.php');


// add a new route to the page
// Note that '/' is a special case in that it cannot accept actions

// adds the user controller to the application
$app->router->add_route('/user', 
    array( 'controller' => 'user',
        'action' => 'profile',
        'args' => array('id' => null, 'page' => null )
    )
);


// adds the browse controller to the application
$app->router->add_route('/browse', 
    array( 'controller' => 'browse',
        'action' => 'all',
        'args' => array('page' => null,'category' => null )
    )
);


// adds the listings controller to the application
$app->router->add_route('/listings', 
    array( 'controller' => 'listings',
        'action' => 'history',
        'args' => array('id' => null, 'page' => null )
    )
);



// adds the message controller to the application
$app->router->add_route('/messages', 
    array( 'controller' => 'messages',
        'action' => 'inbox',
        'args' => array('fromid' => null, 'toid' => null )
    )
);

// Set the route to the admin page
$app->router->add_route('/admin', 
    array( 'controller' => 'admin',
        'action' => 'index',
        'args' => array('tool' => null,'id' => null )
    )
);

// adds the user controller to the application
$app->router->add_route('/install', 
    array( 'controller' => 'install',
        'action' => 'database',
        'args' => array('id' => null)
    )
);


// runs the application
$app->run();
?>
