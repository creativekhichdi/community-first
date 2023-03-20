<?php

namespace WglAddons\Templates;

defined( 'ABSPATH' ) || exit; // Abort, if called directly.

use Elementor\Plugin;
use Elementor\Frontend;
use WglAddons\Includes\Wgl_Loop_Settings;
use WglAddons\Includes\Wgl_Elementor_Helper;
use WglAddons\Includes\Wgl_Carousel_Settings;


/**
* WGL Elementor Team Template
*
*
* @class        WglTeam
* @version      1.0
* @category     Class
* @author       WebGeniusLab
*/

class WglTeam
{
	private static $instance = null;

	public static function get_instance()
	{
		if (null == self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function render($atts, $content = null)
	{
		$primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
		$secondary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
		$h_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);

		extract($atts);

		if ($use_carousel) {
			// carousel options array
			$carousel_options = [
				'slide_to_show' => $posts_per_line,
				'autoplay' => $autoplay,
				'autoplay_speed' => $autoplay_speed,
				'use_pagination' => $use_pagination,
				'pag_type' => $pag_type,
				'pag_offset' => $pag_offset,
				'custom_pag_color' => $custom_pag_color,
				'pag_color' => $pag_color,
				'use_prev_next' => $use_prev_next,

				'prev_next_position' => $prev_next_position,
				'custom_prev_next_color' => $custom_prev_next_color,
				'prev_next_color' => $prev_next_color,
				'prev_next_color_hover' => $prev_next_color_hover,

				'custom_resp' => $custom_resp,
				'resp_medium' => $resp_medium,
				'resp_medium_slides' => $resp_medium_slides,
				'resp_tablets' => $resp_tablets,
				'resp_tablets_slides' => $resp_tablets_slides,
				'resp_mobile' => $resp_mobile,
				'resp_mobile_slides' => $resp_mobile_slides,
				'infinite' => $infinite,
				'slides_to_scroll' => $slides_to_scroll,
				'center_mode' => $center_mode,
			];

			wp_enqueue_script('slick', get_template_directory_uri() . '/js/slick.min.js', [], false, false);
		}

		$team_classes = 'team-col_'.$posts_per_line;
		$team_classes .= ' a'.$info_align;

		ob_start();
			$this->render_wgl_team($atts);
		$team_items = ob_get_clean();

		ob_start();
		?>
		<div class="wgl_module_team <?php echo esc_attr($team_classes); ?>">
			<div class="team-items_wrap">
				<?php
				switch ($use_carousel) {
					case true:
						echo Wgl_Carousel_Settings::init($carousel_options, $team_items, false);
						break;
					default:
						echo \Zikzag_Theme_Helper::render_html($team_items);
						break;
				}
				?>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}


	public function render_wgl_team($atts)
	{
		extract($atts);

		$counter = 0;
		$compile = '';

		// Dimensions for team images
		switch ($posts_per_line) { // ratio = 0.96
			default:
			case '1':
			case '2': $team_image_dims = ['width' => '940', 'height' => '1000']; break;
			case '3': $team_image_dims = ['width' => '880', 'height' => '1000'];  break;
			case '4':
			case '5':
			case '6': $team_image_dims = ['width' => '540', 'height' => '600'];  break;
		}

		list($query_args) = Wgl_Loop_Settings::buildQuery($atts);
		$query_args['post_type'] = 'team';
		$wgl_posts = new \WP_Query($query_args);

		while ($wgl_posts->have_posts()) {
			$wgl_posts -> the_post();
			$counter++;
			$compile .= $this->render_wgl_team_item(false, $atts, $team_image_dims, $counter);
		}
		wp_reset_postdata();

		echo $compile;
	}


	public function render_wgl_team_item($single_member, $item_atts, $team_image_dims, $counter)
	{

		extract($item_atts);
		$team_info = $team_icons = $featured_image = $team_title = $team_icons_wrap = '';
		$id = get_the_ID();
		$post = get_post($id);
		$link_to = get_permalink($id);
		$department = get_post_meta($id, 'department', true);
		$info_array = get_post_meta($id, 'info_items', true);
		$social_array = get_post_meta($id, 'soc_icon', true);
		$info_bg_id = get_post_meta($id, 'mb_info_bg', true);
		$info_bg_id_grid = get_post_meta($id, 'mb_info_bg_grid', true);
		$info_bg_url = wp_get_attachment_url($info_bg_id, 'full');
		$info_bg_url_grid = wp_get_attachment_url($info_bg_id_grid, 'full');
		$wp_get_attachment_url = wp_get_attachment_url(get_post_thumbnail_id($id), 'single-post-thumbnail');

		// Info
		if ($info_array) {
			for ($i = 0, $count = count($info_array); $i < $count; $i++) {
				$info = $info_array[$i];
				$info_name = ! empty($info['name']) ? $info['name'] : '';
				$info_description = ! empty($info['description']) ? $info['description'] : '';
				$info_link = ! empty($info['link']) ? $info['link'] : '';

				if ($single_member && (! empty($info_name) || ! empty($info_description))) {
					$team_info .= '<div class="team-info_item">';
						$team_info .= ! empty($info_name) ? '<h5>'.esc_html($info_name).'</h5>' : '';
						$team_info .= ! empty($info_link) ? '<a href="'.esc_url($info_link).'">' : '';
							$team_info .= '<span>'.esc_html($info_description).'</span>';
						$team_info .= ! empty($info_link) ? '</a>' : '';
					$team_info .= '</div>';
				}
			}
		}

		// Social icons
		if ($social_array) {
			for ($i = 0, $count = count($social_array); $i < $count; $i++) {
				$icon = $social_array[$i];
				$icon_name = ! empty($icon['select']) ? $icon['select'] : '';
				$icon_link = ! empty($icon['link']) ? $icon['link'] : '#';
				if ($icon['select']) {
					$team_icons .= '<a href="'.$icon_link.'" class="team-icon"><i class="'.$icon_name.'"></i></a>';
				}
			}
		}

		if (! $hide_soc_icons && $team_icons) {
			$team_icons_wrap  = '<div class="team__icons">';
				$team_icons_wrap .= $team_icons;
			$team_icons_wrap .= '</div>';
		}

		// Featured Image
		if ($wp_get_attachment_url) {
			$wgl_featured_image_url = ($posts_per_line == '1') ? $wp_get_attachment_url : aq_resize($wp_get_attachment_url, $team_image_dims['width'], $team_image_dims['height'], true, true, true);

			$img_alt = get_post_meta(get_post_thumbnail_id($id), '_wp_attachment_image_alt', true);

			$featured_image = sprintf('<%s class="team-image"><img src="%s" alt="%s" /></%s>',
				$single_link_wrapper && ! $single_member ? 'a href="'.esc_url($link_to).'"' : 'div',
				esc_url($wgl_featured_image_url),
				! empty($img_alt) ? $img_alt : '',
				$single_link_wrapper && ! $single_member ? 'a' : 'div'
			);
		}

		// Title
		if (! $hide_title) {
			$title_tag = is_single() ? 'h1' : 'h2';

			$team_title = '<'.$title_tag.' class="team-title">';
				$team_title .= $single_link_heading && ! $single_member ? '<a href="'.esc_url($link_to).'">' : '';
					$team_title .= get_the_title();
				$team_title .= $single_link_heading && ! $single_member ? '</a>' : '';
			$team_title .= '</'.$title_tag.'>';
		}

		// Excerpt
		if (! $single_member && ! $hide_content) {
			$excerpt = ! empty( $post->post_excerpt ) ? $post->post_excerpt : $post->post_content;
			$excerpt = preg_replace( '~\[[^\]]+\]~', '', $excerpt);
			$excerpt = strip_tags($excerpt);
			$excerpt = \Zikzag_Theme_Helper::modifier_character($excerpt, $letter_count, "");
		}

		// Render team list & team single
		if (! $single_member) :

			echo '<div class="team-item">';
				echo '<div class="team-item_wrap">';

					if ($featured_image) {
						echo '<div class="team-item_media">',
							$featured_image,
						'</div>';
					}

					if (! $hide_content || ! $hide_title || ! $hide_meta) {
						echo '<div class="team-item_info">',
							'<div class="team-title_wrapper">';
								echo $team_title;

								if (! $hide_meta && $department) {
									echo '<div class="team-department">',
										esc_html($department),
									'</div>';
								}

								if (! $hide_content) echo '<div class="team-item_excerpt">', $excerpt, '</div>';

							echo '</div>';

							if (!empty($info_bg_url_grid)) echo '<img class="team-item_info-img" src="'.$info_bg_url_grid.'" alt/>';

						echo '</div>';
					}

					if (! $hide_soc_icons) echo $team_icons_wrap;

				echo '</div>';
			echo '</div>';

		else :

			echo '<div class="team-single_wrapper"', ($info_bg_url ? ' style="background-image: url('.esc_url($info_bg_url).')"' : ''), '>';
				if ($featured_image) {
					echo '<div class="team-image_wrap">',
						$featured_image,
					'</div>';
				}
				echo '<div class="team-info_wrapper">',
					'<div class="team-title_wrapper">',
						$team_title,
						$department ? '<div class="team-info_item team-department"><span>'.esc_html($department).'</span></div>' : '',
					'</div>',
					$team_info,
					$team_icons_wrap,
				'</div>';
			echo '</div>';

		endif;

	}

}