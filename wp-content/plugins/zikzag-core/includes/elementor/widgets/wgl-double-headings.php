<?php
namespace WglAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Utils;


defined( 'ABSPATH' ) || exit; // Abort, if called directly.

class Wgl_Double_Headings extends Widget_Base {

    public function get_name() {
        return 'wgl-double-headings';
    }

    public function get_title() {
        return esc_html__('WGL Double Headings', 'zikzag-core');
    }

    public function get_icon() {
        return 'wgl-double-headings';
    }

    public function get_categories() {
        return [ 'wgl-extensions' ];
    }


    protected function register_controls() {
        $primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
        $secondary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
        $h_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);
        $main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);


        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> GENERAL
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'wgl_double_headings_section',
            [ 'label' => esc_html__('General', 'zikzag-core') ]
        );

        $this->add_control(
            'subtitle',
            [
                'label' => esc_html__('Subtitle', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'dynamic' => [ 'active' => true ],
                'placeholder' => esc_html__('subtitle', 'zikzag-core'),
                'default' => esc_html__('subtitle', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'dbl_title',
            [
                'label' => esc_html__('Title', 'zikzag-core'),
                'type' => Controls_Manager::TEXTAREA,
                'dynamic' => [ 'active' => true ],
                'rows' => 2,
                'default' => esc_html__('Heading​', 'zikzag-core'),
                'placeholder' => esc_html__('Heading​', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'alignment',
            [
                'label' => esc_html__('Alignment', 'zikzag-core'),
                'type' => Controls_Manager::CHOOSE,
                'separator' => 'before',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'zikzag-core'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'zikzag-core'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'zikzag-core'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'left',
                'prefix_class' => 'a',
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => esc_html__('Title Link', 'zikzag-core'),
                'type' => Controls_Manager::URL,
                'dynamic' => [ 'active' => true ],
                'placeholder' => esc_html__('https://your-link.com', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label' => esc_html__('Title Tag', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => '‹h1›',
                    'h2' => '‹h2›',
                    'h3' => '‹h3›',
                    'h4' => '‹h4›',
                    'h5' => '‹h5›',
                    'h6' => '‹h6›',
                    'span' => '‹span›',
                    'div' => '‹div›',
                ],
                'default' => 'h3',
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLES -> TITLE
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_title',
            [
                'label' => esc_html__('Title', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typo',
                'selector' => '{{WRAPPER}} .dbl__title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $h_font_color,
                'selectors' => [
                    '{{WRAPPER}} .dbl__title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLES -> SUBTITLE
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_subtitle',
            [
                'label' => esc_html__('Subtitle', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [ 'subtitle!'  => '' ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'subtitle_typo',
                'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_4['font_family']],
                    'font_weight' => ['default' => \Wgl_Addons_Elementor::$typography_4['font_weight']],
                ],
                'selector' => '{{WRAPPER}} .dbl__subtitle',
            ]
        );

        $this->add_control(
            'subtitle_color',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}} .dbl__subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'subtitle_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .dbl__subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $_s = $this->get_settings_for_display();

        if ($_s['link']['url']) {
            $this->add_render_attribute('link', 'class', 'dbl__link');
            $this->add_link_attributes('link', $_s['link']);
        }

        $this->add_render_attribute('heading_wrapper', 'class', 'wgl-double_heading');

        echo '<div ', $this->get_render_attribute_string( 'heading_wrapper' ), '>';

            if ($_s[ 'subtitle' ]) {
                echo '<div class="dbl__subtitle"><span>',
                    $_s[ 'subtitle' ],
                '</span></div>';
            }
            if ($_s[ 'link' ][ 'url' ]) echo '<a ', $this->get_render_attribute_string( 'link' ), '>';

            if ($_s[ 'dbl_title' ]) {
                echo '<', $_s[ 'title_tag' ], ' class="dbl__title-wrapper">',
                    '<span class="dbl__title">', $_s[ 'dbl_title' ], '</span>',
                '</', $_s[ 'title_tag' ], '>';
            }

            if ($_s[ 'link' ][ 'url' ]) echo '</a>';

        echo '</div>';

    }

    public function wpml_support_module() {
        add_filter( 'wpml_elementor_widgets_to_translate',  [$this, 'wpml_widgets_to_translate_filter']);
    }

    public function wpml_widgets_to_translate_filter( $widgets ){
        return \WglAddons\Includes\Wgl_WPML_Settings::get_translate(
            $this, $widgets
        );
    }

}