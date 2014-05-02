<?php
namespace GarageSale;
/** app/statement.php
 *  This is a helpful builder of database query statements. It builds
 *  prepared statements for the database to execute.
 */
class MySQLStatement {
    
    /** int Constant for indicating an insert query operation */
    const INSERT = 0; //'INSERT';
    /** int Constant for indicating a  select query operation */
    const SELECT = 1; // 'SELECT';
    /** int Constant for indicating an update query operation */
    const UPDATE = 2; // 'UPDATE';
    /** int Constant for indicating a  delete query operation */
    const DELETE = 3; // 'DELETE';
    /** int Constant for indicating a  COUNT query operation */
    const COUNT  = 4; // 'COUNT';
    
    /** array which facilitates selection of query match options 
     *  values correspond to constants above.
     */
    public static $QUERYTYPE = array (
        0 => 'INSERT',
        1 => 'SELECT',
        2 => 'UPDATE',
        3 => 'DELETE',
        4 => 'COUNT'
    );
    
    /** string Constant for indicating order ascending */
    const ASC  = 'ASC';
    /** string Constant for indicating order descending */
    const DESC = 'DESC';
    
    /** Defines the type of statement this will be, i.e. a SELECT,
     *  UPDATE, DELETE, etc. 
     */
    private $statement_type;
    
    /** string The table prefix to use when generating queries */
    private $table_prefix;
    
    /** string The table to run the query on */
    private $statement_table;
    
    /** int The limit to the number of records to read from the database 
     */
    private $statement_limit = -1;
    
    /** int The offset point for reading records from the database */
    private $statement_offset = -1;
    
    /** string Field to order the results by */
    private $order_fields;
    
    /** string direction to order the resutls, use constants ASC or 
     *  DESC 
     */
    private $order_directions;
    
    /** array of parameters to be used when generating the WHERE part
     *  of the statement. each row contains field name, field type, and
     *  value to bind, as well as comparison expression operator (=,<,>)
     */  
    private $where_params;
    
	/** array containing the paramters for binding fields. Behaves like
	 *  the where params but there is no need to specify expressions.
	 */
	private $field_params;
        
    /** array The associations to make between fields of joined 
     *  tables 
     */
    private $inner_join_links;
	
    /** array specifying which fields to affect in the database
	 */
    private $field_names;

    /** array containing the values to set on an insert or update 
	 */
    private $field_values;

    /** array containing the values that should be distinct in a query
	 */
    private $distinct_fields;

    /** array containing the columns to group together and return rows
     *  with unique values in these columns
	 */
    private $grouped_fields;
        
    /** array containing the as associations for aliases/inner joins
	 */
    private $as_associations;
        
    /** array statements to union in this instance */
    private $unions;
    
    /** column to compute an average for
    private $average;
        
    /** Constructor for the statement class
     *  @param int $statement_type type of this statement. 
     *         Should be INSERT, SELECT, UPDATE, DELETE, COUNT, etc
     *  @param string $statement_table Table in database to query
     *  @param string $table_prefix prefix to use in front of table name
     */
    function __construct( $statement_type, 
        $statement_table, 
        $table_prefix = '' 
    ){
        // initialize inputs
        $this->statement_type = $statement_type;
        $this->statement_table = $statement_table;
        $this->table_prefix = $table_prefix;
        
        // initialize defaults
        $this->statement_limit = -1;
        $this->statement_offset = -1;
        $this->where_params = array();
        $this->field_params = array();
        $this->field_names = array();
        $this->inner_join_links = array();
        $this->order_fields = array();
        $this->order_directions = array();
        $this->distinct_fields = array();
        $this->grouped_fields = array();
        $this->as_associations = array();
        $this->unions = array();
    }
    

    /** Sets the values/field names to use for an insert
     *  @param array $values an array containing a set of value to 
     *         update/set
	 *         Each entry should have the following format:
	 *         array( 'name' => fieldname|null, 
	 *                'value' => $value, 
	 *                'type'=> $type,
     *                'table' => $table );
     *  @return class a reference to the same object
     */
    function values( $values )
    {
        // make sure there is actually something in here
        if( count($values) == 0 ){
            return $this;
        }

        // not a lot of error checking here, quite a bit of trust
        $this->field_params = array_merge($this->field_params, $values);

        // return self reference
        return $this;
    }

    /** Add a value to the array of values to use to build this thing
     *  @param mixed $value the value to add/alter to the table
	 *  @param string $type the type of input it is: i s d b
     *  @param string $field the name of the field to associate the 
     *         input value with
     *  @return class a self reference if successful, null if not
     */
    function value( $value, $type, $field = null, $table = null )
    {
        // next id in field_params
        $count = count($this->field_params);
        
        // this is really all there is too it.
        $this->field_params[$count] = array(
            'name' => $field,
            'value' => $value,
            'type' => $type,
            'table' => $table
        );

        // return self reference
        return $this;
    }
    
    
    /** Explicitly sets field values for select statements
     *  @param array $fields Array of a list of fields to add to the 
     *         field set
     *  @param string $table specify the table to assoc with this set
     *  @return class a reference to this object instance
     */
    function fields( $fields, $table = null )
    {
        // merge the arrays
        foreach( $fields as $f ){
            $this->field( $f, $table );
        }
        
        // return self
        return $this;
    }
    
    /** Explicitly sets field value for select statements
     *  @param string $fields A field name to add to the set
     *  @param string $table specify the table to add to the set
     *  @return class a reference to this object instance
     */
    function field( $field, $table = null )
    {
        // get the size of file names
        $count = count($this->field_names);
        
        // add field
        $this->field_names[$count] = array(
            'field' => $field,
            'table' => $table
        );
        
        // return self
        return $this;
    }
    
    /** Explicitly sets field value for select statements and computes
     *  it as an average
     *  @param string $fields A field name to add to the set
     *  @param string $table specify the table to add to the set
     *  @return class a reference to this object instance
     */
    function average( $field, $alias = null, $table = null )
    {
        // check for alias
        if( $alias != null ){
            
            // add field
            $this->as_associations[] = array(
                'field' => $field,
                'table' => ( $table == null ) ? 
                    $this->statement_table : $table,
                'name' => $alias,
                'funct' => 'AVG'
            );
            
        } else {
        
            // add field
            $this->field_names[] = array(
                'field' => $field,
                'table' => ( $table == null ) ? 
                    $this->statement_table : $table,
                'funct' => 'AVG'
            );
        }
        
        // return self
        return $this;
    }
    
    
    
    /** Sets a new where parameter with the given expression.
     *  @param string $name The name of the table field
     *  @param string $type The type of the table field: s, i, d, b
     *  @param string $value The value to match in the where expression
     *  @param string $exp comparator to use in the expression
     *  @param string $method Type of comparison to do : 'AND' or 'OR'
     *  @param string $table specify a table in the database
     *  @param string $group Open or close grouping of parameters. 
     *         To indicate an opening of a group set group to '(' to
     *         close the group set $group to ')'
     *  @return A reference to the same object
     */
    function where_exp($name, $type, $value, $exp, $method = 'AND', 
        $table = null, $group = null
    ) {
        $size = count($this->where_params);
        $this->where_params[$size] = array(
            'name'   => $name,
            'type'   => $type,
            'value'  => $value,
            'expr'   => $exp,
            'method' => $method,
            'group'  => $group,
            'table'  => $table
        );
        
        return $this;
    }
    
    
    /** Clears all where params
     *  @return object A reference to the same object
     */
    function where_clear_all( )
    {
        unset($this->where_params);
        $this->where_params = array();
        return $this;
    }
    
    
    /** Clears the value of a where param to allow for multiple uses of
     *  the same statement instance
     *  @param string $name The name of the table field to clear
     *  @return object A reference to the same object
     */
    function where_clear( $name )
    {
        // find the object
        $count = $count( $this->where_params );
        for( $i=0; $i < $count; $i++ ){
        
            // found
            if( $this->where_params[$i]['name'] === $name ){
            
                // splice it out
                array_splice( $this->where_params, $i );
                return $this;
            }
        }
        return $this;
    }
    
    /** Sets a new where parameter using an '=' expression to compare
     *  @param string $name The name of the table field
     *  @param string $type The type of the table field: s, i, d, b
     *  @param string $value The value to match in the where expression
     *  @param string $method Type of comparison to do : 'AND' or 'OR'
     *  @param string $table specify a table in the database
     *  @param string $group Open or close grouping of parameters. 
     *         To indicate an opening of a group set group to '(' to
     *         close the group set $group to ')'
     *  @return A reference to the same object
     */
    function where($name, $type, $value, $method = 'AND', 
    	$table = null, $group = null
    ){
        return $this->
        	where_exp($name,$type,$value,'=',$method,$table,$group);
    }
    
    /** Sets a new where parameter using an '>' expression to compare
     *  @param string $name The name of the table field
     *  @param string $type The type of the table field: s, i, d, b
     *  @param string $value The value to match in the where expression
     *  @param string $method Type of comparison to do : 'AND' or 'OR'
     *  @param string $table specify a table in the database
     *  @param string $group Open or close grouping of parameters. 
     *         To indicate an opening of a group set group to '(' to
     *         close the group set $group to ')'
     *  @return A reference to the same object
     */
    function where_gt( $name, $type, $value, $method = 'AND', 
        $table = null, $group = null
    ){
        return $this->
        	where_exp($name,$type,$value,'>',$method,$table,$group);
    }
    
    /** Sets a new where parameter using an '>=' expression to compare
     *  @param string $name The name of the table field
     *  @param string $type The type of the table field: s, i, d, b
     *  @param string $value The value to match in the where expression
     *  @param string $method Type of comparison to do : 'AND' or 'OR'
     *  @param string $table specify a table in the database
     *  @param string $group Open or close grouping of parameters. 
     *         To indicate an opening of a group set group to '(' to
     *         close the group set $group to ')'
     *  @return A reference to the same object
     */
    function where_gte( $name, $type, $value, $method = 'AND', 
        $table = null, $group = null 
    ){
        return $this->
        	where_exp($name,$type,$value,'>=',$method,$table,$group);
    }
    
    
    /** Sets a new where parameter using an '<' expression to compare
     *  @param string $name The name of the table field
     *  @param string $type The type of the table field: s, i, d, b
     *  @param string $value The value to match in the where expression
     *  @param string $method Type of comparison to do : 'AND' or 'OR'
     *  @param string $table specify a table in the database
     *  @param string $group Open or close grouping of parameters. 
     *         To indicate an opening of a group set group to '(' to
     *         close the group set $group to ')'
     *  @return A reference to the same object
     */
    function where_lt( $name, $type, $value, $method = 'AND', 
        $table = null, $group = null 
    ){
        return $this->
        	where_exp($name,$type,$value,'<',$method,$table,$group);
    }
    
    
    /** Sets a new where parameter using an '<=' expression to compare
     *  @param string $name The name of the table field
     *  @param string $type The type of the table field: s, i, d, b
     *  @param string $value The value to match in the where expression
     *  @param string $method Type of comparison to do : 'AND' or 'OR'
     *  @param string $table specify a table in the database
     *  @param string $group Open or close grouping of parameters. 
     *         To indicate an opening of a group set group to '(' to
     *         close the group set $group to ')'
     *  @return A reference to the same object
     */
    function where_lte( $name, $type, $value, $method = 'AND', 
        $table = null, $group = null 
    ){
        return $this->
        	where_exp($name,$type,$value,'<=',$method,$table,$group);
    }
    
    /** Sets a new where parameter using a '!=' expression to compare
     *  @param string $name The name of the table field
     *  @param string $type The type of the table field: s, i, d, b
     *  @param string $value The value to match in the where expression
     *  @param string $method Type of comparison to do : 'AND' or 'OR'
     *  @param string $table specify a table in the database
     *  @param string $group Open or close grouping of parameters. 
     *         To indicate an opening of a group set group to '(' to
     *         close the group set $group to ')'
     *  @return A reference to the same object
     */
    function where_ne( $name, $type, $value, $method = 'AND', 
        $table = null, $group = null
    ){
        return $this->
        	where_exp($name,$type,$value,'<>',$method,$table,$group);
    }
    
    /** Sets a new where parameter using 'LIKE' pattern matching
     *  @param string $name The name of the table field
     *  @param string $type The type of the table field: s, i, d, b
     *  @param string $value The value to match in the where expression
     *  @param string $method Type of comparison to do : 'AND' or 'OR'
     *  @param string $table specify a table in the database
     *  @param string $group Open or close grouping of parameters. 
     *         To indicate an opening of a group set group to '(' to
     *         close the group set $group to ')'
     *  @return A reference to the same object
     */
    function where_like( $name, $type, $value, $method = 'AND', 
        $table = null, $group = null
    ) {
        return $this->where_exp(
            $name,$type,$value,'LIKE',$method,$table,$group
        );
    }
    
    /** Sets a new where parameter using 'LIKE' pattern matching
     *  @param string $name The name of the table field
     *  @param string $type The type of the table field: s, i, d, b
     *  @param string $value The value to match in the where expression
     *  @param string $method Type of comparison to do : 'AND' or 'OR'
     *  @param string $table specify a table in the database
     *  @param string $group Open or close grouping of parameters. 
     *         To indicate an opening of a group set group to '(' to
     *         close the group set $group to ')'
     *  @return A reference to the same object
     */
    function where_nlike( $name, $type, $value, $method = 'AND', 
        $table = null, $group = null
    ){
        return $this->where_exp(
            $name,$type,$value,'NOT LIKE', $method, $table, $group
        );
    }
    
    /** Sets the LIMIT to return from a query.
     *  @param $limit The limiting number of records to return
     *  @param $offset the offset to start returning records from
     *  @return A reference to itself;
     */
    function limit( $limit, $offset = 0 )
    {
        $this->statement_limit = $limit;
        $this->statement_offset = $offset;
        return $this;
    }
    
    
    /** Sets the ORDER of the results to return
     *  @param string $field The name of the field to sort by
     *  @param int $direction The direction to sort the field
     *  @return A reference to itself;
     */
    function order( $field, $direction = MySQLStatement::DESC )
    {
        $this->order_fields[] = $field;
        $this->order_directions[] = $direction;
        return $this;
    }
    
    /** Sets up the join parameters for a join operation
     *  @param array $join_array contains pertinent joining information
     *         array(
     *             'table' => $other_table,
     *             'other' => $other_field,
     *             'this'  => $this_field
     *             'source'=> $source_table  // for 'this' (optional)
     *         );
     *  @return class a reference to an instance of itself
     */
    function inner_join( $join_array )
    {
        // get next index
        $count = count( $this->inner_join_links );
        
        // set next index
        $this->inner_join_links[$count] = $join_array;
        
        return $this;
    }
    
    /** Uses another instance of a statment to add a union to the 
     *  current statement.
     *  @param object $stmt an instance of the Statement class
     *  @return object a reference to an instance of itself
     */
    function union( $stmt )
    {
        // simply add to the list
        $this->unions[] = $stmt;
        
        return $this;
    }
    
    /** Sets up aliases for columns in a given table, best used for 
     *  inner joins.
     *  @param string $table_name Name of the table to source the alias
     *         from
     *  @param string $field_name name of the column to source the alias
     *         from
     *  @param string $new_name name for the alias
     *  @return class a reference to an instance of itself
     */
    function alias( $table_name, $field_name, $new_name )
    {
        // save to table
        $this->as_associations[] = array(
            'table' => $table_name,
            'field' => $field_name,
            'name'  => $new_name
        );
        return $this;
    }
    
    
    /** Sets up distinct fields in a selection query
     *  @param string $field_name The name of the field that should be
     *         distinct in the query.
     *  @return class a reference to an instance of itself
     */
    function distinct( $field_name )
    {
        // set distinct field
        $this->distinct_fields[] = $field_name;
        
        return $this;
    }
    
    
    /** Sets up grouped fields in a selection query
     *  @param string $field_name The name of the field that should be
     *         grouped in the query.
     *  @param string $table_name The name of the table to group in
     *  @return class a reference to an instance of itself
     */
    function group( $field_name, $table_name = null )
    {
        // set up table name if needed
        if( $table_name == null ){
            $group_val = $field_name;
        } else {
            $group_val = $this->table_prefix . $table_name . '.' . 
                $field_name;
        }
    
        // set grouped field
        $this->grouped_fields[] = $group_val;
        
        return $this;
    }
    
    
    /** Generates an unprepared, unbound string to use for the query
     *  @return string a string of the statements unprepared query
     */
    function get_query()
    {
        // begin query
        $query = MySQLStatement::$QUERYTYPE[ $this->statement_type ]; 

        // start query
        if( $this->statement_type === MySQLStatement::COUNT ) {
        
            // counts are pretty quick, override original query here
            $query = "SELECT COUNT(*) AS id FROM " . 
                $this->table_prefix . $this->statement_table;
        
        }else if( $this->statement_type == MySQLStatement::SELECT ||
            $this->statement_type == MySQLStatement::DELETE
        ){

            // up distinct fields
            if( count($this->distinct_fields) > 0 ){
                $query .= ' DISTINCT ' . $this->distinct_fields[0];
                
                // now loop through the rest
                $c = count( $this->distinct_fields );
                for( $i = 1; $i < $c; $i++ ){
                    $query .= ', ' . $this->distinct_fields[$i];
                }
            }

            // see if we need to specify tables
            elseif( count($this->field_names) > 0 ){

                // add each field name to list, leave comma off last
                $count = count( $this->field_names );
                $i = 0;
                foreach( $this->field_names as $param ){

                    // do we need a comma?
                    if( $i == $count-1 ){
                        $comma = '';
                    } else {
                        $comma = ',';
                    }
                    $i++;
                    
                    // get the field name from the parameters
                    $field_name = $param['field'];
                    
                    // identify if we need to specify table
                    if( $param['table'] != null ){
                
                        // update the field name with prefix and table 
                        $field_name = $this->table_prefix . 
                            $param['table'] . '.' . 
                            $field_name;
                    }
                    
                    // check for functions
                    if( isset($param['funct']) ){
                        $field_name = $param['funct'] . '(' . 
                            $field_name . ')';
                    }

                    // finally set the field name
                    $query .= " $field_name $comma " ;
                }
            } elseif($this->statement_type != MySQLStatement::DELETE) {
                
                // use all selector
                $query .= ' * ';
            }
            
            // check for aliases
            if( count($this->as_associations) > 0 ){
                
                // loop add aliases
                foreach( $this->as_associations as $alias ){
                    
                    // compute field name
                    $field = $this->table_prefix . $alias['table'] . 
                        '.' . $alias['field'];
                    
                    // check for function
                    if( isset($alias['funct']) ){
                        $field = $alias['funct'] . '(' . $field . ')';
                    }
                    
                    // 
                    $query .= ', ' . $field . ' AS ' . $alias['name'];
                }
            } 

            // add the table selection options
            $query .= ' FROM ' . $this->table_prefix . 
                $this->statement_table;

        } elseif ($this->statement_type == MySQLStatement::INSERT) {

            // grow query to insert/ update
            $query .= ' INTO ' . $this->table_prefix . 
                $this->statement_table ;

            // count the number of params
            $count = count( $this->field_params );
            
            // see if we need to specify tables
            if( $count > 0 ){

                // add each field name to list, leave comma off last
                
                $i = 0;
                $found = false;
                foreach( $this->field_params as $param ){

                    // do we need a comma?
                    if( $i === $count-1 ){
                        $comma = '';
                    } else {
                        $comma = ',';
                    }
                    $i++;

                    
                    // if there is no field names
                    if( !isset($param['name']) || 
                        $param['name'] == NULL 
                    ){
                        // skip to the next param
                        continue;
                    }
                    
                    // formatting check
                    if( $found === false ){
                        $query .= ' ( ';
                        $found = true;
                    }
                    
                    
                    
                    // get the field name from the parameters
                    $field_name = $param['name'];
                    
                    // identify if we need to specify table
                    if( isset($param['table']) && 
                        $param['table'] != null 
                    ){
                
                        // update the field name with prefix and table
                        $field_name = $this->table_prefix . 
                            $this->field_params[$i]['table'] . '.' . 
                            $field_name;
                    }
                    
                    // finally set the field name
                    $query .= " $field_name $comma " ;
                }
                
                // formatting check
                if( $found === true ){
                    $query .= ' ) ';
                }
            }
            

            // values part prepare
            $query .= ' VALUES (';

            // add each field name to list, leave comma off last
            $count = count( $this->field_params );
            for( $i=0; $i<$count; $i++ ){

                // do we need a comma?
                if( $i == $count-1 ){
                    $comma = '';
                } else {
                    $comma = ',';
                }

                // finally set the field values
                $query .= " ? $comma " ;
            }

            // values finish
            $query .= ') ';
                
        } elseif ($this->statement_type == MySQLStatement::UPDATE) {
            
            // grow query to update
            $query .= ' ' . $this->table_prefix . 
                    $this->statement_table .
                    ' SET ';

            // add each field name to list, leave comma off last
            $count = count( $this->field_params );
            for( $i=0; $i<$count; $i++ ){

                // do we need a comma?
                if( $i == $count-1 ){
                    $comma = '';
                } else {
                    $comma = ',';
                }
                
                    
                // get the field name from the parameters
                $field_name = $this->field_params[$i]['name'];
                
                // identify if we need to specify table
                if( isset($this->field_params['table']) && 
                    $this->field_params['table'] != null 
                ){
                
                    // update the field name with the prefix and table name
                    $field_name = $this->table_prefix . 
                        $this->field_params[$i]['table'] . '.' . 
                        $field_name;
                }

                // finally set the field name
                $query .= " $field_name = ? $comma " ;
            }
            
        } else {
        
            // somthing went wrong
            return null;
        }

        
        // do inner join
        if( count($this->inner_join_links) > 0 ){
        
            // loop through entries to inner join
            foreach( $this->inner_join_links as $link ){
                
                // check for source table
                if( !isset($link['source']) ){
                    // set it for them
                    $link['source'] = $this->statement_table;
                }
                
                // grow query
                $query .= ' INNER JOIN '. 
                    $this->table_prefix . $link['table'].
                    ' ON ' . 
                        $this->table_prefix . 
                        $link['table'] .'.'. $link['other'] .
                    ' = '. 
                    $this->table_prefix . $link['source'] . '.' . 
                    $link['this'];
            
            }
        
        }
        
        
        // check if there are where params to bind
        if( count($this->where_params) !== 0 )
        {
            // start where area
            $query .= ' WHERE ';
            
            // loop through params and give names
            for( $i=0; $i<count($this->where_params); $i++ )
            {
                // if not the first one, we have to and it
                if( $i > 0 )
                {
                    // $query .= ' AND ';
                    // use the method specified
                    $query .= ' ' . $this->where_params[$i]['method'] . ' ';
                }
                
                // group this item
                if( $this->where_params[$i]['group'] == '(' ) {
                	$query .= ' ( ';
                }
                
                // idenitfy field name
                $field_name = $this->where_params[$i]['name'];
                
                // check if ned table specified
                if( isset($this->where_params[$i]['table']) &&
                    $this->where_params[$i]['table'] != null 
                ){
                    // update fild name with table specifier
                    $field_name = $this->table_prefix . 
                        $this->where_params[$i]['table'] . '.' .
                        $field_name;
                }
                
                // grow query
                $query .= ' ' . $field_name . ' ' .
                        $this->where_params[$i]['expr'] . ' ? ';
                        
                // check for ungroup
                if( $this->where_params[$i]['group'] == ')' ){
                	$query .= ')';
                }
            }
        }
        
        // group by commands
        if( count($this->grouped_fields) > 0 ){
            
            // update query
            $query .= ' GROUP BY ' . $this->grouped_fields[0];
            
            // try to inflate query
            $count = count($this->grouped_fields);
            for( $i=1; $i<$count; $i++ ){
                $query .= ', ' . $this->grouped_fields[$i];
            }
        }
        
        
        // go ahead and do the union here
        if( count($this->unions) > 0 ){
            
            // loop and update our query 
            foreach( $this->unions as $union_stmt ){
                $query .= ' UNION (' . $union_stmt->get_query() . ')';
            }
        }
        
        // check if there is an order to set
        if( count( $this->order_fields ) > 0 )
        {
            // grow the query
            $query .= ' ORDER BY ' . $this->order_fields[0] . ' ' .
                    $this->order_directions[0];
            
            // get count of order fields
            $count = count( $this->order_fields );
            
            // loop over and add others
            for( $i = 1; $i < $count; $i++ ){
                
                // add next
                $query .= ', ' . $this->order_fields[$i] . ' ' .
                    $this->order_directions[$i]; 
            }
        }
        
        // check if there is a limit to set
        if( $this->statement_limit >= 0 )
        {
            $query .= ' LIMIT ' . $this->statement_offset . ', ' .
                    $this->statement_limit;
        }
        
        
        // return the query string
        return $query;
    }
    
    /** Gets the params set by the values and where methods
     *  @return array The array of values and where parameters saved
     */
    function get_params()
    {
        $params = array_merge($this->field_params, $this->where_params);
        
        // also loop and get other params
        if( count($this->unions) > 0 ){
            
            // merge params
            foreach( $this->unions as $union_stmt ){
                $params = array_merge(
                    $params, 
                    $union_stmt->get_params()
                );
            }
        }
        
        return $params;
    }
    
    /** Gets the field params set by the values and where methods
     *  @return array The array of values and where parameters saved
     */
    function get_fieldparams()
    {
        return $this->field_params;
    }
    
    /** Gets the where params set by the values and where methods
     *  @return array The array of values and where parameters saved
     */
    function get_whereparams()
    {
        return $this->where_params;
    }
    
    /** Gets the type of the statement associated with this instance
     *  @return int constant statement type defined at initialization  
     */
    function get_type()
    {
        return $this->statement_type;
    }
    
    
    /** Gets the number of where params specified
     *  @return int number of where params in where_params array 
     */
    function count_where()
    {
        return count( $this->where_params );
    }
}
?>
