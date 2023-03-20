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
use Elementor\Plugin;


class Wgl_Header_Delimiter extends Widget_Base {

    public function get_name() {
        return 'wgl-header-delimiter';
    }

    public function get_title() {
        return esc_html__('WGL Delimiter', 'zikzag-core' );
    }

    public function get_icon() {
        return 'wgl-header-delimiter';
    }

    public function get_categories() {
        return [ 'wgl-header-modules' ];
    }

    public function get_script_depends() {
        return [
            'wgl-elementor-extensions-widgets',
        ];
    }

    protected function register_controls() {
        $primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
        $secondary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
        $h_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);
        $main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);

        $this->start_controls_section(
            'section_delimiter_settings',
            [
                'label' => esc_html__( 'Delimiter Settings', 'zikzag-core' ),
            ]
        );

        $this->add_control(
            'delimiter_height',
            array(
                'label' => esc_html__( 'Delimiter Height', 'zikzag-core' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'step' => 1,
                'default' => 100,
                'description' => esc_html__( 'Enter value in pixels', 'zikzag-core' ),
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .delimiter' => 'height: {{VALUE}}px;',
                ],
            )
        );

        $this->add_control(
            'delimiter_width',
            array(
                'label' => esc_html__( 'Delimiter Width', 'zikzag-core' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'step' => 1,
                'default' => 1,
                'description' => esc_html__( 'Enter value in pixels', 'zikzag-core' ),
                'selectors' => [
                    '{{WRAPPER}} .delimiter' => 'width: {{VALUE}}px;',
                ],
            )
        );

        $this->add_control(
            'delimiter_align',
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
                    '{{WRAPPER}} .delimiter-wrapper' => 'text-align: {{VALUE}};',
                ],
            )
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'delimiter_background',
                'label' => esc_html__( 'Background', 'zikzag-core' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .delimiter',
            ]
        );


        $this->add_control(
            'delimiter_padding',
            [
                'label' => esc_html__( 'Margin', 'zikzag-core' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .delimiter' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    public function render(){
        echo '<div class="delimiter-wrapper"><div class="delimiter"></div></div>';
    }
}