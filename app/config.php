<?php
namespace GarageSale;
/** app/config.php
 *  This class contains all configuration data for the rest of the app.
 *  It services both the front and the back end and contains much data
 *  that must be customized by the installer of the system.
 *  
 *  Certain values MUST be changed by the installer in order to ensure
 *  security. 
 *      config::$salt
 */
class Config {
    
    /* GENERAL */
    
    /** Base path refers to the path as navigated by a browser,
     *  distinguish from the internal path which refers to the actual
     *  location from the root over the server.
     */
    public $base_path = '/COMS309/trunk/index.php/'; 
    
    // for local testing setup
    //public $base_path = '/coms/index.php/';
    
    /** Root path refers to the root installation path of the GarageSale
     *  system.
     */
    public $root_path = '/COMS309/trunk/';
     
    // for local testing
    //public $root_path = '/coms/';
    
    /** Internal path is the root to files on the server. This is how
     *  a file will get linked to by any server scripts.
     */
    public $internal_path = '/COMS309/trunk/';
    
    // for local testing setup
    //public $internal_path = '/coms/';
    
    
    /* SECURITY */
    
    /** The salt value adds security to encryptions. The value should be
     *  chosen at install time to be a completely random, unique string
     *  of characters and not shared with anyone.
     */
    public $salt = 
        'a9K5aY7SQT/$#95hi48!5(aizpakH51^slso[5lh7JK8$lal)';
    
    /** The default algorithm to prefer for encryption and hashing.
     */
    public $algorithm = 'sha512';
    
    /** integer Consecutive comments is the upper limit on the number of
     *  consecutive comments a user can leave on a post. This is used
     *  to limit the spamming power of rougue users.
     */
    public $consecutive_comments = 5;
    
    /** integer Consecutive offers is the upper limit on the number of
     *  consecutive offers a user can leave on a post. This is used
     *  to limit the spamming power of rougue users.
     */
    public $consecutive_offers = 4;
    
    /* DATABASE */
    
    /** dbconfig is a multidimensional array containing the config info
     *  for the various methods of database connection. It is provided
     *  in this format to improve modularity. I.e. different database
     *  classes for different connection types (sqlite, mysql, etc)
     *
     *  The first dimension is the database type. I.e. mysql. The array
     *  of host information on this type can be retrieved with a call 
     *  like get_db_config('mysql'). 
     */
    public $databases = array(
        'mysql' => array(
            'host'      => 'mysql.cs.iastate.edu',
            'database'  => 'db30907',
            'username'  => 'u30907',
            'password'  => 'axTrtSRJj',
            'prefix'    => 'gs_'
        ),
        'sqlite' => array(
            'not_supported' => true
        )
    );
    
    
    
}

?>
