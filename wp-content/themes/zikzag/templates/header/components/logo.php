<?php


if (!class_exists('Zikzag_Get_Logo')) {
	class Zikzag_Get_Logo{

		public function __construct($location = 'bottom', $menu = false, $custom_logo = false, $custom_logo_height = false){
			return $this->init($location, $menu, $custom_logo, $custom_logo_height );
		}

		public function init($location, $menu, $custom_logo, $custom_logo_height){


			$id = !is_archive() ? get_queried_object_id() : 0;
			$img_alt = '';

			// Get Default Logotype

			$header_logo_src =  empty($custom_logo) ? Zikzag_Theme_Helper::get_option('header_logo') : $custom_logo;
			$header_logo_id = !empty($header_logo_src) ? $header_logo_src['id'] : '';
			$header_logo_src = !empty($header_logo_src) ? $header_logo_src['url'] : '';

			// logo default image alt
			$def_img_alt = get_post_meta($header_logo_id, '_wp_attachment_image_alt', true);

			// Get Sticky Logotype
			$logo_sticky_src =  empty($custom_logo) ? Zikzag_Theme_Helper::get_option('logo_sticky') : $custom_logo;
			$logo_sticky_id = !empty($logo_sticky_src) ? $logo_sticky_src['id'] : '';
			$logo_sticky_src =  !empty($logo_sticky_src) ? $logo_sticky_src['url'] : '';

			// logo sticky image alt
			$sticky_img_alt = get_post_meta($logo_sticky_id, '_wp_attachment_image_alt', true);

			// Get Mobile Logotype
			$menu = !empty($menu) ? '_menu' : '';

			$logo_mobile_src =  Zikzag_Theme_Helper::get_option('logo_mobile'.$menu);
			$logo_mobile_id = !empty($logo_mobile_src) ? $logo_mobile_src['id'] : '';
			$logo_mobile_src =  !empty($logo_mobile_src) ? $logo_mobile_src['url'] : '';
						// logo mobile image alt
			$mobile_img_alt = get_post_meta($logo_mobile_id, '_wp_attachment_image_alt', true);

			$logo_height_custom = Zikzag_Theme_Helper::get_option('logo_height_custom');
			$logo_height = Zikzag_Theme_Helper::get_option('logo_height');
			$logo_height = $logo_height['height'];

			if(!empty($custom_logo_height)){
				$logo_height = $custom_logo_height;
				$logo_height_custom = '1';
			}

			$sticky_logo_height_custom = Zikzag_Theme_Helper::get_option('sticky_logo_height_custom');
			$sticky_logo_height = Zikzag_Theme_Helper::get_option('sticky_logo_height');
			$sticky_logo_height = $sticky_logo_height['height'];

			$mobile_logo_height_custom = Zikzag_Theme_Helper::get_option('mobile_logo'.$menu.'_height_custom');
			$mobile_logo_height = Zikzag_Theme_Helper::get_option('mobile_logo'.$menu.'_height');
			$mobile_logo_height = $mobile_logo_height['height'];

			$logo_height_css = $mobile_height_style = $sticky_height_style ='';

			if (!empty($logo_height) && $logo_height_custom == '1') {
				$logo_height_css .= 'height:'.(esc_attr((int) $logo_height)).'px;';
			}
			$logo_height_style = !empty($logo_height_css) ? ' style="'.$logo_height_css.'"' : '';

			switch (true) {
				case !empty($sticky_logo_height) && $sticky_logo_height_custom == '1' && $location == 'sticky':
					$sticky_height_style .= 'height:'.(esc_attr((int) $sticky_logo_height)).'px;';
					break;

				case !empty($mobile_logo_height) && $mobile_logo_height_custom == '1' && $location == 'mobile':
				$mobile_height_style .= 'height:'.(esc_attr((int) $mobile_logo_height)).'px;';
				break;

				default:
				if(!empty($logo_height) && $logo_height_custom == '1'){
					$sticky_height_style = $mobile_height_style = $logo_height_css;
				}
				break;
			}
			// Set Sticky Height Logotype
			$sticky_height_style = !empty($sticky_height_style) ? ' style="'.$sticky_height_style.'"' : '';

			// Set Mobile Height Logotype
			$mobile_height_style = !empty($mobile_height_style) ? ' style="'.$mobile_height_style.'"' : '';
			$class = !empty($logo_mobile_src) ? " logo-mobile_enable" : '';

			?><div class='wgl-logotype-container<?php echo esc_attr($class);?>'>
				<a href='<?php echo esc_url(home_url('/')) ?>'>
					<?php
					switch (true) {
						case $location == 'bottom':
                            if (!empty($header_logo_src)) {
                                ?>
                                <img class="default_logo" src="<?php echo esc_url($header_logo_src); ?>" alt="<?php echo esc_attr($def_img_alt); ?>" <?php echo Zikzag_Theme_Helper::render_html($logo_height_style);?>>
                                <?php
                            } else {
                                ?>
                                <h1 class="logo-name">
                                    <?php echo get_bloginfo( 'name' ); ?>
                                </h1>
                                <?php
                            }
						break;

                        case !empty($logo_sticky_src) && $location == 'sticky':
                            echo '<img class="logo-sticky"
                                src="'.esc_url($logo_sticky_src).'"
                                alt="'.esc_attr($sticky_img_alt).'"
                                ',Zikzag_Theme_Helper::render_html($sticky_height_style),
                                '>';
                                break;

						case !empty($logo_mobile_src) && $location == 'mobile':
						?>
						<img class="logo-mobile" src="<?php echo esc_url($logo_mobile_src);?>" alt="<?php echo esc_attr($mobile_img_alt); ?>" <?php echo Zikzag_Theme_Helper::render_html($mobile_height_style);?>>
						<?php
						break;

						default:
						if (!empty($header_logo_src)) {
							?>
							<img class="default_logo" src="<?php echo esc_url($header_logo_src); ?>" alt="<?php echo esc_attr($def_img_alt); ?>" <?php echo Zikzag_Theme_Helper::render_html($logo_height_style);?>>
							<?php
						} else {
							?>
							<h1 class="logo-name">
								<?php echo get_bloginfo( 'name' ); ?>
							</h1>
							<?php
						}
						break;
					}

					?>
				</a>
			</div>
			<?php
		}
	}
}
?>
