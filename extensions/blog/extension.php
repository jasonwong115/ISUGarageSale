<?php
/** extensions/blog/extension.php
 *  Extension that provides functions for displaying parts of a blog
 *  else where such as recent posts, etc. 
 */
class blog_extension extends GarageSale\Extension {
    
    /** Acts as a constructor for the extension and is used to set up
     *  things like scripts and styles
     */
    function load()
    {
        
    }
    
    /** Gets the most recent posts from the blog
     *  @param int $limit the max number of posts to retrieve
     *  @return array a list of recent posts from the blog
     */
    function recent_posts( $limit = 5 )
    {
        // grab posts model
        $posts_model = $this->app->model('blog_posts','blog/models');
        
        // get limit most recent posts
        return $posts_model->limit($limit)->page(0)->get_all();
    }
    
    /** Makes a link to a post in the archive 
     *  @param array $post A single row from the database
     *  @return string a link to the archived post
     */
    function archive_link( $post ){
        
        //  make link
        return $this->app->form_path(
            $this->simple_link( $post )
        );
    }
    
    
    /** Makes a simple (unformatted) link to a post in the archive 
     *  @param array $post A single row from the database
     *  @return string a link to the archived post
     */
    function simple_link( $post )
    {
        // convert to unix time from mysql time
        $time = strtotime($post['date_created']);
        // check for bad dates
        $time = ($time<0) ? 0 : $time;
        
        // grab y,m,d formats
        $year = date('Y',$time);
        $month = date('n',$time);
        $day = date('j',$time);
        
        //  make link
        return
            'blog/archive/'.$year.'/'.$month.'/'.
            $day.'/'.$post['slug'];
    }
    
    /** Makes a link to a post in the archive given post id 
     *  @param array $post_id Id of the post to look up
     *  @return string a link to the archived post
     */
    function post_link( $post_id )
    {
        // get model
        $model = $this->app->model('blog_posts','blog/models');
        
        // get post item
        $post = $model->get_item( $post_id );
        
        // return link
        return ($post != null ) ? 
            $this->archive_link( $post ) :
            $this->app->form_path('blog');
    }
    
    /** Gets the most recent comments from the blog
     *  @param int $limit the max number of comments to retrieve
     *  @return array a list of recent comments from the blog
     */
    function recent_comments( $limit = 5 )
    {
        // grab posts model
        $comments_model = $this->app->
            model('blog_comments','blog/models');
        
        // get limit most recent posts
        return $comments_model->limit($limit)->page(0)->get_all();
    }
}
?>
