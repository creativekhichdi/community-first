<?php

defined( 'ABSPATH' ) || exit;

use WglAddons\Templates\WglButton;


if (! class_exists('Zikzag_Get_Header')) {
	class Zikzag_Get_Header
	{
		protected $html_render = 'bottom';
		protected $id;
		protected $side_area_enabled;

		protected $header_type;
		protected $header_page_select_id;
		protected $header_sticky_page_select_id;

		private static $instance = null;

		public static function get_instance()
		{
			if (null == self::$instance) {
				self::$instance = new self( );
			}

			return self::$instance;
		}

		public function __construct() {

			$this->init();
		}

        public function header_vars()
        {
            $this->id = !is_archive() ? get_queried_object_id() : 0;

            /**
            * Header Template
            *
            *
            * @access public
            * @since 1.0.0
            */
            $this->header_type = Zikzag_Theme_Helper::get_option('header_type');

            if ($this->header_type === 'custom') {

                $header_page_select = Zikzag_Theme_Helper::get_option('header_page_select');

                if ($header_page_select) {
                    $this->header_page_select_id = intval($header_page_select);
                    $this->header_page_select_id = $this->multiLanguageSupport( $this->header_page_select_id );
                }
            }

            /**
            * Sticky Header Template
            *
            *
            * @access public
            * @since 1.0.0
            */

            if (Zikzag_Theme_Helper::options_compare('header_sticky', 'mb_customize_header_layout', 'custom') == '1') {
                $header_sticky_page_select = Zikzag_Theme_Helper::get_option('header_sticky_page_select');

                if (!empty($header_sticky_page_select)) {
                    $this->header_sticky_page_select_id = intval($header_sticky_page_select);
                    $this->header_sticky_page_select_id = $this->multiLanguageSupport( $this->header_sticky_page_select_id );
                }
            }

            // RWMB opions
            if (
                class_exists('RWMB_Loader')
                && $this->id !== 0
                && rwmb_meta('mb_customize_header_layout') == 'custom'
            ) {
                if (rwmb_meta('mb_header_content_type') !== 'default') {
                    $this->header_type = 'custom';
                    $this->header_page_select_id = (int) rwmb_meta('mb_customize_header');
                    $this->header_page_select_id = $this->multiLanguageSupport( $this->header_page_select_id );
                }

                if (rwmb_meta('mb_sticky_header_content_type') !== 'default') {
                    $this->header_sticky_page_select_id = (int) rwmb_meta('mb_customize_sticky_header');
                    $this->header_sticky_page_select_id = $this->multiLanguageSupport( $this->header_sticky_page_select_id );
                }
            }
        }

		public function init()
		{
			$this->header_vars();
			/**
			* Generate html header rendered
			*
			*
			* @since 1.0
			* @access public
			*/

			$this->require_components();
			$this->header_render_html();
		}

		public function require_components() {
			require_once ( get_theme_file_path( '/templates/header/components/logo.php' ) );
		}

		/**
        * Multi Language Support
        *
        *
        * @since 1.0
        * @access public
        */
        public function multiLanguageSupport($id)
        {

            if (class_exists('Polylang') && function_exists('pll_current_language')) {
                $currentLanguage = pll_current_language();
                $translations = PLL()->model->post->get_translations($id);

                $polylang_id = $translations[$currentLanguage] ?? '';
                $id = !empty($polylang_id) ? $polylang_id : $id;
            }

            if (class_exists('SitePress')) {
                $id = wpml_object_id_filter($id, 'header', false, ICL_LANGUAGE_CODE);
            }

            return $id;
        }

		/**
		* Generate header class
		*
		*
		* @since 1.0
		* @access public
		*/
		public function header_class()
		{
			$header_shadow = Zikzag_Theme_Helper::get_option( 'header_shadow');
			$header_on_bg = Zikzag_Theme_Helper::get_option('header_on_bg');
			$header_class = '';

			if ($this->header_type === 'custom') {

				if (! empty($this->header_page_select_id)) {

					if (did_action( 'elementor/loaded' )) {

						// Get the page settings manager
						$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );

						// Get the settings model for header post
						$page_settings_model = $page_settings_manager->get_model( $this->header_page_select_id );

						$header_on_bg = $page_settings_model->get_settings( 'header_on_bg' );
					}
				}
			} else {
				if ($header_shadow == '1') {
					$header_class .= ' header_shadow';
				}
			}

			$header_on_bg = 'posts' === get_option( 'show_on_front' ) && (is_home() || is_front_page()) ? false : $header_on_bg;
			// Build Header Class

			if (! empty($header_on_bg)) {
				$header_class .= ' header_overlap';
			}

			return $header_class;
		}

		/**
		* Generate header editor
		*
		*
		* @since 1.0
		* @access public
		*/

		public function header_bar_editor($location = null,$position = null)
		{
			if (! $position) return;

			/*
			 * Define Theme options and field configurations.
			*/

			${'header_'.$position.'_editor'} = Zikzag_Theme_Helper::get_option($location.'_header_bar_'.$position.'_editor');
			$html_render = ${'header_'.$position.'_editor'};
			// Header Bar HTML Editor render
			$html = "";
			if (! empty($html_render)) {
				$html .= "<div class='".esc_attr($location)."_header ".esc_attr($position)."_editor header_render_editor header_render'>";
					$html .= "<div class='wrapper'>";
							$html .= do_shortcode( $html_render );
					$html .= "</div>";
				$html .= "</div>";
			}

			return $html;
		}

		/**
		* Generate header delimiter
		*
		*
		* @since 1.0
		* @access public
		*/

		public function header_bar_delimiter($k = null)
		{
			if (! $k) return;

			/*
			* Define Theme options and field configurations.
			*/

			$get_number = (int) filter_var($k, FILTER_SANITIZE_NUMBER_INT);
			$height = Zikzag_Theme_Helper::get_option( 'bottom_header_delimiter'.$get_number.'_height');
			$width = Zikzag_Theme_Helper::get_option( 'bottom_header_delimiter'.$get_number.'_width');

			$bg_color = Zikzag_Theme_Helper::get_option( 'bottom_header_delimiter'.$get_number.'_bg');

			$margin = Zikzag_Theme_Helper::get_option( 'bottom_header_delimiter'.$get_number.'_margin');

			$margin_left = ! empty($margin['margin-left']) ? (int)$margin['margin-left'] : '';
			$margin_right = ! empty($margin['margin-right']) ? (int)$margin['margin-right'] : '';

			$custom_sticky = '';
			if ($this->html_render === 'sticky') {
				$custom_sticky = Zikzag_Theme_Helper::get_option('bottom_header_delimiter'.$get_number.'_sticky_custom');
				if (! empty($custom_sticky)) {
					$bg_color = Zikzag_Theme_Helper::get_option('bottom_header_delimiter'.$get_number.'_sticky_color');
					$height  = Zikzag_Theme_Helper::get_option('bottom_header_delimiter'.$get_number.'_sticky_height');
				}
			}

			// Header Bar Delimiter render
			$style = "";
			if (is_array($height)) {
				$style .= 'height: '.esc_attr((int) $height['height'] ).'px;';
			}

			if (is_array($width)) {
				$style .= 'width: '.esc_attr((int) $width['width'] ).'px;';
			}

			if (! empty($bg_color['rgba'])) {
				$style .= 'background-color: '.esc_attr($bg_color['rgba']).';';
			}

			if (! empty($margin_left)) {
				$style .= 'margin-left:'.esc_attr((int) $margin_left).'px;';
			}

			if (! empty($margin_right)) {
				$style .= 'margin-right:'.esc_attr((int) $margin_right).'px;';
			}

			echo '<div class="delimiter-wrapper"><div class="delimiter"'.(! empty($style) ? ' style="'.$style.'"' : '').'></div></div>';

		}

		/**
		* Generate header button
		*
		*
		* @since 1.0
		* @access public
		*/

		public function header_bar_button($k = null)
		{
			if (! $k) return;

			/*
			 * Define Theme options and field configurations.
			*/

			$get_number = (int) filter_var($k, FILTER_SANITIZE_NUMBER_INT);
			$button_text = Zikzag_Theme_Helper::get_option('bottom_header_button'.$get_number.'_title');

			$link = Zikzag_Theme_Helper::get_option('bottom_header_button'.$get_number.'_link');

			$target = Zikzag_Theme_Helper::get_option('bottom_header_button'.$get_number.'_target');

			$size = Zikzag_Theme_Helper::get_option('bottom_header_button'.$get_number.'_size');

			$options_btn = $this->html_render === 'sticky' ? '_sticky' : '';

			$customize = Zikzag_Theme_Helper::get_option('bottom_header_button'.$get_number.'_custom'.$options_btn);

			$customize = empty($customize) ? 'def' : 'color';

			$bg_color = Zikzag_Theme_Helper::get_option('bottom_header_button'.$get_number.'_bg'.$options_btn);

			$bg_color = isset($bg_color['rgba']) ? $bg_color['rgba'] : '';

			$text_color = Zikzag_Theme_Helper::get_option('bottom_header_button'.$get_number.'_color_txt'.$options_btn);

			$text_color = isset($text_color['rgba']) ? $text_color['rgba'] : '';

			$border_color = Zikzag_Theme_Helper::get_option('bottom_header_button'.$get_number.'_border'.$options_btn);
			$border_color = isset($border_color['rgba']) ? $border_color['rgba'] : '';

			$bg_color_hover = Zikzag_Theme_Helper::get_option('bottom_header_button'.$get_number.'_hover_bg'.$options_btn);
			$bg_color_hover = isset($bg_color_hover['rgba']) ? $bg_color_hover['rgba'] : '';

			$text_color_hover = Zikzag_Theme_Helper::get_option('bottom_header_button'.$get_number.'_hover_color_txt'.$options_btn);
			$text_color_hover = isset($text_color_hover['rgba']) ? $text_color_hover['rgba'] : '';

			$border_color_hover = Zikzag_Theme_Helper::get_option('bottom_header_button'.$get_number.'_hover_border'.$options_btn);
			$border_color_hover = isset($border_color_hover['rgba']) ? $border_color_hover['rgba'] : '';

			$border_radius = Zikzag_Theme_Helper::get_option('bottom_header_button'.$get_number.'_radius');
			switch ($size) {
				case 's': $size = 'sm'; break;
				default:
				case 'm': $size = 'md'; break;
				case 'l': $size = 'lg'; break;
				case 'xl': $size = 'xl'; break;
			}

			$button_css_id =  uniqid( "zikzag_button_" );

			$settings = [
				'text' => $button_text,
				'link' => [
					'url' => $link,
					'is_external' => $target,
					'nofollow' => '',
				],
				'size' => $size,
				'border_radius' => $border_radius,
				'button_css_id' => $button_css_id,
			];


			// Start Custom CSS
			$styles = '';
			ob_start();

				if ($customize != 'def') {
					if ($customize == 'color') {
						echo "#$button_css_id {
								  color: ".(! empty($text_color) ? esc_attr($text_color) : 'transparent').";
							  }";
						echo "#$button_css_id:hover {
								  color: ".(! empty($text_color_hover) ? esc_attr($text_color_hover) : 'transparent').";
							  }";
						$border_color = ! empty($border_color) ? esc_attr($border_color) : 'transparent';
						echo "#$button_css_id {
								  border-color: $border_color;
								  background-color: $bg_color;
							  }";
						echo "#$button_css_id:hover {
								  border-color: ".(! empty($border_color_hover) ? esc_attr($border_color_hover) : 'transparent').";
								  background-color: $bg_color_hover;
							  }";
					}
				}

			$styles .= ob_get_clean();

			// Register css
			if (! empty($styles)) {
				Zikzag_Theme_Helper::enqueue_css($styles);
			}

			unset($this->render_attributes);

			echo '<div class="header_button">';
				echo '<div class="wrapper">';

					$this->add_render_attribute( 'wrapper', 'class', 'elementor-button-wrapper' );

					if (! empty( $settings['link']['url'] )) {
						$this->add_render_attribute( 'button', 'href', $settings['link']['url'] );
						$this->add_render_attribute( 'button', 'class', 'elementor-button-link' );

						if ($settings['link']['is_external']) {
							$this->add_render_attribute( 'button', 'target', '_blank' );
						}

						if ($settings['link']['nofollow']) {
							$this->add_render_attribute( 'button', 'rel', 'nofollow' );
						}
					}

					$this->add_render_attribute( 'button', 'class', 'wgl-button elementor-button' );
					$this->add_render_attribute( 'button', 'role', 'button' );

					$this->add_render_attribute( 'button', 'id', $button_css_id );


					if (! empty( $settings['size'] )) {
						$this->add_render_attribute( 'button', 'class', 'elementor-size-' . $settings['size'] );
					}

					if (isset($settings['hover_animation'])) {
						$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['hover_animation'] );
					}

					if (isset($settings['border_radius'])) {
						$this->add_render_attribute( 'button', 'style',  'border-radius: '.( (int) esc_attr($settings['border_radius']) !== 0  ? (int) esc_attr($settings['border_radius']).'px' : '0px').';' );
					}

					?>
					<div <?php echo Zikzag_Theme_Helper::render_html($this->get_render_attribute_string( 'wrapper' )); ?>>
						<a <?php echo Zikzag_Theme_Helper::render_html($this->get_render_attribute_string( 'button' )); ?>>
							<?php $this->render_text($settings); ?>
						</a>
					</div>
					<?php

				echo '</div>';
			echo '</div>';
		}

		public function render_text($settings) {
			$settings_icon_align = isset($settings['icon_align']) ? $settings['icon_align'] : '';

			$this->add_render_attribute( [
				'content-wrapper' => [
					'class' => [
						'elementor-button-content-wrapper',
						'elementor-align-icon-' . $settings_icon_align,
					]
				],
				'wrapper' => [
					'class' => 'elementor-button-icon',
				],
				'text' => [
					'class' => 'elementor-button-text',
				],
			] );

			?>
			<span <?php echo Zikzag_Theme_Helper::render_html($this->get_render_attribute_string( 'content-wrapper' )); ?>>
				<span <?php echo Zikzag_Theme_Helper::render_html($this->get_render_attribute_string( 'text' )); ?>><?php echo Zikzag_Theme_Helper::render_html($settings['text']); ?></span>
			</span>
			<?php
		}


		/**
			 * Add render attribute.
			 *
			 * Used to add attributes to a specific HTML element.
			 *
			 * The HTML tag is represented by the element parameter, then you need to
			 * define the attribute key and the attribute key. The final result will be:
			 * `<element attribute_key="attribute_value">`.
			 *
			 * Example usage:
			 *
			 * `$this->add_render_attribute( 'wrapper', 'class', 'custom-widget-wrapper-class' );`
			 * `$this->add_render_attribute( 'widget', 'id', 'custom-widget-id' );`
			 * `$this->add_render_attribute( 'button', [ 'class' => 'custom-button-class', 'id' => 'custom-button-id' ] );`
			 *
			 * @since 1.0.0
			 * @access public
			 *
			 * @param array|string $element   The HTML element.
			 * @param array|string $key       Optional. Attribute key. Default is null.
			 * @param array|string $value     Optional. Attribute value. Default is null.
			 * @param bool         $overwrite Optional. Whether to overwrite existing
			 *                                attribute. Default is false, not to overwrite.
			 *
			 * @return Element_Base Current instance of the element.
			 */
		public function add_render_attribute( $element, $key = null, $value = null, $overwrite = false) {
			if (is_array( $element )) {
				foreach ( $element as $element_key => $attributes) {
					$this->add_render_attribute( $element_key, $attributes, null, $overwrite );
				}

				return $this;
			}

			if (is_array( $key )) {
				foreach ( $key as $attribute_key => $attributes) {
					$this->add_render_attribute( $element, $attribute_key, $attributes, $overwrite );
				}

				return $this;
			}

			if (empty( $this->render_attributes[ $element ][ $key ] )) {
				$this->render_attributes[ $element ][ $key ] = [];
			}

			settype( $value, 'array' );

			if ($overwrite) {
				$this->render_attributes[ $element ][ $key ] = $value;
			} else {
				$this->render_attributes[ $element ][ $key ] = array_merge( $this->render_attributes[ $element ][ $key ], $value );
			}

			return $this;
		}

		public function get_render_attribute_string( $element) {
			if (empty( $this->render_attributes[ $element ] )) {
				return '';
			}

			return Zikzag_Theme_Helper::render_html_attributes( $this->render_attributes[ $element ] );
		}

		/**
		* Generate header spacer
		*
		*
		* @since 1.0
		* @access public
		*/

		public function header_bar_spacer($location = null, $key = null) {
			if (!$key)
				return;

			/*
			 * Define Theme options and field configurations.
			*/

			$get_number = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT);
			$spacer = Zikzag_Theme_Helper::get_option($location.'_header_spacer'.$get_number);
			// Header Bar Spacer render
			$html = "";
			if (is_array($spacer)) {
				$html .= "<div class='header_spacing spacer_".$get_number."' style='width:".esc_attr( (int) $spacer['width'] )."px;'>";
				$html .= "</div>";
			}

			return $html;
		}

		public function header_bar_spacer_height($location = null, $key = null) {
			if (!$key)
				return;

			/*
			 * Define Theme options and field configurations.
			*/

			$get_number = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT);
			$spacer = Zikzag_Theme_Helper::get_option($location.'_header_spacer'.$get_number);
			// Header Bar Spacer render
			$html = "";
			if (is_array($spacer)) {
				$html .= "<div class='header_spacing spacer_".$get_number."' style='display:block;height:".esc_attr( (int) $spacer['width'] )."px;'>";
				$html .= "</div>";
			}

			return $html;
		}

		/**
		* Generate header builder layout
		*
		*
		* @since 1.0
		* @access public
		*/
		public function build_header_layout( $section = 'bottom') {

            $sticky = '';

            if (
                $this->html_render == 'sticky'
            ) {
                if (!empty($this->header_sticky_page_select_id)) {
                    $sticky = '_sticky';
                    $this->header_type = 'custom';
                }
                $section = 'bottom';
            }

            if ($this->header_type === 'custom' && $section === 'bottom') {
                require_once get_theme_file_path('/templates/header/elementor-builder/header-builder' . $sticky . '.php');
            } else {
                $this->header_default($section);
            }
		}

		public function header_default( $section = 'bottom') {

			$header_layout = Zikzag_Theme_Helper::get_option($section.'_header_layout');
			$lavalamp_active = Zikzag_Theme_Helper::get_option('lavalamp_active');

			// Get item from recycle bin
			$j =0;
			$header_layout_top = $header_layout_middle = $header_layout_bottom = [];

			// Build Row Item
			$counter = 1;
			if ($section == 'bottom') {
				$header_layout = array_slice($header_layout, 1);
				$count = count($header_layout);
				$half = 3;
				for($i = 0 ;$i<3;$i++) {
					switch ($i) {
						case 0:
							$header_layout_top = array_slice($header_layout, $j, $half);
							break;
						case 1:
							$header_layout_middle = array_slice($header_layout, $j, $half);
							break;
						case 2:
							$header_layout_bottom = array_slice($header_layout, $j, $half);
							break;
					}

					$j = $j+$half;
				}

				// wgl Header Builder Row
				$counter = 3;
			}

			/**
			* Generate sticky builder(default)
			*/
			$inc_sticky = 0;
			$sticky_present_element = false;
			$sticky_last_row = '';
			$sticky_key_last_row = [];

			for ($i=1; $i <= $counter; $i++) {
				if ($section == 'bottom') {
					switch ($i) {
						case 1:
							$sticky_loc = '_top';
							break;
						case 2:
							$sticky_loc = '_middle';
							break;
						case 3:
							$sticky_loc = '_bottom';
							break;
					}
					$sticky_header_layout = ${"header_layout" . $sticky_loc};

					//Disabled Sticky Options
					$disabled_sticky = false;
					foreach ($sticky_header_layout as $s => $d) {
						if (isset($sticky_header_layout[$s]['disable_row']) && $sticky_header_layout[$s]['disable_row'] == 'true') {
							$disabled_sticky = true;
							continue;
						}
					}
					if (!$disabled_sticky) {
						foreach ($sticky_header_layout as $key => $v) {
							if (isset($sticky_header_layout[$key]['disable_row'])) {
								unset($sticky_header_layout[$key]['disable_row']);
							}
							if (count($sticky_header_layout[$key]) == 1 && empty($sticky_header_layout[$key]['placebo']) || count($sticky_header_layout[$key]) > 1) {
								$sticky_present_element = true;
								$sticky_key_last_row[] = $key;
							}
						}
					}

				} else {
					$sticky_present_element = true;
				}

				if (! empty($sticky_header_layout)) {
					if ($sticky_present_element && $this->html_render == 'sticky') {
						$inc_sticky++;
						$sticky_present_element = false;
					}
				}
			}

			if (is_array($sticky_key_last_row)) {
				$last_element = end($sticky_key_last_row);
				if ($last_element) {
					switch ($last_element) {
						case array_key_exists($last_element, $header_layout_top):
							$sticky_last_row = '_top';
							break;
						case array_key_exists($last_element, $header_layout_middle):
							$sticky_last_row = '_middle';
							break;
						case array_key_exists($last_element, $header_layout_bottom):
							$sticky_last_row = '_bottom';
							break;
					}
				}
			}
			/**
			* End Generate sticky builder(default)
			*/

			$location = '';
			$has_element = false;

			$counter = $inc_sticky > 1  ? 1 : $counter;

			for ($i=1; $i <= $counter; $i++) {
				if ($section == 'bottom') {
					switch ($i) {
						case 1: $location = '_top'; break;
						case 2: $location = '_middle'; break;
						case 3: $location = '_bottom'; break;
					}

					if ($inc_sticky > 1) {
						$location = $sticky_last_row;
					}

					$header_layout = ${"header_layout" . $location};

					//Disabled Row Options
					$disabled_row = false;
					foreach ($header_layout as $s => $d) {
						if (isset($header_layout[$s]['disable_row']) && $header_layout[$s]['disable_row'] == 'true') {
							$disabled_row = true;
							continue;
						}
					}

					if (!$disabled_row) {
						foreach ($header_layout as $key => $v) {
							if (isset($header_layout[$key]['disable_row'])) {
								unset($header_layout[$key]['disable_row']);
							}
							if (count($header_layout[$key]) == 1 && empty($header_layout[$key]['placebo']) || count($header_layout[$key]) > 1) {
								$has_element = true;
							}
						}
					}

				} else {
					$has_element = true;
				}

				if (! empty($header_layout)) {
					if ($has_element) {
						switch ($section) {
							case 'mobile_content':
								foreach ($header_layout as $part => $value) {
									if (! empty($header_layout[$part]) && $part != 'items') {

										if (count($header_layout[$part]) == 1 && empty($header_layout[$part]['placebo']) || count($header_layout[$part]) > 1) {
											foreach ($header_layout[$part] as $key => $value) {
												if ($key != 'placebo') {
													switch ($key) {
														case 'item_search':
															echo "<div class='header_search search_mobile_menu'>";
																echo "<div class='header_search-field'>";
																	get_search_form();
																echo "</div>";
															echo "</div>";
															break;

				    									case 'logo':
					    									$logo_render = '';

					    									if($this->header_type === 'custom'){
					    										if (!empty($this->header_page_select_id)) {

					    											if ( did_action( 'elementor/loaded' ) ) {

																					// Get the page settings manager
					    												$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );

																					// Get the settings model for header post
					    												$page_settings_model = $page_settings_manager->get_model( $this->header_page_select_id );


					    												$use_custom_logo = $page_settings_model->get_settings( 'use_custom_menu_logo' );

					    												$custom_logo = $page_settings_model->get_settings( 'custom_menu_logo' );
					    												$enable_logo_height = $page_settings_model->get_settings( 'enable_menu_logo_height' );
					    												$logo_height = $page_settings_model->get_settings( 'menu_logo_height' );

					    												$custom_size = $logo = false;

					    												if(!empty($use_custom_logo)){
					    													$logo = $custom_logo;

					    													if(!empty($enable_logo_height)){
					    														if(!empty($logo_height)){
					    															$custom_size = $logo_height;
					    														}
					    													}
					    												}

					    												$custom_logo_render = !empty($logo) ? 'bottom' : $this->html_render;
					    												$logo_condition = !empty($logo) ? false : true;
					    												new Zikzag_Get_Logo( $custom_logo_render, $logo_condition, $logo, $custom_size );
					    											}
					    										}

					    									}else{
															    $logo_render = self::get_logo($this->html_render, true);
					    									}

					    									echo !empty($logo_render) ? $logo_render : '';
				    									break;

														case 'menu':
															$menu = '';
															$custom_mobile_menu = Zikzag_Theme_Helper::get_option('custom_mobile_menu');

															if(!empty($custom_mobile_menu)){
																$menu = Zikzag_Theme_Helper::get_option('mobile_menu');
															}

															if (class_exists( 'RWMB_Loader' ) && $this->id !== 0) {
																if (rwmb_meta('mb_customize_header_layout') == 'custom') {
																	if($this->html_render === 'mobile'){
																		$menu = rwmb_meta('mb_mobile_menu_header');
																	}
																}
															}

															if (has_nav_menu( 'main_menu' )) {
																echo "<nav class='primary-nav'>";
																	zikzag_main_menu($menu);
																echo "</nav>";
															}
															break;

														case stripos($key,'html') !== false:
															$this_header_bar_editor = $this->header_bar_editor('mobile',$key);
															echo ! empty($this_header_bar_editor) ? $this->header_bar_editor('mobile',$key)  : '';
															break;

														case 'wpml':
															if (class_exists('SitePress')) {
																echo "<div class='sitepress_container'>";
																do_action('wpml_add_language_selector');
																echo "</div>";
															}
															break;

														case stripos($key,'spacer') !== false:
															$this_header_bar_spacer = $this->header_bar_spacer_height('mobile',$key);
															echo ! empty($this_header_bar_spacer) ? $this->header_bar_spacer_height('mobile',$key)  : '';
															break;
													}
												}
											}
										}
									}
								}
								break;
							default:
								echo '<div class="wgl-header-row wgl-header-row-section'.esc_attr($location).'"'.$this->row_style_color($location).'>';
								  echo '<div class="'.esc_attr($this->row_width_class($location, $section)).'">';
									echo '<div class="wgl-header-row_wrapper"'.$this->row_style_height($location).'>';
										foreach ($header_layout as $part => $value) {
											if (! empty($header_layout[$part]) && $part != 'items') {

												$area_name = '';
												switch ($part) {
													case stripos($part,'center') !== false: $area_name = 'center'; break;
													case stripos($part,'left') !== false: $area_name = 'left'; break;
													case stripos($part,'right') !== false: $area_name = 'right'; break;
												}
												$column_class  = $this->column_class($location, $area_name);

												$class_area = 'position_'.$area_name.$location;

												echo "<div class='".esc_attr(sanitize_html_class($class_area))." header_side".esc_attr($column_class)."'>";

												if (count($header_layout[$part]) == 1 && empty($header_layout[$part]['placebo']) || count($header_layout[$part]) > 1) {
													echo "<div class='header_area_container'>";
													foreach ($header_layout[$part] as $key => $value) {
														if ($key != 'placebo' && $key != 'pos_column') {
															switch ($key) {
																case 'item_search':
																	$this->search($this->html_render,$location);
																	break;

																case 'cart':
																	if (class_exists( 'WooCommerce' ))
																	$this->cart($location, $section);
																	break;

																case 'login':
																	if (class_exists( 'WooCommerce' ))
																	$this->login_in($location, $section);
																	break;

																case 'side_panel':
																	$this->side_panel_enabled = true;
																	$this->get_side_panel($location, $section);
																	break;

																case 'logo':

									    							$logo_render = '';

														        	if($this->header_type === 'custom'){
														        		if (!empty($this->header_page_select_id)) {

														        			if ( did_action( 'elementor/loaded' ) ) {

																					// Get the page settings manager
														        				$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );

																					// Get the settings model for header post
														        				$page_settings_model = $page_settings_manager->get_model( $this->header_page_select_id );


														        				$use_custom_logo = $page_settings_model->get_settings( 'use_custom_logo' );

														        				$custom_logo = $page_settings_model->get_settings( 'custom_logo' );
														        				$enable_logo_height = $page_settings_model->get_settings( 'enable_logo_height' );
														        				$logo_height = $page_settings_model->get_settings( 'logo_height' );

														        				$custom_size = $logo = false;

														        				if(!empty($use_custom_logo)){
														        					$logo = $custom_logo;

														        					if(!empty($enable_logo_height)){
														        						if(!empty($logo_height)){
														        							$custom_size = $logo_height;
														        						}
														        					}
														        				}

														        				$custom_logo_render = !empty($logo) ? 'bottom' : $this->html_render;
														        				new Zikzag_Get_Logo( $custom_logo_render, false, $logo, $custom_size );
														        			}
														        		}

														        	}else{
														        		$logo_render = self::get_logo($this->html_render);
														        	}

									    							echo !empty($logo_render) ? $logo_render : '';
									    								break;

																case 'menu':
																	$menu = '';
																	if (class_exists( 'RWMB_Loader' ) && $this->id !== 0) {
																		if (rwmb_meta('mb_customize_header_layout') == 'custom') {
																			$menu = rwmb_meta('mb_menu_header');
																		}
																	}
																	if (has_nav_menu( 'main_menu' )) {
																		echo "<nav class='primary-nav".($lavalamp_active == '1' ? ' menu_line_enable' : '')."' ".$this->row_style_height($location).">";
																			zikzag_main_menu ($menu);
																		echo "</nav>";
																		echo '<div class="mobile-hamburger-toggle"><div class="hamburger-box"><div class="hamburger-inner"></div></div></div>';
																	}
																	break;

																case stripos($key,'html') !== false:
																	$this_header_bar_editor = $this->header_bar_editor($section,$key);
																	echo ! empty($this_header_bar_editor) ? $this->header_bar_editor($section,$key)  : '';
																	break;

																case 'wpml':
																	if (class_exists('SitePress')) {
																		echo "<div class='sitepress_container' ".$this->row_style_height($location).">";
																		do_action('wpml_add_language_selector');
																		echo "</div>";
																	}
																	break;

																case stripos($key,'delimiter') !== false:
																	$this->header_bar_delimiter($key);
																	break;

																case stripos($key,'button') !== false:
																	$this->header_bar_button($key);
																	break;

																case stripos($key,'spacer') !== false:
																	$this_header_bar_spacer = $this->header_bar_spacer($section,$key);
																	echo ! empty($this_header_bar_spacer) ? $this->header_bar_spacer($section,$key)  : '';
																	break;
															}
														}
													}
													echo "</div>";
												}
												echo "</div>";
											}
										}
									echo '</div>';
								  echo '</div>';
								echo '</div>';
							break;
						}

						$has_element = false;
					}
				}
			}
		}

		private function row_width_class($s = '_middle', $section = 'bottom')
		{
			/**
			* Loop Header Row Style Color
			*
			*
			* @since 1.0
			* @access private
			*/
			$class = '';


			switch ($section) {
				case 'bottom':
					$width_container = Zikzag_Theme_Helper::get_option('header'.$s.'_full_width');
					if ($width_container == '1') {
						$class = "fullwidth-wrapper";
					} else {
						$class = 'wgl-container';
					}
					break;

				case 'sticky':
					$width_container = Zikzag_Theme_Helper::get_option('header_custom_sticky_full_width');
					if ($width_container == '1') {
						$class = "fullwidth-wrapper";
					} else {
						$class = 'wgl-container';
					}
					break;

				default:
					$class = 'wgl-container';
					break;
			}

			return $class;
		}

		private function row_style_color($s = '_middle')
		{
			$style = '';

			switch ($this->html_render) {
				case 'bottom':
				case 'sticky':
					$header_background = Zikzag_Theme_Helper::get_option('header'.$s.'_background');
					$header_background_image = Zikzag_Theme_Helper::get_option('header'.$s.'_background_image');
					$header_background_image = isset($header_background_image['url']) ? $header_background_image['url'] : '';

					$header_color = Zikzag_Theme_Helper::get_option('header'.$s.'_color');
					$header_bottom_border = Zikzag_Theme_Helper::get_option('header'.$s.'_bottom_border');
					$header_border_height = Zikzag_Theme_Helper::get_option('header'.$s.'_border_height');
					$header_border_height = $header_border_height['height'] ?? '';
					$header_bottom_border_color = Zikzag_Theme_Helper::get_option('header'.$s.'_bottom_border_color');

					$style = '';
					if (! empty($header_background['rgba'])) {
						$style .= ! empty($header_background['rgba']) ? 'background-color: '.esc_attr($header_background['rgba']).';' : '';
					}

					if (! empty($header_background_image)) {
						$style .= 'background-size:cover;background-repeat:no-repeat; background-image:url('.esc_attr($header_background_image).');';
					}

					if (! empty($header_bottom_border)) {
						$style .= ! empty($header_border_height) ? 'border-bottom-width: '.(int) (esc_attr($header_border_height)).'px;' : '';
						if (! empty($header_bottom_border_color['rgba'])) {
							$style .= 'border-bottom-color: '.esc_attr($header_bottom_border_color['rgba']).';';
						}

						$style .= 'border-bottom-style: solid;';
					}
					if (! empty($header_color['rgba'])) {
						$style .= ! empty($header_color['rgba']) ? 'color: '.esc_attr($header_color['rgba']).';' : '';
					}

					$customize_width = Zikzag_Theme_Helper::get_option('header'.$s.'_max_width_custom');

					if ($customize_width == '1') {
						$max_width = Zikzag_Theme_Helper::get_option( 'header'.$s.'_max_width');
						$max_width = $max_width['width'];

						$style .= 'max-width: '.esc_attr((int) $max_width ).'px; margin-left: auto; margin-right: auto;';
					}

					break;
			}

			$style = ! empty($style) ? ' style="'.$style.'"' : '';

			return $style;
		}

		private function row_style_height($s = '_middle')
		{
			/**
			* Loop Row Style Height
			*
			*
			* @since 1.0
			* @access private
			*/

			$header_mobile_height = Zikzag_Theme_Helper::get_option('header_mobile_height')['height'] ?? false;
            $header_height = Zikzag_Theme_Helper::get_option('header'.$s.'_height')['height'] ?? false;


			$style = '';

			switch ($this->html_render) {
				case 'mobile':
					if ($header_mobile_height) {
						$style = 'height: '. (int) esc_attr($header_mobile_height) .'px;';
						$style = ' style="'. $style .'"';
					}

					break;

				default:
					if ($header_height) {
						$style = 'height:'.(int) esc_attr($header_height).'px;';
						$style = ' style="'.$style.'"';
					}

					break;
			}

			return $style;
		}
		protected function side_panel_style_icon()
		{
			$sticky_icon_switcher = Zikzag_Theme_Helper::get_option('bottom_header_side_panel_sticky_custom');

			$value = '';
			if ($sticky_icon_switcher === '1') {
				$value = $this->html_render === 'sticky' ? 	'_sticky' : '';
			}

			$icon_background = Zikzag_Theme_Helper::get_option('bottom_header_side_panel'.$value.'_background');
			$icon_color = Zikzag_Theme_Helper::get_option('bottom_header_side_panel'.$value.'_color');

			$style = ! empty($icon_background['rgba']) ? 'background-color: '.esc_attr($icon_background['rgba']).';' : '';
			$style .= ! empty($icon_color['rgba']) ? 'color: '.esc_attr($icon_color['rgba']).';' : '';

			return !empty($style) ? ' style="'.$style.'"' : '';
		}

		private function column_class($s = '_middle', $area = '')
		{
			/**
			* Loop column class
			*
			*
			* @since 1.0
			* @access private
			*/
			$dispay = Zikzag_Theme_Helper::get_option('header_column'.$s.'_'.$area.'_display');
			$v_align = Zikzag_Theme_Helper::get_option('header_column'.$s.'_'.$area.'_vert');
			$h_align = Zikzag_Theme_Helper::get_option('header_column'.$s.'_'.$area.'_horz');

			$column_class = ! empty($dispay) ? " display_".$dispay : "";
			$column_class .= ! empty($v_align) ? " v_align_".$v_align : "";
			$column_class .= ! empty($h_align) ? " h_align_".$h_align : "";

			return $column_class;
		}

		/**
		* Generate header mobile menu
		*
		*
		* @since 1.0
		* @access public
		*/
		public function build_header_mobile_menu()
		{

			$header_queries = Zikzag_Theme_Helper::get_option('header_mobile_queris');

			if ($this->header_type === 'custom') {

				if (! empty($this->header_page_select_id)) {

					if (did_action( 'elementor/loaded' )) {

						// Get the page settings manager
						$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );

						// Get the settings model for header post
						$page_settings_model = $page_settings_manager->get_model( $this->header_page_select_id );

						$header_queries = $page_settings_model->get_settings( 'mobile_breakpoint' ) ?? $header_queries;
					}
				}
			}

			$sub_menu_position = Zikzag_Theme_Helper::get_option('mobile_position');
			$mobile_header_custom =  Zikzag_Theme_Helper::get_option('mobile_header');

			echo "<div class='mobile_nav_wrapper' data-mobile-width='".$header_queries."'>";

				echo "<div class='container-wrapper'>";

					echo "<div class='wgl-menu_overlay'></div>";

					echo "<div class='wgl-menu_outer", ( ! empty($sub_menu_position) ? ' sub-menu-position_'.esc_attr($sub_menu_position) : ''), "' id='wgl-perfect-container'>";

						echo '<div class="wgl-menu-outer_header">',
							'<div class="mobile-hamburger-close">',
								'<div class="mobile-hamburger-toggle">',
									'<div class="hamburger-box">',
										'<div class="hamburger-inner"></div>',
									'</div>',
								'</div>',
							'</div>',
						'</div>';

						echo "<div class='wgl-menu-outer_content'>";
							if (! empty($mobile_header_custom)) {
								$this->build_header_layout( 'mobile_content' );
							} else {
								if (has_nav_menu( 'main_menu' )) {
									echo "<nav class='primary-nav'>";
										$logo = self::get_logo($this->html_render, true);
										echo ! empty($logo) ? $logo : '';
										$menu = '';
										if (class_exists( 'RWMB_Loader' ) && $this->id !== 0) {
											if (rwmb_meta('mb_customize_header_layout') == 'custom') {
												$menu = rwmb_meta('mb_menu_header');
											}
										}
										zikzag_main_menu ($menu);
									echo "</nav>";
								}
							}
						echo "</div>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		}
		public function header_render_html() {
			$mobile_header_custom =  Zikzag_Theme_Helper::get_option('mobile_header');

			echo "<header class='wgl-theme-header", esc_attr($this->header_class()), "'>";

				// header output
				echo "<div class='wgl-site-header", (! empty($mobile_header_custom) ? ' mobile_header_custom' : ""), "'>";
					echo "<div class='container-wrapper'>";
					$this->build_header_layout();
					echo "</div>";
				echo "</div>";

				// sticky header output
				get_template_part('templates/header/block', 'sticky');

				// mobile output
				get_template_part('templates/header/block', 'mobile');

			echo "</header>";
            get_template_part('templates/header/block', 'side_area');
		}

		/**
		* Get header Logotype
		*
		*
		* @since 1.0
		* @access public
		*/
		public static function get_logo($location, $menu = false) {
			new Zikzag_Get_Logo( $location, $menu );
		}
		/**
		* Get Header Search
		*
		*
		* @since  1.0
		* @access public
		*/
		public function search($html_render = '',$location = '')
		{
			$description = esc_html__('Type To Search', 'zikzag');
			$search_style = Zikzag_Theme_Helper::get_option('search_style');
			$search_style =  ! empty($search_style) ? $search_style : 'standard';

			$render_search = true;
			if ($search_style === 'alt') {
				if ($this->html_render != 'sticky') {
					$render_search = true;
				} else {
					$render_search = false;
				}
			}

			$search_class = ' search_'.Zikzag_Theme_Helper::get_option('search_style');

			$options_btn = $this->html_render === 'sticky' ? '_sticky' : '';

			$customize = Zikzag_Theme_Helper::get_option('bottom_header_item_search_custom'.$options_btn);
			$customize = empty($customize) ? 'def' : 'color';

			$text_color = Zikzag_Theme_Helper::get_option('bottom_header_item_search_color_txt'.$options_btn);
			$text_color = isset($text_color['rgba']) ? $text_color['rgba'] : '';

			$text_color_hover = Zikzag_Theme_Helper::get_option('bottom_header_item_search_hover_color_txt'.$options_btn);
			$text_color_hover = isset($text_color_hover['rgba']) ? $text_color_hover['rgba'] : '';


			$search_css_id =  uniqid( "zikzag_search_" );

			$settings = [
				'search_css_id' => $search_css_id,
			];


			// Start Custom CSS
			$styles = '';
			if ($this->html_render !== 'mobile') {
				ob_start();

					if ($customize != 'def') {
						if ($customize == 'color') {
							echo "#$search_css_id:hover {
									  color: ".(! empty($text_color_hover) ? esc_attr($text_color_hover) : 'transparent')."  !important;
								  }";
						}
					}

				$styles .= ob_get_clean();
			}
			// Register css
			if (! empty($styles)) {
				Zikzag_Theme_Helper::enqueue_css($styles);
			}

			unset($this->render_attributes);

			$this->add_render_attribute( 'search', 'class', ['wgl-search elementor-search header_search-button-wrapper'] );
			$this->add_render_attribute( 'search', 'role', 'button' );

			if ($this->html_render !== 'mobile' && $customize != 'def') {
				if ($customize == 'color') {
					$this->add_render_attribute( 'search', 'style', [ 'color: '.(! empty($text_color) ? esc_attr($text_color) : 'transparent').';' ] );
				}
			}

			$this->add_render_attribute( 'search', 'id', $search_css_id );

			echo '<div class="header_search'.esc_attr($search_class).'"'.( $this->row_style_height($location) ).'>';

				echo '<div ', $this->get_render_attribute_string( 'search' ), '>',
					'<div class="header_search-button flaticon-search-3"></div>',
					'<div class="header_search-close"></div>',
				'</div>';

				if ($render_search) {
					echo '<div class="header_search-field">';
						if ($search_style === 'alt') {
						    echo '<div class="header_search-wrap">',
								'<div class="zikzag_module_double_headings aleft">',
									'<h3 class="header_search-heading_description heading_title">',
										apply_filters('zikzag_desc_search', $description),
									'</h3>',
								'</div>',
								'<div class="header_search-close"></div>',
							'</div>';
						}
						echo get_search_form(false);
					echo '</div>';
				}

			echo '</div>';
		}

		/**
		* Get Side Panel Icon
		*
		*
		* @since 1.0
		* @access public
		*/
		public function get_side_panel($location, $section)
		{
			echo '<div class="side_panel"', $this->row_style_height($location), '>',
				'<div class="side_panel_inner"', $this->side_panel_style_icon(), '>',
					'<a href="#" class="side_panel-toggle">',
						'<span class="side_panel-toggle-inner">',
							'<span></span><span></span><span></span>',
						'</span>',
					'</a>',
				'</div>',
			'</div>';
		}

		/**
		* Get Header Login
		*
		*
		* @since 1.0
		* @access public
		*/
		public function login_in($location, $section) {
			$output = '';
			$link = get_permalink( get_option('woocommerce_myaccount_page_id') );
			$query_args = [
				'action' => urlencode('signup_form'),
			];
			$url = add_query_arg( $query_args, $link );

			$link_logout = wp_logout_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) );
			echo "<div class='login-in woocommerce'".$this->row_style_height($location).">";

				echo "<span class='login-in_wrapper'>";
				if (is_user_logged_in()) {

					echo "<a class='login-in_link-logout' href='".esc_url( $link_logout )."'>".esc_html__('Logout', 'zikzag')."</a>";
				} else {
					echo "<a class='login-in_link' href='".esc_url_raw( $url )."'>".esc_html__('Login', 'zikzag')."</a>";
				}

				echo '</span>';

				echo "<div class='login-modal wgl_modal-window'>";
					echo "<div class='overlay'></div>";
					echo "<div class='modal-dialog modal_window-login'>";
						echo "<div class='modal_header'>";
						echo "</div>";
						echo "<div class='modal_content'>";
							wc_get_template( 'myaccount/form-login.php' );
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		}

		/**
		* Get Header Cart
		*
		*
		* @since 1.0
		* @access public
		*/
		public function cart($location, $section)
		{
			echo '<div class="wgl-mini-cart_wrapper">',
				'<div class="mini-cart woocommerce"', ( $this->row_style_height($location) ), '>',
					$this->icon_cart(), self::woo_cart(),
				'</div>',
			'</div>';
		}

		public function icon_cart()
		{
			ob_start();
			$link = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : WC()->cart->get_cart_url();

			$options_btn = $this->html_render === 'sticky' ? '_sticky' : '';

			$customize = Zikzag_Theme_Helper::get_option('bottom_header_cart_custom'.$options_btn);
			$customize = empty($customize) ? 'def' : 'color';

			$text_color = Zikzag_Theme_Helper::get_option('bottom_header_cart_color_txt'.$options_btn);
			$text_color = isset($text_color['rgba']) ? $text_color['rgba'] : '';

			$text_color_hover = Zikzag_Theme_Helper::get_option('bottom_header_cart_hover_color_txt'.$options_btn);
			$text_color_hover = isset($text_color_hover['rgba']) ? $text_color_hover['rgba'] : '';

			$cart_css_id =  uniqid( "zikzag_woo_" );

			$settings = [
				'cart_css_id' => $cart_css_id,
			];

			// Start Custom CSS
			$styles = '';
			if ($this->html_render !== 'mobile') {
				ob_start();
					if ($customize != 'def') {
						if ($customize == 'color') {
							echo "#$cart_css_id:hover {
									  color: ".(! empty($text_color_hover) ? esc_attr($text_color_hover) : 'transparent')."  !important;
								  }";
						}
					}
				$styles .= ob_get_clean();
			}

			// Register css
			if (! empty($styles)) {
				Zikzag_Theme_Helper::enqueue_css($styles);
			}

			unset($this->render_attributes);

			$this->add_render_attribute( 'cart', 'class', ['wgl-cart woo_icon elementor-cart'] );
			$this->add_render_attribute( 'cart', 'role', 'button' );
			$this->add_render_attribute( 'cart', 'title', esc_attr__( 'Click to open Shopping Cart', 'zikzag' ) );

			if ($this->html_render !== 'mobile') {
				if ($customize == 'color') {
					$this->add_render_attribute( 'cart', 'style', [ 'color: '.(! empty($text_color) ? esc_attr($text_color) : 'transparent').';' ] );
				}
			}

			$this->add_render_attribute( 'cart', 'id', $cart_css_id );

			echo '<a ', Zikzag_Theme_Helper::render_html( $this->get_render_attribute_string( 'cart' ) ), '>',
				'<span class="woo_mini-count flaticon-bag-1">',
					(WC()->cart->cart_contents_count > 0 ?  '<span>' . esc_html( WC()->cart->cart_contents_count ) . '</span>' : ''),
				'</span>',
			'</a>';

			return ob_get_clean();
		}

		public static function woo_cart()
		{
			ob_start();
				echo '<div class="wgl-woo_mini_cart">';
					woocommerce_mini_cart();
				echo '</div>';
			return ob_get_clean();
		}


		public function in_array_r($needle, $haystack, $strict = false)
		{
			if (is_array($haystack)) {
				foreach ($haystack as $item) {
					if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
						return true;
					}
				}
			}

			return false;
		}

	}

	new Zikzag_Get_Header();
}
