<?php
global $wgl_blog_atts;

// Default settings for blog item
$trim = true;
if (! $wgl_blog_atts ) {
    $opt_likes = Zikzag_Theme_Helper::get_option('blog_list_likes');
    $opt_share = Zikzag_Theme_Helper::get_option('blog_list_share');
    $opt_meta = Zikzag_Theme_Helper::get_option('blog_list_meta');
    $opt_meta_author = Zikzag_Theme_Helper::get_option('blog_list_meta_author');
    $opt_meta_comments = Zikzag_Theme_Helper::get_option('blog_list_meta_comments');
    $opt_meta_categories = Zikzag_Theme_Helper::get_option('blog_list_meta_categories');
    $opt_meta_date = Zikzag_Theme_Helper::get_option('blog_list_meta_date');
    $opt_read_more = Zikzag_Theme_Helper::get_option('blog_list_read_more');
    $opt_hide_media = Zikzag_Theme_Helper::get_option('blog_list_hide_media');
    $opt_hide_title = Zikzag_Theme_Helper::get_option('blog_list_hide_title');
    $opt_hide_content = Zikzag_Theme_Helper::get_option('blog_list_hide_content');
    $opt_letter_count = Zikzag_Theme_Helper::get_option('blog_list_letter_count');
    $opt_blog_columns = Zikzag_Theme_Helper::get_option('blog_list_columns');
    $opt_blog_columns = empty($opt_blog_columns) ? '12' : $opt_blog_columns;

    global $wp_query;
    $wgl_blog_atts = [
        'query' => $wp_query,
        'animation_class' => '',
        // General
        'blog_layout' => 'grid',
        // Content
        'blog_columns' => $opt_blog_columns,
        'hide_media' => $opt_hide_media,
        'hide_content' => $opt_hide_content,
        'hide_blog_title' => $opt_hide_title,
        'hide_postmeta' => $opt_meta,
        'meta_author' => $opt_meta_author,
        'meta_comments' => $opt_meta_comments,
        'meta_categories' => $opt_meta_categories,
        'meta_date' => $opt_meta_date,
        'hide_likes' => ! $opt_likes,
        'hide_share' => ! $opt_share,
        'read_more_hide' => $opt_read_more,
        'content_letter_count' => empty($opt_letter_count) ? '85' : $opt_letter_count,
        'crop_square_img' => 'true',
        'heading_tag' => 'h6',
        'items_load'  => 4,
        'heading_margin_bottom' => '10px',

    ];
    $trim = false;
}

extract($wgl_blog_atts);

if ($crop_square_img) {
    $image_size = 'zikzag-180-180';
} else {
     $image_size = 'full';
}

global $wgl_query_vars;
if (! empty($wgl_query_vars)) {
    $query = $wgl_query_vars;
}

$blog_styles = ''; 

$blog_attr = ! empty($blog_styles) ? ' style="'.esc_attr($blog_styles).'"' : '';

$heading_attr = isset($heading_margin_bottom) && $heading_margin_bottom != '' ? ' style="margin-bottom: '.(int) $heading_margin_bottom.'px"' : '';
while ($query->have_posts()) : $query->the_post();          

    echo '<div class="wgl_col-'.esc_attr($blog_columns).' item">';

    $single = Zikzag_SinglePost::getInstance();
    $single->set_data();
    $title = get_the_title();

    $blog_item_classes = ' format-'.$single->get_pf();
    $blog_item_classes .= ' '.$animation_class;
    $blog_item_classes .= is_sticky() ? ' sticky-post' : '';
    
    $single->set_data_image(true, $image_size,$aq_image = true);
    $has_media = $single->meta_info_render;

    if ($hide_media) {
        $has_media = false;
    }
    $blog_item_classes .= ! $has_media ? ' format-no_featured' : '';
    $meta_to_show = [
        'comments' => ! $meta_comments,
        'author' => ! $meta_author,
    ];    
    $meta_to_show_cats = [
        'category' => ! $meta_categories,
    ];
    $meta_to_show_date = [
        'date' => ! $meta_date,
    ];

    ?>
    <div class="blog-post <?php echo esc_attr($blog_item_classes); ?>"<?php echo Zikzag_Theme_Helper::render_html($blog_attr);?>>
        <div class="blog-post_wrapper clearfix">
            <?php
            // Media blog post
            if (! $hide_media ) {
                $link_feature = true;
                $single->render_featured($link_feature, $image_size, $aq_image = true);
            }
            ?>
            <div class="blog-post_content">
            <?php 
                if (! $hide_postmeta) {
                    $single->render_post_meta($meta_to_show_cats);
                } 

                // Blog Title
                if (! $hide_blog_title && ! empty($title)) :
                    echo sprintf('<%1$s class="blog-post_title"%2$s><a href="%3$s">%4$s</a></%1$s>', esc_html($heading_tag), $heading_attr, esc_url(get_permalink()), esc_html($title) );
                endif;

                // Content Blog
                if (! $hide_content ) $single->render_excerpt($content_letter_count, $trim, ! $read_more_hide, $read_more_text);
                ?>          

                <div class='blog-post_meta-desc'>  
                    <?php
                        // Read more link
                        if (! $read_more_hide && $hide_content) :
                            ?>
                            <a href="<?php echo esc_url(get_permalink()); ?>" class="button-read-more"><?php echo esc_html($read_more_text); ?></a> 
                       <?php
                        endif;

                        if (! $hide_postmeta && ! $meta_date ) {
                            $single->render_post_meta($meta_to_show_date);
                        } 

                        if (! $hide_share ||  ! $hide_likes || ! $meta_comments || ! $meta_author ) echo '<div class="post_meta-wrap">';
                        // Likes in blog
                        if (! $hide_share ||  ! $hide_likes || ! $meta_comments || ! $meta_author ) echo '<div class="blog-post_info-wrap">';

                        // Render shares
                        if (! $hide_share && function_exists('wgl_theme_helper') ) : 
                            echo wgl_theme_helper()->render_post_list_share();
                        endif;

                        if (! $hide_likes) : ?>
                                <div class="blog-post_likes-wrap">
                                    <?php
                                    if (! $hide_likes && function_exists('wgl_simple_likes')) {
                                        echo wgl_simple_likes()->likes_button( get_the_ID(), 0 );
                                    }
                                    ?>
                                </div>
                            <?php
                        endif;

                        //Post Meta render comments,author
                        if (! $hide_postmeta ) {
                            $single->render_post_meta($meta_to_show);
                        } 

                        if (! $hide_share ||  ! $hide_likes || ! $meta_comments || ! $meta_author ): ?> 
                            </div>
                            </div>
                        <?php
                        endif;

                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php

    echo '</div>';

endwhile;
wp_reset_postdata();
