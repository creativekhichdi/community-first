<?php

namespace WglAddons\Widgets;

defined( 'ABSPATH' ) || exit; // Abort, if called directly.

use WglAddons\Includes\Wgl_Icons;
use WglAddons\Widgets\Wgl_Button;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;


class Wgl_Pricing_Table extends Widget_Base
{

    public function get_name() {
        return 'wgl-pricing-table';
    }

    public function get_title() {
        return esc_html__('WGL Pricing Table', 'zikzag-core');
    }

    public function get_icon() {
        return 'wgl-pricing-table';
    }

    public function get_categories() {
        return [ 'wgl-extensions' ];
    }


    protected function register_controls()
    {
        $primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
        $h_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);
        $main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);

        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> GENERAL
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_content_general',
            [ 'label' => esc_html__('General', 'zikzag-core') ]
        );

        $this->add_responsive_control(
            'p_alignment',
            [
                'label' => esc_html__('Alignment', 'zikzag-core'),
                'type' => Controls_Manager::CHOOSE,
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
            'p_title',
            [
                'label' => esc_html__('Title', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
                'placeholder' => esc_html__('Title...', 'zikzag-core'),
                'default' => esc_html__('Basic Plan', 'zikzag-core'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'p_currency',
            [
                'label' => esc_html__('Currency', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
                'placeholder' => esc_html__('Currency...', 'zikzag-core'),
                'default' => esc_html__('$', 'zikzag-core'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'p_price',
            [
                'label' => esc_html__('Price', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
                'placeholder' => esc_html__('Price...', 'zikzag-core'),
                'default' => esc_html__('159,99', 'zikzag-core'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'p_period',
            [
                'label' => esc_html__('Period', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
                'placeholder' => esc_html__('Period...', 'zikzag-core'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'p_content',
            [
                'label' => esc_html__('Content', 'zikzag-core'),
                'type' => Controls_Manager::WYSIWYG,
                'label_block' => true,
                'default' => esc_html__('Your content...', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'p_description',
            [
                'label' => esc_html__('Description', 'zikzag-core'),
                'type' => Controls_Manager::TEXTAREA,
                'dynamic' => [ 'active' => true ],
                'label_block' => true,
                'placeholder' => esc_html__('Description...', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => esc_html__('Enable hover animation', 'zikzag-core'),
                'type' => Controls_Manager::SWITCHER,
                'description' => esc_html__('Lift up the item on hover.', 'zikzag-core'),
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> HIGHLIGHTER
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_content_highlighter',
            [ 'label' => esc_html__('Highlighter', 'zikzag-core') ]
        );

        $this->add_control(
            'highlighter_switch',
            [
                'label' => esc_html__('Use highlighting element?', 'zikzag-core'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'highlighter_text',
            [
                'label' => esc_html__('Highlighting Text', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'condition' => [ 'highlighter_switch' => 'yes' ],
                'dynamic' => [ 'active' => true ],
                'default' => esc_html__('Best', 'zikzag-core'),
                'label_block' => true,
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> BUTTON
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_content_button',
            [ 'label' => esc_html__('Button', 'zikzag-core') ]
        );

        $this->add_control(
            'b_switch',
            [
                'label' => esc_html__('Use button?','zikzag-core'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'b_title',
            [
                'label' => esc_html__('Button Text', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'condition' => [ 'b_switch' => 'yes' ],
                'dynamic' => [ 'active' => true ],
                'label_block' => true,
                'default' => esc_html__('Choose Plan', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'b_link',
            [
                'label' => esc_html__('Button Link', 'zikzag-core'),
                'type' => Controls_Manager::URL,
                'condition' => [ 'b_switch' => 'yes' ],
                'dynamic' => [ 'active' => true ],
                'label_block' => true,
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> HIGHLIGHTER
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_highlighter',
            [
                'label' => esc_html__('Highlighter', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [ 'highlighter_switch!' => '' ],
            ]
        );

        $this->add_control(
            'highlighter_color',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .pricing_highlighter' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'highlighter_bg_color',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#04131f',
                'selectors' => [
                    '{{WRAPPER}} .pricing_highlighter' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'highlighter_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
                    'top' => 200,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit'  => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .pricing_highlighter' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'highlighter_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
                    'top' => 6,
                    'right' => 14,
                    'bottom' => 6,
                    'left' => 6,
                    'unit'  => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .pricing_highlighter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'highlighter_b_radius',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default' => [
                    'top' => 21,
                    'right' => 0,
                    'bottom'=> 0,
                    'left' => 21,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .pricing_highlighter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> TITLE
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
                'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_family']],
                    'font_weight' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_weight']],
                ],
                'selector' => '{{WRAPPER}} .pricing_title',

            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $h_font_color,
                'selectors' => [
                    '{{WRAPPER}} .pricing_title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_bg',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pricing_title' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .pricing_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .pricing_title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'title_border_radius',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .pricing_title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'title_border',
                'selector' => '{{WRAPPER}} .pricing_title',
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> PRICE
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_price',
            [
                'label' => esc_html__('Price', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pricing_price_typo',
                'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_family']],
                    'font_weight' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_weight']],
                ],
                'selector' => '{{WRAPPER}} .pricing_price_wrap',
            ]
        );

        $this->add_control(
            'custom_price_color',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}} .pricing_price_wrap' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'price_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
                    'top' => '1',
                    'right' => '0',
                    'bottom'=> '26',
                    'left'  => '0',
                    'isLinked' => false
                ],
                'selectors' => [
                    '{{WRAPPER}} .pricing_price_wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

	    $this->add_group_control(
		    Group_Control_Border::get_type(),
		    [
			    'name' => 'price_border',
			    'selector' => '{{WRAPPER}} .pricing_price_wrap',
			    'fields_options' => [
				    'border' => [ 'default' => 'solid' ],
				    'width' => [
					    'default' => [
						    'top' => '0',
						    'right' => '0',
						    'bottom' => '1',
						    'left' => '0',
						    'isLinked' => false
					    ],
				    ],
				    'color' => [ 'default' => '#e6e6e6' ],
			    ],
		    ]
	    );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> PERIOD
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_period',
            [
                'label' => esc_html__('Period', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pricing_period_typo',
                'selector' => '{{WRAPPER}} .pricing_period',
                'fields_options' => [
                    'font_size' => [
                        'default' => [ 'size' => 0.3, 'unit' => 'em' ]
                    ]
                ],
            ]
        );

        $this->add_control(
            'period_color',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pricing_period' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'period_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .pricing_period' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> CONTENT
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_content',
            [
                'label' => esc_html__('Content', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pricing_content_typo',
                'selector' => '{{WRAPPER}} .pricing_content',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $h_font_color,
                'selectors' => [
                    '{{WRAPPER}} .pricing_content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content-padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .pricing_content' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}} !important; padding-right: {{RIGHT}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> DESCRIPTION
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_description',
            [
                'label' => esc_html__('Description', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [ 'p_description!' => '' ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pricing_desc_typo',
                'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_3['font_family']],
                    'font_weight' => ['default' => \Wgl_Addons_Elementor::$typography_3['font_weight']],
                ],
                'selector' => '{{WRAPPER}} .pricing_desc',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $main_font_color,
                'selectors' => [
                    '{{WRAPPER}} .pricing_desc' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'description_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .pricing_desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> BUTTON
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_button',
            [
                'label' => esc_html__('Button', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [ 'b_switch!' => '' ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .wgl-button',
            ]
        );

        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'tab_button_idle',
            [ 'label' => esc_html__('Idle', 'zikzag-core') ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .wgl-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'b_bg_idle',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .wgl-button',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [ 'label' => esc_html__('Hover', 'zikzag-core') ]
        );

        $this->add_control(
            'b_color_hover',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .wgl-button:hover, {{WRAPPER}} .wgl-button:focus' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'b_bg_hover',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $h_font_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-button:hover, {{WRAPPER}} .wgl-button:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_hover_box_shadow',
                'selector' => '{{WRAPPER}} .wgl-button:hover',
            ]
        );

        $this->add_control(
            'b_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'condition' => [ 'border_border!' => '' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-button:hover, {{WRAPPER}} .wgl-button:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .wgl-button',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'b_border_radius',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'b_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'condition' => [ 'b_switch' => 'yes' ],
                'separator' => 'before',
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'b_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'condition' => [ 'b_switch' => 'yes' ],
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> BACKGROUND
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_bg',
            [
                'label' => esc_html__('Background', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'bg_scheme',
            [
                'label' => esc_html__('Customize background for:', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'module' => esc_html__('whole module', 'zikzag-core'),
                    'sections'  => esc_html__('separate sections', 'zikzag-core'),
                ],
                'default' => 'module',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'module',
                'label' => esc_html__('Background', 'zikzag-core'),
                'types' => [ 'classic', 'gradient' ],
                'condition' => [ 'bg_scheme' => 'module' ],
                'selector' => '{{WRAPPER}} .pricing_plan_wrap',
            ]
        );

        $this->add_control(
            'header_s_bg',
            [
                'label' => esc_html__('Header Section Background', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'condition' => [ 'bg_scheme' => 'sections' ],
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}} .pricing_plan_wrap .pricing_header' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_s_bg',
            [
                'label' => esc_html__('Content Section Background', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'condition' => [ 'bg_scheme' => 'sections' ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pricing_plan_wrap .pricing_content' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'footer_s_bg',
            [
                'label' => esc_html__('Footer Section Background', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'condition' => [ 'bg_scheme' => 'sections' ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pricing_plan_wrap .pricing_footer' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'bg_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'separator' => 'before',
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
                    'top' => 50,
                    'right' => 50,
                    'bottom' => 55,
                    'left' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .pricing_header' => 'padding-top: {{TOP}}{{UNIT}};',
                    '{{WRAPPER}} .pricing_header, {{WRAPPER}} .pricing_content, {{WRAPPER}} .pricing_footer' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};',
                    '{{WRAPPER}} .pricing_footer' => 'padding-bottom: {{BOTTOM}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'bg_border_radius',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default' => [
                    'top' => 10,
                    'right' => 10,
                    'bottom' => 10,
                    'left' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .pricing_plan_wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'bg_border',
                'selector' => '{{WRAPPER}} .pricing_plan_wrap',
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $_s = $this->get_settings_for_display();

        $title = $description = $highlighter = $button = '';

        // Wrapper classes
        $wrap_classes = $_s['hover_animation'] ? ' hover-animation' : '';

        // Title
        if (!empty($_s['p_title'])) {
            $title .= '<div class="pricing_title_wrapper">';
            $title .= '<h4 class="pricing_title">';
                $title .= esc_html($_s['p_title']);
            $title .= '</h4>';
            $title .= '</div>';
        }

        // Currency
        $currency = ! empty($_s['p_currency']) ? '<span class="pricing_currency">'.esc_html($_s['p_currency']).'</span>' : '';

        // Price
        if ( isset($_s['p_price']) ) {
            preg_match( "/(\d+)(\.| |,)(\d+)$/", $_s['p_price'], $matches, PREG_OFFSET_CAPTURE );
            switch ( isset($matches[0]) ) {
                case false:
                    $price = '<div class="pricing_price">'.esc_html($_s['p_price']).'</div>';
                    break;
                case true:
                    $price = '<div class="pricing_price">';
                        $price .= esc_html($matches[1][0]);
                        $price .= '<span class="price_decimal">'.esc_html($matches[3][0]).'</span>';
                    $price .= '</div>';
                    break;
            }
        }

        // Period
        $period = ! empty($_s['p_period']) ? '<span class="pricing_period">'.esc_html($_s['p_period']).'</span>' : '';

        // Description
        if ( $_s['p_description'] ) {
            $allowed_html = [
                'a' => [
                    'href' => true, 'title' => true,
                    'class' => true, 'style' => true,
                ],
                'br' => [ 'class' => true ],
                'em' => [],
                'strong' => [],
                'span' => [ 'class' => true, 'style' => true ],
                'p' => [ 'class' => true, 'style' => true ],
                'ul' => [ 'class' => true, 'style' => true ],
                'ol' => [ 'class' => true, 'style' => true ],
            ];
            $description = '<div class="pricing_desc">'.wp_kses( $_s['p_description'], $allowed_html ).'</div>';
        }

        // Highlighter
        if ( $_s['highlighter_switch'] && ! empty($_s['highlighter_text']) ) {
            $highlighter = '<div class="pricing_highlighter">'.esc_html($_s['highlighter_text']).'</div>';
        }

        // Button
        if ( $_s['b_switch'] ) {
            $button_options = [
                'icon_type' => '',
                'text' => $_s['b_title'],
                'link' => $_s['b_link'],
                'size' => 'md',
            ];
            ob_start();
                echo Wgl_Button::init_button($this, $button_options);
            $button = ob_get_clean();
        }

        // Render
        echo '<div class="wgl-pricing_plan', $wrap_classes, '">',
            '<div class="pricing_plan_wrap">',
                '<div class="pricing_header">',
                    $highlighter,
                    $title,
                    '<div class="pricing_price_wrap">',
                        $currency,
                        $price,
                        $period,
                    '</div>',
                '</div>',
                '<div class="pricing_content">',
                    $_s['p_content'],
                '</div>',
                '<div class="pricing_footer">',
                    $description,
                    $button,
                '</div>',
            '</div>',
        '</div>';
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
