<?php

global $wgl_blog_atts;

// Default settings for blog item
$trim = true;
if (!$wgl_blog_atts) {
	$opt_letter_count = Zikzag_Theme_Helper::get_option('blog_list_letter_count');
	$opt_blog_columns = Zikzag_Theme_Helper::get_option('blog_list_columns');

	global $wp_query;
	$wgl_blog_atts = [
		'query' => $wp_query,
		// General
		'blog_layout' => 'grid',
		// Content
		'blog_columns' => empty($opt_blog_columns) ? '12' : $opt_blog_columns,
		'hide_media' => Zikzag_Theme_Helper::get_option('blog_list_hide_media'),
		'hide_content' => Zikzag_Theme_Helper::get_option('blog_list_hide_content'),
		'hide_blog_title' => Zikzag_Theme_Helper::get_option('blog_list_hide_title'),
		'hide_postmeta' => Zikzag_Theme_Helper::get_option('blog_list_meta'),
		'meta_author' => Zikzag_Theme_Helper::get_option('blog_list_meta_author'),
		'meta_comments' => Zikzag_Theme_Helper::get_option('blog_list_meta_comments'),
		'meta_categories' => Zikzag_Theme_Helper::get_option('blog_list_meta_categories'),
		'meta_date' => Zikzag_Theme_Helper::get_option('blog_list_meta_date'),
		'hide_likes' => !Zikzag_Theme_Helper::get_option('blog_list_likes'),
		'hide_share' => !Zikzag_Theme_Helper::get_option('blog_list_share'),
		'read_more_hide' => Zikzag_Theme_Helper::get_option('blog_list_read_more'),
		'content_letter_count' => empty($opt_letter_count) ? '85' : $opt_letter_count,
		'crop_square_img' => 'true',
		'heading_tag' => 'h3',
		'read_more_text' => esc_html__('Read More', 'zikzag'),
		'items_load'  => 4,
		'heading_margin_bottom' => '0',
	];
	$trim = false;
}

extract($wgl_blog_atts);

$image_size = 'full';
if ($crop_square_img && $blog_columns !== '12') {
	$image_size = 'zikzag-990-840';
}

global $wgl_query_vars;
if (!empty($wgl_query_vars)) {
	$query = $wgl_query_vars;
}

// Allowed HTML render
$allowed_html = [
	'a' => [
		'href' => true,
		'title' => true,
	],
	'br' => [],
	'b' => [],
	'em' => [],
	'strong' => []
];

$heading_attr = isset($heading_margin_bottom) && $heading_margin_bottom != '' ? ' style="margin-bottom: ' . (int) $heading_margin_bottom . 'px"' : '';

while ($query->have_posts()) : $query->the_post();

	echo '<div class="wgl_col-' . esc_attr($blog_columns) . ' item">';

	$single = Zikzag_SinglePost::getInstance();
	$single->set_data();

	$pf = $single->get_pf();
	$blog_item_classes = ' format-' . $pf;

	$blog_item_classes .= $hide_media ? ' hide_media' : '';
	$blog_item_classes .= is_sticky() ? ' sticky-post' : '';

	$single->set_data_image(true, $image_size, $aq_image = true);

	$has_media = $single->meta_info_render;
	if ($hide_media) $has_media = false;

	$blog_item_classes .= !$has_media ? ' format-no_featured' : '';

	$meta_cats['category'] = !$meta_categories;
	$meta_cats['date'] = !$meta_date;
	
	$meta_args['author'] = !$meta_author;
	$meta_args['comments'] = !$meta_comments;
	
	$meta_info_args['likes'] = !$hide_likes;
	$meta_info_args['share'] = !$hide_share && function_exists('wgl_theme_helper');



	// Build the structure
	echo '<div class="blog-post', esc_attr($blog_item_classes), '">';
	echo '<div class="blog-post_wrapper">';

	// Media
	if (!$hide_media) {
		$single->render_featured(true, $image_size, true);
	}

	// Cats
	if (!$hide_postmeta) {
		$single->render_post_meta($meta_cats);
	}

	echo '<div class="blog-post_content">';

	// Blog Title
	if (!$hide_blog_title && !empty($title = get_the_title())) {
		printf('<%1$s class="blog-post_title"%2$s><a href="%3$s">%4$s</a></%1$s>', esc_html($heading_tag), $heading_attr, esc_url(get_permalink()), wp_kses($title, $allowed_html));
	}

	// Date, Author, Comments, Likes
	if (!$hide_postmeta && !$meta_author || !$meta_comments) {
		echo '<div class="post_meta-wrap">';
			$single->render_post_meta($meta_args);
		echo '</div>'; // meta-wrap
	}

	// Content Blog
	if (!$hide_content) {
		$single->render_excerpt($content_letter_count, $trim);
	}

	if(!$hide_likes || !$hide_share && function_exists('wgl_theme_helper')):
		echo '<div class="blog-post_meta-wrap">';
	endif;

	// Read more
	if (!$read_more_hide) {
		echo '<a href="', esc_url(get_permalink()), '" class="button-read-more">',
			'<span class="read-more-arrow"></span>',
			esc_html($read_more_text),
			'</a>';
	}

	if(!$hide_likes || !$hide_share && function_exists('wgl_theme_helper')):
		echo '<div class="meta-info-wrap">'; 
		$single->render_post_meta($meta_info_args);
		echo '</div>';
		echo '</div>';
	endif;

	wp_link_pages(
		[
			'before' => '<div class="page-link">' . esc_html__('Pages', 'zikzag') . ': ',
			'after' => '</div>'
		]
	);

	echo '</div>'; // post_content
	echo '</div>'; // post_wrapper
	echo '</div>'; // blog-post
	echo '</div>'; // item

endwhile;
wp_reset_postdata();
