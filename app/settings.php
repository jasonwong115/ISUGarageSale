<?php
namespace GarageSale;

/** app/settings.php
 *  This class provides access to site configuration files and has
 *  methods for parsing values out of these setting files.
 *  Setting files are located in the app/settings directory and have 
 *  extension '.set'. Lines begining with ; are comments and are ignored
 *  parsable values have format key=value. 
 */
class Settings {
    
    /** the settings is an associative array of values parsed from the
     *  configuration file if it exists
     */
    private $settings = array();
    
    /** Path to the file to save / open */
    private $file_name = null;
    
    /** The Settings class constructor, opens and parses the requested
     *  configuration into the private settings table.
     *  @param string $config_file the name of the settings to load
     */
    function __construct( $config_file )
    {
        // save file name
        $this->file_name = $config_file;
        
        // check if file exists
        if( file_exists('app/settings/'.$config_file.'.set') ){
            
            // open the file for reading
            $file = fopen('app/settings/'.$config_file.'.set','r');
            
            // bail on fail
            if( !$file ){
                return;
            }
            
            // read all lines
            while(!feof($file)){
                
                // line 
                $line = trim(fgets($file));

                // check for comment
                if( strlen($line) == 0 || substr($line,0,1) == ';' ){
                    continue;
                }
                
                // get the key value pair
                $key_vals = explode('=',$line,2);
                
                // trim and save them
                $this->settings[trim($key_vals[0])] =trim($key_vals[1]);
                
            }
        }
    }
    
    
    /** Fetch a setting value from the settings table. 
     *  @param string $key The name of the setting to look up in the
     *         settings table.
     *  @return The value of the setting if available or null if not
     */
    function get( $key )
    {
        // check for key existance
        if( isset($this->settings[$key]) ){
        
            // got it, send it
            return $this->settings[$key];
        }
        
        // otherwise, nope
        return null;
    }
    
    
    /** Set a new value or overwrite an existing setting for later 
     *  writing out. 
     *  @param string $key The name of the setting to look up in the
     *         settings table.
     *  @param mixed $value The value to give to this key association
     *  @return object a reference to the same object
     */
    function set( $key, $value )
    {
        // set it up
        $this->settings[$key] = $value;
        
        return $this;
    }
    
    
    /** Writes out the settings to the appropriate settings file
     *  @param string $comment Comments to leave at the top of the page
     *         of the settings file.
     *  @return true on success false on failure
     */
    function save( $comment = '; Settings file saved' )
    {
        // set it up
        $data = $comment . "\n";
        
        // add settings values
        foreach( $this->settings as $key => $value ){
            
            // update data
            $data .= $key . '=' . $value . "\n";
        }
        
        // check for file exists
        $file= (!file_exists('app/settings/'.$this->file_name.'.set')) ?
            fopen('app/settings/'.$this->file_name.'.set','a+') :
            fopen('app/settings/'.$this->file_name.'.set','w+');
        
        // write and retrn
        return (fwrite($file,$data)) ? true : false ;
    }
    
}
?>
