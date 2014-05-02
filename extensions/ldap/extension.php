<?php
require_once('ldapUser.class.php');

/** extensions/ldap/extension.php
 *  Creates an extension for the application that provides methods for 
 *  accessing an ldap server for verification of users, etc.
 */
class ldap_extension extends GarageSale\Extension {
    
    /** Instance of the ldap connection */
    private $connection = null;
    
    /** Acts as a constructor for the extension and is used to set up
     *  things like scripts and styles
     */
    function load()
    {
        // connect to directory
        $this->connection = ldap_connect('ldap.iastate.edu');
        
        // bind it
        $binding = ldap_bind($this->connection);
    }
    
    
    /** Gets the directory information on a single user
     *  @param string $username The name of the user to search for
     *  @return object a new ldapUser instance with the loaded values
     */
    function get_user( $username, $filter=array('*') )
    {
        // gets the user info
        $result = ldap_search(
            $this->connection,
            "dc=iastate,dc=edu",
            "(uid=$username)",
            $filter
        );
        
        // check for valid result
        if( !$result ){
            return null;
        }
        
        // get the entries
        $info = ldap_get_entries($this->connection, $result);
        
        // check for results
        if( $info['count'] == 0 ){
            return null;
        }
        
        // return one result
        return new GarageSale\ldapUser($info[0]);
    }
    
    
}
?>
