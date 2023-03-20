<?php

defined( 'ABSPATH' ) || exit;

/**
 * The dedault template for single posts rendering
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package     WordPress
 * @subpackage  Zikzag
 * @since       1.0
 * @version     1.0
 */

use WglAddons\Templates\WglBlog;

get_header();
the_post();

$sb = Zikzag_Theme_Helper::render_sidebars('single');
$single_type = Zikzag_Theme_Helper::get_option('single_type_layout');

$column = $sb['column'] ?? '';
$row_class = $sb['row_class'] ?? '';
$container_class = $sb['container_class'] ?? '';
$layout = $sb['layout'] ?? '';

if (empty($single_type)) {
	$single_type = 2;
}

if (class_exists( 'RWMB_Loader' )) {
	$mb_type = rwmb_meta('mb_post_layout_conditional');
	if (! empty($mb_type) && $mb_type != 'default') {
		$single_type = rwmb_meta('mb_single_type_layout');
	}
}

$row_class .= ' single_type-'.$single_type;

if ($single_type === '3') {
	echo '<div class="post_featured_bg" style="background-color: ', Zikzag_Theme_Helper::get_option('page_title_bg_image')['background-color'], '">';
		get_template_part('templates/post/single/post', $single_type.'_image');
	echo '</div>';
}

?>
<div class="wgl-container<?php echo apply_filters('zikzag_container_class', $container_class); ?>">
  <div class="row<?php echo apply_filters('zikzag_row_class', $row_class); ?>">
	<div id='main-content' class="wgl_col-<?php echo apply_filters('zikzag_column_class', $column); ?>"><?php

		get_template_part('templates/post/single/post', $single_type);

		// Navigation
		get_template_part('templates/post/post-navigation');

		// Related Posts
		$show_post_related = Zikzag_Theme_Helper::get_option('single_related_posts');

		if (class_exists( 'RWMB_Loader' )) {
			$mb_blog_show_r = rwmb_meta('mb_blog_show_r');
			if (! empty($mb_blog_show_r) && $mb_blog_show_r != 'default') {
				$show_post_related = $mb_blog_show_r === 'off' ? null : $mb_blog_show_r;
			}
		}

		if ($show_post_related && class_exists('Zikzag_Core') && class_exists('\Elementor\Plugin')) :

			$mb_blog_cat_r = [];

			$mb_blog_carousel_r = Zikzag_Theme_Helper::options_compare('blog_carousel_r', 'mb_blog_show_r', 'custom');
			$mb_blog_title_r = Zikzag_Theme_Helper::options_compare('blog_title_r', 'mb_blog_show_r', 'custom');

			$cats = Zikzag_Theme_Helper::get_option('blog_cat_r');
			if (! empty($cats)) {
				$mb_blog_cat_r[] = implode(',', $cats);
			}

			if (class_exists( 'RWMB_Loader' ) && get_queried_object_id() !== 0) {
				if (rwmb_meta('mb_blog_show_r') == 'custom') {
					$mb_blog_cat_r = get_post_meta(get_the_id(), 'mb_blog_cat_r');
				}
			}

			$mb_blog_column_r = Zikzag_Theme_Helper::options_compare('blog_column_r', 'mb_blog_show_r', 'custom');
			$mb_blog_number_r = Zikzag_Theme_Helper::options_compare('blog_number_r', 'mb_blog_show_r', 'custom');


			// Render the Related Posts
			echo '<section class="single related_posts">';
				// Get Cats_Slug
				if ($categories = get_the_category()) {
					$post_categ = $post_category_compile = '';
					foreach ($categories as $category) {
						$post_categ = $post_categ . $category->slug . ',';
					}
					$post_category_compile .= '' . trim($post_categ, ',') . '';

					if (! empty($mb_blog_cat_r[0])) {
						$categories = get_categories( [ 'include' => $mb_blog_cat_r[0] ] ); 
						$post_categ = $post_category_compile = '';
						foreach ($categories as $category) {
							$post_categ = $post_categ . $category->slug . ',';
						}
						$post_category_compile .= '' . trim($post_categ, ',') . '';
					}

					$mb_blog_cat_r = $post_category_compile;
				}
				echo '<div class="zikzag_module_title">',
					'<h4>',
						! empty($mb_blog_title_r) ? esc_html($mb_blog_title_r).' ' : esc_html__('Related Posts', 'zikzag').' ',
					'</h4>',
				'</div>';

				global $wgl_related_posts;
				$wgl_related_posts = true;
				
				$atts = [
					'blog_navigation' => 'none',
					'use_navigation' => null,
					'blog_layout' => !empty($mb_blog_carousel_r) ? 'carousel' : 'grid',
					'hide_share' => true,
					'hide_content' => true,
					'hide_likes' => true,
					'meta_author' => true,
					'meta_comments' => true,
					'read_more_hide' => false,
					'read_more_text' => esc_html__('Read More', 'zikzag'),
					'heading_tag' => 'h4',
					'content_letter_count' => 90,
					'crop_square_img' => 1,
					'items_load' => 4,
					'name_load_more' => esc_html__('Load More', 'zikzag'),
					'blog_columns' => ! empty($mb_blog_column_r) ? $mb_blog_column_r : (($layout == 'none') ? '4' : '6'),
					// Carousel
					'autoplay' => '',
					'autoplay_speed' => 3000,
					'slides_to_scroll' => false,
					'use_pagination' => '',
					'pag_type' => 'circle',
					'pag_offset' => '',
					'custom_resp' => true,
					'resp_medium' => '',
					'pag_color' => '',
					'custom_pag_color' => '',
					'resp_tablets_slides' => '',
					'resp_tablets' => '',
					'resp_medium_slides' => '',
					'resp_mobile' => '767',
					'resp_mobile_slides' => '1',
					// Query
					'number_of_posts' => (int) $mb_blog_number_r,
					'categories' => $mb_blog_cat_r,
					'order_by' => 'rand'
				];
				$by_posts[$post->post_name] = $post->post_title;
				$atts['exclude_any'] = 'yes';
				$atts['by_posts'] = $by_posts; // excluding current post

				$related_items = new WglBlog();
				echo Zikzag_Theme_Helper::render_html($related_items->render($atts));
				
				unset($GLOBALS['related_posts']);
			echo '</section>';

		endif;

		// Comments
		if (comments_open() || get_comments_number() ) : ?>
			<div class="row">
				<div class="wgl_col-12">
				  <?php
					comments_template();
				  ?>
				</div>
			</div>
			<?php
		endif;
		?>

	</div>

	<?php
		echo !empty($sb['content']) ? $sb['content'] : '';
	?>
  </div>

</div>
<?php

get_footer();

?>
