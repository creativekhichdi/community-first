<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://themeforest.net/user/webgeniuslab
 * @since             1.0.0
 * @package           Zikzag_Core
 *
 * @wordpress-plugin
 * Plugin Name:       Zikzag Core
 * Plugin URI:        https://themeforest.net/user/webgeniuslab
 * Description:       Core plugin for Zikzag Theme.
 * Version:           1.2.1
 * Author:            WebGeniusLab
 * Author URI:        https://themeforest.net/user/webgeniuslab
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       zikzag-core
 * Domain Path:       /languages
 */

defined('WPINC') || die; // Abort, if called directly.

/**
 * Current version of the plugin.
 */
$plugin_data = get_file_data(__FILE__, ['version' => 'Version']);
define('WGL_CORE_VERSION', $plugin_data['version']);

class Zikzag_CorePlugin
{
    function __construct()
    {
        add_action( 'admin_init', array( $this, 'check_version' ) );
        if ( ! self::compatible_version() ) {
            return;
        }
    }

    static function activation_check()
    {
        if ( ! self::compatible_version() ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die(__('Zikzag Core plugin compatible with Zikzag theme only!', 'zikzag-core'));
        }
    }

    // The backup sanity check, in case the plugin is activated in a weird way,
    // or the theme change after activation.
    function check_version()
    {
        if ( ! self::compatible_version() ) {
            if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
                deactivate_plugins( plugin_basename( __FILE__ ) );
                add_action( 'admin_notices', array( $this, 'disabled_notice' ) );
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] );
                }
            }
        }
    }

    function disabled_notice()
    {
        echo '<strong>',
            esc_html__( 'Zikzag Core plugin compatible with Zikzag theme only!', 'zikzag-core' ),
        '</strong>';
    }

    static function compatible_version() {
        $theme_name = wp_get_theme()->get( 'TextDomain' );
        $theme_name = str_replace('-child', '', $theme_name);
        $wgl_theme = stripos(trim(dirname(plugin_basename(__FILE__))), $theme_name) !== false;

        if ( !(bool) $wgl_theme ) {
             return false;
         }

        return true;
    }
}

new Zikzag_CorePlugin();

register_activation_hook( __FILE__, array( 'Zikzag_CorePlugin', 'activation_check' ) );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wgl-core-activator.php
 */
function activate_zikzag_core()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wgl-core-activator.php';
    Zikzag_Core_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wgl-core-deactivator.php
 */
function deactivate_zikzag_core()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wgl-core-deactivator.php';
    Zikzag_Core_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_zikzag_core' );
register_deactivation_hook( __FILE__, 'deactivate_zikzag_core' );


/**
 * The code that runs during plugin activation.
 * admin-specific hooks add theme option preset hooks.
 */
function add_defaults_preset()
{
    $name = wp_get_theme()->get( 'TextDomain' );
    $name = str_replace('-child', '', $name);
    if(function_exists($name.'_default_preset')){
        $presets =  call_user_func($name.'_default_preset');
        $options_presets = array();
        if(is_array($presets)){
            foreach ($presets as $key => $value) {
                $options_presets[$key] = json_decode($presets[$key],true);
            }
        }

        $default_option = get_option( $name . '_set_preset');
        $default_option['default'] = $options_presets;

        update_option( $name . '_set_preset', $default_option );
    }
}

register_activation_hook(__FILE__,'add_defaults_preset'  );

add_action('after_setup_theme','wgl_role_preset');

function wgl_role_preset(){
    $name = wp_get_theme()->get( 'TextDomain' );
    $name = str_replace('-child', '', $name);
    $default_option = get_option( $name . '_set_preset');
    if(!$default_option){
        add_defaults_preset();
    }

}
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wgl-core.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_zikzag_core()
{
    $plugin = new Zikzag_Core();
    $plugin->run();
}

run_zikzag_core();
