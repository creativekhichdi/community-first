<?php

defined( 'ABSPATH' ) || exit;

/**
* Zikzag Theme Helper
*
*
* @class        Zikzag_Theme_Helper
* @version      1.0
* @category     Class
* @author       WebGeniusLab
*/

if (! class_exists('Zikzag_Theme_Helper')) {
	class Zikzag_Theme_Helper
	{

		private static $instance = null;
		public static function get_instance()
		{
			if (null == self::$instance) {
				self::$instance = new self( );
			}
			return self::$instance;
		}

        public static function get_option($name) {
            if (  class_exists( 'Redux' ) && class_exists( 'Zikzag_Core_Public' ) ) {

                // Customizer
                if (!empty($GLOBALS['zikzag_set']) && $GLOBALS['zikzag_set'] != NULL) {
                    $theme_options = $GLOBALS['zikzag_set'];
                } else {
                    $theme_options = get_option( 'zikzag_set' );
                }

                if (empty($theme_options)) {
                    $theme_options = get_option( 'zikzag_default_options' );
                }

                return isset($theme_options[$name]) ? $theme_options[$name] : null;

            }else{
                $default_option = get_option( 'zikzag_default_options' );
                return isset($default_option[$name]) ? $default_option[$name] : null;
            }
        }

		public static function options_compare($name,$check_key = false,$check_value = false)
		{
			$option = self::get_option($name);
			if (class_exists( 'RWMB_Loader' ) && get_queried_object_id() !== 0) {
				if ($check_key) {
					$var = rwmb_meta($check_key);
					if (! empty($var)) {
						if ($var == $check_value) {
							$option = rwmb_meta('mb_'.$name);
						}
					}
				} else {
					$var = rwmb_meta('mb_'.$name);
					$option = ! empty($var) ? rwmb_meta('mb_'.$name) : self::get_option($name);
				}
			}
			return $option;
		}

		public static function bg_render($name,$check_key = false,$check_value = false)
		{
			$id = !is_archive() ? get_queried_object_id() : 0;
			$image = Zikzag_Theme_Helper::get_option($name."_bg_image");

			// Get image src
			$src = ! empty($image['background-image']) ? $image['background-image'] : '';

			// Get image repeat
			$repeat = ! empty($image['background-repeat']) ? $image['background-repeat'] : '';

			// Get image size
			$size = ! empty($image['background-size']) ? $image['background-size'] : '';

			// Get image attachment
			$attachment = ! empty($image['background-attachment']) ? $image['background-attachment'] : '';

			// Get image position
			$position = ! empty($image['background-position']) ? $image['background-position'] : '';

			if (class_exists( 'RWMB_Loader' ) && 0 !== $id) {

				$conditional_logic = rwmb_meta($check_key);

				if ($conditional_logic == 'on') {

					$repeat = $size = $attachment = $position  = '';
					// Get metaboxes image src
					$src = rwmb_meta('mb_'.$name.'_bg')['image'];

					// Check if metaboxes image exist
					if (! empty($src)) {
						// Get metaboxes image repeat
						$repeat = rwmb_meta('mb_'.$name.'_bg')['repeat'];
						$repeat = ! empty($repeat) ? $repeat : '';

						// Get metaboxes image size
						$size = rwmb_meta('mb_'.$name.'_bg')['size'];
						$size = ! empty($size) ? $size : '';

						// Get metaboxes image attachment
						$attachment = rwmb_meta('mb_'.$name.'_bg')['attachment'];
						$attachment = ! empty($attachment) ? $attachment : '';

						// Get metaboxes image position
						$position = rwmb_meta('mb_'.$name.'_bg')['position'];
						$position = ! empty($position) ? $position : '';
					}
				}
			}

			// Background render
			$style = '';
			$style .= ! empty($src) ? 'background-image:url('.esc_url($src).');' : '';

			if (! empty($src)) {
				$style .= ! empty($size) ? ' background-size:'.esc_attr($size).';' : '';
				$style .= ! empty($repeat) ? ' background-repeat:'.esc_attr($repeat).';' : '';
				$style .= ! empty($attachment) ? ' background-attachment:'.esc_attr($attachment).';' : '';
				$style .= ! empty($position) ? ' background-position:'.esc_attr($position).';' : '';
			}

			return $style;
		}

		public static function preloader()
		{
			if (self::get_option('preloader') == '1' || self::get_option('preloader') == true) {
				$preloader_background = self::get_option('preloader_background');
				$preloader_color_1 = self::get_option('preloader_color_1');

				$bg_styles = ! empty($preloader_background) ? ' style=background-color:'.$preloader_background.';' : "";
				$circle_color_1 = ! empty($preloader_color_1) ? ' style=background-color:'.$preloader_color_1.';' : "";

				echo '<div id="preloader-wrapper" '.esc_attr($bg_styles).'>',
					'<div class="preloader-container">',
						'<div ', $circle_color_1, '></div>',
						'<div ', $circle_color_1, '></div>',
						'<div ', $circle_color_1, '></div>',
						'<div ', $circle_color_1, '></div>',
						'<div ', $circle_color_1, '></div>',
						'<div ', $circle_color_1, '></div>',
						'<div ', $circle_color_1, '></div>',
						'<div ', $circle_color_1, '></div>',
						'<div ', $circle_color_1, '></div>',
					'</div>',
				'</div>';
			}
		}

		public static function pagination($range = 5, $query = false, $alignment = 'left')
		{
			if ($query != false) {
				$wp_query = $query;
			} else {
				global $paged, $wp_query;
			}
			if (empty($paged)) {
				$query_vars = $wp_query->query_vars;
				$paged = isset($query_vars['paged']) ? $query_vars['paged'] : 1;
			}

			$max_page = $wp_query->max_num_pages;

			// Abort if pagination not need
			if ($max_page < 2) return;

			switch ($alignment) {
				case 'right':  $class_alignment = 'aright'; break;
				case 'center': $class_alignment = 'acenter'; break;
				case 'left':
				default: $class_alignment = ''; break;
			}

			$big = 999999999;

			$test_pag = paginate_links(array(
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'type' => 'array',
				'current'   => max( 1, $paged ),
				'total'     => $max_page,
				'prev_text' => '<i class="flaticon-back"></i>',
				'next_text' => '<i class="flaticon-keyboard-right-arrow-button"></i>',
			));
			$test_comp = '';
			foreach ($test_pag as $key => $value) {
				$test_comp .= '<li class="page">'.$value.'</li>';
			}
			return '<ul class="wgl-pagination '.esc_attr($class_alignment).'">'.$test_comp.'</ul>';
		}

		public static function hexToRGB($hex = "#ffffff")
		{
			if (strlen($hex) < 1) {
				$hex = "#ffffff";
			}
			$color['r'] = hexdec(substr($hex, 1, 2));
			$color['g'] = hexdec(substr($hex, 3, 2));
			$color['b'] = hexdec(substr($hex, 5, 2));

			return $color['r'] . "," . $color['g'] . "," . $color['b'];
		}

		/**
	     * Given a HEX string returns a HSL array equivalent.
	     * @param string $color
	     * @return array HSL associative array
	     */
	    public static function hexToHsl( $color )
	    {

	        // Sanity check
	        $color = self::_checkHex($color);

	        // Convert HEX to DEC
	        $R = hexdec($color[0].$color[1]);
	        $G = hexdec($color[2].$color[3]);
	        $B = hexdec($color[4].$color[5]);

	        $HSL = array();

	        $var_R = ($R / 255);
	        $var_G = ($G / 255);
	        $var_B = ($B / 255);

	        $var_Min = min($var_R, $var_G, $var_B);
	        $var_Max = max($var_R, $var_G, $var_B);
	        $del_Max = $var_Max - $var_Min;

	        $L = ($var_Max + $var_Min)/2;

	        if ($del_Max == 0)
	        {
	            $H = 0;
	            $S = 0;
	        }
	        else
	        {
	            if ( $L < 0.5 ) $S = $del_Max / ( $var_Max + $var_Min );
	            else            $S = $del_Max / ( 2 - $var_Max - $var_Min );

	            $del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
	            $del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
	            $del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

	            if      ($var_R == $var_Max) $H = $del_B - $del_G;
	            else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B;
	            else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R;

	            if ($H<0) $H++;
	            if ($H>1) $H--;
	        }

	        $HSL['H'] = ($H*360);
	        $HSL['S'] = $S;
	        $HSL['L'] = $L;

	        return $HSL;
	    }

		/**
	     * You need to check if you were given a good hex string
	     * @param string $hex
	     * @return string Color
	     * @throws Exception "Bad color format"
	     */
	    private static function _checkHex( $hex )
	    {
	        // Strip # sign is present
	        $color = str_replace("#", "", $hex);

	        // Make sure it's 6 digits
	        if( strlen($color) == 3 ) {
	            $color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
	        } else if( strlen($color) != 6 ) {
	            throw new Exception("HEX color needs to be 6 or 3 digits long");
	        }

	        return $color;
	    }

	    /**
	     * Given a Hue, returns corresponding RGB value
	     * @param int $v1
	     * @param int $v2
	     * @param int $vH
	     * @return int
	     */
	    private static function _huetorgb( $v1,$v2,$vH )
	    {
	        if( $vH < 0 ) {
	            $vH += 1;
	        }

	        if( $vH > 1 ) {
	            $vH -= 1;
	        }

	        if( (6*$vH) < 1 ) {
	               return ($v1 + ($v2 - $v1) * 6 * $vH);
	        }

	        if( (2*$vH) < 1 ) {
	            return $v2;
	        }

	        if( (3*$vH) < 2 ) {
	            return ($v1 + ($v2-$v1) * ( (2/3)-$vH ) * 6);
	        }

	        return $v1;

	    }

	    /**
	     *  Given a HSL associative array returns the equivalent HEX string
	     * @param array $hsl
	     * @return string HEX string
	     * @throws Exception "Bad HSL Array"
	     */
	    public static function hslToHex( $hsl = array() )
	    {
	        list($H,$S,$L) = array( $hsl['H']/360,$hsl['S'],$hsl['L'] );

	        if( $S == 0 ) {
	            $r = $L * 255;
	            $g = $L * 255;
	            $b = $L * 255;
	        } else {

	            if($L<0.5) {
	                $var_2 = $L*(1+$S);
	            } else {
	                $var_2 = ($L+$S) - ($S*$L);
	            }

	            $var_1 = 2 * $L - $var_2;

	            $r = round(255 * self::_huetorgb( $var_1, $var_2, $H + (1/3) ));
	            $g = round(255 * self::_huetorgb( $var_1, $var_2, $H ));
	            $b = round(255 * self::_huetorgb( $var_1, $var_2, $H - (1/3) ));

	        }

	        // Convert to hex
	        $r = dechex($r);
	        $g = dechex($g);
	        $b = dechex($b);

	        // Make sure we get 2 digits for decimals
	        $r = (strlen("".$r)===1) ? "0".$r:$r;
	        $g = (strlen("".$g)===1) ? "0".$g:$g;
	        $b = (strlen("".$b)===1) ? "0".$b:$b;

	        return $r.$g.$b;
	    }


	    /**
	     * Given a HEX value, returns a darker color. If no desired amount provided, then the color halfway between
	     * given HEX and black will be returned.
	     * @param int $amount
	     * @return string Darker HEX value
	     */
	    public static function darken( $_hsl, $amount )
	    {
	        // Darken
	        $darkerHSL = self::_darken($_hsl, $amount);
	        // Return as HEX
	        return '#'.self::hslToHex($darkerHSL);
	    }

	    private static function _darken( $hsl, $amount)
	    {

	        // Check if we were provided a number
	        if( $amount ) {
	            $hsl['L'] = ($hsl['L'] * 100) - $amount;
	            $hsl['L'] = ($hsl['L'] < 0) ? 0:$hsl['L']/100;
	        } else {
	            // We need to find out how much to darken
	            $hsl['L'] = $hsl['L']/2 ;
	        }

	        return $hsl;
    	}


		// https://github.com/opensolutions/smarty/blob/master/plugins/modifier.truncate.php
		public static function modifier_character($string, $length = 80, $etc = '... ', $break_words = false)
		{
			if ($length == 0 ) return '';

			if (mb_strlen($string, 'utf8') > $length) {
				$length -= mb_strlen($etc, 'utf8');
				if (! $break_words) {
					$string = preg_replace('/\s+\S+\s*$/su', '', mb_substr($string, 0, $length + 1, 'utf8'));
				}
				return mb_substr($string, 0, $length, 'utf8') . $etc;
			} else {
				return $string;
			}
		}

		public static function load_more($query = false, $name_load_more = '', $class = '')
		{
			$name_load_more = ! empty($name_load_more) ? $name_load_more : esc_html__( 'Load More', 'zikzag' );

			$uniq = uniqid();
			$ajax_data_str = htmlspecialchars( json_encode($query), ENT_QUOTES, 'UTF-8' );

			echo
			  '<div class="clear"></div>',
			  '<div class="load_more_wrapper', (! empty($class) ? ' '.esc_attr($class) : '' ), '">',
				'<div class="button_wrapper">',
					'<a href="#" class="load_more_item"><span>', esc_html($name_load_more), '</span></a>',
				'</div>',
				'<form class="posts_grid_ajax">',
					"<input type='hidden' class='ajax_data' name='", esc_attr($uniq), "_ajax_data' value='", esc_attr($ajax_data_str), "' />",
				'</form>',
			  '</div>'
			;
		}

		public static function render_html($args) {
			return isset($args) ? $args : '';
		}

		public static function in_array_r($needle, $haystack, $strict = false)
		{
			if (is_array($haystack)) {
				foreach ($haystack as $item) {
					if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && self::in_array_r($needle, $item, $strict))) {
						return true;
					}
				}
			}

			return false;
		}

		public static function render_sidebars($args = 'page')
		{
			$output = array();
			$sidebar_style = '';

			$layout = self::get_option( $args.'_sidebar_layout');
			$sidebar = self::get_option( $args.'_sidebar_def');
			$sidebar_width = self::get_option($args.'_sidebar_def_width');
			$sticky_sidebar = self::get_option($args.'_sidebar_sticky');
			$sidebar_gap = self::get_option($args.'_sidebar_gap');
			$sidebar_class = $sidebar_style = '';
			$id = !is_archive() ? get_queried_object_id() : 0;

			$zikzag_core = class_exists('Zikzag_Core');

			if (is_archive() || is_search() || is_home() || is_page()) {
				if (! $zikzag_core) {
					if (is_active_sidebar( 'sidebar_main-sidebar' )) {
						$layout = 'right';
						$sidebar = 'sidebar_main-sidebar';
						$sidebar_width = 9;
					}
				}
			}

			if (function_exists('is_shop') && is_shop() && ! $zikzag_core) {
				if (is_active_sidebar( 'shop_products' )) {
					$layout = 'right';
					$sidebar = 'shop_products';
					$sidebar_width = 9;
				} else {
					$column = 12;
					$sidebar = '';
					$layout = 'none';
				}
			}

			if (is_single() && ! $zikzag_core) {
				if (function_exists('is_product') && is_product() && is_active_sidebar('shop_single')) {
					$layout = 'right';
					$sidebar = 'shop_single';
					$sidebar_width = 9;
				} elseif (is_active_sidebar('sidebar_main-sidebar')) {
					$layout = 'right';
					$sidebar = 'sidebar_main-sidebar';
					$sidebar_width = 9;
				}
			}

			if (class_exists( 'RWMB_Loader' ) && 0 !== $id) {
				$mb_layout = rwmb_meta('mb_page_sidebar_layout');
				if (! empty($mb_layout) && $mb_layout != 'default') {
					$layout = $mb_layout;
					$sidebar = rwmb_meta('mb_page_sidebar_def');
					$sidebar_width = rwmb_meta('mb_page_sidebar_def_width');
					$sticky_sidebar = rwmb_meta('mb_sticky_sidebar');
					$sidebar_gap = rwmb_meta('mb_sidebar_gap');
				}
			}

			if ($sticky_sidebar) {
				wp_enqueue_script('theia-sticky-sidebar', get_template_directory_uri() . '/js/theia-sticky-sidebar.min.js', array(), false, false);
				$sidebar_class .= 'sticky-sidebar';
			}

			if (isset($sidebar_gap) && $sidebar_gap != 'def' && $layout != 'default') {
				$layout_pos = $layout == 'left' ? 'right' : 'left';
				$sidebar_style = 'style="padding-'.$layout_pos.': '.$sidebar_gap.'px;"';
			}

			$column = 12;
			if ($layout == 'left' || $layout == 'right') {
				$column = (int) $sidebar_width;
			} else {
				$sidebar = '';
			}

			//GET Params sidebar
			if (isset($_GET['shop_sidebar']) && ! empty($_GET['shop_sidebar'])) {
				$layout = $_GET['shop_sidebar'];
				$sidebar = 'shop_products';
				$column = 9;
			}

			if (! is_active_sidebar( $sidebar )) {
				$column = 12;
				$sidebar = '';
				$layout = 'none';
			}

			$output['column'] = $column;
			$output['row_class'] = $layout != 'none' ? ' sidebar_'.esc_attr($layout) : '';
			$output['container_class'] = $layout != 'none' ? ' wgl-content-sidebar' : '';
			$output['layout'] = $layout;
			$output['content'] = '';

			if ($layout == 'left' || $layout == 'right') {
			  $output['content'] .= '<div class="sidebar-container '.$sidebar_class.' wgl_col-'.(12 - (int)$column).'" '.$sidebar_style.'>';
				if (is_active_sidebar( $sidebar )) {
					$output['content'] .= "<aside class='sidebar'>";
						ob_start();
							dynamic_sidebar( $sidebar );
						$output['content'] .= ob_get_clean();
					$output['content'] .= "</aside>";
				}
			  $output['content'] .= "</div>";
			}

			return $output;
		}

		public static function posted_meta_on()
		{
			global $post;

			printf('<span><time class="entry-date published" datetime="%1$s">%2$s</time></span><span>' . esc_html__( 'Published in', 'zikzag' ) . ' <a href="%3$s" rel="gallery">%4$s</a></span>',
				esc_attr(get_the_date('c')),
				esc_html(get_the_date()),
				esc_url(get_permalink($post->post_parent)),
				esc_html(get_the_title($post->post_parent))
			);

			printf( '<span class="author vcard">%1$s</span>',
				sprintf(
					'<a class="url fn n" href="%1$s">%2$s</a>',
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
					esc_html( get_the_author() )
				)
			);

			$metadata = wp_get_attachment_metadata();

			if ($metadata) {
				printf( '<span class="full-size-link"><span class="screen-reader-text">%1$s </span><a href="%2$s" title="%2$s">%1$s %3$s &times; %4$s</a></span>',
					esc_html_x( 'Full size', 'Used before full size attachment link.', 'zikzag' ),
					esc_url( wp_get_attachment_url() ),
					esc_attr( absint( $metadata['width'] ) ),
					esc_attr( absint( $metadata['height'] ) )
				);
			}

			$allowed_html = [
				'span' => [ 'class' => true, 'style' => true ],
				'br' => [],
				'em' => [],
				'strong' => []
			];

			edit_post_link(
				sprintf(
					/* translators: %s: Name of current post */
					wp_kses( __( 'Edit<span class="screen-reader-text"> "%s"</span>', 'zikzag' ), $allowed_html ) ,
						get_the_title()
					),'<span class="edit-link">','</span>'
			);
		}

		public static function enqueue_css($style)
		{
			if (! empty($style)) {
				ob_start();
					echo self::render_html($style);
				$css = ob_get_clean();
				$css = apply_filters( 'zikzag_enqueue_shortcode_css', $css, $style );
			}
		}

		public static function render_html_attributes(array $attributes)
		{
			$rendered_attributes = [];

			foreach ( $attributes as $attribute_key => $attribute_values) {
				if (is_array( $attribute_values )) {
					$attribute_values = implode( ' ', $attribute_values );
				}

				$rendered_attributes[] = sprintf( '%1$s="%2$s"', $attribute_key, esc_attr( $attribute_values ) );
			}

			return implode( ' ', $rendered_attributes );
		}

		public static function render_quote_icon_svg()
		{
			$quote_icon_svg = '<svg viewBox="0 0 62.4 44.8">
			<path class="st0" d="M40.8,30.6c-0.9,2.3-1.7,3.9-0.9,6.5c0.7,2.3,2.5,3.9,4.5,5.2c3.7,2.4,8.7,3.6,12.6,0.9
				c3.8-2.5,5.8-7.3,4.4-11.7c-0.4-1.3-1.2-2.4-2.2-3.2c-2.1-3.2-6.1-4.5-9.9-4.3c-2.2,0.1-4.5,0.8-6.2,2.4c-0.6,0.6-1.1,1.2-1.6,2
				c-0.2,0.4-0.6,1.4-0.8,2C40.7,30.7,40.6,31.3,40.8,30.6z"/>
			<path class="st0" d="M10,41.2c4.6,3.3,12.4,4.1,15.9-1.2c1-1.5,1.5-3.1,1.6-4.6c0.6-3-0.5-6-2.8-8c-0.5-0.7-1.1-1.3-1.6-2
				c-1.8-2-4.9-2.2-7.2-1c-2.6,0-5.2,0.7-7.3,2.7C4.1,31.2,5.5,37.9,10,41.2z"/>
			<path class="st1" d="M42,36.2c-7.2-11.4,14.9-17.4,18.7-4.6c1.7,5.8-2.8,11.2-8.6,11.6c-3.9,0.2-7.6-1.4-10.5-4.1
				C29,27.4,38.5,10.5,48.7,1.3c0.7-0.7-0.3-1.7-1.1-1.1C39.2,7.9,31.1,19.6,35,31.5c2.8,8.4,12.2,16.1,21.3,12
				c9.3-4.1,7.1-16.5-1.4-20.1c-8.5-3.5-20.1,4-14.3,13.4C41.2,37.8,42.5,37,42,36.2L42,36.2z"/>
			<path class="st1" d="M8,36.2c-7.2-11.4,14.9-17.4,18.7-4.6c1.7,5.8-2.8,11.2-8.6,11.6c-3.9,0.2-7.6-1.4-10.5-4.1
				C-5,27.4,4.5,10.5,14.7,1.3c0.7-0.7-0.3-1.7-1.1-1.1C5.2,7.9-2.9,19.6,1,31.5c2.8,8.4,12.2,16.1,21.3,12
				c9.3-4.1,7.1-16.5-1.4-20.1c-8.5-3.5-20.1,4-14.3,13.4C7.2,37.8,8.5,37,8,36.2L8,36.2z"/>
			</svg>';

			return $quote_icon_svg;
		}

		/**
		 * Check licence activation
		 */
		public static function wgl_theme_activated()
		{
			$licence_key = get_option( 'wgl_licence_validated' );
			$licence_key = empty( $licence_key ) ? get_option( Wgl_Theme_Verify::get_instance()->item_id ) : $licence_key;

			if (! empty($licence_key)) {
				return $licence_key;
			}

			return false;
		}

	}

	new Zikzag_Theme_Helper();
}
?>