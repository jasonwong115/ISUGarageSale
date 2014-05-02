
    <!-- A full width content widget -->
    <div class="admin-content-widget widget-full">
        <h4>Choose theme</h4>
        <div>
            Current theme: <?php echo $current_theme; ?>
        </div>
        <ul>
        <?php
        if( count($theme_list) > 0 ){
            foreach( $theme_list as $theme )
            {
                // if theme already active
                if( $current_theme == $theme['path'] ){
                    continue;
                }
                
                echo <<< THEMELINK
                <li><a href="$self_link/usetheme?theme=${theme['path']}"
                    >${theme['name']}</a>
                </li>
THEMELINK;
            }
        }
        ?>
        </ul>
    </div>
