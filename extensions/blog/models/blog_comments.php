<?php
/** extensions/forum/models/blog_comments.php
 *  The posts model updates and retrieves data on comments for the 
 *  blog extension.
 */
class blog_comments_model extends GarageSale\Model {
    
    private $table = 'blog_comments';
    
    /** Adds a new comment to the database
     *  @param int $user_id id of the posting user
     *  @param int $post_id id of the post
     *  @param array $values associative data with key values matching 
     *         the columns of the blog_comments table of teh database.
     *  @return bool true on success false on failure
     */
    function new_item( $user_id, $post_id, $values )
    {
        // insert into the forum boards
        $this->stmt = $this->db->insert($this->table);
        
        // set integers as needed
        $values['post_id'] = (int)$post_id;
        $values['user_id'] = (int)$user_id;
        $values['date_created'] = null;
        $values['status'] = (int)GarageSale\BaseDatabase::STATUS_ACTIVE;
        
        // make values
        $update_values = $this->make_values( $values );
        
        // set values
        $this->stmt->values( $update_values );
        
        // attempt insert
        return parent::set();
    }
    
    
    /** Gets all the comments on a post
     *  @param int $post_id id of the post to look for comments on
     *  @return array list of posts
     */
	function get_all()
    {
        // select groups
        $this->stmt->where('status','i', 
            GarageSale\BaseDatabase::STATUS_ACTIVE )->
            order('date_created', GarageSale\MySQLStatement::DESC);
        
        
        return parent::get();
    }
    
    
    /** Gets all the comments on a post
     *  @param int $post_id id of the post to look for comments on
     *  @return array list of posts
     */
	function get_post( $post_id )
    {
        // select groups
        $this->stmt = $this->db->select($this->table);
        $this->stmt->where('post_id','i', $post_id );
        
        
        return $this->get_all();
    }
    
    /** Counts the number of posts in the database
     *  @param int $post_id id of the post to count comments for
     *  @return int the number of posts in the databse
     */
    function count( $post_id )
    {
        $this->stmt = $this->db->count($this->table);
        $this->stmt->where('post_id','i',$post_id);
        
        $this->limit(-1)->page(-1);
        
        // count
        $count = $this->get_all();
        
        // get value
        return (int)$count[0]['id'];
    }
    
    /** Get a single comment from the database
     *  @param int $comment_id Id of the post to fetch
     *  @return array single row of the result from the database or null
     *          if no results were found.
     */
    function get_item( $comment_id )
    {
        $this->stmt = $this->db->select($this->table);
        
        // where
        $this->stmt->where('id','i',$comment_id);
        
        // limit to 1
        $this->stmt->limit(1);
    
        $post = parent::get();
        
        return (count($cat) > 0) ? $post[0] : null;
    }
    
    /** Gets the very last item inserted into database
     *  @return array 1 row from the database
     */
    function last_post()
    {
        $this->stmt = $this->db->select($this->table);
        
        // get it
        $this->stmt->limit(1)->order('id',
            GarageSale\MySQLStatement::DESC);
        
        // return it
        $post = parent::get();
        return $post[0];
    }
} 

?>
