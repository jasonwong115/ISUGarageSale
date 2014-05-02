<?php
// uses the slider extension to print the slider here
 
$slider->display(); 
?>

<h2>Welcome</h2>

<?php if( $app->has_extension('blog') ){ ?>
<!-- Sidebar -->
<div class="sidebar_area">
    <h3 style="margin-top: 0;">
        Recent Comments</h3>

    <?php
    //
    $blog = $app->extension('blog');
    
    // get recent comments
    $recent_comments = $blog->recent_comments(10);
    
    // loop and display them
    if( count($recent_comments) == 0 ){
        echo "No recent comments";
    }
    
    // oop over all recent comments
    foreach( $recent_comments as $comment ){
        
        // get name of person
        $who = $app->user->name_from_id((int)$comment['user_id']);
        // and link
        $link = $blog->post_link( $comment['post_id'] );
        
        echo <<< COMMENT
        
        <a href="$link" style="color: inherit;">
        <strong>${comment['name']}</strong>
        <br />
        <small>by $who on ${comment['date_created']}</small>
        <br />
        ${comment['comment']}
        </a>
        <br />
        
COMMENT;
    }
    
    ?>
</div>

<div class="main_gutter">
<?php } else { ?>

<!-- Full content -->
<div class="full-content-provider">
<?php } ?>

    <p>
    Hello and welcome to the ISU Garage Sale. You can begin browsing the
    Garage by selecting one of the categories above or by performing a
    quick search in the box provided. 
    </p>
    
    <!-- Join our comm -->
    <h3> Join Our Community! </h3>
    <p>
    Want to get to know our community? Navigate to our new 
    <a href="<?php echo $app->form_path('forum'); ?>" 
        style="font-weight:bold;"
        >Message Boards</a>
    to introduce yourself and begin a discussion or check out our 
    <a href="<?php echo $app->form_path('blog'); ?>" 
        style="font-weight:bold;"
        >Team Updates</a> 
    to see what we've been working on! We're a friendly group around 
    here and we're sure you'll find plenty to talk about.
    </p>
    <p>
    Whether there is something in particular you are searching for or
    just want to talk about the CyclONE Nation, we've got a spot for you
    here.
    </p>

    <?php
    if( $app->has_extension('blog') ){

    $recent = $blog->recent_posts( 3 );
    ?>

        <h3>Recent News</h3>
        <?php 
        // check count
        if( count($recent) == 0 ){
            echo "No news available";
        }
        
        // go over each post
        foreach( $recent as $post ){
        
            $who = $app->user->name_from_id( (int)$post['user_id'] );
        
            //$post_sub = substr(strip_tags($post['post']),0,350);
            $post_sub = $post['post'];
            
            $link = $blog->archive_link( $post );
        
            echo <<< POST
            
            <h4>${post['name']}</h4>
            <small>by $who on ${post['date_created']}</small>
            <br />
            
            $post_sub
            <br />
            <a href="$link">Read more</a>
            <hr />
            
POST;
        }
    }
    ?>
    
</div>


