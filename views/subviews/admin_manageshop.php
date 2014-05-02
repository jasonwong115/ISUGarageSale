
    <!-- A small content widget -->
    <div class="admin-content-widget widget-small">
        <h4>Update Categories</h4>
        
        <!-- Category update form -->
            
            <!-- Make it a list -->
            <ul>
            <!-- Display categories -->
            <?php
            
            
            // start a new queue
            $stack = array();
            
            // foreach category in the category list
            foreach( $categories as $category ){
                // push in the queue
                $stack[] = $category;
            }
            
            // go through queue
            while( count( $stack ) > 0 ){
                
                // next category
                $next = array_pop( $stack );
                
                // test for end of sublist
                if( $next == null ){
                    echo "</ul></li>\n";
                    
                    // move on
                    continue;
                }
                
                // otherwise output category
                echo '<li><a href="'. $self_link .'updatecategories'.
                    '?c='. $next['id'] .'">'.
                    $next['display_name']."</a>\n";
                
                // add children if need be
                if( $next['child_item'] != null ){
                    
                    // output list start
                    echo "<ul>\n";
                    // add null terminator
                    $stack[] = null;
                    
                    // add every child item
                    foreach( $next['child_item'] as $child ){
                        $stack[] = $child;
                    }
                    
                }
            }
            
            ?>
        </ul>
        
    </div>
    
    <!-- A small content widget -->
    <div class="admin-content-widget widget-small">
        
        <?php
        
        // messages
        if( isset($update_message) ){
            echo "<div>$update_message</div>";
        }
        
        // if we have info to update
        if( isset( $category_info ) ) {
            
            // display form
        ?>
        
        <h4>Edit Category</h4>
        <!-- Update categories form -->
        <form method="post" 
            action="<?php echo $self_link . 'updatecategories' . 
                '?c=' . $_GET['c'] ; ?>">
        
            <!-- Category Display name -->
            <strong>Display Name</strong>
            <br />
            <input type="text" name="display_name"
                value="<?php echo $display_name_value; ?>" 
                />
            <br />
            
            
            <!-- Category slug name -->
            <strong>Category Slug</strong>
            <br />
            <input type="text" name="name"
                value="<?php echo $name_value; ?>"/>
            <br />
            
            
            <!-- Description -->
            <strong>Description</strong>
            <br />
            <textarea type="text" name="description"
                ><?php echo $description_value; ?></textarea>
            <br />
            
            
            <!-- Category order -->
            <strong>Category Order</strong>
            <br />
            <input type="text" name="category_order"
                value="<?php echo $category_order_value; ?>"
                />
            <br />
            
            
            <!-- Parent id -->
            <strong>Parent Category</strong>
            <!-- Make it a list -->
            <ul>
            
            <li><input type="radio" value="0" name="parentid" />
                None
            </li>
            
            <!-- Display categories -->
            <?php
            
            
            // start a new queue
            $stack = array();
            
            // foreach category in the category list
            foreach( $categories as $category ){
                // push in the queue
                $stack[] = $category;
            }
            
            // go through queue
            while( count( $stack ) > 0 ){
                
                // next category
                $next = array_pop( $stack );
                
                // test for end of sublist
                if( $next == null ){
                    echo '</ul></li>';
                    
                    // move on
                    continue;
                }
                
                // check for if this one should be checked
                $checked = '';
                if( $next['id'] == $parentid_value ){
                    
                    // checked is true
                    $checked = ' checked ';
                }
                
                // Can't have category parent be the same as itself
                if( $next['id'] != $_GET['c'] ) {
                
                    // otherwise output category
                    echo '<li> <input type="radio" name="parentid"'.
                        'value="'.$next['id'].'"'.
                         $checked . ' />'.
                        $next['display_name'];
                }
                
                // add children if need be
                if( $next['child_item'] != null ){
                    
                    // output list start
                    echo '<ul>';
                    
                    // add null terminator
                    $stack[] = null;
                    
                    // add every child item
                    foreach( $next['child_item'] as $child ){
                        $stack[] = $child;
                    }
                    
                }
            }
            
            ?>
            </ul>
            
            <!-- Submit button -->
            <input type="submit" value="Update &raquo;" />
            
        </form>
        
        <?php
        } elseif( isset( $delete_info )  ) {
            
            // display category delete info
        ?>
            
            <h4> You are about to delete: </h4>
            
            <strong>Category Name</strong>
            <br />
            <?php echo $delete_info['display_name']; ?>
            <br />
            
            <!-- Category slug name -->
            <strong>Category Slug</strong>
            <br />
            <?php echo $delete_info['name']; ?>
            <br />
            
            
            <!-- Description -->
            <strong>Description</strong>
            <br />
            <?php echo $delete_info['name']; ?>
            <br />
        
        <?php
        } else {
        ?>
        
        
        <h4> Create Category </h4>
        
        <form method="post"
            action="<?php echo $self_link; ?>newcategory">
        
            
            <!-- Category Display name -->
            <strong>Display Name</strong>
            <br />
            <input type="text" name="display_name"
                value="<?php echo $display_name_value; ?>" />
            <br />
            
            
            <!-- Category slug name -->
            <strong>Category Slug</strong>
            <br />
            <input type="text" name="name"  
                value="<?php echo $name_value; ?>"/>
            <br />
            
            
            <!-- Description -->
            <strong>Description</strong>
            <br />
            <textarea type="text" name="description"
                ><?php echo $description_value; ?></textarea>
            <br />
            
            
            <!-- Category order -->
            <strong>Category Order</strong>
            <br />
            <input type="text" name="category_order"
                value="<?php echo $category_order_value; ?>" />
            <br />
            
            
            <!-- Parent id -->
            <strong>Parent Category</strong>
            <!-- Make it a list -->
            <ul>
            <!-- Display categories -->
            <?php
            
            
            // start a new queue
            $stack = array();
            
            // foreach category in the category list
            foreach( $categories as $category ){
                // push in the queue
                $stack[] = $category;
            }
            
            // go through queue
            while( count( $stack ) > 0 ){
                
                // next category
                $next = array_pop( $stack );
                
                // test for end of sublist
                if( $next == null ){
                    echo '</ul></li>';
                    
                    // move on
                    continue;
                }
            
                // set up checked value
                $checked = '';
                
                // check for checked
                if( $parentid_value == $next['id'] ){
                    $checked = 'checked';
                }
            
                // otherwise output category
                echo '<li> <input type="radio" name="parentid" '.
                    'value="'.$next['id'].'" '.
                    $checked . ' />'.
                    $next['display_name'];
                
                // add children if need be
                if( $next['child_item'] != null ){
                    
                    // output list start
                    echo '<ul>';
                    
                    // add null terminator
                    $stack[] = null;
                    
                    // add every child item
                    foreach( $next['child_item'] as $child ){
                        $stack[] = $child;
                    }
                }
            }
            
            ?>
            </ul>
            
        
            <input type="submit" value="Create &raquo;"  />
        
        </form>
        
        <?php  
        }
        
        ?>
        
    </div>
    
    
    <!-- Delete Category -->
    <div class="admin-content-widget widget-small">
    
        <h4>Delete Category</h4>
    
        <?php
        
        // if we have info to update
        if( isset( $category_info ) ) {
            
            // display form
        ?>
        
        <!-- Delete categories confirm link -->
        
            
        <a href="<?php echo $self_link . 'confirmdelete' .
            '?c=' . $_GET['c'] ; ?>">
            Delete this category
        </a>
        
        <?php
        } elseif( isset($delete_info) ) {
        ?>
        
        <a href="<?php echo $self_link . 'deletecategory' .
            '?c=' . $_GET['c'] ; ?>"
            >Confirm</a>
        
        <a href="<?php echo $self_link ;?>"
            >Cancel</a>
        
        <?php
        } else {
        
            // display message
            echo "Choose a category to delete";
        }
        
        ?>
    
    </div> 
    
    
