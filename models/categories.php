<?php
/** models/categories.php
 *  The categories model is responsible for grabbing all data associated
 *  with categories available to the garage
 */
class categories_model extends GarageSale\Model {
    
    
    /** Gets all active categores from the database
     *  @return sorted and active results from the categories table
     */
    function get_all()
    {
        // order by the order asc
        $this->stmt->order( 'category_order', 
            GarageSale\MySQLStatement::DESC );
        
        // look at active entries
        $this->stmt->where( 'status', 'i', 
            GarageSale\BaseDatabase::STATUS_ACTIVE );
        
        return parent::get();
    }
    
    
    function get_category( $category_name )
    {
        // set the where for category name
        $this->stmt->where('name', 's', $category_name );
        
        // execute get
        return $this->get_all();
    }
    
    /** Gets all active categories from the database that are associated
     *  under this category
     *  @return array a hierarchial list of all the categories
     */
    function get_category_list( $category_id )
    {
        // clear everything
        $this->stmt->where_clear_all();
        
        // only look for top level categories to begin
        $this->stmt->where( 'id', 'i', $category_id );
        
        // save the results
        $result = $this->get_all();
        
        // init no child
        if( count($result) > 0 ){ 
            $result[0]['child_item'] = $this->
                get_category_children( $category_id );
                
        }
                
        return $result;
    }
    
    /** Gets all active categories from the database that are associated
     *  as children under this category recursively
     *  @return array a hierarchial list of all the categories
     */
    function get_category_children( $category_id )
    {
        /* ----------------------------------------------------------
         * We are about to get the available categories. We will need
         * to do some fancy queuing to get all the nested results.
         *
         * ----------------------------------
         * Begin Category Selection SQL Query
         */
        
        $this->stmt = $this->db->select('categories');
        
        // only look for top level categories to begin
        $this->stmt->where( 'parentid', 'i', $category_id );
        
        // save the results
        $result = $this->get_all();
        
        
        // if we have results, check for children
        if( count($result) > 0 ){
            
            // count results
            $count = count( $result );
            
            // loop over all results
            for( $i=0; $i < $count; $i++ ){
                // finally add child items recursively
                $result[$i]['child_item'] = 
                    $this->get_category_children( 
                        (int)$result[$i]['id'] 
                    );
                    
            }
        }
        
        /* -------------------------------------------------------------
         * Cool. At this point there should be a complete nested list
         * of category results from the database. Return it.
         */
         
        return $result;
    }
	/** Check if the category inputted actually exists
     *  @param string $category category to check
     *  @return the categories information
     */
	function category_exists($category){
		$this->stmt = $this->db->select('categories');
		$this->stmt->where('name', 'i', $category);
		return parent::get();
	}
    
    
} 

?>
