<?php
/** extensions/sidebar/extension.php
 *  Adds cool features for sidebars
 */
class sidebar_extension extends GarageSale\Extension {
    
    
    /** Acts as a constructor for the extension and is used to set up
     *  things like scripts and styles
     */
    function load()
    {
        
    }
    
    
    /** Renders the comments to the page based on the view
     *  @param int $limit number of comments to get
     */
    function render_recent_comments( $limit = 5 )
    {
        $result = $this->recent_comments( $limit );
        
        // get listings model for lookups
        $listings_model = $this->app->model('listings');
        
        // array of found items
        $listings_found = array();
        
        include( 'views/display_comments.php');
    }
    
    /** Get recent comments on items in the Garage
     *  @param int $limit number of comments to get
     *  @return array A number of rows of comments
     */
    function recent_comments( $limit = 5 )
    {
        $model = $this->app->model('comments');
        return $model->limit($limit)->page(0)->
            get_all( GarageSale\MySQLStatement::DESC );
    }
    
    
    /** Renders the reviews to the page based on the view
     *  @param int $limit number of reviews to get
     */
    function render_recent_reviews( $limit = 5 )
    {
        $result = $this->recent_reviews( $limit );
        
        // get listings model for lookups
        $listings_model = $this->app->model('listings');
        
        // array of found items
        $listings_found = array();
        
        include( 'views/display_reviews.php');
    }
    
    /** Get recent reviews on items in the Garage
     *  @param int $limit number of reviews to get
     *  @return array A number of rows of reviews
     */
    function recent_reviews( $limit = 5 )
    {
        $model = $this->app->model('reviews');
        return $model->limit($limit)->page(0)->
            get_all( GarageSale\MySQLStatement::DESC );
    }
    
    
}
?>
