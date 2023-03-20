<?php

$opt_blog_columns = Zikzag_Theme_Helper::get_option('blog_list_columns');
$opt_blog_columns = empty($opt_blog_columns) ? '12' : $opt_blog_columns;
$wgl_blog_atts = array(
    // General
    'blog_layout' => 'grid',
    'hide_likes' => 'true',
    'hide_share' => 'true',
    'blog_navigation' => 'pagination',
    'blog_navigation_align' => 'left',
    'blog_columns' => $opt_blog_columns
);

extract($wgl_blog_atts);

// Row class for grid and massonry
if ( in_array($blog_layout, array('grid', 'masonry')) ) {

    switch ( $blog_columns ) {
        case '12': $row_class = ' blog_columns-1'; break;
        case '6': $row_class = ' blog_columns-2'; break;
        case '4': $row_class = ' blog_columns-3'; break;
        case '3': $row_class = ' blog_columns-4'; break;
    }

}
$row_class .= " blog-style-standard";
// Render wraper
if (have_posts()): ?>
    <div class="blog-posts blog-posts-list">
        <?php
        echo '<div class="container-grid row '. esc_attr($row_class) .'">';
		    get_template_part('templates/post/post-standard');
    	echo '</div>';
        ?>
    </div>
<?php
endif;
