<?php
/** extendsions/blog/controller.php
 *  The blog class is an extension for the GarageSale core application.
 *  It can be turned on and off via the manifest file, someday through
 *  the admin panel.
 */
class blog extends GarageSale\Controller {
    
    /** Index is the entry point for the blog. It simply shows the most
     *  recent posts and can scroll by page.
     *  @param array $args The arguments passed to the controller via
     *         the url
     */
    function index( $args ){
        
        // add index view
        $this->view->add('subviews',
            array('blog/views/index'));
        
    }
    
    /** Browses all posts in the blog
     *  @param array $args Arguments from the url. Not needed.
     */
    function browse( $args )
    {
        
        // this page
        $this->view->add('page_title','Updates from Our Team');
        
        // start the model
        $posts_model = $this->app->model('blog_posts','blog/models');
        
        // use hard limit for now
        $limit = 5;
        $page = 0;
        
        // get the page
        if( isset($_GET['p']) ){
            $page = ((int)$_GET['p']) - 1;
        }
        
        // get all
        $result = $posts_model->limit($limit)->page($page)->get_all();
        
        // add results to page
        $this->view->add('result',$result);
        
        // add count for paging
        $count = $posts_model->count();
        
        // save paging info
        $paging = array(
            'pages' => ($count==0) ? 0 : ceil($count/$limit),
            'current' => ($count==0) ? 0 : $page+1
        );
        
        // add to page
        $this->view->add('paging',$paging);
        
        // set up self link
        $this->view->add('self_link',
            $this->app->form_path('blog/browse')
        );
        
    } 
    
    /** Looks in the archive for a posts based on the date/title
     *  @param array $args The arguements from the url. Looks at 
     *         'year', 'month', 'day', 'post'
     */
    function archive( $args )
    {
        
        // this page
        $this->view->add('page_title','Archived');
        
        // start the model
        $posts_model = $this->app->model('blog_posts','blog/models');
        
        // comments model
        $comments_model = $this->app->
            model('blog_comments','blog/models');
        
        // get all
        $result = $posts_model->get_archived(
            $args['post'],
            $args['year'],
            $args['month'],
            $args['day']);
        
        // next and previous links
        $next_link = null;
        $prev_link = null;
        
        //
        if( count($result) > 0 ){
            
            // add page title
            $this->view->add('page_title', $result[0]['name']);
            
            $next = $posts_model->next_post($result[0]['date_created']);
            $prev = $posts_model->prev_post($result[0]['date_created']);
            
            // get extension
            $blog = $this->app->extension('blog');
            
            // if next is there
            if( $next != null ){
                
                // generate the next item link
                $next_link = $blog->archive_link( $next );
            }
            
            
            // if prev is there
            if( $prev != null ){
                
                // generate the next item link
                $prev_link = $blog->archive_link( $prev );
            }
            
        }
        
        // add next and prev
        $this->view->add('next_post',$next_link);
        $this->view->add('prev_post',$prev_link);
        
        // add results to page
        $this->view->add('result',$result);
        
        
        
        // add comments form
        $comment_form = $this->view->form(
            'blog/forms/blog_comment',
            'blog/archive/'.$args['year'].
            '/'.$args['month'].'/'.$args['day'].'/'.$args['post']
        );
        
        
        
        // create action message
        $action_message = null;
        
        // do comment actions
        if( $this->app->user->is_logged_in() &&
            count( $result ) > 0 &&
            $_SERVER['REQUEST_METHOD'] == 'POST' && 
            $comment_form->validate( $action_message )
        ){
            $success = $comments_model->
                new_item( 
                    $this->app->user->get_user_id(), 
                    $result[0]['id'], 
                    $_POST );
                
            // check for success
            if( !$success ) {
                $action_message = "We encountered an error adding
                    your comment to our database.";
            } else {
                
                $action_message = "Comment successful";
                
                
                // reset the form
                $comment_form->reset()->create();
            }
        }
        
        // add it
        $this->view->add('comment_form',$comment_form);
        
        // add action message
        $this->view->add('action_message',$action_message);
        
        // get comments page
        $page = 0;
        if( isset($_GET['cp']) ){
            $page = ((int)$_GET['cp']) - 1;
        }
        
        // hard limit
        $limit = 10;
        
        // get comments result
        $comment_result = (count($result) > 0) ? 
            $comments_model->limit($limit)->page($page)->
                get_post((int)$result[0]['id']) :
            array();
        
        // add comment result
        $this->view->add('comment_result', $comment_result);
        
        // get count
        $comment_count = (count($result) > 0) ? 
            $comments_model->count((int)$result[0]['id']) :
            0;
            
        
        // do comments paging here
        $this->view->add('comment_paging',array(
            'pages'   => ((int)ceil($comment_count/$limit)),
            'current' => $page+1
        ));
        
        // comment page link
        $page_link = $this->app->form_path(
            'blog/archive/'.$args['year'].
            '/'.$args['month'].'/'.$args['day'].
            '/'.$args['post'].'?cp='
        );
        
        $this->view->add('page_link',$page_link);
        
        // load wysiwyg
        $this->app->extension('wysiwyg');
    }
    
    /** Add a new post to the blog
     *  @param $args Arguments from the url. This action should not be
     *         concerned with any arguments
     */
    function newpost( $args ){
        
        // page title
        $this->view->add('page_title','New Blog Post');
        
        // make sure user is logged in
        if( !$this->app->user->is_logged_in() ){
            $this->app->redirect('user/login');
        }
        
        // must be admin
        if(  
            $this->app->user->get_user_level() < 
                GarageSale\User::USER_ADMIN  
        ){
            $this->view->add('err_msg','You do not have sufficient
            privalages to view this page');
            $this->view->add('subviews',array('err_msg'));
            return;
        }
        
        // default values
        $action_message = null;
        
        // get form
        $post_form = $this->view->
            form('blog/forms/blog_post','blog/newpost');
        
        
        // add to page
        $this->view->add('post_form',$post_form);
        
        // check for a post action
        if( $_SERVER['REQUEST_METHOD'] == 'POST' &&
            $post_form->validate( $action_message ) 
        ){
             
            
            // get posts model
            $posts_model = $this->app->
                model('blog_posts','blog/models');
            
            
            // enter post
            $success = $posts_model->
                new_item( $this->app->user->get_user_id(), $_POST );
            
            // check success
            if( $success ){
                // look for most recent
                $last = $posts_model->last_item();
                
                // chek validity
                if( count($last) > 0 ){
                    
                    // get extension
                    $blog = $this->app->extension('blog');
                    
                    // redirect to new post
                    $this->app->redirect(
                        $blog->simple_link($last)
                    );
                }else{
                
                    // something odd happened. Safe redirect 
                    $this->app->redirect('blog');
                }
            }
            
            $action_message = 'An error occured entering your data int
                the database.';
        }
        
        // load wysiwyg editor
        $wysiwyg = $this->app->extension('wysiwyg');
        
        // add action message
        $this->view->add('action_message',$action_message);
        
        
    }
    
}
?>
