<?php

/**
 * A class file to connect to database
 */
class DB_CONNECT {

    /**
     * Function to connect with database
     */
    function connect() {
		//Get database information
		require_once('../app/config.php');
		//Get constants from database file
		require_once('../app/database.php');
		$config = new GarageSale\Config();
	
		// select the type of database to use
		$db_config = $config->databases['mysql'];
	
		/* ---------------------------------------
		 * load values from the configuration file
		 */
	
		// host of database
		$host = $db_config['host'];
		// name of database
		$database = $db_config['database'];
		// user name to login
		$username = $db_config['username'];
		// password to log in
		$password = $db_config['password'];
		// prefix to use for table namesf
		$prefix = $db_config['prefix'];
		
		$con = mysqli_connect($host,$username,$password,$database);

        // returing connection cursor
        return $con;
    }

   

}

?>