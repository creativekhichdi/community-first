<?php

// Class Theme Helper
require_once ( get_theme_file_path( '/core/class/theme-helper.php' ) );

// Class Walker comments
require_once ( get_theme_file_path( '/core/class/walker-comment.php' ) );

// Class Walker Mega Menu
require_once ( get_theme_file_path( '/core/class/walker-mega-menu.php' ) );

// Class Theme Cats Meta
require_once ( get_theme_file_path( '/core/class/theme-cat-meta.php' ) );

// Class Single Post
require_once ( get_theme_file_path( '/core/class/single-post.php' ) );

// Class Tinymce
require_once ( get_theme_file_path( '/core/class/tinymce-icon.php' ) );

// Class Theme Autoload
require_once ( get_theme_file_path( '/core/class/theme-autoload.php' ) );

// Class Theme Elementor Pro Support
if (class_exists('\ElementorPro\Modules\ThemeBuilder\Module')) {
    require_once(get_theme_file_path('/core/class/theme-elementor-pro-support.php'));
}

// Class Theme Dashboard
require_once ( get_theme_file_path( '/core/class/theme-panel.php' ) );

// Class Theme Verify
require_once ( get_theme_file_path( '/core/class/theme-verify.php' ) );

function zikzag_content_width() {
	if (! isset( $content_width )) {
		$content_width = 940;
	}
}
add_action( 'after_setup_theme', 'zikzag_content_width', 0 );

function zikzag_theme_slug_setup() {
	add_theme_support('title-tag');
}
add_action('after_setup_theme', 'zikzag_theme_slug_setup');

add_action('init', 'zikzag_page_init');
if (! function_exists('zikzag_page_init')) {
	function zikzag_page_init() {
		add_post_type_support('page', 'excerpt');
	}
}


add_action('admin_init', 'zikzag_elementor_dom');
if (!function_exists('zikzag_elementor_dom')) {
    function zikzag_elementor_dom()
    {
        if(!get_option('wgl_elementor_e_dom') && class_exists('\Elementor\Core\Experiments\Manager')){
            $new_option = \Elementor\Core\Experiments\Manager::STATE_INACTIVE;
			update_option('elementor_experiment-e_dom_optimization', $new_option);
            update_option('wgl_elementor_e_dom', 1);
        }
    }
}

if (! function_exists('zikzag_main_menu')) {
	function zikzag_main_menu ($location = '') {
		wp_nav_menu( [
			'theme_location'  => 'main_menu',
			'menu'  => $location,
			'container' => '',
			'container_class' => '',
			'after' => '',
			'link_before' => '<span>',
			'link_after' => '</span>',
			'walker' => new Zikzag_Mega_Menu_Waker()
		] );
	}
}

// return all sidebars
if (! function_exists('zikzag_get_all_sidebar')) {
	function zikzag_get_all_sidebar() {
		global $wp_registered_sidebars;
		$out = [];
		if (empty( $wp_registered_sidebars ) )
			return;
		 foreach ($wp_registered_sidebars as $sidebar_id => $sidebar) :
			$out[$sidebar_id] = $sidebar['name'];
		 endforeach;
		 return $out;
	}
}

if (! function_exists('zikzag_get_custom_menu')) {
	function zikzag_get_custom_menu() {
		$taxonomies = [];

		$menus = get_terms('nav_menu');
		foreach ($menus as $key => $value) {
			$taxonomies[$value->name] = $value->name;
		}
		return $taxonomies;
	}
}

function zikzag_get_attachment($attachment_id) {
	$attachment = get_post( $attachment_id );
	return [
		'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
		'caption' => $attachment->post_excerpt,
		'description' => $attachment->post_content,
		'href' => get_permalink( $attachment->ID ),
		'src' => $attachment->guid,
		'title' => $attachment->post_title
	];
}

if (! function_exists('zikzag_reorder_comment_fields')) {
	function zikzag_reorder_comment_fields($fields) {
		$new_fields = [];

		$myorder = [ 'author', 'email', 'url', 'comment' ];

		foreach ($myorder as $key) {
			$new_fields[ $key ] = isset($fields[ $key ]) ? $fields[ $key ] : '';
			unset( $fields[ $key ] );
		}

		if ($fields) {
			foreach ($fields as $key => $val) {
				$new_fields[ $key ] = $val;
			}
		}

		return $new_fields;
	}
}
add_filter('comment_form_fields', 'zikzag_reorder_comment_fields');

function zikzag_mce_buttons_2($buttons) {
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}
add_filter( 'mce_buttons_2', 'zikzag_mce_buttons_2' );

if (!function_exists('zikzag_header_enable')) {
    function zikzag_header_enable() {

        $header_switch = Zikzag_Theme_Helper::get_option('header_switch');
        $header_switch = is_null($header_switch) ? true : $header_switch;

        if(empty($header_switch)) return false;

        $id = !is_archive() ? get_queried_object_id() : 0;
        // Don't render header if in metabox set to hide it.
        if (
            class_exists('RWMB_Loader')
            && $id !== 0
            && rwmb_meta('mb_customize_header_layout') == 'hide'
        ) {
            return false;
        }

        //hide if 404 page
        $page_not_found = Zikzag_Theme_Helper::get_option('404_show_header');
        if (is_404() && !(bool) $page_not_found) return;

        return true;
    }
}

add_filter('zikzag_header_enable', 'zikzag_header_enable');

if (!function_exists('zikzag_page_title_enable')) {
    function zikzag_page_title_enable()
    {
        $id = !is_archive() ? get_queried_object_id() : 0;

        $output['mb_page_title_switch'] = '';
        if (is_404()) {
            $output['page_title_switch'] = Zikzag_Theme_Helper::get_option('404_page_title_switcher') ? 'on' : 'off';
        } else {
            $output['page_title_switch'] = Zikzag_Theme_Helper::get_option('page_title_switch') ? 'on' : 'off';
            if (class_exists('RWMB_Loader') && $id !== 0) {
                $output['mb_page_title_switch'] = rwmb_meta('mb_page_title_switch');
            }
        }

        $output['single'] = ['type' => '', 'layout' => ''];

        /**
         * Check the Post Type
         *
         * Aimed to prevent Page Title rendering for the following pages:
         *	- blog single type 3;
         *
         * @since 1.0.0
         */
        if (
            get_post_type($id) == 'post'
            && is_single()
        ) {
            $output['single']['type'] = 'post';
            $output['single']['layout'] = Zikzag_Theme_Helper::options_compare('single_type_layout', 'mb_post_layout_conditional', 'custom');
            if ($output['single']['layout'] === '3') {
                $output['page_title_switch'] = 'off';
            }
        }

        if (isset($output['mb_page_title_switch']) && $output['mb_page_title_switch'] == 'on') {
            $output['page_title_switch'] = 'on';
        }

        if (
            is_home()
            || is_front_page()
            || isset($output['mb_page_title_switch']) && $output['mb_page_title_switch'] == 'off'
        ) {
            $output['page_title_switch'] = 'off';
        }

        return $output;
    }
}

add_filter('zikzag_page_title_enable', 'zikzag_page_title_enable');

if (!function_exists('zikzag_footer_enable')) {
    function zikzag_footer_enable()
    {
        $output = [];
        $output['footer_switch'] = Zikzag_Theme_Helper::get_option('footer_switch');
        $output['copyright_switch'] = Zikzag_Theme_Helper::get_option('copyright_switch');

        if (class_exists('RWMB_Loader') && get_queried_object_id() !== 0) {
            $output['mb_footer_switch'] = rwmb_meta('mb_footer_switch');
            $output['mb_copyright_switch'] = rwmb_meta('mb_copyright_switch');

            if ($output['mb_footer_switch'] == 'on') {
                $output['footer_switch'] = true;
            } elseif ($output['mb_footer_switch'] == 'off') {
                $output['footer_switch'] = false;
            }

            if ($output['mb_copyright_switch'] == 'on') {
                $output['copyright_switch'] = true;
            } elseif ($output['mb_copyright_switch'] == 'off') {
                $output['copyright_switch'] = false;
            }
        }

        // Hide on 404 page
        $page_not_found = Zikzag_Theme_Helper::get_option('404_show_footer');
        if (is_404() && !$page_not_found) $output['footer_switch'] = $output['copyright_switch'] = false;

        return $output;
    }
}

add_filter('zikzag_footer_enable', 'zikzag_footer_enable');

add_action('zikzag_preloader', 'Zikzag_Theme_Helper::preloader');

if (!function_exists('zikzag_after_main_content')) {
    function zikzag_after_main_content()
    {
        global $zikzag_dynamic_css;

		$scroll_up = Zikzag_Theme_Helper::get_option('scroll_up');
		$scroll_up_text = Zikzag_Theme_Helper::get_option('scroll_up_text');

		if (is_page()) {
			$social_shares = Zikzag_Theme_Helper::get_option('show_soc_icon_page');

			if (class_exists( 'RWMB_Loader' ) && get_queried_object_id() !== 0) {
				$mb_social_shares = rwmb_meta('mb_customize_soc_shares');

				if ($mb_social_shares == 'on') {
					$social_shares = '1';
				} elseif ($mb_social_shares == 'off') {
					$social_shares = '';
				}
			}

			if (!empty($social_shares) && function_exists('wgl_theme_helper')) {
				echo wgl_theme_helper()->render_social_shares();
			}
		}

		if ($scroll_up) {
			echo '<a href="#" id="scroll_up"><span class="scroll_up-arrow"></span>',
				'<span class="scroll_up-text">',$scroll_up_text ? $scroll_up_text : esc_html__('BACK TO TOP','zikzag'),'</span>',
			'</a>';
		}

		if (isset($zikzag_dynamic_css['style']) && ! empty($zikzag_dynamic_css['style'])) {
			echo '<span id="zikzag-footer-inline-css" class="dynamic_styles-footer">',
				Zikzag_Theme_Helper::render_html($zikzag_dynamic_css['style']),
			'</span>';
		}
    }
}

add_action('zikzag_after_main_content', 'zikzag_after_main_content');

function zikzag_tiny_mce_before_init( $settings) {

	$settings['theme_advanced_blockformats'] = 'p,h1,h2,h3,h4';
	$header_font_color = Zikzag_Theme_Helper::get_option('header-font')['color'];
	$theme_color = Zikzag_Theme_Helper::get_option('theme-primary-color');

	$style_formats = [
		[
			'title' => esc_html__('Dropcap', 'zikzag'),
			'items' => [
				[
					'title' => esc_html__('Primary style', 'zikzag'),
					'inline' => 'span',
					'classes' => 'dropcap-bg',
				], [
					'title' => esc_html__('Secondary style', 'zikzag'),
					'inline' => 'span',
					'classes' => 'dropcap-bg secondary',
				],
			],
		],
		[
			'title' => esc_html__('Highlighter', 'zikzag'),
			'items' => [
				[
					'title' => esc_html__('Primary color', 'zikzag'),
					'inline' => 'span',
					'classes' => 'highlighter',
				], [
					'title' => esc_html__('Secondary color', 'zikzag'),
					'inline' => 'span',
					'classes' => 'highlighter secondary',
				],
			],
		],
		[
			'title' => esc_html__('Font Weight', 'zikzag'),
			'items' => [
				[
					'title' => esc_html__('Default', 'zikzag'),
					'inline' => 'span',
					'styles' => [ 'font-weight' => 'inherit' ],
				], [
					'title' => esc_html__('Lightest (100)', 'zikzag'),
					'inline' => 'span',
					'styles' => [ 'font-weight' => '100' ],
				], [
					'title' => esc_html__('Lighter (200)', 'zikzag'),
					'inline' => 'span',
					'styles' => [ 'font-weight' => '200' ],
				], [
					'title' => esc_html__('Light (300)', 'zikzag'),
					'inline' => 'span',
					'styles' => [ 'font-weight' => '300' ],
				], [
					'title' => esc_html__('Normal (400)', 'zikzag'),
					'inline' => 'span',
					'styles' => [ 'font-weight' => '400' ],
				], [
					'title' => esc_html__('Medium (500)', 'zikzag'),
					'inline' => 'span',
					'styles' => [ 'font-weight' => '500' ],
				], [
					'title' => esc_html__('Semi-Bold (600)', 'zikzag'),
					'inline' => 'span',
					'styles' => [ 'font-weight' => '600' ],
				], [
					'title' => esc_html__('Bold (700)', 'zikzag'),
					'inline' => 'span',
					'styles' => [ 'font-weight' => '700' ],
				], [
					'title' => esc_html__('Bolder (800)', 'zikzag'),
					'inline' => 'span',
					'styles' => [ 'font-weight' => '800' ],
				], [
					'title' => esc_html__('Extra Bold (900)', 'zikzag'),
					'inline' => 'span',
					'styles' => [ 'font-weight' => '900' ],
				],
			]
		],
		[
			'title' => esc_html__('List Style', 'zikzag'),
			'items' => [
				[
					'title' => esc_html__('Dot, primary color', 'zikzag'),
					'selector' => 'ul',
					'classes' => 'zikzag_dot',
				], [
					'title' => esc_html__('Dot, secondary color', 'zikzag'),
					'selector' => 'ul',
					'classes' => 'zikzag_dot secondary',
				], [
					'title' => esc_html__('Check, primary color', 'zikzag'),
					'selector' => 'ul',
					'classes' => 'zikzag_check',
				], [
					'title' => esc_html__('Check, secondary color', 'zikzag'),
					'selector' => 'ul',
					'classes' => 'zikzag_check secondary',
				], [
					'title' => esc_html__('Plus, primary color', 'zikzag'),
					'selector' => 'ul',
					'classes' => 'zikzag_plus',
				], [
					'title' => esc_html__('Plus, secondary color', 'zikzag'),
					'selector' => 'ul',
					'classes' => 'zikzag_plus secondary',
				], [
					'title' => esc_html__('Hyphen, primary color', 'zikzag'),
					'selector' => 'ul',
					'classes' => 'zikzag_hyphen',
				], [
					'title' => esc_html__('Hyphen, secondary color', 'zikzag'),
					'selector' => 'ul',
					'classes' => 'zikzag_hyphen secondary',
				], [
					'title' => esc_html__('Arrow Top, primary color', 'zikzag'),
					'selector' => 'ul',
					'classes' => 'zikzag_arrow-top',
				], [
					'title' => esc_html__('Arrow Top, secondary color', 'zikzag'),
					'selector' => 'ul',
					'classes' => 'zikzag_arrow-top secondary',
				], [
					'title' => esc_html__('Arrow Bottom, primary color', 'zikzag'),
					'selector' => 'ul',
					'classes' => 'zikzag_arrow-bottom',
				], [
					'title' => esc_html__('Arrow Bottom, secondary color', 'zikzag'),
					'selector' => 'ul',
					'classes' => 'zikzag_arrow-bottom secondary',
				], [
					'title' => esc_html__('No List Style', 'zikzag'),
					'selector' => 'ul',
					'classes' => 'no-list-style',
				],
			]
		],
	];

	$settings['style_formats'] = str_replace( '"', "'", json_encode( $style_formats ) );
	$settings['extended_valid_elements'] = 'span[*],a[*],i[*]';
	return $settings;
}
add_filter( 'tiny_mce_before_init', 'zikzag_tiny_mce_before_init' );

function zikzag_theme_add_editor_styles() {
	add_editor_style( 'css/font-awesome.min.css' );
}
add_action( 'current_screen', 'zikzag_theme_add_editor_styles' );

function zikzag_categories_postcount_filter($variable)
{
	if (strpos($variable,'</a> (')) {
		$variable = str_replace('</a> (', '<span class="post_count">(', $variable);
		$variable = str_replace('</a>&nbsp;(', '<span class="post_count">', $variable);
		$variable = str_replace(')', ')</span></a>', $variable);
	} else {
		$variable = str_replace('</a> <span class="count">(', '<span class="post_count">', $variable);
		$variable = str_replace(')', '</span></a>', $variable);
	}

	$pattern1 = '/cat-item-\d+/';
	preg_match_all( $pattern1, $variable,$matches );
	if (isset($matches[0])) {
		foreach ($matches[0] as $value) {
			$int = (int) str_replace('cat-item-', '', $value);
			$icon_image_id = get_term_meta ( $int, 'category-icon-image-id', true );
			if (! empty($icon_image_id)) {
				$icon_image = wp_get_attachment_image_src ( $icon_image_id, 'full' );
				$icon_image_alt = get_post_meta($icon_image_id, '_wp_attachment_image_alt', true);
				$replacement = '$1<img class="cats_item-image" src="'. esc_url($icon_image[0]) .'" alt="'.(! empty($icon_image_alt) ? esc_attr($icon_image_alt) : '').'"/>';
				$pattern = '/(cat-item-'.$int.'+.*?><a.*?>)/';
				$variable = preg_replace( $pattern, $replacement, $variable );
			}
		}
	}

	return $variable;
}
add_filter('wp_list_categories', 'zikzag_categories_postcount_filter');

function zikzag_render_archive_widgets($link_html, $url, $text, $format, $before, $after)
{
	$text = wptexturize($text);
	$url  = esc_url($url);

	if ('link' == $format) {
		$link_html = "\t<link rel='archives' title='" . esc_attr($text) . "' href='$url' />\n";
	} elseif ('option' == $format) {
		$link_html = "\t<option value='$url'>$before $text $after</option>\n";
	} elseif ('html' == $format) {

		$after = str_replace('(', '', $after);
		$after = str_replace(' ', '', $after);
		$after = str_replace('&nbsp;', '', $after);
		$after = str_replace(')', '', $after);

		$after = ! empty($after) ? " <span class='post_count'>".esc_html($after)."</span> " : "";

		$link_html = "<li>" . esc_html($before) . "<a href='" . esc_url($url) . "'>" . esc_html($text) . $after . "</a></li>";
	} else { // custom
		$link_html = "\t$before<a href='$url'>$text</a>$after\n";
	}

	return $link_html;
}
add_filter( 'get_archives_link', 'zikzag_render_archive_widgets', 10, 6 );

// Add image size
if (function_exists( 'add_image_size' )) {
	add_image_size( 'zikzag-990-840',  990, 840, true  );
	add_image_size( 'zikzag-440-440',  440, 440, true  );
	add_image_size( 'zikzag-180-180',  180, 180, true  );
	add_image_size( 'zikzag-120-120',  120, 120, true  );
}

// Include Woocommerce init if plugin is active
if (class_exists( 'WooCommerce' )) {
	require_once( get_theme_file_path ( '/woocommerce/woocommerce-init.php' ) );
}

add_filter('zikzag_enqueue_shortcode_css', 'zikzag_render_css');
function zikzag_render_css($styles) {
	global $zikzag_dynamic_css;
	if (! isset($zikzag_dynamic_css['style'])) {
		$zikzag_dynamic_css = [];
		$zikzag_dynamic_css['style'] = $styles;
	} else {
		$zikzag_dynamic_css['style'] .= $styles;
	}
}

/**
* Add a pingback url auto-discovery header for single posts, pages, or attachments.
*/
function zikzag_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'zikzag_pingback_header' );