<?php

namespace WglAddons\Widgets;

defined('ABSPATH') || exit; // Abort, If called directly.

use WglAddons\Includes\Wgl_Icons;
use WglAddons\Includes\Wgl_Carousel_Settings;
use WglAddons\Includes\Wgl_Elementor_Helper;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;



class Wgl_Header_Logo extends Widget_Base {

    public function get_name() {
        return 'wgl-header-logo';
    }

    public function get_title() {
        return esc_html__('WGL Logo', 'zikzag-core' );
    }

    public function get_icon() {
        return 'wgl-header-logo';
    }

    public function get_categories() {
        return [ 'wgl-header-modules' ];
    }

    protected function register_controls() {
        $primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
        $secondary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
        $third_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-third-color'));
        $h_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);
        $main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);

        /*-----------------------------------------------------------------------------------*/
        /*  Build Icon/Image Box
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_logo_settings',
            [
                'label' => esc_html__( 'Logo Settings', 'zikzag-core' ),
            ]
        );

        $this->add_control(
            'use_custom_logo',
            array(
                'label' => esc_html__('Use Custom Logo?','zikzag-core' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'On', 'zikzag-core' ),
                'label_off' => esc_html__( 'Off', 'zikzag-core' ),
                'return_value' => 'yes',
            )
        );

        $this->add_control(
            'custom_logo',
            array(
                'label' => esc_html__( 'Custom Logo', 'zikzag-core' ),
                'type' => Controls_Manager::MEDIA,
                'condition' => [ 'use_custom_logo'  => 'yes' ],
                'label_block' => true,
                'default' => [ 'url' => Utils::get_placeholder_image_src() ],
            )
        );

        $this->add_control(
            'enable_logo_height',
            array(
                'label' => esc_html__('Enable Logo Height?','zikzag-core' ),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [ 'use_custom_logo'  => 'yes' ],
            )
        );

        $this->add_control('logo_height',
            array(
                'label' => esc_html__('Logo Height', 'zikzag-core'),
                'type' => Controls_Manager::NUMBER,
                'condition' => [
                    'use_custom_logo' => 'yes',
                    'enable_logo_height'  => 'yes',
                ],
                'min' => 1,
                'default' => '',
            )
        );

        $this->add_control(
            'logo_align',
            array(
                'label' => esc_html__( 'Alignment', 'zikzag-core' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'zikzag-core' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'zikzag-core' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'zikzag-core' ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'label_block' => false,
                'default' => 'left',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .wgl-logotype-container' => 'text-align: {{VALUE}};',
                ],
            )
        );

        $this->end_controls_section();

    }

    public function render() {

        $settings = $this->get_settings_for_display();
        extract($settings);

        $custom_size = $logo = false;


        if (! empty($use_custom_logo)) {
            $logo = $custom_logo;

            if (! empty($enable_logo_height)) {
                if (! empty($logo_height)) {
                    $custom_size = $logo_height;
                }
            }
        }

        $menu = false;

        require_once ( get_theme_file_path( '/templates/header/components/logo.php' ) );

        new \Zikzag_Get_Logo( 'bottom', false, $logo, $custom_size );
    }

}