<?php 

defined( 'ABSPATH' ) || exit;

use WglAddons\Templates\WglPortfolio;

get_header();

$sb = Zikzag_Theme_Helper::render_sidebars('portfolio_single');
$row_class = $sb['row_class'];
$column = $sb['column'];
$container_class = $sb['container_class'];

$defaults = [
	'posts_per_row' => '1',
	'portfolio_layout' => '',
	'portfolio_icon_pack' => '',
];

// HTML allowed for rendering
$allowed_html = [
	'a' => [
		'href' => true, 'title' => true,
		'class' => true, 'style' => true,
	],
	'br' => [ 'class' => true ],
	'b' => [],
	'em' => [],
	'strong' => []
];

$item = new WglPortfolio();

echo '<div class="wgl-portfolio-single_wrapper">';

  echo '<div class="wgl-container single_portfolio', apply_filters('zikzag_container_class', $container_class), '">';
	echo '<div class="row', apply_filters('zikzag_row_class', $row_class), '">';
		echo '<div id="main-content" class="wgl_col-', apply_filters('zikzag_column_class', $column), '">';

			while ( have_posts() ) : the_post();
				echo $item->wgl_portfolio_single_item($defaults, $item_class = '');
			endwhile;
			wp_reset_postdata();

			// Navigation
			get_template_part('templates/post/post-navigation');

			// Related
			$related_switch = Zikzag_Theme_Helper::get_option('portfolio_related_switch');
			if (class_exists( 'RWMB_Loader' )) {
				$mb_related_switch = rwmb_meta('mb_portfolio_related_switch');      
				if ($mb_related_switch == 'on') {
					$related_switch = true;
				} elseif ($mb_related_switch == 'off') {
					$related_switch = false;
				}
			}

			if ($related_switch && class_exists('Zikzag_Core') && class_exists('Elementor\Plugin')) :
				$mb_pf_cat_r = [];

				$mb_pf_carousel_r = Zikzag_Theme_Helper::options_compare('pf_carousel_r', 'mb_portfolio_related_switch', 'on');
				$mb_pf_title_r = Zikzag_Theme_Helper::options_compare('pf_title_r', 'mb_portfolio_related_switch', 'on');
				$mb_pf_column_r = Zikzag_Theme_Helper::options_compare('pf_column_r', 'mb_portfolio_related_switch', 'on');
				$mb_pf_number_r = Zikzag_Theme_Helper::options_compare('pf_number_r', 'mb_portfolio_related_switch', 'on');
				$mb_pf_number_r = ! empty($mb_pf_number_r) ? $mb_pf_number_r : '12';

				if (class_exists( 'RWMB_Loader' )) {
					$mb_pf_cat_r = get_post_meta(get_the_id(), 'mb_pf_cat_r'); // store terms’ IDs in the post meta and doesn’t set post terms.
				}

				if (! $mb_pf_carousel_r) {
					wp_enqueue_script('isotope');
				}

				$cats = get_the_terms( get_the_id(), 'portfolio-category' );
				$cats = $cats ?: [];
				$cat_slugs = [];
				foreach ($cats as $cat) {
					$cat_slugs[] = 'portfolio-category:'.$cat->slug;
				}

				if (! empty($mb_pf_cat_r[0])) {
					$cat_slugs = [];
					$list = get_terms( 'portfolio-category', [ 'include' => $mb_pf_cat_r[0] ] );
					foreach ($list as $value) {
						$cat_slugs[] = 'portfolio-category:'.$value->slug;
					}
				}

				$atts = [
					'portfolio_layout' => 'related',
					'img_click_action' => 'single',
					'gallery_mode' => false,
					'single_link_title' => 'yes',
					'show_content' => '',
					'add_animation' => '',
					'add_divider' => 'yes',
					'show_filter' => '',
					'info_position' => 'under_image',
					'horizontal_align' => 'center',
					'image_anim' => 'simple',
					'view_all_link' => '',
					'show_view_all' => 'no',
					'item_el_class' => '', 
					'css' => '',
					'view_style' => 'standard',
					'crop_images' => 'yes',
					'show_portfolio_title' => 'true',
					'show_meta_categories' => 'true',
					'add_overlay' => 'true',
					'custom_overlay_color' => 'rgba(34,35,40,.7)',
					'grid_gap' => '30px',
					'featured_render' => '1',
					'portfolio_icon_type' => '',
					'items_load' => $mb_pf_column_r,
					// Carousel
					'autoplay' => true,
					'autoplay_speed' => '5000',
					'c_infinite_loop' => true,
					'c_slide_per_single' => 1,
					'mb_pf_carousel_r' => $mb_pf_carousel_r,
					'posts_per_row' => $mb_pf_column_r,
					'use_pagination' => false,
					'arrows_center_mode' => '',
					'center_info' => '',
					'use_prev_next' => '',
					'center_mode' => '',
					'variable_width' => '',
					'navigation' => '',
					'pag_type' => 'circle', 
					'pag_offset' => '',
					'custom_resp' => true, 
					'resp_medium' => '',
					'pag_color' => '', 
					'custom_pag_color' => '',
					'resp_tablets_slides' => '', 
					'resp_tablets' => '', 
					'resp_medium_slides' => '',
					'resp_mobile' => '600', 
					'resp_mobile_slides'=> '1', 
					// Query
					'number_of_posts' => $mb_pf_number_r,
					'order_by' => 'menu_order',
					'post_type' => 'portfolio',
					'taxonomies' => $cat_slugs,
				];
				$featured_render = new WglPortfolio();

				$featured_post = $featured_render->render($atts);
				if ($featured_render->post_count > 0) {
					echo '<section class="related_portfolio">';
						if (! empty($mb_pf_title_r)) {
							echo '<div class="zikzag_module_title">',
								'<h4>', esc_html($mb_pf_title_r), '</h4>',
							'</div>';
						}
						echo $featured_post;
					echo '</section>';
				}
			endif;

			// Comments
			if (comments_open() || get_comments_number()) {
				echo '<div class="row">';
					echo '<div class="wgl_col-12">';
						comments_template('', true);
					echo '</div>';
				echo '</div>';
			}

		echo '</div>';

		// Sidebar
		if (!empty($sb['content'])) echo $sb['content'];

	echo '</div>';
  echo '</div>';
echo '</div>';


get_footer();

