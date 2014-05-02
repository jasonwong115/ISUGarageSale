<?php
namespace GarageSale;

// requre statement class
require_once( 'app/statement.php' );
// requre database class
require_once( 'app/database.php' );

/** app/model.php
 *  This class provides the base class for creating a model of data
 *  access.
 *  In order to user you simply need to invoke it from the app with
 *  the specified model in the models directory. I.e:
 *
 *  $model = $app->model('listings');
 *
 *  Will load and return teh model in models/listings.php. The model in 
 *  this file must be named 'listings_model' for this to function 
 *  properly.
 */
class Model {
    
    /** object The database instance to use for drawing data records */
    protected $db = null;
    
    
    /**string The name of the table to associate with this model */
    private $table = '';
    
    /** int Saves which page to grab from the database */
    private $page = 0;
    
    /** int limits the number of records from the database */
    private $limit = -1;
    
    /** object The current working statement for the model */
    protected $stmt = null; 
    
    /** Constructs a new instance of the model
     *  @param string $table The name of the table to associate with
     *         this model
     *  @param object $database The database instance to use for drawing
     *         data.
     */
    function __construct( $table, $database )
    {
        $this->table = $table;
        // save database
        $this->db = $database;
        
        // default statement to select
        $this->stmt = $this->db->select($this->table);
    }
    
    /** Gets the most fundamental values from the model, i.e all columns
     *  and as many rows as the implementation deems appropriate
     *  @return an associative array of results from the database
     */
    function get()
    {
    	// do pre run stuff
    	$this->prerun();
        return $this->db->statement_result($this->stmt);
    }
    
    
    /** Executes the current model statement.
     *  @return true on success false on failure
     */
    function set()
    {
    	//$this->prerun();// don't do by default
        return $this->db->statement_execute($this->stmt);
    }
    
    /** Prepares the statement to run by setting important pre run
     *  information like the limit.
     *  @return object The instance of this model 
     */
    function prerun()
    {
    	// do the pre run if the limit has been set only
    	if( $this->limit != -1 ){
        	$this->stmt->limit( $this->limit, $this->limit*$this->page);
        }
        return $this;
    }
    
    /** Saves which page to grab from the database
     *  @param int $page The page to grab from the database
     *  @return object instance of the same object 
     */
    function page($page)
    {
        $this->page = $page;
        return $this;
    }
    
    /** limits the number of records from the database
     *  @param int $limit The limit on records to grab from database
     *  @return object instance of the same object 
     */
    function limit( $limit )
    {
        $this->limit = $limit;
        return $this;
    }
    
    
    /** Makes the values array for inserts and updates
     *  @param array $values The values to update in the database
     *  @return array formatted array ready for execution
     */
    protected function make_values( $values )
    {
        
        // this array holds the values of everything to update
        $update_values = array();
    
        // loop through each item in post values and set up array
        foreach( $values as $name => $value ){
            
            // check for image paths
            if( substr($name,0,11) === 'image_file_' || 
                $name === 'submit'
            ){
                continue;
            }
        
            // defaul type to string
            $type = 's';
        
            // check for type of int
            if( is_int($value) ){
                $type = 'i';
            }
                        
            $update_values[] = array(
                'name' => $name,
                'value'=> $value,
                'type' => $type
            );
        }
        
        return $update_values;
    }
}
?>
