<?php

namespace WglAddons\Includes;

use Elementor\Plugin;

defined( 'ABSPATH' ) || exit;

/**
* Wgl Elementor Helper Settings
*
*
* @class        Wgl_Elementor_Helper
* @version      1.0
* @category     Class
* @author       WebGeniusLab
*/

if (!class_exists('Wgl_Elementor_Helper')) {
    class Wgl_Elementor_Helper
    {

        private static $instance = null;
        public static function get_instance() {
            
            if ( null == self::$instance ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function get_wgl_icons() {
            return [
                'long-next',
                'search',
                'bag',
                'search-1',
                'expand',
                'keyboard-right-arrow-button',
                'close-button',
                'favorite-heart-button',
                'add-plus-button',
                'back',
                'handshake',
                'idea',
                'money',
                'report',
                'search-2',
                'graphic',
                'payment',
                'chess',
                'money-1',
                'quote',
                'correct',
                'first',
                'business-and-finance',
                'mission',
                'next',
                'next-top',
                'next-bottom',
                'work',
                'goal',
                'business-and-finance-1',
                'placeholder',
                'phone-call',
                'email',
                'play',
                'search-3',
                'bag-1',
                'startup',
                'chain',
                'blog',
                'heart',
                'user',
                'dashboard',
                'jigsaw',
                'advisor',
                'paper',
                'round-information-button',
                'round-done-button',
                'round-add-button',
                'home-button',
                'round-help-button',
                'text-documents',
                'delete-button',
                'speech-bubble-with-ellipsis',
                'round-delete-button',
                'wall-clock',
                'circle',
                'preferences',
                'music-control-settings-button',
                'web-mark-as-favorite-star',
                'see',
                'shine',
            ];
        }

        public static function enqueue_css($style) {
            if (! (bool) Plugin::$instance->editor->is_edit_mode()) {
                if (! empty($style)) {
                    ob_start();             
                        echo $style;
                    $css = ob_get_clean();
                    $css = apply_filters( 'zikzag_enqueue_shortcode_css', $css, $style );   

                    return $css;
                }
            } else {
                echo '<style>'.esc_attr($style).'</style>';
            }
        }

        public function get_elementor_templates() {
            
            $options = [];

            $_templates = get_posts( array(
                'post_type' => 'elementor_library',
                'posts_per_page' => -1,
            ));
            
            if ( ! empty( $_templates ) && ! is_wp_error( $_templates ) ) {
                
                foreach ( $_templates as $_template ) {
                    $options[ $_template->ID ] = $_template->post_title;
                }
                
                update_option( 'temp_count', $options );
                
                return $options;
            }
        }
              
    }
    new Wgl_Elementor_Helper;
}
?>