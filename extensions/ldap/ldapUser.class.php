<?php
namespace GarageSale;

/** Represents a single user drawn from an LDAP directory
 */
class ldapUser {
    
    /** This particular user's information
     */
    public $info = null;
    
    
    /** Construct a new instance of the ldap user
     *  @param array $info A single row from an ldap request
     */
    function __construct( $info )
    {
        $this->info = $info;
    }
    
    /** Attempts to get a key from the information returned
     *  @param string $key The value to try and get from the user's
     *         information.
     *  @param int $offset Gets a value that is not the nth entry
     *         in the directory listing
     *  @return string value returned by the ldap request for the key
     *          or null if no key exists
     */
    function get( $key, $offset=0 )
    {
        // check for key exists
        if( array_key_exists($key, $this->info) ){
            $values = $this->info[$key];
            
            // check for key
            if( (int)$values['count'] > $offset){
                return $values[0];
            }
        }
        
        return null;
    }
    
    
    /** Attempts to get the displayname of the current user
     *  @param int $offset Gets a value that is not the nth entry
     *         in the directory listing
     *  @return string value returned by the ldap request for the key
     *          or null if no key exists 
     */
    function get_displayname( $offset=0 )
    {
        return $this->get('displayname');
    }
    
    
    /** Attempts to get the description of the current user
     *  @param int $offset Gets a value that is not the nth entry
     *         in the directory listing
     *  @return string value returned by the ldap request for the key
     *          or null if no key exists 
     */
    function get_description( $offset=0 )
    {
        return $this->get('description');
    }
    
    
    /** Attempts to get the major of the current user
     *  @param int $offset Gets a value that is not the nth entry
     *         in the directory listing
     *  @return string value returned by the ldap request for the key
     *          or null if no key exists 
     */
    function get_major( $offset=0 )
    {
        return $this->get('isupersonmajor');
    }
    
    
    /** Attempts to get the surname of the current user
     *  @param int $offset Gets a value that is not the nth entry
     *         in the directory listing
     *  @return string value returned by the ldap request for the key
     *          or null if no key exists 
     */
    function get_lastname( $offset=0 )
    {
        return $this->get('sn');
    }
    
    
    /** Attempts to get the given name of the current user
     *  @param int $offset Gets a value that is not the nth entry
     *         in the directory listing
     *  @return string value returned by the ldap request for the key
     *          or null if no key exists 
     */
    function get_givenname( $offset=0 )
    {
        return $this->get('givenname');
    }
    
    
    /** Attempts to get the middle name of the current user
     *  @param int $offset Gets a value that is not the nth entry
     *         in the directory listing
     *  @return string value returned by the ldap request for the key
     *          or null if no key exists 
     */
    function get_middlename( $offset=0 )
    {
        return $this->get('isupersonmiddlename');
    }
    
    
    /** Attempts to get the title of the current user
     *  @param int $offset Gets a value that is not the nth entry
     *         in the directory listing
     *  @return string value returned by the ldap request for the key
     *          or null if no key exists 
     */
    function get_title( $offset=0 )
    {
        return $this->get('title');
    }
    
    
    /** Attempts to get the college of the current user
     *  @param int $offset Gets a value that is not the nth entry
     *         in the directory listing
     *  @return string value returned by the ldap request for the key
     *          or null if no key exists 
     */
    function get_college( $offset=0 )
    {
        return $this->get('isupersoncollege');
    }
    
    
    /** Attempts to get the status of the current user
     *  @param int $offset Gets a value that is not the nth entry
     *         in the directory listing
     *  @return string value returned by the ldap request for the key
     *          or null if no key exists 
     */
    function get_status( $offset=0 )
    {
        return $this->get('isupersonstatus');
    }
    
    
    /** Attempts to get the user class (i.e. student/staff) of the user
     *  @param int $offset Gets a value that is not the nth entry
     *         in the directory listing
     *  @return string value returned by the ldap request for the key
     *          or null if no key exists 
     */
    function get_userclass( $offset=0 )
    {
        return $this->get('userclass');
    }
    
    
    /** Attempts to get the email of the user
     *  @param int $offset Gets a value that is not the nth entry
     *         in the directory listing
     *  @return string value returned by the ldap request for the key
     *          or null if no key exists 
     */
    function get_email( $offset=0 )
    {
        return $this->get('mail');
    }
}
?>
