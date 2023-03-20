<?php

$single = Zikzag_SinglePost::getInstance();
$single->set_data();

$show_tags = Zikzag_Theme_Helper::get_option('single_meta_tags');
$single_author_info = Zikzag_Theme_Helper::get_option('single_author_info');
$single_meta = Zikzag_Theme_Helper::get_option('single_meta');
$hide_featured = Zikzag_Theme_Helper::options_compare('post_hide_featured_image', 'mb_post_hide_featured_image', '1');
$single->set_post_views(get_the_ID());

$show_share = Zikzag_Theme_Helper::get_option('single_share');
$show_likes = Zikzag_Theme_Helper::get_option('single_likes');
$show_views = Zikzag_Theme_Helper::get_option('single_views');

$meta_cats = $meta_args = $meta_likes = [];

if ( ! $single_meta ) :
	$meta_cats['category'] = !(bool)Zikzag_Theme_Helper::get_option('single_meta_categories');	
	$meta_cats['date'] = !(bool)Zikzag_Theme_Helper::get_option('single_meta_date');
	$meta_args['author'] = !(bool)Zikzag_Theme_Helper::get_option('single_meta_author');
	$meta_args['comments'] = !(bool)Zikzag_Theme_Helper::get_option('single_meta_comments');
	$meta_likes['likes'] = (bool)Zikzag_Theme_Helper::get_option('single_likes');
	$meta_likes['views'] = (bool)Zikzag_Theme_Helper::get_option('single_views');
endif;

?>
<article class="blog-post blog-post-single-item format-<?php echo esc_attr($single->get_pf()); ?>">
	<div <?php post_class("single_meta"); ?>>
		<div class="item_wrapper">
			<div class="blog-post_content"><?php

				// Featured Image
				if ( ! $hide_featured ) {
					$single->render_featured();
				}

				// Date,  Cats
				if ( ! $single_meta ) $single->render_post_meta($meta_cats);
				
				// Title
				echo '<h2 class="blog-post_title">', get_the_title(), '</h2>';
				
				echo '<div class="post_meta-wrap">';

					// Author, Comments
					if ( ! $single_meta ) $single->render_post_meta($meta_args);

					// Likes, Views
					if ( ! $single_meta ) $single->render_post_meta($meta_likes);

				echo '</div>'; // meta-wrap

				the_content();

				wp_link_pages(
					[
						'before' => '<div class="page-link"><span class="pagger_info_text">' .esc_html__( 'Pages', 'zikzag' ). ': </span>',
						'after' => '</div>'
					]
				);

				if ( ! $show_tags && has_tag() || $show_share && function_exists('wgl_theme_helper') ) :

					echo '<div class="single_post_info">';

						// Shares
						if ( $show_share && function_exists('wgl_theme_helper') ) :
							
							echo '<div class="single_info-share_social-wpapper">',
							wgl_theme_helper()->render_post_share('yes'),
							'</div>';
						
						endif; 

						// Tags
						if ( ! $show_tags && has_tag() ) {
							the_tags('<div class="tagcloud-wrapper"><div class="tagcloud">', ' ', '</div></div>');
						}

					echo '</div>'; // post_info

					echo '<div class="post_info-divider"></div>';

				else :

					echo '<div class="post_info-divider"></div>';

				endif;

				// Author Info
				if ( $single_author_info ) $single->render_author_info();

				?>
				<div class="clear"></div>
			</div>
		</div>
	</div>
</article>
