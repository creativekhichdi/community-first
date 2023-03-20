<?php
namespace WglAddons\Widgets;

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


if ( ! defined( 'ABSPATH' ) ) exit; // If this file is called directly, abort.

class Wgl_Header_Menu extends Widget_Base {

    public function get_name() {
        return 'wgl-menu';
    }

    public function get_title() {
        return esc_html__('WGL Menu', 'zikzag-core' );
    }

    public function get_icon() {
        return 'wgl-header-menu';
    }

    public function get_categories() {
        return [ 'wgl-header-modules' ];
    }

    public function get_script_depends() {
        return [
            'wgl-elementor-extensions-widgets',
        ];
    }

    // Adding the controls fields for the premium title
    // This will controls the animation, colors and background, dimensions etc
    protected function register_controls() {
        $theme_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-custom-color'));
        $second_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
        $third_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-third-color'));
        $header_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);
        $main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);

        /*-----------------------------------------------------------------------------------*/
        /*  Build Icon/Image Box
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_navigation_settings',
            [
                'label' => esc_html__( 'Navigation Settings', 'zikzag-core' ),
            ]
        );

        $this->add_control(
            'menu_choose',
            array(
                'label'             => esc_html__('Menu', 'zikzag-core'),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                    'default'          => esc_html__('Default', 'zikzag-core'),
                    'custom'          => esc_html__('Custom Menu', 'zikzag-core'),
                ],
                'default'           => 'default',
            )
        );

        $this->add_control(
            'custom_menu',
            array(
                'label'             => esc_html__('Custom Menu', 'zikzag-core'),
                'type'              => Controls_Manager::SELECT,
                'options'           => zikzag_get_custom_menu(),
                'default'           => 'Main',
                'condition' => [
                    'menu_choose' => 'custom',
                ],
            )
        );


        $this->add_control(
            'lavalamp_active',
            array(
                'label' => esc_html__('Enable Lavalamp Marker', 'zikzag-core'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
            )
        );

        $this->add_control(
            'menu_height',
            array(
                'label' => esc_html__( 'Menu Height', 'zikzag-core' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'step' => 1,
                'default' => 100,
                'description' => esc_html__( 'Enter value in pixels', 'zikzag-core' ),
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .primary-nav' => 'height: {{VALUE}}px;',
                ],
            )
        );

        $this->add_control(
            'menu_align',
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
            )
        );


        $this->end_controls_section();

        /*-----------------------------------------------------------------------------------*/
        /*  Style Section
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'menu_section',
            [
                'label' => esc_html__( 'Navigation Style', 'zikzag-core' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'menu_items_padding',
            [
                'label' => esc_html__( 'Padding', 'zikzag-core' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .primary-nav > ul > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .primary-nav > ul' => 'margin-left: -{{LEFT}}{{UNIT}}; margin-right: -{{RIGHT}}{{UNIT}}; margin-bottom: -{{BOTTOM}}{{UNIT}}',
                ],
            ]
        );

        $this->start_controls_tabs( 'menu_items_color_tabs' );

        $this->start_controls_tab(
            'custom_menu_items_color_normal',
            array(
                'label' => esc_html__( 'Normal' , 'zikzag-core' ),
            )
        );

        $this->add_control(
            'menu_items_color',
            array(
                'label' => esc_html__( 'Items Idle', 'zikzag-core' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .primary-nav > ul > li > a' => 'color: {{VALUE}}',
                ),
            )
        );


        $this->end_controls_tab();

        $this->start_controls_tab(
            'custom_menu_items_color_hover',
            array(
                'label' => esc_html__( 'Hover' , 'zikzag-core' ),
            )
        );

        $this->add_control(
            'menu_items_hover_color',
            array(
                'label' => esc_html__( 'Items Hover', 'zikzag-core' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .primary-nav > ul > li:hover > a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .primary-nav > ul > li:hover > a  > span > .menu-item_plus:before' => 'color: {{VALUE}}',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'custom_fonts_menu_items',
                'selector' => '{{WRAPPER}} .primary-nav>div>ul,{{WRAPPER}} .primary-nav>ul',
            )
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_sub_menu_settings',
            [
                'label' => esc_html__( 'Sub Menu Settings', 'zikzag-core' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'custom_sub_menu_background',
                'label' => esc_html__( 'Sub Menu Background', 'zikzag-core' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .primary-nav ul li ul',
            ]
        );

        $this->add_control(
            'custom_sub_menu_color',
            array(
                'label' => esc_html__( 'Color', 'zikzag-core' ),
                'type' => Controls_Manager::COLOR,
                'default' => $header_font_color,
                'selectors' => array(
                    '{{WRAPPER}} .primary-nav ul li ul' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'sub_menu_border',
                'selector' => '{{WRAPPER}} .primary-nav ul li ul li:not(:last-child)',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'sub_menu_shadow',
                'selector' => '{{WRAPPER}} .primary-nav ul li ul',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'custom_fonts_sub_menu_items',
                'selector' => '{{WRAPPER}} .primary-nav>div>ul ul,{{WRAPPER}} .primary-nav>ul ul',
            )
        );

        $this->end_controls_section();

    }

    public function render(){
        $settings = $this->get_settings_for_display();
        extract($settings);

        $menu = '';

        if($menu_choose === 'custom'){
            $menu = !empty($custom_menu) ? $custom_menu : '';
        }

        $this->add_render_attribute( 'menu', [
            'class' => [
                'primary-nav',
                'align-'.$menu_align,
                (!empty($lavalamp_active) ? 'menu_line_enable' : ''),
            ],
        ] );

        if (has_nav_menu( 'main_menu' )) {
            echo "<nav ",$this->get_render_attribute_string( 'menu' ),">";
            zikzag_main_menu ($menu);
            echo "</nav>";
            echo '<div class="mobile-hamburger-toggle"><div class="hamburger-box"><div class="hamburger-inner"></div></div></div>';
        }

    }

}