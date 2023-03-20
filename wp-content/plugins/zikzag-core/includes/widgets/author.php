<?php

class Author extends WP_Widget
{
	// If WPML is active and was setup to have more than one language this website is multilingual.
	private $isMultilingual = false; // Is this site multilingual?
	
	function __construct()
	{
		parent::__construct(
			'combined_image_author_widget', // Base ID
			esc_html__( 'WGL Blog Author', 'zikzag-core' ), // Name
			array( 'description' => esc_html__( 'WGL Widget ', 'zikzag-core' ), ) // Args
		);

		if (is_admin() === true) {
			add_action('admin_enqueue_scripts', array($this, 'enqueue_backend_scripts') );
		}
	}


	public function enqueue_backend_scripts()
	{
		wp_enqueue_media(); //Enable the WP media uploader
		wp_enqueue_script('zikzag-upload-img', get_template_directory_uri() . '/core/admin/js/img_upload.js', array('jquery'), false, true);
	}
	

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget($args, $instance)
	{
		$html = '';
		$title_name = 'title';
		$author_name = 'name';
		$text_name = 'text';
		$image_name = 'image';
		$image_signature = isset($instance['signature']) && ! empty($instance['signature']) ? $instance['signature'] : '';
		$background_image = isset($instance['bg']) && ! empty($instance['bg']) ? $instance['bg'] : '';

		$attachment_id = attachment_url_to_postid ($instance[$image_name]);
		$alt = '';
		// if no alt attribute is filled out then echo "Featured Image of article: Article Name"
		if ('' === get_post_meta($attachment_id, '_wp_attachment_image_alt', true)) {
			$alt = the_title_attribute(array('before' => esc_html__('Featured author image: ', 'zikzag-core'), 'echo' => false));
		} else {
			$alt = trim(strip_tags(get_post_meta($attachment_id, '_wp_attachment_image_alt', true)));
		}

		$widgetImg = ( (isset($instance[$image_name])) && (! empty($instance[$image_name])) )? '<img class="author-widget_img" src="' . esc_url(aq_resize($instance[$image_name], "350", "350", true, true, true)) . '" alt="'.esc_attr($alt).'">' :'';        

		// Get Image Signature
		$attachment_id_s = attachment_url_to_postid ($image_signature);
		$alt_s = '';
		// if no alt attribute is filled out then echo "Featured Image of article: Article Name"
		if ('' === get_post_meta($attachment_id_s, '_wp_attachment_image_alt', true)) {
			$alt_s = the_title_attribute(array('before' => esc_html__('Featured author signature: ', 'zikzag-core'), 'echo' => false));
		} else {
			$alt_s = trim(strip_tags(get_post_meta($attachment_id_s, '_wp_attachment_image_alt', true)));
		}

		$widgetImgSign = $image_signature ? '<img class="author-widget_sign" src="' . esc_url($image_signature) . '" alt="'.esc_attr($alt_s).'">' :'';
		
		$title = ! empty($instance[$title_name]) ? $instance[$title_name] : false; 
		$author_name = ! empty($instance[$author_name]) ? $instance[$author_name] : false; 
		$text = ! empty($instance[$text_name]) ? $instance[$text_name] : '';

		$socials = [];
		foreach (wgl_user_social_medias_arr() as $soc_name => $value) {
			$socials[$soc_name] = isset($instance[$soc_name]) && ! empty($instance[$soc_name]) ? $instance[$soc_name] : '';
		}
		
		$widgetClasses = 'zikzag_author-widget widget zikzag_widget';

		$bg_image = ! empty($background_image) ? 'background-image: url('.esc_url($background_image).');' : '';

		echo '<div class="', esc_attr($widgetClasses), '">';
		
		  if (! empty($title)) echo '<h3 class="widget-title">', esc_html($title), '</h3>';

		  echo '<div class="author-widget_wrapper"', (! empty($bg_image) ? ' style="'.esc_attr($bg_image).'"' : ''), '>';

			if (! empty($widgetImg)) {
				echo '<div class="author-widget_img-wrapper">', $widgetImg, '</div>';
			}

			if (! empty($widgetImgSign)) {
				echo '<div class="author-widget_img_sign-wrapper">', $widgetImgSign, '</div>';
			}

			if (! empty($author_name)) {
				echo '<h4 class="author-widget_title">', esc_html($author_name), '</h4>';
			}

			if (! empty($text)) {
				echo '<p class="author-widget_text">', esc_html($text), '</p>';
			}

			if (! empty($socials)) {
				echo '<div class="author-widget_social">';
				foreach ( $socials as $name => $link) if ($link) {
					$icon_pref = 'fab fa-';
					if ($name == 'telegram') $icon_pref = 'flaticon-';
					echo '<a class="author-widget_social-link ', esc_attr($icon_pref), esc_attr($name), '" href="', esc_url($link), '"></a>';
				}
				echo '</div>';
			}

		  echo '</div>';
		echo '</div>';
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance)
	{

		$title_name = 'title';
		$author_name = 'name';
		$text_name = 'text';
		$image_name = 'image';
		$image_signature = 'signature';
		$bg_image = 'bg';

		$title = isset($instance[$title_name]) && ! empty($instance[$title_name])  ? $instance[$title_name] : '';
		$name = isset($instance[$author_name]) && ! empty($instance[$author_name]) ? $instance[$author_name] : '';
		$text = isset($instance[$text_name]) && ! empty($instance[$text_name])     ? $instance[$text_name] : '';
		$image = isset($instance[$image_name]) && ! empty($instance[$image_name])  ? $instance[$image_name] : '';
		$signature = isset($instance[$image_signature]) && ! empty($instance[$image_signature]) ? $instance[$image_signature] : '';        
		$bg = isset($instance[$bg_image]) && ! empty($instance[$bg_image]) ? $instance[$bg_image] : '';

		foreach ( wgl_user_social_medias_arr() as $soc_name => $value) {
			${$soc_name} = isset($instance[$soc_name]) && ! empty($instance[$soc_name]) ? $instance[$soc_name] : '';
		}

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( $title_name ) ); ?>"><?php esc_html_e( 'Title:', 'zikzag-core' ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr(  $this->get_field_id( $title_name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $title_name ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( $author_name ) ); ?>"><?php esc_html_e( 'Author Name:', 'zikzag-core' ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr(  $this->get_field_id( $author_name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $author_name ) ); ?>" type="text" value="<?php echo esc_attr( $name ); ?>">
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( $text_name ) ); ?>"><?php esc_html_e( 'Text:', 'zikzag-core' ); ?></label> 
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( $text_name ) ); ?>" name="<?php echo esc_attr(  $this->get_field_name( $text_name ) ); ?>" row="2"><?php echo esc_html( $text ); ?></textarea>
		</p>

		<p>
		  <label for="<?php echo esc_attr( $this->get_field_id($image_name) ); ?>"><?php esc_html_e( 'Author Image:', 'zikzag-core' ); ?></label><br />
			<img class="zikzag_media_image" src="<?php if (! empty($instance[$image_name])) {echo esc_url( $instance[$image_name] );} ?>" style="max-width: 100%" />
			<input type="text" class="widefat zikzag_media_url" name="<?php echo esc_attr( $this->get_field_name($image_name) ); ?>" id="<?php echo esc_attr( $this->get_field_id($image_name) ); ?>" value="<?php echo esc_attr( $image ); ?>">
			<a href="#" class="button zikzag_media_upload"><?php esc_html_e('Upload', 'zikzag-core'); ?></a>
		</p>        

		<p>
		  <label for="<?php echo esc_attr( $this->get_field_id($bg_image) ); ?>"><?php esc_html_e( 'Background Image:', 'zikzag-core' ); ?></label><br />
			<img class="zikzag_media_image" src="<?php if (! empty($instance[$bg_image])) {echo esc_url( $instance[$bg_image] );} ?>" style="max-width: 100%" />
			<input type="text" class="widefat zikzag_media_url" name="<?php echo esc_attr( $this->get_field_name($bg_image) ); ?>" id="<?php echo esc_attr( $this->get_field_id($bg_image) ); ?>" value="<?php echo esc_attr( $bg ); ?>">
			<a href="#" class="button zikzag_media_upload"><?php esc_html_e('Upload', 'zikzag-core'); ?></a>
		</p>        

		<p>
		  <label for="<?php echo esc_attr( $this->get_field_id($image_signature) ); ?>"><?php esc_html_e( 'Author Signature:', 'zikzag-core' ); ?></label><br />
			<img class="zikzag_media_image" src="<?php if (! empty($instance[$image_signature])) {echo esc_url( $instance[$image_signature] );} ?>" style="max-width: 100%" />
			<input type="text" class="widefat zikzag_media_url" name="<?php echo esc_attr( $this->get_field_name($image_signature) ); ?>" id="<?php echo esc_attr( $this->get_field_id($image_signature) ); ?>" value="<?php echo esc_attr( $signature ); ?>">
			<a href="#" class="button zikzag_media_upload"><?php esc_html_e('Upload', 'zikzag-core'); ?></a>
		</p>

		<?php
		foreach ( wgl_user_social_medias_arr() as $soc_name => $value) { ?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( $soc_name ) ); ?>" style="text-transform: capitalize;"><?php echo esc_html( $value.':' ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $soc_name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $soc_name ) ); ?>" type="text" value="<?php echo esc_attr( ${$soc_name} ); ?>">
			</p>
			<?php
		}
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance) {
		return $new_instance;
	}

}

function author_register_widgets() {
	register_widget('author');
}

add_action('widgets_init', 'author_register_widgets');

?>