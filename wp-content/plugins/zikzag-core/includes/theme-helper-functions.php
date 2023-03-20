<?php

// Add extra profile information
add_action( 'show_user_profile', 'wgl_extra_user_profile_fields' );
add_action( 'edit_user_profile', 'wgl_extra_user_profile_fields' );

function wgl_user_social_medias_arr() {
    return [ 
        'instagram' => 'Instagram', 
        'facebook-f' => 'Facebook', 
        'linkedin-in' => 'Linkedin', 
        'twitter' => 'Twitter', 
        'telegram-plane' => 'Telegram'
    ];
}

function wgl_extra_user_profile_fields( $user )
{ 
    ?>
    <h3><?php esc_html_e( 'Social media accounts', 'zikzag-core' ); ?></h3>

    <table class="form-table">
        <?php
        foreach ( wgl_user_social_medias_arr() as $social => $value) { ?>
            <tr>
                <th><label for="<?php echo esc_attr($social); ?>" style="text-transform: capitalize;"><?php esc_html_e( $value, 'zikzag-core' ); ?></label></th>
                <td>
                    <input type="text" name="<?php echo esc_attr($social); ?>" id="<?php echo esc_attr($social); ?>" value="<?php echo esc_attr( get_the_author_meta( $social, $user->ID ) ); ?>" class="regular-text" /><br />
                    <span class="description"><?php esc_html_e( 'Your '.$value.' url.', 'zikzag-core' ); ?></span>
                </td>
            </tr>
            <?php
        } ?>
    </table>
    <?php
}

add_action( 'personal_options_update', 'wgl_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'wgl_save_extra_user_profile_fields' );

function wgl_save_extra_user_profile_fields( $user_id )
{
    if (! current_user_can( 'edit_user', $user_id ) ) return false; 

    foreach ( wgl_user_social_medias_arr() as $social => $value) {
        update_user_meta( $user_id, $social, $_POST[ $social ] );
    }
}

add_action('wp_head','wgl_wp_head_custom_code',1000);
function wgl_wp_head_custom_code()
{
    // this code not only js or css / can insert any type of code
    
    if (class_exists('Zikzag_Theme_Helper')) {
        $header_custom_code = Zikzag_Theme_Helper::get_option('header_custom_js');
    }
    echo isset($header_custom_code) ? "<script>".$header_custom_code."</script>" : '';
}

add_action('wp_footer', 'wgl_custom_footer_js', 1000);

function wgl_custom_footer_js()
{
    if (class_exists('Zikzag_Theme_Helper')){
        $custom_js = Zikzag_Theme_Helper::get_option('custom_js');
    }
    echo isset($custom_js) ? '<script id="wgl_custom_footer_js">'.$custom_js.'</script>' : '';
}

// If Redux is running as a plugin, this will remove the demo notice and links
add_action( 'redux/loaded', 'remove_demo' );


/**
 * Removes the demo link and the notice of integrated demo from the redux-framework plugin
 */
if (! function_exists( 'remove_demo' )) {
    function remove_demo() {
        // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
        if ( class_exists( 'ReduxFrameworkPlugin' )) {
            remove_filter( 'plugin_row_meta', array(
                ReduxFrameworkPlugin::instance(),
                'plugin_metalinks'
            ), null, 2 );

            // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
            remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );
        }
    }
}

// Get User IP
if (! function_exists('wgl_get_ip')) {
    function wgl_get_ip()
    {
        if ( isset($_SERVER['HTTP_CLIENT_IP']) && ! empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] )) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = ( isset( $_SERVER['REMOTE_ADDR'] ) ) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        }
        $ip = filter_var( $ip, FILTER_VALIDATE_IP );
        $ip = ( $ip === false ) ? '0.0.0.0' : $ip;
        return $ip;
    }
}

// Minify dynamic CSS
if (! function_exists('wgl_minify_css')) {
	function wgl_minify_css( $css = null ) {
		if ( !$css ) { return; }

		// Combine css
		$css = str_replace( ', ', ',', $css );

		// Remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );

		// Remove space after colons
		$css = str_replace( ': ', ':', $css );

		// Remove whitespace
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );
		$css = trim( $css );

		return $css;
	}
}

?>
