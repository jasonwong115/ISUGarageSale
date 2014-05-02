<?php
/** extensions/forum/models/blog_categories.php
 *  The posts model updates and retrieves data on categories for the 
 *  blog extension.
 */
class blog_posts_model extends GarageSale\Model {
    
    
    /** Adds a new post to the database
     *  @param int $user_id id of the posting user
     *  @param array $values associative data with key values matching 
     *         the columns of the blog_posts table of teh database.
     *  @return bool true on success false on failure
     */
    function new_item( $user_id, $values )
    {
        // insert into the forum boards
        $this->stmt = $this->db->insert('blog_posts');
        
        // set integers as needed
        $values['category_id'] = (int) $values['category_id'];
        $values['user_id'] = (int)$user_id;
        $values['date_created'] = null;
        $values['status'] = (int)GarageSale\BaseDatabase::STATUS_ACTIVE;
        $values['slug'] = $this->slugify($values['name']);
        
        
        // make values
        $update_values = $this->make_values( $values );
        
        // set values
        $this->stmt->values( $update_values );
        
        // attempt insert
        return parent::set();
    }
    
    
    /** Gets all the posts
     *  @return array list of posts
     */
	function get_all( )
    {
        // select groups
        $this->stmt = $this->db->select('blog_posts');
        $this->stmt->where('status','i', 
            GarageSale\BaseDatabase::STATUS_ACTIVE )->
            order('date_created', GarageSale\MySQLStatement::DESC);
        
        
        return parent::get();
    }
    
    /** Counts the number of posts in the database
     *  @return int the number of posts in the databse
     */
    function count()
    {
        $this->stmt = $this->db->count('blog_posts');
        $this->stmt->where('status','i',
            GarageSale\BaseDatabase::STATUS_ACTIVE);
        
        // count
        $count = parent::get();
        
        // get value
        return (int)$count[0]['id'];
    }
    
    /** Gets posts based on slugs and their location in the year/month/
     *  day timefram
     *  @param string $slug Slug to match
     *  @param string $year year to look in
     *  @param string $month month to look in
     *  @param string $day day to look in
     *  @return array Resulting output from database
     */
    function get_archived( $slug, $year=null, $month=null, $day=null ){
        
        $this->stmt->where( 'slug','s',$slug );
        
        // begin stuff
        $begin_year = ($year==null) ? 0 : $year;
        $begin_month = ($month==null) ? 0 : $month;
        $begin_day = ($day==null) ? 0 : $day;
        
        // end stuff
        $end_year = ($year==null) ? date("Y") : $year;
        $end_month = ($month==null) ? date("n") : $month;
        $end_day = ($day==null) ? date("j") : $day;
        
        // create datetimes (ints)
        $begin = mktime(
            0, 0, 0,
            $month, $day, $year
        );
        // make end time
        $end = mktime(
            23, 59, 59,
            $month, $day, $year
        );
        
        
        // time should be greater than the start
        $this->stmt->where_gte('date_created','s',
        
                // check for zero begin
                ( $begin == 0 ) ?
                '0000-00-00 00:00:00' : 
                date('Y-m-d H:i:s',$begin)
            );
        // and less than the end
        $this->stmt->where_lte('date_created','s',
            date('Y-m-d H:i:s',$end));
        
        
        return parent::get();
    }
    
    /** Get a single post from the database
     *  @param int $post_id Id of the post to fetch
     *  @return array single row of the result from the database or null
     *          if no results were found.
     */
    function get_item( $post_id )
    {
        $this->stmt = $this->db->select('blog_posts');
        
        // where
        $this->stmt->where('id','i',$post_id);
        
        // limit to 1
        $this->stmt->limit(1);
    
        $post = parent::get();
        
        return (count($post) > 0) ? $post[0] : null;
    }
    
    /** Gets the next post after a certain time
     *  @param string $date_from The datetime from the previous post
     *  @return array A single row or null if none found
     */
    function next_post( $date_from ){
        
        //  new select
        $this->stmt = $this->db->select('blog_posts');
        
        // only greater than and limit to 1
        $this->stmt->where_gt('date_created','s',$date_from)->limit(1);
        
        $result = parent::get();
        
        if( count($result) > 0 ){
            return $result[0];
        }
        
        return null;
    }
    
    /** Gets the previous post before a certain time
     *  @param string $date_from The datetime from the next post
     *  @return array A single row or null if none found
     */
    function prev_post( $date_from ){
        
        //  new select
        $this->stmt = $this->db->select('blog_posts');
        
        // only greater than and limit to 1
        $this->stmt->where_lt('date_created','s',$date_from)->limit(1);
        
        $result = parent::get();
        
        if( count($result) > 0 ){
            return $result[0];
        }
        
        return null;
    }
    
    
    /** Gets the very last item inserted into database
     *  @return array 1 row from the database
     */
    function last_item()
    {
        $this->stmt = $this->db->select('blog_posts');
        
        // get it
        $this->stmt->limit(1)->order('id',
            GarageSale\MySQLStatement::DESC);
        
        // return it
        $post = parent::get();
        return ( count($post) > 0 ) ? $post[0] : array();
    }
    
    /** Slugifies the post title for ease of access later
     *  @param string $text String to slugify
     *  @return string Slugified string
     */
    static public function slugify($text)
    { 
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)){
            return 'n-a';
        }

        return $text;
    }
    
} 

?>
