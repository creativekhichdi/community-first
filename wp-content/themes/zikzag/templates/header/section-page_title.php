<?php

defined('ABSPATH') || exit;

/**
 *  Page Title area
 *
 *
 *  @class       Zikzag_Get_Page_Title
 *  @version     1.0
 *  @category    Class
 *  @author      WebGeniusLab
 */

if (!class_exists('Zikzag_Get_Page_Title')) {

	class Zikzag_Get_Page_Title
	{
		private static $instance = null;
		public static function get_instance()
		{
			if (null == self::$instance) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct()
		{
			$this->init();
		}

		private $page_title_switch;
		private $mb_page_title_switch;
		private $heading_page_title;
		private $single;

		protected $id;

		public function init()
		{
			$this->id = !is_archive() ? get_queried_object_id() : 0;

			$page_title = apply_filters('zikzag_page_title_enable', true);

            $this->page_title_switch = $page_title['page_title_switch'];
            $this->mb_page_title_switch = $page_title['mb_page_title_switch'];
            $this->single = $page_title['single'];

			/**
			 *  Generate html header rendered
			 *
			 *
			 *  @since  1.0
			 *  @access public
			 */
			$this->page_title_render_html();
		}


		public function page_title_render_html()
		{
			if ($this->page_title_switch == 'on') {
				// Title
				$zikzag_page_title = $this->zikzag_page_title();
				if (!empty($zikzag_page_title)) {
					$page_title_font = Zikzag_Theme_Helper::options_compare('page_title_font', 'mb_page_title_switch', 'on');
					$page_title_font_color = !empty($page_title_font['color']) ? 'color: ' . $page_title_font['color'] . ';' : '';
					$page_title_font_size = !empty($page_title_font['font-size']) ? ' font-size: ' . (int) $page_title_font['font-size'] . 'px;' : '';
					$page_title_font_height = !empty($page_title_font['line-height']) ? ' line-height: ' . (int) $page_title_font['line-height'] . 'px;' : '';
					$title_style = 'style="' . $page_title_font_color . $page_title_font_size . $page_title_font_height . '"';
				}

				// Breadcrumbs
				if ($this->single['type'] == 'post' && in_array($this->single['layout'], range(1, 2))) {
					// Blog type 1-2 have individual options for fine customization
					$page_title_breadcrumbs_switch = ($this->mb_page_title_switch == 'on') ? rwmb_meta('mb_page_title_breadcrumbs_switch') : Zikzag_Theme_Helper::get_option('blog_single_page_title_breadcrumbs_switch');
				} else {
					$page_title_breadcrumbs_switch = Zikzag_Theme_Helper::options_compare('page_title_breadcrumbs_switch', 'mb_page_title_switch', 'on');
				}
				if ($page_title_breadcrumbs_switch) {
					$page_title_breadcrumbs_font = Zikzag_Theme_Helper::options_compare('page_title_breadcrumbs_font', 'mb_page_title_switch', 'on');
					$page_title_breadcrumbs_font_color = !empty($page_title_breadcrumbs_font['color']) ? 'color: ' . $page_title_breadcrumbs_font['color'] . ';' : '';
					$page_title_breadcrumbs_font_size = !empty($page_title_breadcrumbs_font['font-size']) ? ' font-size: ' . (int) $page_title_breadcrumbs_font['font-size'] . 'px;' : '';
					$page_title_breadcrumbs_font_height = !empty($page_title_breadcrumbs_font['line-height']) ? ' line-height: ' . (int) $page_title_breadcrumbs_font['line-height'] . 'px;' : '';
					$breadcrumbs_style = ' style="' . $page_title_breadcrumbs_font_color . $page_title_breadcrumbs_font_size . $page_title_breadcrumbs_font_height . '"';
					ob_start();
					get_template_part('templates/breadcrumbs');
					$breadcrumbs_part = ob_get_clean();
				}

				// Parallax
				$parallax_class = '';
				$parallax = Zikzag_Theme_Helper::options_compare('page_title_parallax', 'mb_page_title_switch', 'on');
				if ($parallax) {
					wp_enqueue_script('jquery-paroller', get_template_directory_uri() . '/js/jquery.paroller.min.js', [], false, false);
					$parallax_class = ' page_title_parallax';
					$parallax_speed = apply_filters("pagetitle_parallax_speed", Zikzag_Theme_Helper::options_compare('page_title_parallax_speed', 'mb_page_title_switch', 'on'));
					$parallax_data_speed = !empty($parallax_speed) ? $parallax_speed : '0.3';
				}

				// Attributes
				$classes = $this->page_title_classes();
				$styles = $this->page_title_styles();
				$data_attr = $parallax ? ' data-paroller-factor=' . $parallax_data_speed : '';

				// Render
				echo '<div class="page-header', $classes, $parallax_class, '"', $styles, $data_attr, '>';
				echo '<div class="page-header_wrapper">';
				echo '<div class="wgl-container">';
				echo '<div class="page-header_content">';
				if (!empty($zikzag_page_title)) {
					$user_tag = Zikzag_Theme_Helper::options_compare('page_title_tag', 'mb_page_title_switch', 'on');
					$theme_tag = !empty($this->heading_page_title) ? $this->heading_page_title : 'div';
					$tag = !empty($user_tag) && $user_tag != 'def' ? $user_tag : $theme_tag;
					printf(
						'<%1$s class="page-header_title" %2$s>%3$s</%1$s>',
						$tag,
						$title_style,
						$zikzag_page_title
					);
				}
				if ($page_title_breadcrumbs_switch) {
					echo '<div class="page-header_breadcrumbs"', $breadcrumbs_style, '>',
						$breadcrumbs_part,
						'</div>';
				}
				echo '</div>';
				echo '</div>';
				echo '</div>';
				echo '</div>'; // page-header
			}
		}

		public function zikzag_page_title()
		{
			$title = '';
			if (is_home() || is_front_page()) {
				$title = '';
			} elseif (is_category()) {
				$title = single_cat_title('', false);
			} elseif (is_tag()) {
				$title = single_term_title("", false) . esc_html__(' Tag', 'zikzag');
			} elseif (is_date()) {
				$title = get_the_time('F Y');
			} elseif (is_author()) {
				$title = esc_html__('Author:', 'zikzag') . ' ' . get_the_author();
			} elseif (is_search()) {
				$title = esc_html__('Search', 'zikzag');
			} elseif (is_404()) {
				$this->heading_page_title = 'h1';
				$title = (bool) Zikzag_Theme_Helper::get_option('404_custom_title_switch') ? Zikzag_Theme_Helper::get_option('404_page_title_text') : esc_html__('Error Page', 'zikzag');
			} elseif (is_singular('portfolio')) {
				$portfolio_title_conditional = Zikzag_Theme_Helper::get_option('portfolio_title_conditional') == '1' ? 'on' : 'off';
				$title = $portfolio_title_conditional == 'on' ? esc_html__('Portfolio Single', 'zikzag') : esc_html(get_the_title());
				$title = apply_filters('zikzag_page_title_portfolio_text', $title);
			} elseif (is_singular('team')) {
				$team_title_condition = Zikzag_Theme_Helper::get_option('team_title_conditional') == '1' ? 'on' : 'off';
				$title = $team_title_condition == 'on' ? esc_html__('Team', 'zikzag') : esc_html(get_the_title());
				$title = apply_filters('zikzag_page_title_team_text', $title);
			} elseif (function_exists('is_product') && (is_product())) {
				$shop_title_conditional = Zikzag_Theme_Helper::get_option('shop_title_conditional') == '1' ? 'on' : 'off';
				$title = $shop_title_conditional == 'on' ? esc_html__('Product', 'zikzag') : esc_html(get_the_title());
				$title = apply_filters('zikzag_page_title_shop_text', $title);
			} elseif (is_archive()) {
				if (function_exists('is_shop') && (is_shop() || is_product_category() || is_product_tag())) {
					$title = esc_html__('Shop', 'zikzag');
				} else {
					$title = esc_html__('Archive', 'zikzag');
				}
			} else {
				global $post;

				if (!empty($post)) {
					$id = $post->ID;
					$post_type = get_post_type($post);
					$blog_title_conditional = Zikzag_Theme_Helper::get_option('blog_title_conditional') == '1' ? 'on' : 'off';
					if ($post_type == 'post') {
						$title = ($blog_title_conditional == 'on') ? esc_html__('Blog Post', 'zikzag') : esc_html(get_the_title($id));
						$title = apply_filters('zikzag_page_title_blog_text', $title);
					} else {
						$this->heading_page_title = 'h1';
						$title = esc_html(get_the_title($id));
					}
				} else {
					$title = esc_html__('No Posts', 'zikzag');
				}
			}
			if ($this->mb_page_title_switch == 'on') {
				$custom_title_switch = rwmb_meta('mb_page_change_tile_switch');

				if (!empty($custom_title_switch)) {
					$custom_title = rwmb_meta('mb_page_change_tile');
					$title = !empty($custom_title) ? esc_html($custom_title) : '';
					$title = apply_filters('zikzag_page_title_custom_text', $title);
				}
			}

			return $title;
		}

		public function page_title_classes()
		{
			if (is_singular('portfolio') || function_exists('is_product') && is_product()) {
				// Mentioned post types have individual options for fine customization
				switch (true) {
					case (is_singular('portfolio')):
						$post_type = 'portfolio';
						break;
					case (function_exists('is_product') && is_product()):
						$post_type = 'shop';
						break;
				}
				$page_title_align = Zikzag_Theme_Helper::get_option($post_type . '_single_title_align');
				$breadcrumbs_align = Zikzag_Theme_Helper::get_option($post_type . '_single_breadcrumbs_align');
				$breadcrumbs_block = Zikzag_Theme_Helper::get_option($post_type . '_single_breadcrumbs_block_switch');
				if (class_exists('RWMB_Loader') && $this->id !== 0 && rwmb_meta('mb_page_title_switch') == 'on') {
					$page_title_align = rwmb_meta('mb_page_title_align');
					$breadcrumbs_align = rwmb_meta('mb_page_title_breadcrumbs_align');
					$breadcrumbs_block = rwmb_meta('mb_page_title_breadcrumbs_block_switch');
				}
			} else {
				$page_title_align = Zikzag_Theme_Helper::options_compare('page_title_align', 'mb_page_title_switch', 'on');
				$breadcrumbs_align = Zikzag_Theme_Helper::options_compare('page_title_breadcrumbs_align', 'mb_page_title_switch', 'on');
				$breadcrumbs_block = Zikzag_Theme_Helper::options_compare('page_title_breadcrumbs_block_switch', 'mb_page_title_switch', 'on');
			}

			$breadcrumbs_align_class = $breadcrumbs_align != $page_title_align ? ' breadcrumbs_align_' . esc_attr($breadcrumbs_align) : '';
			$breadcrumbs_align_class .= !$breadcrumbs_block ? ' breadcrumbs_inline' : '';

			$pt_classes = ' page-header_align_' . (!empty($page_title_align) ? esc_attr($page_title_align) : 'left');
			$pt_classes .= $breadcrumbs_align_class;

			return esc_attr($pt_classes);
		}

		public function page_title_styles()
		{
			// Check custom post types
			switch (get_post_type($this->id)) {
				case 'post':
					$cpt_type_title = 'post';
					$cpt_title = is_single() ? 'single' : 'archive';
					break;
				case 'team':
					$cpt_type_title = 'team';
					$cpt_title = is_single() ? 'single' : 'archive';
					break;
				case 'portfolio':
					$cpt_type_title = 'portfolio';
					$cpt_title = is_single() ? 'single' : 'archive';
					break;
				default:
					$cpt_title = $cpt_type_title = '';
			}

			// Check the Shop page type
			$shop_title = '';
			if (class_exists('WooCommerce')) {
				switch (true) {
					case (is_shop()):
						$shop_title = 'catalog';
						break;
					case (is_cart()):
						$shop_title = 'cart';
						break;
					case (is_product()):
						$shop_title = 'single';
						break;
					case (is_checkout()):
						$shop_title = 'checkout';
						break;
				}
			}

			if (is_singular('portfolio') || function_exists('is_product') && is_product() || is_404()) {
				// Mentioned post types have individual options for fine customization
				switch (true) {
					case (is_404()):
						$post_type = '404';
						break;
					case (is_singular('portfolio')):
						$post_type = 'portfolio_single';
						break;
					case (function_exists('is_product') && is_product()):
						$post_type = 'shop_single';
						break;
				}

				$page_title_bg_switch = Zikzag_Theme_Helper::get_option($post_type . '_title_bg_switch');
				$page_title_height = Zikzag_Theme_Helper::get_option('page_title_height')['height'];

				$page_title_bg_color = Zikzag_Theme_Helper::get_option($post_type . '_page_title_bg_image')['background-color'] !== ''
					? Zikzag_Theme_Helper::get_option($post_type . '_page_title_bg_image')['background-color']
					: Zikzag_Theme_Helper::get_option('page_title_bg_image')['background-color'];

				$page_title_padding = Zikzag_Theme_Helper::get_option($post_type . '_page_title_padding') !== ''
					? Zikzag_Theme_Helper::get_option($post_type . '_page_title_padding')
					: Zikzag_Theme_Helper::get_option('page_title_padding');

				$page_title_margin = Zikzag_Theme_Helper::get_option($post_type . '_page_title_margin');
				$page_title_margin = $page_title_margin['margin-bottom'] !== ''
					? $page_title_margin
					: Zikzag_Theme_Helper::get_option('page_title_margin');

				if (class_exists('RWMB_Loader') && $this->id !== 0 && rwmb_meta('mb_page_title_switch') == 'on') {
					$page_title_bg_switch = rwmb_meta('mb_page_title_bg_switch');
					$page_title_bg_color = rwmb_meta('mb_page_title_bg')['color'];
					$page_title_height = rwmb_meta('mb_page_title_height');
					$page_title_margin = rwmb_meta('mb_page_title_margin');
					$page_title_padding = rwmb_meta('mb_page_title_padding');
				}
			} else {
				// Default
				$page_title_bg_switch = Zikzag_Theme_Helper::options_compare('page_title_bg_switch', 'mb_page_title_switch', 'on');
				$page_title_bg_color = Zikzag_Theme_Helper::get_option('page_title_bg_image')['background-color'];
				$page_title_height = Zikzag_Theme_Helper::get_option('page_title_height')['height'];
				if (class_exists('RWMB_Loader') && $this->mb_page_title_switch == 'on') {
					$page_title_bg_color = rwmb_meta('mb_page_title_bg')['color'];
					$page_title_height = rwmb_meta('mb_page_title_height');
				}
				$page_title_margin = Zikzag_Theme_Helper::options_compare('page_title_margin', 'mb_page_title_switch', 'on');
				$page_title_padding = Zikzag_Theme_Helper::options_compare('page_title_padding', 'mb_page_title_switch', 'on');
			}

			$style = '';
			if (is_404()) {
				switch ($page_title_bg_switch) {
					case true:
						$style .= !empty(Zikzag_Theme_Helper::bg_render('404_page_title'))
							? Zikzag_Theme_Helper::bg_render('404_page_title')
							: Zikzag_Theme_Helper::bg_render('page_title');
						break;
					default:
						break;
				}
			} elseif ($shop_title && !empty(Zikzag_Theme_Helper::bg_render('shop_' . $shop_title . '_page_title'))) {
				$style .= function_exists('is_product') && !is_product() ? Zikzag_Theme_Helper::bg_render('shop_' . $shop_title . '_page_title') : ($page_title_bg_switch ? Zikzag_Theme_Helper::bg_render('shop_single_page_title') : '');
			} elseif ($cpt_title && $page_title_bg_switch && !empty(Zikzag_Theme_Helper::bg_render($cpt_type_title . '_' . $cpt_title . '_page_title'))) {
				$style .= Zikzag_Theme_Helper::bg_render($cpt_type_title . '_' . $cpt_title . '_page_title');
			} else {
				$style .= $page_title_bg_switch ? Zikzag_Theme_Helper::bg_render('page_title', 'mb_page_title_switch', 'on') : '';
			}
			$style .= $page_title_bg_switch && !empty($page_title_bg_color) ? 'background-color: ' . $page_title_bg_color . ';' : '';
			$style .= $page_title_bg_switch && !empty($page_title_height) ? ' height: ' . (int) $page_title_height . 'px;' : '';
			$style .= $page_title_margin['margin-bottom'] != '' ? ' margin-bottom: ' . (int) $page_title_margin['margin-bottom'] . 'px;' : '';
			$style .= $page_title_padding['padding-top'] != '' ? ' padding-top: ' . (int) $page_title_padding['padding-top'] . 'px;' : '';
			$style .= $page_title_padding['padding-bottom'] != '' ? ' padding-bottom: ' . (int) $page_title_padding['padding-bottom'] . 'px;' : '';

			return $style ? ' style="' . esc_attr($style) . '"' : '';
		}
	}

	new Zikzag_Get_Page_Title();
}
