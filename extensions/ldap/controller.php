<?php
/** extendsions/ldap/controller.php
 *  Used for testing the ldap functions
 */
class ldap extends GarageSale\Controller {
    
    /** Index is the entry point for the blog. It simply shows the most
     *  recent posts and can scroll by page.
     *  @param array $args The arguments passed to the controller via
     *         the url. This contains $args['name'] only.
     */
    function test( $args ){
        
        $ldap = $this->app->extension('ldap');
        
        $this->view->add('user_args',$ldap->get_user($args['name']));
        
        /*
        $stmt = $this->app->db->select('reviews');
        $stmt->average('rating','rating_average')->
            where('userid','i',1)->where('status','i',
                GarageSale\BaseDatabase::STATUS_ACTIVE)->
            limit(1);
        
        echo $stmt->get_query();
        $this->view->add('result',$this->app->db->
            statement_result($stmt));*/
    }
    
}
?>
