<?php


/**
 * Class - Options
 */
class wps_ic_options {

	public static $options;
	public static $recommendedSettings;
	public static $safeSettings;

	public function __construct() {
		$this::$recommendedSettings = [
			'live-cdn'               => 1,
			'serve'                  => [
				'jpg'   => 1,
				'png'   => 1,
				'gif'   => 1,
				'svg'   => 1,
				'css'   => 1,
				'js'    => 1,
				'fonts' => 0
			],
			'css'                    => 1,
			'js'                     => 1,
			'fonts'                  => 0,
			'generate_adaptive'      => 1,
			'generate_webp'          => 1,
			'retina'                 => 1,
			'lazy'                   => 1,
			'qualityLevel'           => 2,
			'on-upload'              => 0,
			'emoji-remove'           => 1,
			'disable-oembeds'        => 0,
			'disable-dashicons'      => 0,
			'disable-gutenberg'      => 0,
			'external-url'           => 0,
			'disable-cart-fragments' => 0,
			'iframe-lazy'            => 0,
			'critical'               => [ 'css' => 0 ],
			'css_minify'             => 0,
			'js_minify'              => 0,
			'js_defer'               => 0,
			'delay-js'               => 0,
			'inline-js'              => 0,
			'cache'                  => [ 'advanced' => 1, 'mobile' => 1, 'minify' => 0 ],
			'local'                  => [ 'media-library' => 0 ],
			'status'                 => [
				'hide_in_admin_bar'        => 0,
				'hide_cache_status'        => 0,
				'hide_critical_css_status' => 0,
				'hide_preload_status'      => 0
			],
			'hide_compress'          => 0,
		];

		$this::$safeSettings = [
			'live-cdn'               => 0,
			'serve'                  => [
				'jpg'   => 0,
				'png'   => 0,
				'gif'   => 0,
				'svg'   => 0,
				'css'   => 0,
				'js'    => 0,
				'fonts' => 0
			],
			'css'                    => 0,
			'js'                     => 0,
			'fonts'                  => 0,
			'generate_adaptive'      => 0,
			'generate_webp'          => 0,
			'retina'                 => 0,
			'lazy'                   => 0,
			'qualityLevel'           => 2,
			'on-upload'              => 0,
			'emoji-remove'           => 0,
			'disable-oembeds'        => 0,
			'disable-dashicons'      => 0,
			'disable-gutenberg'      => 0,
			'external-url'           => 0,
			'disable-cart-fragments' => 0,
			'iframe-lazy'            => 0,
			'critical'               => [ 'css' => 0 ],
			'css_minify'             => 0,
			'js_minify'              => 0,
			'js_defer'               => 0,
			'delay-js'               => 0,
			'inline-js'              => 0,
			'cache'                  => [ 'advanced' => 0, 'mobile' => 0, 'minify' => 0 ],
			'local'                  => [ 'media-library' => 0 ],
			'status'                 => [
				'hide_in_admin_bar'        => 0,
				'hide_cache_status'        => 0,
				'hide_critical_css_status' => 0,
				'hide_preload_status'      => 0
			],
			'hide_compress'          => 0,
		];

		return $this;
	}


	public function get_preset( $preset ) {
		$settings = '';

		switch ( $preset ) {
			case 'recommended':
				$settings = self::$recommendedSettings;
				break;
			case 'safe':
				$settings = self::$safeSettings;
				break;
		}

		return $settings;
	}


	public function setMissingSettings( $settings ) {
		foreach ( $this::$recommendedSettings as $option_key => $option_value ) {
			if ( is_array( $option_value ) ) {
				foreach ( $option_value as $sub_key => $sub_value ) {
					if ( ! isset( $settings[ $option_key ][ $sub_key ] ) ) {
						$settings[ $option_key ][ $sub_key ] = 0;
					}
				}
			} else {
				if ( ! isset( $settings[ $option_key ] ) ) {
					$settings[ $option_key ] = 0;
				}
			}
		}

		return $settings;
	}


	/**
	 * Save settings
	 */
	public function save_settings() {
		if ( ! empty( $_POST ) ) {
			$options                           = get_option( WPS_IC_SETTINGS );
			$_POST['wp-ic-setting']['unlocks'] = $options['unlocks'];

			if ( empty( $_POST['wp-ic-setting']['optimization'] ) || $_POST['wp-ic-setting']['optimization'] == '0' ) {
				$_POST['wp-ic-setting']['optimization'] = 'maximum';
			}

			if ( empty( $_POST['wp-ic-setting']['optimize_upload'] ) ) {
				$_POST['wp-ic-setting']['optimize_upload'] = '0';
			}

			if ( empty( $_POST['wp-ic-setting']['ignore_larger_images'] ) ) {
				$_POST['wp-ic-setting']['ignore_larger_images'] = '0';
			}

			if ( empty( $_POST['wp-ic-setting']['resize_larger_images'] ) ) {
				$_POST['wp-ic-setting']['resize_larger_images'] = '0';
			}

			if ( empty( $_POST['wp-ic-setting']['resize_larger_images_width'] ) ) {
				$_POST['wp-ic-setting']['resize_larger_images_width'] = '2048';
			}

			if ( empty( $_POST['wp-ic-setting']['ignore_larger_images_width'] ) ) {
				$_POST['wp-ic-setting']['ignore_larger_images_width'] = '2048';
			}

			if ( empty( $_POST['wps_no']['time'] ) ) {
				$_POST['wp-ic-setting']['wps_no']['time'] = '';
			}

			if ( empty( $_POST['wp-ic-setting']['backup'] ) ) {
				$_POST['wp-ic-setting']['backup'] = '0';
			}

			if ( empty( $_POST['wp-ic-setting']['hide_compress'] ) ) {
				$_POST['wp-ic-setting']['hide_compress'] = '0';
			}

			if ( empty( $_POST['wp-ic-setting']['thumbnails_locally'] ) ) {
				$_POST['wp-ic-setting']['thumbnails_locally'] = '0';
			}

			if ( empty( $_POST['wp-ic-setting']['debug'] ) ) {
				$_POST['wp-ic-setting']['debug'] = '0';
			}

			if ( empty( $_POST['wp-ic-setting']['preserve_exif'] ) ) {
				$_POST['wp-ic-setting']['preserve_exif'] = '0';
			}

			if ( empty( $_POST['wp-ic-setting']['night_owl'] ) ) {
				$_POST['wp-ic-setting']['night_owl'] = '0';
			}

			if ( empty( $_POST['wp-ic-setting']['otto'] ) ) {
				$_POST['wp-ic-setting']['otto'] = 'off';
			}

			if ( empty( $_POST['wp-ic-setting']['night_owl_upload'] ) ) {
				$_POST['wp-ic-setting']['night_owl_upload'] = '0';
			}

			if ( ! empty( $_POST['wp-ic-setting']['thumbnails'] ) ) {
				foreach ( $_POST['wp-ic-setting']['thumbnails'] as $key => $value ) {
					$_POST['wp-ic-setting']['thumbnails'][ $key ] = 1;
				}
			}

			// Sanitize
			foreach ( $_POST['wp-ic-setting'] as $key => $value ) {
				$_POST['wp-ic-setting'][ $key ] = $value;
			}

			update_option( WPS_IC_SETTINGS, $_POST['wp-ic-setting'] );
		}
	}


	/**
	 * Get compress stats (total images, total saved)
	 * @return mixed|void
	 */
	public function get_stats() {
		global $wpdb;

		$query = $wpdb->prepare( "SELECT COUNT(ID) as images, SUM(saved) as saved FROM " . $wpdb->prefix . "ic_compressed ORDER by ID" );
		$query = $wpdb->get_results( $query );

		return [ 'images' => $query[0]->images, 'saved' => $query[0]->saved ];
	}


	/**
	 * Update stats
	 */
	public function update_stats( $attachment_ID = 1, $saved = '', $action = 'add' ) {
		global $wpdb;

		$attachment_ID = (int) $attachment_ID;
		$saved         = sanitize_text_field( $saved );

		if ( $action == 'add' ) {
			$query = $wpdb->prepare( "INSERT INTO " . $wpdb->prefix . "ic_compressed (created, attachment_ID, saved, count) VALUES (%s, %s, %s, %s) ON DUPLICATE KEY UPDATE created=%s, count=count+1, restored=0", current_time( 'mysql' ), $attachment_ID, $saved, current_time( 'mysql' ), '1' );
			$wpdb->query( $query );
		} else {
			//
		}
	}


	/**
	 * Get various settings for WP Compress
	 * @return mixed|void
	 */
	public function get_settings() {
		$settings = get_option( WPS_IC_SETTINGS );

		if ( ! $settings ) {
			$this->set_recommended_options();
			$settings = get_option( WPS_IC_SETTINGS );
		}

		return $settings;
	}


	/**
	 * Set recommended options
	 */
	public function set_recommended_options() {
		$settings_opt = get_option( WPS_IC_SETTINGS );

		if ( ! empty( $settings['hide_compress'] ) ) {
			$settings['hide_compress'] = '0';
		}

		if ( ! $settings_opt ) {
			// Save the settings
			update_option( WPS_IC_SETTINGS, self::$recommendedSettings );
		}
	}


	/**
	 * Set missing options
	 */
	public function set_missing_options() {
		$settings = array();

		$settings = get_option( WPS_IC_SETTINGS );

		if ( ! $settings ) {
			$settings['live-cdn'] = '1';
		}

		// Save the settings
		update_option( WPS_IC_SETTINGS, $settings );
	}


	/**
	 * Fetch specific option or all options if key is empty
	 *
	 * @param null $key
	 *
	 * @return bool|mixed|void
	 */
	public function get_option( $key = null ) {
		$options = get_option( WPS_IC_OPTIONS );

		if ( $key == null ) {
			if ( empty( $options ) ) {
				return false;
			}

			return $options;
		} else {
			if ( empty( $options[ $key ] ) ) {
				return false;
			}

			return $options[ $key ];
		}
	}


	/**
	 * Set option with key and value
	 *
	 * @param $key
	 * @param $value
	 */
	public function set_option( $key, $value ) {
		$options         = get_option( WPS_IC_OPTIONS );
		$options[ $key ] = $value;
		update_option( WPS_IC_OPTIONS, $options );
	}

	/**
	 * Setup default settings
	 */
	public function set_defaults() {
		$this->set_recommended_options();
	}

	public function getDefault() {
		return self::$recommendedSettings;
	}


	public function getSafe() {
		return self::$safeSettings;
	}

}