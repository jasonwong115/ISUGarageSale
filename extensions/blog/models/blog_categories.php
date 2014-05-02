<?php
/** extensions/forum/models/blog_categories.php
 *  The posts model updates and retrieves data on categories for the 
 *  blog extension.
 */
class blog_categories_model extends GarageSale\Model {
    
    
    /** Adds a new category to the database
     *  @param array $values associative data with key values matching 
     *         the columns of the blog_categories table of teh database.
     *  @return bool true on success false on failure
     */
    function new_item( $values )
    {
        // insert into the forum boards
        $this->stmt = $this->db->insert('blog_categories');
        
        // set integers as needed
        $values['category_order'] = (int) $values['category_order'];
        $values['date_created'] = null;
        $values['status'] = (int)GarageSale\BaseDatabase::STATUS_ACTIVE;
        
        // make values
        $update_values = $this->make_values( $values );
        
        // set values
        $this->stmt->values( $update_values );
        
        // attempt insert
        return parent::set();
    }
    
    
    /** Gets all the categories
     *  @return array list of categories
     */
	function get_all( $thread_id )
    {
        // select groups
        $this->stmt = $this->db->select('blog_categories');
        $this->stmt->where('status','i', 
            GarageSale\BaseDatabase::STATUS_ACTIVE )->
            order('category_order', GarageSale\MySQLStatement::ASC);
        
        
        return parent::get();
    }
    
    
    /** Get a single category from the database
     *  @param int $category_id Id of the category to fetch
     *  @return array single row of the result from the database or null
     *          if no results were found.
     */
    function get_item( $category_id )
    {
        $this->stmt = $this->db->select('blog_categories');
        
        // where
        $this->stmt->where('id','i',$category_id);
        
        // limit to 1
        $this->stmt->limit(1);
    
        $cat = parent::get();
        
        return (count($cat) > 0) $cat[0] : null;
    }
    
    /** Gets the very last item inserted into database
     *  @return array 1 row from the database
     */
    function last_post()
    {
        $this->stmt = $this->db->select('blog_categories');
        
        // get it
        $this->stmt->limit(1)->order('id',
            GarageSale\MySQLStatement::DESC);
        
        // return it
        $post = parent::get();
        return $post[0];
    }
} 

?>
