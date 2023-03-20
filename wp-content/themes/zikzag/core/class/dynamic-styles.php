<?php

defined('ABSPATH') || exit;

/**
* Zikzag Dynamic Styles
*
*
* @class Zikzag_Dynamic_Styles
* @version 1.0
* @category Class
* @author WebGeniusLab
*/

class Zikzag_Dynamic_Styles
{
	public $settings;
	protected static $instance = null;
	private $gtdu;
	private $use_minify;

	private $header_page_select_id;

	public static function instance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function register_script()
	{
		$this->gtdu = get_template_directory_uri();
		$this->use_minify = Zikzag_Theme_Helper::get_option('use_minify') ? '.min' : '';
		// Register action
		add_action('wp_enqueue_scripts', array($this,'css_reg') );
		add_action('wp_enqueue_scripts', array($this,'js_reg') );
		// Register action for Admin
		add_action('admin_enqueue_scripts', array($this,'admin_css_reg') );
		add_action('admin_enqueue_scripts', array($this, 'admin_js_reg') );

		//Support Elementor Header Builder
		add_action('wp_enqueue_scripts', [$this, 'get_elementor_css_theme_builder']);
        add_action('wp_enqueue_scripts', [$this, 'elementor_column_fix']);
	}

	/* Register CSS */
	public function css_reg()
	{
		/* Register CSS */
		wp_enqueue_style('zikzag-default-style', get_bloginfo('stylesheet_url'));
		// Flaticon register
		wp_enqueue_style('zikzag-flaticon', $this->gtdu . '/fonts/flaticon/flaticon.css');
		// Font-Awesome
		wp_enqueue_style('font-awesome-5-all', $this->gtdu . '/css/all.min.css');
		wp_enqueue_style('zikzag-main', $this->gtdu . '/css/main'.$this->use_minify.'.css');
		// Rtl css
		if (is_rtl()) {
			wp_enqueue_style('zikzag-rtl', get_template_directory_uri() . '/css/rtl' . $this->use_minify . '.css');
		}
	}

    /**
     * Multi Language Support
     *
     *
     * @access public
     * @since 1.0.0
     */
    public function multiLanguageSupport($id, $page_type)
    {

        if (class_exists('Polylang') && function_exists('pll_current_language')) {
            $currentLanguage = pll_current_language();
            $translations = PLL()->model->post->get_translations($id);

            $polylang_id = $translations[$currentLanguage] ?? '';
            $id = !empty($polylang_id) ? $polylang_id : $id;
        }

        if (class_exists('SitePress')) {
            $id = wpml_object_id_filter($id, $page_type, false, ICL_LANGUAGE_CODE);
        }

        return $id;
    }

	public function get_elementor_css_theme_builder()
    {
        $current_post_id = get_the_ID();
        $css_files = [];

        $locations[] = $this->get_elementor_css_cache_header();
        $locations[] = $this->get_elementor_css_cache_header_sticky();
        $locations[] = $this->get_elementor_css_cache_footer();
        $locations[] = $this->get_elementor_css_cache_side_panel();

        foreach ($locations as $location) {
            //* Don't enqueue current post here (let the preview/frontend components to handle it)
            if ($location && $current_post_id !== $location) {
                $css_file = new \Elementor\Core\Files\CSS\Post($location);
                $css_files[] = $css_file;
            }
        }

        if (!empty($css_files)) {
            \Elementor\Plugin::$instance->frontend->enqueue_styles();
            foreach ($css_files as $css_file) {
                $css_file->enqueue();
            }
        }
    }

    public function get_elementor_css_cache_header()
    {
        /**
         * Post CSS file constructor.
         *
         * Initializing the CSS file of the post. Set the post ID and initiate the stylesheet.
         *
         * @param int $header_page_select_id Post ID.
         */

        $this->header_type = Zikzag_Theme_Helper::get_option('header_type');

        $header_page_select = Zikzag_Theme_Helper::get_option('header_page_select');
        if (!empty($header_page_select)) {
            $this->header_page_select_id = intval($header_page_select);
        }

        $id = !is_archive() ? get_queried_object_id() : 0;

        if (
            class_exists('RWMB_Loader')
            && $id !== 0
            && rwmb_meta('mb_customize_header_layout') == 'custom'
            && rwmb_meta('mb_header_content_type') !== 'default'
        ) {
            $this->header_type = 'custom';
            $this->header_page_select_id = (int) rwmb_meta('mb_customize_header');
		}

		$this->header_page_select_id = $this->multiLanguageSupport( $this->header_page_select_id, 'header' );

        if (
            $this->header_type == 'custom'
            && class_exists('\Elementor\Core\Files\CSS\Post')
        ) {
            return $this->header_page_select_id;
		}

		return false;
	}

	public function get_elementor_css_cache_header_sticky()
    {

        $id = !is_archive() ? get_queried_object_id() : 0;
        $header_sticky_page_select_id = '';

        $header_type = Zikzag_Theme_Helper::get_option('header_type');

        if (Zikzag_Theme_Helper::options_compare('header_sticky', 'mb_customize_header_layout', 'custom') == '1') {
            $header_sticky_page_select = Zikzag_Theme_Helper::get_option('header_sticky_page_select');

            if (!empty($header_sticky_page_select)) {
                $header_sticky_page_select_id = intval($header_sticky_page_select);
            }
        }

        if (class_exists( 'RWMB_Loader' ) && $id !== 0) {
            $customize_header = rwmb_meta('mb_customize_header_layout');
            if ($customize_header == 'custom') {
                $custom_sticky_header = rwmb_meta('mb_sticky_header_content_type');
                if ($custom_sticky_header !== 'default') {
                    $header_sticky_page_select_id = (int) rwmb_meta('mb_customize_sticky_header');
                }
            }
		}

		$header_sticky_page_select_id = $this->multiLanguageSupport( $header_sticky_page_select_id, 'header' );

        if (
            $header_type == 'custom'
            && !empty($header_sticky_page_select_id)
            && class_exists('\Elementor\Core\Files\CSS\Post')
        ) {
            return $header_sticky_page_select_id;
		}

		return false;
    }

	public function get_elementor_css_cache_footer()
	{
        // footer option
        $footer_switch = Zikzag_Theme_Helper::get_option('footer_switch');

        if (class_exists('RWMB_Loader') && get_queried_object_id() !== 0) {
            if (rwmb_meta('mb_footer_switch') == 'on') {
                $footer_switch = true;
            } elseif (rwmb_meta('mb_footer_switch') == 'off') {
                $footer_switch = false;
            }
        }

        //hide if 404 page
        $page_not_found = Zikzag_Theme_Helper::get_option('404_show_footer');
        if (is_404() && !(bool) $page_not_found) $footer_switch = false;

        if ($footer_switch) {
            $footer_content_type = Zikzag_Theme_Helper::options_compare('footer_content_type','mb_footer_switch','on');
            if (
				'pages' == $footer_content_type
				&& class_exists('\Elementor\Core\Files\CSS\Post')
            ) {

                $footer_page_select = Zikzag_Theme_Helper::options_compare('footer_page_select', 'mb_footer_switch', 'on');

                if ($footer_page_select) {
                    $footer_page_select_id = intval($footer_page_select);
					$footer_page_select_id = $this->multiLanguageSupport( $footer_page_select_id, 'footer' );

					return $footer_page_select_id;
                }
            }
		}

		return false;
	}

	public function get_elementor_css_cache_side_panel()
    {
        $side_panel_enable = Zikzag_Theme_Helper::get_option('side_panel_enable');

        if (empty($side_panel_enable)) {
            return;
        }

        $content_type = Zikzag_Theme_Helper::options_compare('side_panel_content_type','mb_customize_side_panel','custom');

        if (
            $content_type == 'pages'
            && class_exists('\Elementor\Core\Files\CSS\Post')
        ) {
            $page_select = Zikzag_Theme_Helper::options_compare('side_panel_page_select', 'mb_customize_side_panel', 'custom');

            if (!$page_select) {
                // Bailout, if nothing to render
                return;
            }

            $page_select = intval($page_select);
            $page_select = $this->multiLanguageSupport($page_select, 'side-panel');

            return $page_select;
        }

        return false;
    }


	/* Register JS */
	public function js_reg()
	{
		$theme_version = wp_get_theme()->get('Version') ?? false;
		wp_enqueue_script('zikzag-theme-addons', $this->gtdu . '/js/theme-addons'.$this->use_minify.'.js', array('jquery'), $theme_version, true);
		wp_enqueue_script('zikzag-theme', $this->gtdu . '/js/theme.js', array('jquery'), $theme_version, true);

		wp_localize_script( 'zikzag-theme', 'wgl_core', array(
			'ajaxurl' => esc_js( admin_url( 'admin-ajax.php' ) ),
			) );

		if (is_singular() && comments_open() && get_option( 'thread_comments' )) {
			wp_enqueue_script( 'comment-reply' );
		}

		wp_enqueue_script('perfect-scrollbar', get_template_directory_uri() . '/js/perfect-scrollbar.min.js', array(), $theme_version, false);
	}

	/* Register css for admin panel */
	public function admin_css_reg()
	{
		// Font-Awesome
		wp_enqueue_style('font-awesome-5-all', $this->gtdu . '/css/all.min.css');
		// Main admin styles
		wp_enqueue_style('zikzag-admin', $this->gtdu . '/core/admin/css/admin.css');
		// Add standard wp color picker
		wp_enqueue_style('wp-color-picker');
	}

	/* Register css and js for admin panel */
	public function admin_js_reg()
	{
		/* Register JS */
		wp_enqueue_media();
		wp_enqueue_script('wp-color-picker');
		wp_localize_script('wp-color-picker', 'wpColorPickerL10n', array(
			'clear'            => esc_html__('Clear', 'zikzag'),
			'clearAriaLabel'   => esc_html__('Clear color', 'zikzag'),
			'defaultString'    => esc_html__('Default', 'zikzag'),
			'defaultAriaLabel' => esc_html__('Select default color', 'zikzag'),
			'pick'             => esc_html__('Select', 'zikzag'),
			'defaultLabel'     => esc_html__('Color value', 'zikzag'),
		));

		//Admin Js
		wp_enqueue_script('zikzag-admin', $this->gtdu . '/core/admin/js/admin.js');
		// If active Metabox IO
		if (class_exists('RWMB_Loader')) {
			wp_enqueue_script('zikzag-metaboxes', $this->gtdu . '/core/admin/js/metaboxes.js');
		}

        $currentTheme = wp_get_theme();
        $theme_name = $currentTheme->parent() == false ? wp_get_theme()->get( 'Name' ) : wp_get_theme()->parent()->get( 'Name' );
        $theme_name = trim($theme_name);

        $purchase_code = $email = '';
        if( Zikzag_Theme_Helper::wgl_theme_activated() ){
            $theme_details = get_option('wgl_licence_validated');
            $purchase_code = $theme_details['purchase'];
            $email = $theme_details['email'];
        }

        wp_localize_script('zikzag-admin', 'wgl_verify', [
            'ajaxurl' => esc_js(admin_url('admin-ajax.php')),
            'wglUrlActivate' => esc_js(Wgl_Theme_Verify::get_instance()->api. 'verification'),
            'wglUrlDeactivate' => esc_js(Wgl_Theme_Verify::get_instance()->api. 'deactivate'),
            'domainUrl' => esc_js(site_url( '/' )),
            'themeName' => esc_js($theme_name),
            'purchaseCode' => esc_js($purchase_code),
            'email' => esc_js($email),
            'message' => esc_js(esc_html__( 'Thank you, your license has been validated', 'zikzag' )),
            'ajax_nonce' => esc_js( wp_create_nonce('_notice_nonce') )
        ]);
	}

	public function init_style() {
		add_action('wp_enqueue_scripts', [$this, 'add_style'] );
		add_action('wp_enqueue_scripts', [$this, 'elementor_column_fix'] );
	}

	public function add_style()
	{
		$css = '';
		$id = !is_archive() ? get_queried_object_id() : 0;
		/*-----------------------------------------------------------------------------------*/
		/* Body Style
		/*-----------------------------------------------------------------------------------*/
		$page_colors_switch = Zikzag_Theme_Helper::options_compare('page_colors_switch','mb_page_colors_switch','custom');
		$use_gradient_switch = Zikzag_Theme_Helper::options_compare('use-gradient','mb_page_colors_switch','custom');
		if ($page_colors_switch == 'custom') {
			$theme_primary_color = Zikzag_Theme_Helper::options_compare('page_theme_color','mb_page_colors_switch','custom');
			$theme_secondary_color = Zikzag_Theme_Helper::options_compare('page_theme_secondary_color','mb_page_colors_switch','custom');

			$bg_body = Zikzag_Theme_Helper::options_compare('body_background_color','mb_page_colors_switch','custom');
			// Go top color
			$scroll_up_arrow_color = Zikzag_Theme_Helper::options_compare('scroll_up_arrow_color','mb_page_colors_switch','custom');
			// Gradient colors
			$theme_gradient_from = Zikzag_Theme_Helper::options_compare('theme-gradient-from','mb_page_colors_switch','custom');
			$theme_gradient_to = Zikzag_Theme_Helper::options_compare('theme-gradient-to','mb_page_colors_switch','custom');
		} else {
			$theme_primary_color = esc_attr(Zikzag_Theme_Helper::get_option('theme-primary-color'));
			$theme_secondary_color = esc_attr(Zikzag_Theme_Helper::get_option('theme-secondary-color'));

			$bg_body = esc_attr(Zikzag_Theme_Helper::get_option('body-background-color'));
			// Go top color
			$scroll_up_arrow_color = Zikzag_Theme_Helper::get_option('scroll_up_arrow_color');
			// Gradient colors
			$theme_gradient = Zikzag_Theme_Helper::get_option('theme-gradient');
			$second_gradient = Zikzag_Theme_Helper::get_option('second-gradient');
			$theme_gradient_from = $theme_gradient['from'] ?? null;
			$theme_gradient_to = $theme_gradient['to'] ?? null;
		}

		$hsl_color =  Zikzag_Theme_Helper::hexToHsl( $theme_primary_color);
		$darken_color_thirteen =  Zikzag_Theme_Helper::darken( $hsl_color, 13);
		/*-----------------------------------------------------------------------------------*/
		/* \End Body style
		/*-----------------------------------------------------------------------------------*/

		/*-----------------------------------------------------------------------------------*/
		/* Body Add Class
		/*-----------------------------------------------------------------------------------*/
		if ($use_gradient_switch) {
			add_filter( 'body_class', function( $classes) {
				return array_merge( $classes, array( 'theme-gradient' ) );
			} );
			$gradient_class = '.theme-gradient';
		} else {
			$gradient_class = '';
		}
		if (defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.0', '>=' )) {
			if(
				empty(get_option( 'elementor_element_wrappers_legacy_mode' ))
				|| \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_dom_optimization' )
			){
				add_filter( 'body_class', function( $classes ) {
					return array_merge( $classes, array( 'new-elementor' ) );
				} );
			}
		}
		/*-----------------------------------------------------------------------------------*/
		/* End Body Add Class
		/*-----------------------------------------------------------------------------------*/

		/*-----------------------------------------------------------------------------------*/
		/* Header Typography
		/*-----------------------------------------------------------------------------------*/
		$header_font = Zikzag_Theme_Helper::get_option('header-font');

		$header_font_family = $header_font_weight = $header_font_color = '';
		if (! empty( $header_font)) {
			$header_font_family = esc_attr($header_font['font-family']);
			$header_font_weight = esc_attr($header_font['font-weight']);
			$header_font_color = esc_attr($header_font['color']);
		}

		// Add Heading h1,h2,h3,h4,h5,h6 variables
		for ( $i = 1; $i <= 6; $i++) {
			${'header-h'.$i} = Zikzag_Theme_Helper::get_option('header-h'.$i);
			${'header-h'.$i.'_family'} = ${'header-h'.$i.'_weight'} = ${'header-h'.$i.'_line_height'} = ${'header-h'.$i.'_size'} = ${'header-h'.$i.'_text_transform'} = '';

			if (! empty( ${'header-h'.$i})) {
				${'header-h'.$i.'_family'} = !empty( ${'header-h'.$i}["font-family"]) ? esc_attr(${'header-h'.$i}["font-family"]) : '';
				${'header-h'.$i.'_weight'} = !empty( ${'header-h'.$i}["font-weight"]) ? esc_attr(${'header-h'.$i}["font-weight"]) : '';
				${'header-h'.$i.'_line_height'} = !empty( ${'header-h'.$i}["line-height"]) ? esc_attr(${'header-h'.$i}["line-height"]) : '';
				${'header-h'.$i.'_size'} = !empty( ${'header-h'.$i}["font-size"]) ? esc_attr(${'header-h'.$i}["font-size"]) : '';
				${'header-h'.$i.'_text_transform'} = !empty( ${'header-h'.$i}["text-transform"]) ? esc_attr(${'header-h'.$i}["text-transform"]) : '';
			}
		}

		/*-----------------------------------------------------------------------------------*/
		/* \End Header Typography
		/*-----------------------------------------------------------------------------------*/

		/*-----------------------------------------------------------------------------------*/
		/* Body Typography
		/*-----------------------------------------------------------------------------------*/
		$main_font = Zikzag_Theme_Helper::get_option('main-font');
		$content_font_family = $content_line_height = $content_font_size = $content_font_weight = $content_color = '';
		if (! empty( $main_font)) {
			$content_font_family = esc_attr($main_font['font-family']);
			$content_font_size = esc_attr($main_font['font-size']);
			$content_font_weight = esc_attr($main_font['font-weight']);
			$content_color = esc_attr($main_font['color']);
			$content_line_height = esc_attr($main_font['line-height']);
			$content_line_height = !empty( $content_line_height) ? round(((int)$content_line_height / (int)$content_font_size), 3) : '';
		}

		/*-----------------------------------------------------------------------------------*/
		/* \End Body Typography
		/*-----------------------------------------------------------------------------------*/

		/*-----------------------------------------------------------------------------------*/
		/* Menu, Sub-menu Typography
		/*-----------------------------------------------------------------------------------*/
		$menu_font = Zikzag_Theme_Helper::get_option('menu-font');
		$menu_font_family = $menu_font_weight = $menu_font_line_height = $menu_font_size = '';
		if (! empty( $menu_font)) {
			$menu_font_family = !empty( $menu_font['font-family']) ? esc_attr($menu_font['font-family']) : '';
			$menu_font_weight = !empty( $menu_font['font-weight']) ? esc_attr($menu_font['font-weight']) : '';
			$menu_font_line_height = !empty( $menu_font['line-height']) ? esc_attr($menu_font['line-height']) : '';
			$menu_font_size = !empty( $menu_font['font-size']) ? esc_attr($menu_font['font-size']) : '';
		}

		$sub_menu_font = Zikzag_Theme_Helper::get_option('sub-menu-font');
		$sub_menu_font_family = $sub_menu_font_weight = $sub_menu_font_line_height = $sub_menu_font_size = '';
		if (! empty( $sub_menu_font)) {
			$sub_menu_font_family = !empty( $sub_menu_font['font-family']) ? esc_attr($sub_menu_font['font-family']) : '';
			$sub_menu_font_weight = !empty( $sub_menu_font['font-weight']) ? esc_attr($sub_menu_font['font-weight']) : '';
			$sub_menu_font_line_height = !empty( $sub_menu_font['line-height']) ? esc_attr($sub_menu_font['line-height']) : '';
			$sub_menu_font_size = !empty( $sub_menu_font['font-size']) ? esc_attr($sub_menu_font['font-size']) : '';
		}
		/*-----------------------------------------------------------------------------------*/
		/* \End Menu, Sub-menu Typography
		/*-----------------------------------------------------------------------------------*/

		/*-----------------------------------------------------------------------------------*/
		/* Additional Font
		/*-----------------------------------------------------------------------------------*/

		$additional_font = Zikzag_Theme_Helper::get_option('additional-font');
		if (! empty($additional_font)) {
			$additional_font_family = esc_attr($additional_font['font-family']);
			$additional_font_weight = esc_attr((int)$additional_font['font-weight']);
		} else {
			$additional_font_family = $additional_font_weight = '';
		}

		/*-----------------------------------------------------------------------------------*/
		/* \End Additional Font
		/*-----------------------------------------------------------------------------------*/

		$menu_color_top = Zikzag_Theme_Helper::get_option('header_top_color')['rgba'] ?? '';
        $menu_color_middle = Zikzag_Theme_Helper::get_option('header_middle_color')['rgba'] ?? '';
        $menu_color_bottom = Zikzag_Theme_Helper::get_option('header_bottom_color')['rgba'] ?? '';

		// Set Queries width to apply mobile style
		$sub_menu_color = Zikzag_Theme_Helper::get_option('sub_menu_color');
		$sub_menu_bg = Zikzag_Theme_Helper::get_option('sub_menu_background')['rgba'] ?? '';

		$sub_menu_border = Zikzag_Theme_Helper::get_option('header_sub_menu_bottom_border') ?? '';
		$sub_menu_border_height = Zikzag_Theme_Helper::get_option('header_sub_menu_border_height')['height'] ?? '';
		$sub_menu_border_color = Zikzag_Theme_Helper::get_option('header_sub_menu_bottom_border_color')['rgba'] ?? '';
		if ($sub_menu_border) {
			$css .= '.primary-nav ul li ul li:not(:last-child), .sitepress_container > .wpml-ls ul ul li:not(:last-child) {'
				. ($sub_menu_border_height ? 'border-bottom-width: '.(int) (esc_attr($sub_menu_border_height)).'px;' : '')
				. ($sub_menu_border_color ? 'border-bottom-color: '.esc_attr($sub_menu_border_color).';' : '')
				.' border-bottom-style: solid;
			}';
		}

		$mobile_sub_menu_bg = Zikzag_Theme_Helper::get_option('mobile_sub_menu_background')['rgba'];

		$mobile_sub_menu_overlay = Zikzag_Theme_Helper::get_option('mobile_sub_menu_overlay')['rgba'];

		$mobile_sub_menu_color = Zikzag_Theme_Helper::get_option('mobile_sub_menu_color');

		$rgb_h_font_color = Zikzag_Theme_Helper::HexToRGB($header_font_color);
		$rgb_primary_color = Zikzag_Theme_Helper::HexToRGB($theme_primary_color);
		$rgb_secondary_color = Zikzag_Theme_Helper::HexToRGB($theme_secondary_color);

		$footer_text_color = Zikzag_Theme_Helper::get_option('footer_text_color');
		$footer_heading_color = Zikzag_Theme_Helper::get_option('footer_heading_color');

		$copyright_text_color = Zikzag_Theme_Helper::options_compare('copyright_text_color','mb_copyright_switch','on');

		// Page Title Background Color
		$page_title_bg_color = Zikzag_Theme_Helper::get_option('page_title_bg_color');
		$hex_page_title_bg_color = Zikzag_Theme_Helper::HexToRGB($page_title_bg_color);

		/*-----------------------------------------------------------------------------------*/
		/* Side Panel Css
		/*-----------------------------------------------------------------------------------*/
		$side_panel_title = Zikzag_Theme_Helper::get_option('side_panel_title_color')['rgba'] ?? '';

		if (class_exists('RWMB_Loader') && get_queried_object_id() !== 0) {
			$side_panel_switch = rwmb_meta('mb_customize_side_panel');
			if ($side_panel_switch === 'custom') {
				$side_panel_title = rwmb_meta('mb_side_panel_title_color');
			}
		}
		/*-----------------------------------------------------------------------------------*/
		/* \End Side Panel CSS
		/*-----------------------------------------------------------------------------------*/

		/*-----------------------------------------------------------------------------------*/
		/* Parse CSS
		/*-----------------------------------------------------------------------------------*/
		global $wp_filesystem;
		if (empty( $wp_filesystem )) {
			require_once( ABSPATH .'/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$files = array('theme_content', 'theme_color', 'footer');
		if (class_exists( 'WooCommerce' )) {
			array_push( $files, 'shop');
		}
		foreach ( $files as $key => $file) {
			$file = get_theme_file_path( '/core/admin/css/dynamic/'.$file.'.css' );
			if ($wp_filesystem->exists( $file)) {
				$file = $wp_filesystem->get_contents( $file );
				preg_match_all('/\s*\\$([A-Za-z1-9_\-]+)(\s*:\s*(.*?);)?\s*/', $file, $vars);

				$found     = $vars[0];
				$varNames  = $vars[1];
				$count     = count( $found);

				for( $i = 0; $i < $count; $i++) {
					$varName  = trim( $varNames[$i]);
					$file = preg_replace('/\\$'.$varName.'(\W|\z)/', (isset( ${$varName}) ? ${$varName} : "").'\\1', $file);
				}

				$line = str_replace( $found, '', $file);

				$css .= $line;
			}
		}
		/*-----------------------------------------------------------------------------------*/
		/* \End Parse css
		/*-----------------------------------------------------------------------------------*/

		$css .= 'body {'
			.(!empty( $bg_body) ? 'background:'.$bg_body.';' : '').'
		}
		ol.commentlist:after {
			'.(!empty( $bg_body) ? 'background:'.$bg_body.';' : '').'
		}';

		/*-----------------------------------------------------------------------------------*/
		/* Typography render
		/*-----------------------------------------------------------------------------------*/
		for ($i = 1; $i <= 6; $i++) {
			$css .= 'h'.$i.',h'.$i.' a, h'.$i.' span {
				'.(!empty( ${'header-h'.$i.'_family'}) ? 'font-family:'.${'header-h'.$i.'_family'}.';' : '' ).'
				'.(!empty( ${'header-h'.$i.'_weight'}) ? 'font-weight:'.${'header-h'.$i.'_weight'}.';' : '' ).'
				'.(!empty( ${'header-h'.$i.'_size'}) ? 'font-size:'.${'header-h'.$i.'_size'}.';' : '' ).'
				'.(!empty( ${'header-h'.$i.'_line_height'}) ? 'line-height:'.${'header-h'.$i.'_line_height'}.';' : '' ).'
				'.(!empty( ${'header-h'.$i.'_text_transform'}) ? 'text-transform:'.${'header-h'.$i.'_text_transform'}.';' : '' ).'
			}';
		}
		/*-----------------------------------------------------------------------------------*/
		/* \End Typography render
		/*-----------------------------------------------------------------------------------*/

		/*-----------------------------------------------------------------------------------*/
		/* Mobile Header render
		/*-----------------------------------------------------------------------------------*/
		$mobile_header = Zikzag_Theme_Helper::get_option('mobile_header');

		// Fetch mobile header height to apply it for mobile styles
		$header_mobile_height = Zikzag_Theme_Helper::get_option('header_mobile_height');
		$header_mobile_min_height = !empty($header_mobile_height['height']) ? 'calc(100vh - '.esc_attr((int)$header_mobile_height['height']).'px - 30px)' : '';
		$header_mobile_height = !empty($header_mobile_height['height']) ? 'calc(100vh - '.esc_attr((int)$header_mobile_height['height']).'px)' : '';

		// Set Queries width to apply mobile style

		$header_queries = Zikzag_Theme_Helper::get_option('header_mobile_queris');

        if ($this->header_type === 'custom'
            && $this->header_page_select_id
            && did_action('elementor/loaded')
        ) {
            //* Page settings manager
            $page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers('page');
            //* Settings model of header post
            $page_settings_model = $page_settings_manager->get_model($this->header_page_select_id);
            //* Mobile header breakpoint
            $header_queries = $page_settings_model->get_settings('mobile_breakpoint') ?? $header_queries;
        }

		$mobile_over_content = Zikzag_Theme_Helper::get_option('mobile_over_content');
		$mobile_sticky = Zikzag_Theme_Helper::get_option('mobile_sticky');

		if ($mobile_header == '1') {
			$mobile_background = Zikzag_Theme_Helper::get_option('mobile_background')['rgba'] ?? '';
			$mobile_color = Zikzag_Theme_Helper::get_option('mobile_color');

			$css .= '@media only screen and (max-width: '.(int)$header_queries.'px) {
				.wgl-theme-header{
					background-color: '.esc_attr($mobile_background).' !important;
					color: '.esc_attr($mobile_color).' !important;
				}
				.hamburger-inner, .hamburger-inner:before, .hamburger-inner:after{
					background-color:'.esc_attr($mobile_color).';
				}
			}';
		}

		$css .= '@media only screen and (max-width: '.(int)$header_queries.'px) {
			.wgl-theme-header .wgl-mobile-header {
				display: block;
			}
			.wgl-site-header,
			.wgl-theme-header .primary-nav {
				display: none;
			}
			.wgl-theme-header .mobile-hamburger-toggle {
				display: inline-block;
			}
			header.wgl-theme-header .mobile_nav_wrapper .primary-nav {
				display: block;
			}
			.wgl-theme-header .wgl-sticky-header {
				display: none;
			}
			.wgl-social-share_pages {
				display: none;
			}
		}';

		if ($mobile_over_content == '1') {
			$css .= '@media only screen and (max-width: '.(int)$header_queries.'px) {
				.wgl-theme-header{
					position: absolute;
					z-index: 99;
					width: 100%;
					left: 0;
					top: 0;
				}
			}';
			if ($mobile_sticky == '1') {
				$css .= '@media only screen and (max-width: '.(int)$header_queries.'px) {
					body .wgl-theme-header .wgl-mobile-header{
						position: absolute;
						left: 0;
						width: 100%;
					}
				}';
			}
		} else {
			$css .= '@media only screen and (max-width: '.(int)$header_queries.'px) {
				body .wgl-theme-header.header_overlap{
					position: relative;
					z-index: 2;
				}
			}';
		}

		if ($mobile_sticky == '1') {
            $css .= '@media only screen and (max-width: ' . (int) $header_queries . 'px) {
                body .wgl-theme-header,
                body .wgl-theme-header.header_overlap {
                    position: sticky;
                    top: 0;
                }
                .admin-bar .wgl-theme-header {
                    top: 32px;
                }
                body.mobile_switch_on {
                    position: static;
                }
                body.admin-bar .sticky_mobile .wgl-menu_outer{
                    top: 0px;
                    height: 100vh;
                }
            }';
        }
		/*-----------------------------------------------------------------------------------*/
		/* \End Mobile Header render
		/*-----------------------------------------------------------------------------------*/

		/*-----------------------------------------------------------------------------------*/
		/* Page Title Responsive
		/*-----------------------------------------------------------------------------------*/
		$page_title_resp = Zikzag_Theme_Helper::get_option('page_title_resp_switch');
		$mb_cond_logic = false;

		if (class_exists('RWMB_Loader') && 0 !== $id) {
			$mb_cond_logic = rwmb_meta('mb_page_title_switch') == 'on' && rwmb_meta('mb_page_title_resp_switch') == '1' ? '1' : '';

			if (rwmb_meta('mb_page_title_switch') == 'on') {
				if (rwmb_meta('mb_page_title_resp_switch') == '1') {
					$page_title_resp = '1';
				}
			}
		}

		if ($page_title_resp == '1') {

			$page_title_height = Zikzag_Theme_Helper::get_option('page_title_resp_height');
			$page_title_height = $page_title_height['height'];

			$page_title_queries = Zikzag_Theme_Helper::options_compare('page_title_resp_resolution', 'mb_page_title_resp_switch', $mb_cond_logic);

			$page_title_padding = Zikzag_Theme_Helper::options_compare('page_title_resp_padding', 'mb_page_title_resp_switch', $mb_cond_logic);

			if ($mb_cond_logic == '1') {
				$page_title_height = rwmb_meta('mb_page_title_resp_height');
			}

			$page_title_font = Zikzag_Theme_Helper::options_compare('page_title_resp_font', 'mb_page_title_resp_switch', $mb_cond_logic);
			$page_title_breadcrumbs_font = Zikzag_Theme_Helper::options_compare('page_title_resp_breadcrumbs_font', 'mb_page_title_resp_switch', $mb_cond_logic);
			$page_title_breadcrumbs_switch = Zikzag_Theme_Helper::options_compare('page_title_resp_breadcrumbs_switch', 'mb_page_title_resp_switch', $mb_cond_logic);

			// Title styles
			$page_title_font_color = !empty( $page_title_font['color']) ? 'color:'.esc_attr($page_title_font['color'] ).' !important;' : '';
			$page_title_font_size = !empty( $page_title_font['font-size']) ? 'font-size:'.esc_attr( (int)$page_title_font['font-size'] ).'px !important;' : '';
			$page_title_font_height = !empty( $page_title_font['line-height']) ? 'line-height:'.esc_attr( (int)$page_title_font['line-height'] ).'px !important;' : '';
			$page_title_additional_style = !(bool)$page_title_breadcrumbs_switch ? 'margin-bottom: 0 !important;' : '';

			$title_style = $page_title_font_color.$page_title_font_size.$page_title_font_height.$page_title_additional_style;

			// Breadcrumbs Styles
			$page_title_breadcrumbs_font_color = !empty( $page_title_breadcrumbs_font['color']) ? 'color:'.esc_attr($page_title_breadcrumbs_font['color'] ).' !important;' : '';
			$page_title_breadcrumbs_font_size = !empty( $page_title_breadcrumbs_font['font-size']) ? 'font-size:'.esc_attr( (int) $page_title_breadcrumbs_font['font-size']).'px !important;' : '';
			$page_title_breadcrumbs_font_height = !empty( $page_title_breadcrumbs_font['line-height']) ? 'line-height:'.esc_attr( (int) $page_title_breadcrumbs_font['line-height'] ).'px !important;' : '';

			$page_title_breadcrumbs_display = !(bool)$page_title_breadcrumbs_switch ? 'display: none !important;' : '';

			$breadcrumbs_style = $page_title_breadcrumbs_font_color.$page_title_breadcrumbs_font_size.$page_title_breadcrumbs_font_height.$page_title_breadcrumbs_display;

			$css .= '@media only screen and (max-width: '.(int)$page_title_queries.'px) {
				.page-header{
					'.( isset( $page_title_padding['padding-top']) && !empty( $page_title_padding['padding-top']) ? 'padding-top:'.esc_attr( (int) $page_title_padding['padding-top'] ).'px !important;' : '' ).'
					'.( isset( $page_title_padding['padding-bottom']) && !empty( $page_title_padding['padding-bottom']) ? 'padding-bottom:'.esc_attr( (int) $page_title_padding['padding-bottom'] ).'px  !important;' : '' ).'
					'.( isset( $page_title_height) && !empty( $page_title_height) ? 'height:'.esc_attr( (int) $page_title_height ).'px !important;' : '' ).'
				}
				.page-header_content .page-header_title{
					'.(isset( $title_style) && !empty( $title_style) ? $title_style : '').'
				}

				.page-header_content .page-header_breadcrumbs{
					'.(isset( $breadcrumbs_style) && !empty( $breadcrumbs_style) ? $breadcrumbs_style : '').'
				}

			}';
		}
		/*-----------------------------------------------------------------------------------*/
		/* \End Page Title Responsive
		/*-----------------------------------------------------------------------------------*/

		/*-----------------------------------------------------------------------------------*/
		/* Portfolio Single Responsive
		/*-----------------------------------------------------------------------------------*/
		$portfolio_resp = Zikzag_Theme_Helper::get_option('portfolio_single_resp');
		$mb_cond_logic_pf = false;

		if (class_exists('RWMB_Loader') && 0 !== $id) {

			$mb_cond_logic_pf = rwmb_meta('mb_portfolio_post_conditional') == 'custom' && rwmb_meta('mb_portfolio_single_resp') == '1' ? '1' : '';

			if (rwmb_meta('mb_portfolio_post_conditional') == 'custom') {
				if (rwmb_meta('mb_portfolio_single_resp') == '1') {
					$portfolio_resp = '1';
				}
			}
		}

		if ($portfolio_resp == '1') {

			$pf_queries = Zikzag_Theme_Helper::options_compare('portfolio_single_resp_breakpoint', 'mb_portfolio_single_resp', $mb_cond_logic_pf);

			$pf_padding = Zikzag_Theme_Helper::options_compare('portfolio_single_resp_padding', 'mb_portfolio_single_resp', $mb_cond_logic_pf);

			$css .= '@media only screen and (max-width: '.esc_attr( (int)$pf_queries ).'px) {
				.wgl-portfolio-single_wrapper.single_type-3 .wgl-portfolio-item_bg .wgl-portfolio-item_title_wrap,
				.wgl-portfolio-single_wrapper.single_type-4 .wgl-portfolio-item_bg .wgl-portfolio-item_title_wrap{
					'.( isset( $pf_padding['padding-top']) && !empty( $pf_padding['padding-top']) ? 'padding-top:'.esc_attr( (int) $pf_padding['padding-top'] ).'px !important;' : '' ).'
					'.( isset( $pf_padding['padding-bottom']) && !empty( $pf_padding['padding-bottom']) ? 'padding-bottom:'. esc_attr( (int) $pf_padding['padding-bottom'] ).'px  !important;' : '' ).'
				}

			}';
		}
		/*-----------------------------------------------------------------------------------*/
		/* \End Portfolio Single Responsive
		/*-----------------------------------------------------------------------------------*/

		/*-----------------------------------------------------------------------------------*/
		/* Gradient css
		/*-----------------------------------------------------------------------------------*/

		require_once( get_theme_file_path('/core/admin/css/dynamic/gradient.php') );

		/*-----------------------------------------------------------------------------------*/
		/* \End Gradient css
		/*-----------------------------------------------------------------------------------*/


		/*-----------------------------------------------------------------------------------*/
		/* Elementor Theme css
		/*-----------------------------------------------------------------------------------*/

		if (did_action('elementor/loaded')) {

            if (defined('ELEMENTOR_VERSION')) {
                if (version_compare(ELEMENTOR_VERSION, '3.0', '<')) {
                    $container_width = get_option('elementor_container_width');
                    $container_width = !empty($container_width) ? $container_width : 1140;
                } else {
                    //* Page settings manager
                    $page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers('page');
                    $kit_id = (new \Elementor\Core\Kits\Manager())->get_active_id();

                    $meta_key = \Elementor\Core\Settings\Page\Manager::META_KEY;
                    $kit_settings = get_post_meta($kit_id, $meta_key, true);

                    if (!$kit_settings) {
                        $container_width = 1140;
                     } else {
                        $container_width = $kit_settings['container_width']['size'] ?? 1140;
                    }
                }
            }

			$css .= 'body.elementor-page main .wgl-container.wgl-content-sidebar,
			body.elementor-editor-active main .wgl-container.wgl-content-sidebar,
			body.elementor-editor-preview main .wgl-container.wgl-content-sidebar {
				max-width: ' . intval($container_width) . 'px;
				margin-left: auto;
				margin-right: auto;
			}';

			$css .= 'body.single main .wgl-container {
				max-width: ' . intval($container_width) . 'px;
				margin-left: auto;
				margin-right: auto;
			}';
		}

		/*-----------------------------------------------------------------------------------*/
		/* \End Elementor Theme css
		/*-----------------------------------------------------------------------------------*/

		/*-----------------------------------------------------------------------------------*/
		/* Add Inline css
		/*-----------------------------------------------------------------------------------*/

		if (function_exists('wgl_minify_css')) {
			$css = wgl_minify_css( $css );
		}

		wp_add_inline_style( 'zikzag-main', $css );

		/*-----------------------------------------------------------------------------------*/
		/* \End Add Inline css
		/*-----------------------------------------------------------------------------------*/
	}

	public function elementor_column_fix()
	{
        $css = '.elementor-column-gap-default > .elementor-column > .elementor-element-populated{
            padding-left: 15px;
            padding-right: 15px;
        }';

        wp_add_inline_style( 'elementor-frontend', $css );
    }
}

if (!function_exists('zikzag_dynamic_styles')) {
	function zikzag_dynamic_styles() {
		return Zikzag_Dynamic_Styles::instance();
	}
}

zikzag_dynamic_styles()->register_script();
zikzag_dynamic_styles()->init_style();
