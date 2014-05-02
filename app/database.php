<?php
namespace GarageSale;

require_once('statement.php');

/** app/database.php
 *  Provides the abstraction for implenting a full database class
 *  A database class contains helpful database connection options for
 *  safely communicating with the database.
 *  The base database class which is used to extend other databases and
 *  provides core functionality between implementations
 */
abstract class BaseDatabase {
    
    /* -----------------------------------------------------------
     * STATUS_* constants define the possible statuses of database 
     *  entries.
     */
     
    /** Indicates a database entry which is active */
    const STATUS_ACTIVE = 0;
    
    /** Indicates a database entry which has addressed and resolved */
    const STATUS_RESOLVED = 1;
    
    /** Inidcates a databse entry which has been closed for use */
    const STATUS_CLOSED = 2;
    
    /** Indicates a database entry which has expired from use */
    const STATUS_EXPIRED = 3;
    
    /** Indicates a databse entry which has been suspended from use */
    const STATUS_SUSPENDED = 4;
    
    /** Indicates a databse entry which has been suspended from use */
    const STATUS_DECLINED = 5;
    
    /** Indicates a databse entry which has been suspended from use */
    const STATUS_ACCEPTED = 6;
    
    /** A User defined status which can mean anything */
    const STATUS_MISC = 7;
    
    /** Indicates an entry which has not yet been read */
    const STATUS_UNREAD = 8;
    
    /** Indicates an entry which has already ben read */
    const STATUS_READ = 9;
	
	/** Indicates an entry which has already ben read */
    const STATUS_BEST = 10;
    
    
    /** An array containing configuration options for connecting to they
     *  databse
     */
    protected $db_config;
    
    /** Construct the new database class instance
     *  @param $db_config Configuration options for connecting to a
     *         database
     */
    function __construct( $db_config )
    {
        $this->db_config = $db_config;
    }
    
    /** Starts a new SELECT statement from a given table
     *  @param string $table_name the name of the table to select from
     *  @return class A new MySQLStatement instance 
     */
    function select( $table_name )
    {
        return new MySQLStatement( 
            MySQLStatement::SELECT, 
            $table_name,
            $this->db_config['prefix'] 
        );
    }
    
    /** Starts a new UPDATE statement from a given table
     *  @param string $table_name the name of the table to select from
     *  @return class A new MySQLStatement instance 
     */
    function update( $table_name )
    {
        return new MySQLStatement( 
            MySQLStatement::UPDATE, 
            $table_name,
            $this->db_config['prefix'] 
        );
    }
    
    /** Starts a new INSERT statement from a given table
     *  @param string $table_name the name of the table to select from
     *  @return class A new MySQLStatement instance 
     */
    function insert( $table_name )
    {
        return new MySQLStatement( 
            MySQLStatement::INSERT, 
            $table_name,
            $this->db_config['prefix'] 
        );
    }
    
    /** Starts a new DELETE statement from a given table
     *  @param string $table_name the name of the table to select from
     *  @return class A new MySQLStatement instance 
     */
    function delete( $table_name )
    {
        return new MySQLStatement( 
            MySQLStatement::DELETE, 
            $table_name,
            $this->db_config['prefix'] 
        );
    }
    
    /** Starts a new COUNT statement from a given table
     *  @param string $table_name the name of the table to select from
     *  @return class A new MySQLStatement instance 
     */
    function count( $table_name )
    {
        return new MySQLStatement( 
            MySQLStatement::COUNT, 
            $table_name,
            $this->db_config['prefix'] 
        );
    }
    
        
    /** Starts a connection to the database. 
     *  @return true on success, false otherwise
     */
    abstract function connect();
    
    /** Disconnect from an active database connection.
     */
    abstract function disconnect();

    /** Indicates whether the database is still connected or not
     *  @return true if connected, false if no
     */
    abstract function is_connected();

    /** Gets the prefix to use for database tables
     *  @return the string provided in the db config for table prfixes 
     */
    abstract function prefix();

    /** Check if a statement has already been prepared previously and
     *  just needs to have parameters bound.
     *  @param $statement_name name of the prepared statement
     *  @return true if the named statement is in the statement array
     *          false otherwise
     */
    abstract function is_prepared( $statement_name );
    
    /** Bind parameters to a statement saved to the given statement name
     *  @param $statement_name name of the prepared statement
     *  @param $field_types is a string of the types of fields: i d s b
     *  @param ... the number of extra params should match the number of
     *         field types provided. 
     *  @return true on success, false on failure or mismatched number
     *          field types to parameters
     */
    abstract function bind_param( $statement_name, $field_types );
    
    /** Get the results of a prepped statement, closet the statement
     *  @param $statement_name name of the prepared statement
     *  @return The result of a databse query with the given statement
     */
    abstract function prepped_result( $statement_name );
    
    /** Executes a statement created using the Statement class
     *  @return true on success, false on failure
     */
    abstract function statement_execute( $statement );
    
    
    /** Executes a statement and gets the results of a statement created
     *  using the statements class
     *  @return A result set generated from executing the statement
     */
    abstract function statement_result( $statement );
    
    /** Prepare a new statement saved with the given statement name
     *  @param $statement_name name of the prepared statement
     *  @return true on success, false on failure
     */
    abstract function prepare( $statement_name, $statement_query );
    
    
    /** Execute a statement that has been prepared and bound 
     *  @param $statement_name name of the prepared statement
     *  @return true on success, false on failure
     */
    abstract function prepped_execute( $statement_name );
    
    /** Gets one row from a statement
     *  @param string $statement_name name of statement to look for
     *  @return array one row from the database
     */
    abstract function prepped_fetch( $statement_name );
    
    /** Gets one row from an undefined statement
     *  @return array one row from the database
     */
    abstract function statement_fetch();
    
    /** Executes an SQL script contained in a file 
     *  @param string $file_path path to the file to execute
     *  @return bool true on success false on failure
     */
    function exec_file( $file_path )
    {
        // if file doesn't exist then we must fail 
        if( !file_exists($file_path) ){
            return false;
        }
        
        // open the file
        $file = fopen( $file_path, 'r' );
        
        // create a new buffer
        $buffer = '';
        
        // loop through file and parse in the prefix as needed
        while( !feof($file) ){
        
            // get the line and parse
            $buffer .= str_replace(
                "%table_prefix%", 
                $this->db_config['prefix'], 
                fgets($file) );
            
        }
        
        // execute the buffer
        $this->prepare( 'exec_script', $buffer );
        // execute
        return $this->prepped_execute('exec_script');
    }
}

/**
 *  The MySQLDatabase extends the core database to offer mysql specific
 *  connection options
 */
class MySQLDatabase extends BaseDatabase {
    
    /** An array of pre-prepared statements ready to use */
    private $prepared_statements;
    
    
    /** The MySQL connection object */
    private $mysqli;
    
    /** Construct the new database class instance
     *  @param $db_config Configuration options for connecting to a
     *         database
     */
    function __construct( $db_config )
    {
        parent::__construct( $db_config );
        $this->prepared_statements = array();
        $this->mysqli = null;
    }
    
    /** Starts a connection to the database. 
     *  @return true on success, false otherwise
     */
    function connect()
    {
        // if its not already connected we can connect now
        if( $this->mysqli == null || $this->mysqli->ping() == false ){
            
            // create a new mysqli instance
            $this->mysqli = new \mysqli(
                $this->db_config['host'],
                $this->db_config['username'],
                $this->db_config['password'],
                $this->db_config['database']
            );
            
            // check for errors
            if( $this->mysqli->connect_errno ){
                return false;
            }
            
            // returns success
            return true;
        }
        
        // already connected, return success
        return true;
    }
    
    
    /** Disconnect from an active database connection.
     */
    function disconnect()
    {
        // if object exists
        if( $this->mysqli != null ){
            // close connection
            $this->mysqli->close();
            $this->mysqli = null;
        }
    }
    

    /** Gets the prefix to use for database tables
     *  @return the string provided in the db config for table prfixes 
     */
    function prefix()
    {
        return $this->db_config['prefix'];
    }
    
    /** Indicates whether the database is still connected or not
     *  @return true if connected, false if no
     */
    function is_connected()
    {
        return $this->mysqli != null && $this->mysqli->ping() == true;
    }
    
    
    /** Check if a statement has already been prepared previously and
     *  just needs to have parameters bound.
     *  @param $statement_name name of the prepared statement
     *  @return true if the named statement is in the statement array
     *          false otherwise
     */
    function is_prepared( $statement_name )
    {
        return isset( $this->prepared_statements[$statement_name] );
    }
    
    
    /** Prepare a new statement saved with the given statement name
     *  @param $statement_name name of the prepared statement
     *  @return true on success, false on failure
     */
    function prepare( $statement_name, $statement_query )
    {
        // make sure there is a mysqli object available
        if( $this->mysqli == null ){
            return false;
        }
        
        // run the preparation and save to the prepped statements
        $this->prepared_statements[$statement_name] = 
            $this->mysqli->prepare($statement_query);
        
                
        // test for failur
        if( !$this->prepared_statements[$statement_name] ){
            return false;
        }
        
        // return success
        return true;
    }
    
    /** Bind parameters to a statement saved to the given statement name
     *  @param $statement_name name of the prepared statement
     *  @param $field_types is a string of the types of fields: i d s b
     *  @param ... the number of extra params should match the number of
     *         field types provided. 
     *  @return true on success, false on failure or mismatched number
     *          field types to parameters
     */
    function bind_param( $statement_name, $field_types /*, ... */ )
    {
        // if this statement is not available, fail
        if( !isset($this->prepared_statements[$statement_name]) || 
            $this->prepared_statements[$statement_name] == null )
        {
            return false;
        }
        
        // get the number of extra arguments
        $numargs = func_num_args();
        
        // make sure the number of parameters match
        if( $numargs < 3 || $numargs-2 != strlen($field_types) ){
            return false;
        }
        
        // get args from this function
        $arg_list = func_get_args();
        
        // arguments to pass to bind param, start with field types 
        $args = array( $field_types );
        
        // load the params into the array
        for( $i = 2; $i < $numargs; $i++ ){
            $args[$i-1] = &$arg_list[$i];
        }
        
        // finally call the bind_param method
        call_user_func_array(
            array(
                $this->prepared_statements[$statement_name],
                'bind_param'
            ),
            $args
        );
    }
    
    /** Execute a statement that has been prepared and bound 
     *  @param $statement_name name of the prepared statement
     *  @return true on success, false on failure
     */
    function prepped_execute( $statement_name )
    {
        if( isset($this->prepared_statements[$statement_name]) &&
            $this->prepared_statements[$statement_name] != null
        ){
            $this->prepared_statements[$statement_name]->execute();
        }
        return true;
    }
    
    
    /** Get the results of a prepped statement, closet the statement
     *  @param $statement_name name of the prepared statement
     *  @return The result of a databse query with the given statement
     */
    function prepped_result( $statement_name )
    {
        // check that statement exists
        if( isset($this->prepared_statements[$statement_name]) &&
            $this->prepared_statements[$statement_name] 
        ){
            $result = array();
        
            // get the number of feilds in the statement
            $count = $this->prepared_statements[$statement_name]->
                            field_count;
            
            // prepare an array to hold these args
            $args = array_fill(0, $count, NULL);
            $vals = array_fill(0, $count, NULL);
            
            // set up args
            for( $i=0; $i < count($args); $i++ ){
                $args[$i] = &$vals[$i]; 
            }
            
            // finally call the bind_result method
            call_user_func_array(
                array(
                    $this->prepared_statements[$statement_name],
                    'bind_result'
                ),
                $args
            );
        
        
            // init counter
            $i = 0;
        
            // loop through and load results
            while($this->prepared_statements[$statement_name]->fetch()){
                $newrow = $vals;
                $result[$i] = $newrow;
                $i++;
            }
            
            // return results
            return $result;
        }
        
        // what to do on fail
        return null;
    }
    
    
    /** Executes a statement created using the Statement class
     *  @return true on success, false on failure
     */
    function statement_execute( $statement )
    {
        // get query and parameters
        $query = $statement->get_query();
        $params = $statement->get_params();
        
        $this->prepare( 'db_generated_stmt', $query );
        
        // generate argument list for bind_param
        $args = array( 'db_generated_stmt', '' );
        
        $param_types = '';
        
        // loop and set in args
        for($i=0; $i<count($params); $i++ )
        {
            $param_types .= $params[$i]['type'];
            $args[$i+2] = $params[$i]['value'];
        }
        
        // set param types string
        $args[1] = $param_types;
        
        // finally call the bind_result method
        call_user_func_array(
            array(
                $this,
                'bind_param'
            ),
            $args
        );
        
        // return execution status
        return $this->prepped_execute('db_generated_stmt');
    }
    
    
    /** Executes a statement and gets the results of a statement created
     *  using the statements class
     *  @return A result set generated from executing the statement
     */
    function statement_result( $statement )
    {
        $this->statement_execute( $statement );
        return $this->prepped_result('db_generated_stmt');
    }
    
    
    /** Gets one row from a statement
     *  @param string $statement_name name of statement to look for
     *  @return array one row from the database
     */
    function prepped_fetch( $statement_name )
    {
        return $this->prepared_statements[$statement_name]->fetch();
    }
    
    /** Gets one row from an undefined statement
     *  @return array one row from the database
     */
    function statement_fetch(){
        return $this->prepped_fetch('db_generated_stmt');
    }
}


/**
 *  The PDOMySQLDatabase extends the core database to offer a robust
 *  method for connection options
 */
class PDOMySQLDatabase extends BaseDatabase {
    
    /** An array of pre-prepared statements ready to use */
    private $prepared_statements;
    
    
    /** The MySQL connection object */
    private $pdo;
    
    /** Construct the new database class instance
     *  @param $db_config Configuration options for connecting to a
     *         database
     */
    function __construct( $db_config )
    {
        parent::__construct( $db_config );
        $this->prepared_statements = array();
        $this->pdo = null;
    }
    
    /** Starts a connection to the database. 
     *  @return true on success, false otherwise
     */
    function connect()
    {
        // if its not already connected we can connect now
        if( $this->pdo == null ){
            
            try {
                // create a new mysqli instance
                $this->pdo = new \PDO('mysql:host='.
                    $this->db_config['host'].';dbname='.
                    $this->db_config['database'],
                    $this->db_config['username'],
                    $this->db_config['password']
                );
                
                // set error mode to exceptions
                $this->pdo->setAttribute(
                    \PDO::ATTR_ERRMODE, 
                    \PDO::ERRMODE_EXCEPTION 
                );
                
            }catch( PDOException $e ) {
                // check for errors
                echo "Error connecting to database.";
                return false;
            }
            
            
            // returns success
            return true;
        }
        
        // already connected, return success
        return true;
    }
    
    
    /** Disconnect from an active database connection.
     */
    function disconnect()
    {
        // if object exists
        if( $this->pdo != null ){
            // close connection
            $this->pdo = null;
        }
    }
    

    /** Gets the prefix to use for database tables
     *  @return the string provided in the db config for table prfixes 
     */
    function prefix()
    {
        return $this->db_config['prefix'];
    }
    
    /** Indicates whether the database is still connected or not
     *  @return true if connected, false if no
     */
    function is_connected()
    {
        return $this->pdo != null;
    }
    
    
    /** Check if a statement has already been prepared previously and
     *  just needs to have parameters bound.
     *  @param $statement_name name of the prepared statement
     *  @return bool true if the named statement is in the statement 
     *          array false otherwise
     */
    function is_prepared( $statement_name )
    {
        return isset( $this->prepared_statements[$statement_name] );
    }
    
    
    
    /** Prepare a new statement saved with the given statement name
     *  @param $statement_name name of the prepared statement
     *  @return true on success, false on failure
     */
    function prepare( $statement_name, $statement_query )
    {
        // make sure there is a mysqli object available
        if( $this->pdo == null ){
            return false;
        }
        
        try {
            // run the preparation and save to the prepped statements
            $this->prepared_statements[$statement_name] = 
                $this->pdo->prepare($statement_query);
                
        } catch ( PDOException $e ) {
            // test for failure
            echo 'Error preparing statement.';
            return false;
        }
        
        // return success
        return true;
    }
    
    /** Bind parameters to a statement saved to the given statement name
     *  @param $statement_name name of the prepared statement
     *  @param $field_types is a string of the types of fields: i d s b
     *  @param ... the number of extra params should match the number of
     *         field types provided. 
     *  @return true on success, false on failure or mismatched number
     *          field types to parameters
     */
    function bind_param( $statement_name, $field_types /*, ... */ )
    {
        // if this statement is not available, fail
        if( !isset($this->prepared_statements[$statement_name]) || 
            $this->prepared_statements[$statement_name] == null )
        {
            return false;
        }
        
        // get the number of extra arguments
        $numargs = func_num_args();
        
        // make sure the number of parameters match
        if( $numargs < 3 || $numargs-2 != strlen($field_types) ){
            return false;
        }
        
        // get args from this function
        $arg_list = func_get_args();
        
        
        // Loop and bind all params
        for( $i = 2; $i < $numargs; $i++ ){
        
            // detirmine the appropriate param type
            // default is string
            $param_type = \PDO::PARAM_STR;
            
            // if is an integer type
            if( $field_types[$i-2] == 'i' ){
                $param_type = \PDO::PARAM_INT;
            
            // if is a blob type
            }else if( $field_types[$i-2] == 'b'){
                $param_type = \PDO::PARAM_LOB;
            }
            
            try {
                // bind the actual params
                $this->prepared_statements[$statement_name]->
                    bindParam( $i-1, $arg_list[$i], $param_type );
            
            // what to on fail
            } catch (PDOException $e ) {
                echo 'Error binding param';
                return false;
            }
            
        }
        
        // success!
        return true;
    }
    
    /** Execute a statement that has been prepared and bound 
     *  @param $statement_name name of the prepared statement
     *  @return true on success, false on failure
     */
    function prepped_execute( $statement_name )
    {
        if( isset($this->prepared_statements[$statement_name]) &&
            $this->prepared_statements[$statement_name] != null
        ){
            // attempt to execute
            try {
                $this->prepared_statements[$statement_name]->execute();
                
            // what to do on faliure
            } catch (PDOException $e ) {
                echo 'Error executing prepared';
                return false;
            }
        }
        return true;
    }
    
    
    /** Get the results of a prepped statement, closet the statement
     *  @param $statement_name name of the prepared statement
     *  @return The result of a databse query with the given statement
     */
    function prepped_result( $statement_name )
    {
        // check that statement exists
        if( isset($this->prepared_statements[$statement_name]) &&
            $this->prepared_statements[$statement_name] 
        ){
            // try to get the results
            try {
                // get the results
                $result = $this->prepared_statements[$statement_name]
                            ->fetchAll();
                            
            } catch ( PDOException $e ) {
                echo 'error getting results';
                return null;
            }
            return $result;
        }
        
        // what to do on fail
        return null;
    }
    
    
    
    /** Executes a statement created using the Statement class
     *  @return true on success, false on failure
     */
    function statement_execute( $statement )
    {
        // protect the user from executing deletes or updates on
        // a statement without a where value set
        $type = $statement->get_type();
        if( ($type == MySQLStatement::UPDATE || 
             $type == MySQLStatement::DELETE ) &&
             $statement->count_where() == 0 
        ){
            
            // fail this execute
            return false;
        }
    
        // get query and parameters
        $query = $statement->get_query();
        $params = $statement->get_params();
        
        
        // prepare the statement        
        $this->prepare( 'db_generated_stmt', $query );
        
        // generate argument list for bind_param
        $args = array( 'db_generated_stmt', '' );
        
        $param_types = '';
        
        // loop and set in args
        for($i=0; $i<count($params); $i++ )
        {
            $param_types .= $params[$i]['type'];
            $args[$i+2] = $params[$i]['value'];
        }
        
        // set param types string
        $args[1] = $param_types;
        
        
        // finally call the bind_result method
        call_user_func_array(
            array(
                $this,
                'bind_param'
            ),
            $args
        );
        
        // return execution status
        return $this->prepped_execute('db_generated_stmt');
    }
    
    
    /** Executes a statement and gets the results of a statement created
     *  using the statements class
     *  @return A result set generated from executing the statement
     */
    function statement_result( $statement )
    {
        $this->statement_execute( $statement );
        return $this->prepped_result('db_generated_stmt');
    }
    
    
    /** Gets one row from a statement
     *  @param string $statement_name name of statement to look for
     *  @return array one row from the database
     */
    function prepped_fetch( $statement_name )
    {
        return $this->prepared_statements[$statement_name]->fetch(
            \PDO::FETCH_ASSOC
        );
    }
    
    /** Gets one row from an undefined statement
     *  @return array one row from the database
     */
    function statement_fetch(){
        return $this->prepped_fetch('db_generated_stmt');
    }
}
?>
