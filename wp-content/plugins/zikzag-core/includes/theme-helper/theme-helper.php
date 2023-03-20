<?php

class WglThemeHelper
{

	protected static $instance = null;

	/**
	 * @var \WP_Post
	 */
	private $post_id;

	public static function instance()
	{
		if (is_null( self::$instance )) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct () {
		$this->post_id = get_the_ID();
	}

    public function render_post_share ($show_share)
    {
        $img_url = wp_get_attachment_image_src(get_post_thumbnail_id($this->post_id), 'single-post-thumbnail')[0] ?? '';

        if ($show_share == "1" || $show_share == "yes") :
        ?>
            <!-- post share block -->
            <div class="share_social-wpapper">
				<span class="share_social-title"><?php echo esc_html__('Share article:','zikzag-core') ?></span>
                <a class="share_link share_twitter" target="_blank" href="<?php echo esc_url('https://twitter.com/intent/tweet?text='. get_the_title() .'&amp;url='. get_permalink()); ?>"><span class="fab fa-twitter"></span></a>
                <a class="share_link share_facebook" target="_blank" href="<?php echo  esc_url('https://www.facebook.com/share.php?u='. get_permalink()); ?>"><span class="fab fa-facebook-f"></span></a>
                <?php
                    if (strlen($img_url) > 0) {
                        echo '<a class="share_link share_pinterest" target="_blank" href="'. esc_url('https://pinterest.com/pin/create/button/?url='. get_permalink() .'&media='. $img_url) .'"><span class="fab fa-pinterest-p"></span></a>';
                    }
                ?>
                <a class="share_link share_linkedin" href="<?php echo esc_url('http://www.linkedin.com/shareArticle?mini=true&url='.substr(urlencode( get_permalink() ),0,1024));?>&title=<?php echo esc_attr(substr(urlencode(html_entity_decode(get_the_title())),0,200));?>" target="_blank" ><span class="fab fa-linkedin-in fa-linkedin"></span></a>
            </div>
            <!-- //post share block -->
        <?php
        endif;
    }

	public function render_post_list_share()
	{
		?>
		<div class="share_post-container">
			<div class="share_social-wpapper">
				<ul>
					<li>
						<a class="share_post share_twitter" target="_blank" href="<?php echo esc_url('https://twitter.com/intent/tweet?text='. get_the_title() .'&amp;url='. get_permalink()); ?>">
							<span class="fab fa-twitter"></span>
						</a>
					</li>
					<li>
						<a class="share_post share_facebook" target="_blank" href="<?php echo  esc_url('https://www.facebook.com/share.php?u='. get_permalink()); ?>">
							<span class="fab fa-facebook-f"></span>
						</a>
					</li>
					<?php
					$img_url = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail')[0] ?? '';
					if (strlen($img_url) > 0) {
						echo '<li>',
							'<a class="share_post share_pinterest" target="_blank" href="'. esc_url('https://pinterest.com/pin/create/button/?url='. get_permalink() .'&media='. $img_url) .'"><span class="fab fa-pinterest-p"></span></a>',
						'</li>';
					}
					?>
					<li>
						<a class="share_post share_linkedin" target="_blank" href="<?php echo esc_url('http://www.linkedin.com/shareArticle?mini=true&url='.substr(urlencode( get_permalink() ),0,1024));?>&title=<?php echo esc_attr(substr(urlencode(html_entity_decode(get_the_title())),0,200));?>">
							<span class="fab fa-linkedin-in"></span>
						</a>
					</li>
				</ul>
			</div>
			<a href="#"></a>
		</div>
		<?php
	}

	public static function render_social_shares()
	{
		$description = esc_html__( 'Socials', 'zikzag-core' );

		$facebook  = Zikzag_Theme_Helper::options_compare('soc_icon_facebook', 'mb_customize_soc_shares', 'on');
		$twitter   =  Zikzag_Theme_Helper::options_compare('soc_icon_twitter', 'mb_customize_soc_shares', 'on');
		$linkedin  = Zikzag_Theme_Helper::options_compare('soc_icon_linkedin', 'mb_customize_soc_shares', 'on');
		$pinterest = Zikzag_Theme_Helper::options_compare('soc_icon_pinterest', 'mb_customize_soc_shares', 'on');
		$tumblr    = Zikzag_Theme_Helper::options_compare('soc_icon_tumblr', 'mb_customize_soc_shares', 'on');

		$fixed  = Zikzag_Theme_Helper::options_compare('soc_icon_position', 'mb_customize_soc_shares', 'on');
		$offset = Zikzag_Theme_Helper::get_option('soc_icon_offset');
		if (class_exists( 'RWMB_Loader' ) && get_queried_object_id() !== 0) {
			$mb_social_shares = rwmb_meta('mb_customize_soc_shares');

			if ($mb_social_shares == 'on') {
				$offset = [];
				$offset['margin-bottom'] = rwmb_meta('mb_soc_icon_offset');
			}
		}

		$share = Zikzag_Theme_Helper::options_compare('soc_icon_style', 'mb_customize_soc_shares', 'on');
		$share_class = ! empty($share) ? ' '.$share.'_style' : ' standard_style';
		$share_class .= ! empty($fixed) ? ' fixed' : '';

		$units = ! empty($fixed) ? "%" : 'px';
		$style =  isset($offset['margin-bottom']) && $offset['margin-bottom'] !== '' ? ' top:'.(int)$offset['margin-bottom'].$units.';' : '';

		?>
		<section class="wgl-social-share_pages<?php echo esc_attr($share_class);?>"<?php
			echo ! empty($style) ? ' style="'.$style.'"' : ''
		?>>
			<div class="share_social-wpapper">
				<?php

				if ($share == 'hovered' ) : ?>
					<div class="share_social-desc">
						<div class="share_social-title">
							<?php
							echo apply_filters('zikzag_desc_socials', $description);
							?>
						</div>
						<div class="share_social-icon-plus"></div>
					</div>
					<?php
				endif;

				?>
				<ul>
					<?php
					if (! empty($twitter) ) : ?>
						<li>
							<a class="share_page share_twitter" target="_blank" href="<?php echo esc_url('https://twitter.com/intent/tweet?text='. get_the_title() .'&amp;url='. get_permalink()); ?>"><span class="fab fa-twitter"></span></a>
						</li><?php
					endif;

					if (! empty($facebook) ) : ?>
						<li>
							<a class="share_page share_facebook" target="_blank" href="<?php echo  esc_url('https://www.facebook.com/share.php?u='. get_permalink()); ?>"><span class="fab fa-facebook-f"></span></a>
						</li>
						<?php
					endif;

					if (! empty($share_pinterest) ) :
						$img_url = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail')[0] ?? '';
						if (strlen($img_url) > 0) {
							echo
								'<li>',
								'<a class="share_page share_pinterest" target="_blank" href="' . esc_url('https://pinterest.com/pin/create/button/?url=' . get_permalink() . '&media=' . $img_url) . '"><span class="fab fa-pinterestp"></span></a>',
								'</li>';
						}
					endif;

					if (! empty($linkedin) ) : ?>
						<li>
							<a class="share_page share_linkedin" target="_blank" href="<?php echo esc_url('http://www.linkedin.com/shareArticle?mini=true&url='.substr(urlencode( get_permalink() ),0,1024));?>&title=<?php echo esc_attr(substr(urlencode(html_entity_decode(get_the_title())),0,200));?>"><span class="fab fa-linkedin-in"></span></a>
						</li>
						<?php
					endif;

					if (! empty($tumblr) ) : ?>
						<li>
							<a class="share_page share_tumblr" target="_blank" href="<?php echo esc_url( 'http://www.tumblr.com/share/link?url=' . urlencode(get_permalink()). '&amp;name=' . urlencode(get_the_title()) .'&amp;description='.urlencode(get_the_excerpt()) );?>"><span class="fab fa-tumblr"></span></a>
						</li>
						<?php
					endif;

					$custom_share = Zikzag_Theme_Helper::get_option('add_custom_share');
					if (isset($custom_share) && ! empty($custom_share)) {
						for ( $i = 1; $i <= 10; $i++) {
							${'custom_share_'.$i} = Zikzag_Theme_Helper::get_option('select_custom_share_text-'.$i);
							${'custom_share_icons'.$i} = Zikzag_Theme_Helper::get_option('select_custom_share_icons-'.$i);

							if (! empty(${'custom_share_'.$i}) ) : ?>
								<li>
									<a class="share_page" href="<?php echo esc_url( ${'custom_share_'.$i} );?>"><span class="<?php echo esc_attr(${'custom_share_icons'.$i});?>"></span></a>
								</li>
								<?php
							endif;
						}
					}
					?>
				</ul>
			</div>
		</section>
		<?php
	}
}

function wgl_theme_helper() {
	return WglThemeHelper::instance();
}
