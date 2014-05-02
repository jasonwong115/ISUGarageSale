<?php
namespace GarageSale;
/** classes/session.php
 *  session class is used to register a new manner of storing sessions
 *  with the server. This class will make user of database connections
 *  to store information on the user session as well as use the standard
 *  sessions provided by PHP.
 *
 *  In order to use this class make sure that the database class and the
 *  config have been included before this class. Both are used in the
 *  constructor of this class.
 *
 *  How to use:
 *  require('classes/sessions.php');
 *  $session = new session($db_class, $config->salt);
 *  $session->start('_s',false);
 *
 *  Use sessions as normal
 *  $_SESSION['stuff'] = 'things';
 *  ....
 *  ....
 *  // not needed but the idea is there....
 *  // $session->close();
 *
 *  @author Tanner Hildebrand
 */
class Session 
{
    /** Database class to use for storing session information */
    private $db;
    
    /** Salt value to add security to encryption */
    private $salt;
    
    /** Constructs a new session object.
     *  Registers the methods the appropriate methods with the session
     *  save handler.
     *  @param  $db This should be a database class object. It can be
     *          already connected or not.
     *  @param  $salt Used in the encryption and decryption of session
     *          data. It should be passed directly from the config
     *  @param bool $simple if true use simpel sessions 
     */
    function __contruct( $db, $salt, $simple= false )
    {
        // set database class and salt
        $this->db = $db;
        $this->salt = $salt;
    
        // register session handler with PHP
        if( $simple != true ){
            session_set_save_handler(array($this,'open'), 
                    array($this,'close'), 
                    array($this, 'read'), 
                    array($this, 'write'), 
                    array($this, 'destroy'), 
                    array($this, 'gc'));
                
            // does some clean up incase there is any interuptions later
            register_shutdown_function('session_write_close');
        }
    }
    
    /** Start is used to begin a session. It calls session_start and
     *  effectively replaces it while setting other important options.
     *  @param $session_name give teh session a cool name.
     *  @param $secure Boolean value true if using https
     */
    static function start( $session_name, $secure = false )
    {
        $session_hash = 'sha512';
        
        // check if this is a viable algorithm
        if( in_array($session_hash, hash_algos()) ){
            // choose this as th has funct
            ini_set('session.hash_function',$session_hash);
        }
        
        // use the most number of bits per character of the hash
        ini_set('session.hash_bits_per_character',5);
        
        // have the standard sessions use only cookies
        ini_set('session.use_only_cookies', 1);
        
        // get the cookie paramters
        $cookie_params = session_get_cookie_params();
        
        // set some paramters
        session_set_cookie_params(
            $cookie_params["lifetime"], 
            $cookie_params["path"],
            $cookie_params["domain"],
            $secure,
            true );
            
        // now go ahead and set the session name
        session_name($session_name);
        
        // start session
        session_start();
        
        // facilitates security by gnerating new session ids
        session_regenerate_id( true );
    }
    
    /** The open function will be used like a constructor for the 
     *  session_set_save_handler method when initiating a new session.
     *  Here it is used to open the database for saving session data.
     *  @return true on success, false on failure
     */
    function open()
    {
        // datbase class should prevent from double connecting
        return $this->db->connect();
        return true;
    }
    
    /** Closes the session, here we will close our database connection
     *  @return true on success, false on failure
     */
    function close()
    {
        return $this->db->disconnect();
        return true;
    }
    
    
    /** Reads the session data from the database based on the id and
     *  decrypts it. This is called whenever $_SESSION['str'] is read.
     *  @param $id string session id
     *  @return decrypted session data based on the session id 
     */
    function read( $id )
    {
        // if this is already created we can reuse
        if( $this->db->is_prepared('sess_read_stmt') == false ){
            // create a safe prepared statement
            $this->db->prepare('sess_read_stmt', 
                "SELECT data FROM ".$this->db->prefix().
                "sessions WHERE id= ? LIMIT 1"
            );
        }
        
        // bind the id parameter as a string
        $this->db->bind_param('sess_read_stmt','s',$id);
        
        // execute statement
        $this->db->prepped_execute('sess_read_stmt');
        
        // get the result of this query
        $result = $this->db->prepped_result('sess_read_stmt');
        
        // get data from the result
        $data = $result->fetch_assoc();
        $data = $data["data"];
        
        // free the result
        $result->close();
        
        // get key value using method below
        // the session id is used to find the key value if in database
        $key = $this->getkey($id);
        
        // decrypt the data
        $data = $this->decrypt($data,$key);
        
        return $data;
    }
    
    /** Session write handler. Called every time a session variable is
     *  stored, e.g. when $_SESSION['str'] = "stuff"
     *  @param $id string of the session id
     *  @param $data string of the session data value
     *  @return true
     */
    function write($id, $data)
    {
        // generate key based on session id
        $key = $this->getkey($id);
        
        // encrypt the session data
        $data = $this->encrypt($data, $key);
        
        // get the time of birth
        $time = time();
        
        // prepare a new statement if needed
        if( $this->db->is_prepared('sess_write_stmt') == false ){
            $this->db->prepare('sess_write_stmt',
                "REPLACE INTO " . $this->db->prefix() .
                "sessions (id, set_time, data, session_key) " . 
                "VALUES (?, ?, ?, ?)"
            );
        }
        
        // bind parameters to the write statement
        // id is a string, time is number, data and key are strings
        $this->db->bind_param('sess_write_stmt','siss', 
                                $id, $time, $data, $key );
        
        // execute the prepared statement
        $this->db->prepped_execute('sess_write_stmt');
        return true;
    }
    
    
    /** Destroys a session variable called when php uses session_destroy
     *  @param $id string session id
     *  @return true
     */
    function destroy($id)
    {
        // create a delete statement if needed
        if( $this->db->is_prepared('sess_delete_stmt') == false ){
            $this->db->prepare('sess_delete_stmt',
                "DELETE FROM " . $this->db->prefix() .
                "sessions WHERE id = ?"
            );
        }
        
        // bind id param
        $this->db->bind_param('sess_delete_stmt','s',$id);
        
        // execute prepared delete
        $this->db->prepped_execute('sess_delete_stmt');
        
        return true;
    }
    
    /** Garbage collection function is used by php to remove old session
     *  items that have exceeded their life span
     *  @param max the lifespan of the seesion item
     *  @return true
     */
    function gc( $max )
    {
        // create a new garbage collection statement if needed
        if( $this->db->is_prepared('sess_garbage_stmt') == false ){
            $this->db->prepare('sess_garbage_stmt',
                "DELETE FROM " . $this->db->prefix() . 
                "sessions WHERE set_time < ?"
            );
        }
        
        // calculate time until expires
        $old = time() - $max;
        
        // bind the old time to the statement
        $this->db->bind_param('sess_garbage_stmt','s',$old);
        
        // execute statemnt
        $this->db->prepped_execute('sess_garbage_stmt');
        return true;
    }
    
    /**Get key function gets the session key value from the database
     * based on the session id
     * @param $id string session id
     * @return the random key value associated with this id
     */
    function getkey( $id )
    {
        // generate a new get key statement as needed
        if( $this->db->is_prepard('sess_getkey_stmt') == false ){
            $this->db->prepare('sess_getkey_stmt',
                "SELECT session_key FROM " . $this->db->prefix() . 
                "sessions WHERE id = ? LIMIT 1"
            );
        }
        
        // bind id to statement
        $this->db->bind_param('sess_getkey_stmt',$id);
        
        // get the statement and work myself from here
        $stmt = $this->db->prepped_statement('sess_garbage_stmt',
                                             's', $old);
        
        // get the results of this statement
        $result = $this->db->prepped_result('sess_garbage_stmt');
        
        // check if a record was returned
        if( $result->num_rows == 1 ){
            // if so get the key from the database
            $key = $result->fetch_assoc();
            $key = $key["session_key"];
            
            // free the result
            $result->close();
            
            return $key;
        } else {
            // free result
            $result->close();
            
            // otherwise generate a new one
            $random_key = hash('sha512', 
                uniqid(mt_rand(1, mt_getrandmax()), true)
            );
            
            return $random_key;
        }
    }
    
    /** Encrypt data using key Copied directly from the internet.
     *  @param $data data to encrypt
     *  @param $key key to use for encrypting 
     *  @return encrypted data
     */
    private function encrypt($data, $key) {
        $key = substr(
            hash('sha256', $this->salt.$key.$this->salt), 0, 32
        
        );
        
        $iv_size = mcrypt_get_iv_size(
            MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB
        );
        
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $encrypted = base64_encode(
            mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 
                $key, 
                $data, 
                MCRYPT_MODE_ECB, 
                $iv
            )
        );
        
        return $encrypted;
    }
    
    /** Decrypt data using key. Copied from the internet
     *  @param $data encryptd data to decrypt
     *  @param $key key to use for decryption
     *  @return decrypted data value 
     */
    private function decrypt($data, $key) {
        $key = substr(
            hash('sha256', $this->salt.$key.$this->salt), 0, 32
        );
        
        $iv_size = mcrypt_get_iv_size(
            MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB
        );
        
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypted = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_256, 
            $key, 
            base64_decode($data), 
            MCRYPT_MODE_ECB, 
            $iv
        );
        
        return $decrypted;
    }
    
    
}

?>
