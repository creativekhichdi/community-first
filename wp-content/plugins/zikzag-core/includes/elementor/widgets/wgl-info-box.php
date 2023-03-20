<?php

namespace WglAddons\Widgets;

defined( 'ABSPATH' ) || exit; // Abort, if called directly.

use WglAddons\Includes\Wgl_Icons;
use WglAddons\Includes\Wgl_Carousel_Settings;
use WglAddons\Templates\WglInfoBoxes;
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
use Elementor\Group_Control_Css_Filter;
use Elementor\Icons_Manager;


class Wgl_Info_Box extends Widget_Base
{

    public function get_name() {
        return 'wgl-info-box';
    }

    public function get_title() {
        return esc_html__('WGL Info Box', 'zikzag-core');
    }

    public function get_icon() {
        return 'wgl-info-box';
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

        $this->add_control(
            'layout',
            [
                'label' => esc_html__('Layout', 'zikzag-core'),
                'type' => 'wgl-radio-image',
                'condition' => [ 'icon_type!' => '' ],
                'options' => [
                    'top' => [
                        'title'=> esc_html__('Top', 'zikzag-core'),
                        'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/style_def.png',
                    ],
                    'left' => [
                        'title'=> esc_html__('Left', 'zikzag-core'),
                        'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/style_left.png',
                    ],
                    'right' => [
                        'title'=> esc_html__('Right', 'zikzag-core'),
                        'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/style_right.png',
                    ],
                ],
                'default' => 'left',
            ]
        );

        $this->add_control(
            'alignment',
            [
                'label' => esc_html__('Alignment', 'zikzag-core'),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => true,
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

	    $this->end_controls_section();

        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> ICON/IMAGE
        /*-----------------------------------------------------------------------------------*/

        $output = [];

        $output[ 'view' ] = [
            'label' => esc_html__('View', 'zikzag-core'),
            'type' => Controls_Manager::SELECT,
            'condition' => [ 'icon_type' => 'font' ],
            'options' => [
                'default' => esc_html__('Default', 'zikzag-core'),
                'stacked' => esc_html__('Stacked', 'zikzag-core'),
                'framed' => esc_html__('Framed', 'zikzag-core'),
            ],
            'default' => 'default',
            'prefix_class' => 'elementor-view-',
        ];

        $output[ 'shape' ] = [
            'label' => esc_html__('Shape', 'zikzag-core'),
            'type' => Controls_Manager::SELECT,
            'condition' => [
                'icon_type' => 'font',
                'view!' => 'default',
            ],
            'options' => [
                'circle' => esc_html__('Circle', 'zikzag-core'),
                'square' => esc_html__('Square', 'zikzag-core'),
            ],
            'default' => 'circle',
            'prefix_class' => 'elementor-shape-',
        ];

        $output[ 'morph_text' ] = [
            'label' => esc_html__('Text within morph', 'zikzag-core'),
            'type' => Controls_Manager::TEXT,
            'condition' => [ 'icon_type' => 'morph' ],
            'default' => esc_html__('01​', 'zikzag-core'),
        ];

        Wgl_Icons::init(
            $this,
            [
                'output' => $output,
                'section' => true,
                'default' => [
                    'extra_media_type' => true,
                    'media_type' => 'font',
                    'icon' => [
                        'library' => 'flaticon',
                        'value' => 'flaticon-work'
                    ],
                ]
            ]
        );

        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> CONTENT
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'wgl_ib_content',
            [ 'label' => esc_html__('Content', 'zikzag-core') ]
        );

        $this->add_control(
            'ib_title',
            [
                'label' => esc_html__('Title', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'dynamic' => [ 'active' => true ],
                'default' => esc_html__('This is the heading​', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'ib_subtitle',
            [
                'label' => esc_html__('Subtitle', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'dynamic' => [ 'active' => true ],
                'default' => '',
            ]
        );

        $this->add_control(
            'ib_content',
            [
                'label' => esc_html__('Content', 'zikzag-core'),
                'type' => Controls_Manager::WYSIWYG,
                'dynamic' => [ 'active' => true ],
                'placeholder' => esc_html__('Description Text', 'zikzag-core'),
                'label_block' => true,
                'default' => esc_html__('Click edit button to change this text.', 'zikzag-core'),
            ]
        );

        $this->end_controls_section();

        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> BUTTON
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_link',
            [ 'label' => esc_html__('Link', 'zikzag-core') ]
        );

	    $this->add_control(
		    'item_link',
		    [
			    'label' => esc_html__('Link', 'zikzag-core'),
			    'type' => Controls_Manager::URL,
			    'dynamic' => [ 'active' => true ],
		    ]
	    );

        $this->add_control(
            'add_item_link',
            [
                'label' => esc_html__('Whole Item Link', 'zikzag-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', 'zikzag-core'),
                'label_off' => esc_html__('Off', 'zikzag-core'),

            ]
        );

        $this->add_control(
            'add_read_more',
            [
                'label' => esc_html__('\'Read More\' Button', 'zikzag-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', 'zikzag-core'),
                'label_off' => esc_html__('Off', 'zikzag-core'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'read_more_text',
            [
                'label' => esc_html__('Button Text', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'condition' => [ 'add_read_more' => 'yes' ],
                'dynamic' => [ 'active' => true ],
                'label_block' => true,
                'separator' => 'before',
                'default' => '',
            ]
        );

	    $this->add_control(
		    'read_more_button_align',
		    [
			    'label' => esc_html__('Button Position', 'zikzag-core'),
			    'type' => Controls_Manager::SELECT,
			    'condition' => [
				    'layout' => [ 'left', 'right' ],
			    	'add_read_more' => 'yes'
			    ],
			    'options' => [
				    'default' => esc_html__('Default', 'zikzag-core'),
				    'left' => esc_html__('Left Corner', 'zikzag-core'),
				    'right' => esc_html__('Right Corner', 'zikzag-core'),
			    ],
			    'prefix_class' => 'btn_position-',
			    'default' => 'right',
		    ]
	    );

	    $this->add_control(
		    'read_more_icon_align',
		    [
			    'label' => esc_html__('Icon Position', 'zikzag-core'),
			    'type' => Controls_Manager::SELECT,
			    'condition' => [
				    'add_read_more' => 'yes',
				    'read_more_text!' => '',
			    ],
			    'options' => [
				    'left' => esc_html__('Before', 'zikzag-core'),
				    'right' => esc_html__('After', 'zikzag-core'),
			    ],
			    'default' => 'left',
		    ]
	    );

	    $this->add_control(
		    'read_more_icon_spacing',
		    [
			    'label' => esc_html__('Icon Spacing', 'zikzag-core'),
			    'type' => Controls_Manager::SLIDER,
			    'condition' => [
			    	'add_read_more' => 'yes',
			    	'read_more_text!' => '',
			    ],
			    'range' => [
				    'px' => [ 'min' => 0, 'max' => 100 ],
			    ],
			    'default' => [ 'size' => 13, 'unix' => 'px' ],
			    'selectors' => [
				    '{{WRAPPER}} .wgl-infobox_button.icon-position-right i' => 'margin-left: {{SIZE}}{{UNIT}};',
				    '{{WRAPPER}} .wgl-infobox_button.icon-position-left i' => 'margin-right: {{SIZE}}{{UNIT}};',
			    ],
		    ]
	    );

	    $this->add_control(
            'read_more_icon_fontawesome',
            [
                'label' => esc_html__('Icon', 'zikzag-core'),
                'type' => Controls_Manager::ICONS,
                'condition' => [ 'add_read_more' => 'yes' ],
                'label_block' => true,
                'description' => esc_html__('Select icon from available libraries.', 'zikzag-core'),
                'default' => [
                    'library' => 'wgl_icons',
                    'value' => 'flaticon flaticon-next-bottom',
                ],
            ]
        );

	    $this->add_responsive_control(
		    'read_more_icon_size',
		    [
			    'label' => esc_html__('Icon Size', 'zikzag-core'),
			    'type' => Controls_Manager::SLIDER,
			    'condition' => [
				    'add_read_more' => 'yes',
				    'read_more_icon_fontawesome!' => [
					    'library' => 'wgl_icons',
					    'value' => 'flaticon flaticon-long-next',
				    ],
			    ],
			    'range' => [
				    'px' => [ 'min' => 10, 'max' => 100 ],
			    ],
			    'default' => [ 'size' => 21 ],
			    'selectors' => [
				    '{{WRAPPER}} .wgl-infobox_button i' => 'font-size: {{SIZE}}{{UNIT}};',
			    ],
		    ]
	    );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> HOVER ANIMATION
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_content_animation',
            [ 'label' => esc_html__('Hover Animation', 'zikzag-core') ]
        );

        $this->add_control(
            'hover_lifting',
            [
                'label' => esc_html__('Lift up the item', 'zikzag-core'),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [ 'hover_toggling'  => '' ],
                'label_on' => esc_html__('On', 'zikzag-core'),
                'label_off' => esc_html__('Off', 'zikzag-core'),
                'return_value' => 'lifting',
                'prefix_class' => 'animation_',
            ]
        );

        $this->add_control(
            'hover_toggling',
            [
                'label' => esc_html__('Toggle Icon/Content Visibility', 'zikzag-core'),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'hover_lifting' => '',
                    'layout!' => [ 'left', 'right' ],
                    'icon_type!' => '',
                ],
                'label_on' => esc_html__('On', 'zikzag-core'),
                'label_off' => esc_html__('Off', 'zikzag-core'),
                'return_value' => 'toggling',
                'prefix_class' => 'animation_',
            ]
        );

        $this->add_responsive_control(
            'hover_toggling_offset',
            [
                'label' => esc_html__('Animation Distance', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'condition' => [
                    'hover_toggling!' => '',
                    'layout!' => [ 'left', 'right' ],
                    'icon_type!' => '',
                ],
                'range' => [
                    'px' => [ 'min' => 40, 'max' => 100 ],
                ],
                'default' => [ 'size' => 60 ],
                'selectors' => [
                    '{{WRAPPER}}.animation_toggling .wgl-infobox .content_wrapper' => 'transform: translateY({{SIZE}}{{UNIT}});',
                    '{{WRAPPER}}.animation_toggling .wgl-infobox:hover .content_wrapper' => 'transform: translateY(0);',
                ],
            ]
        );

        $this->add_responsive_control(
            'hover_toggling_transition',
            [
                'label' => esc_html__('Transition Duration', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'condition' => [
                    'hover_toggling!' => '',
                    'layout!' => [ 'left', 'right' ],
                    'icon_type!' => '',
                ],
                'range' => [
                    'px' => [ 'min' => 0.1, 'max' => 2, 'step' => 0.1 ],
                ],
                'default' => [ 'size' => 0.6 ],
                'selectors' => [
                    '{{WRAPPER}}.animation_toggling .content_wrapper,
                    {{WRAPPER}}.animation_toggling .media-wrapper' => 'transition-duration: {{SIZE}}s;',
                    '{{WRAPPER}}.animation_toggling .wgl-infobox .wgl-infobox-button_wrapper' => 'transition-duration: calc({{SIZE}}s/2); transition-delay: calc({{SIZE}}s/2);',
                    '{{WRAPPER}}.animation_toggling .wgl-infobox:hover .wgl-infobox-button_wrapper' => 'transition-duration: calc({{SIZE}}s/2); transition-delay: 0s;',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> MORPH
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_morph',
            [
                'label' => esc_html__('Morph', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [ 'icon_type' => 'morph' ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'morph',
                'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_2['font_family']],
                ],
                'selector' => '{{WRAPPER}} .morph_text',
            ]
        );

        $this->add_responsive_control(
            'morph_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'separator' => 'before',
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '48',
                    'left' => '-11',
                ],
                'selectors' => [
                    '{{WRAPPER}} .morph_text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'morph_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}} .morph_text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_morph_styles' );

        $this->start_controls_tab(
            'tab_morph_idle',
            [ 'label' => esc_html__('Idle', 'zikzag-core') ]
        );

        $this->add_control(
            'morph_color_idle',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $h_font_color,
                'selectors' => [
                    '{{WRAPPER}} .morph_text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'morph_bg_idle',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#f7f7f7',
                'selectors' => [
                    '{{WRAPPER}} .morph_text:before' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_morph_hover',
            [ 'label' => esc_html__('Hover', 'zikzag-core') ]
        );

        $this->add_control(
            'morph_color_hover',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $h_font_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox:hover .morph_text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'morph_bg_hover',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox:hover .morph_text:before' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
	    $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> ICON
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_icon',
            [
                'label' => esc_html__('Icon', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [ 'icon_type' => 'font' ],
            ]
        );

        $this->start_controls_tabs( 'icon_colors' );

        $this->start_controls_tab(
            'icon_colors_idle',
            [ 'label' => esc_html__('Idle', 'zikzag-core') ]
        );

        $this->add_control(
            'primary_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.elementor-view-framed .elementor-icon, {{WRAPPER}}.elementor-view-default .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'secondary_color',
            [
                'label' => esc_html__('Additional Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'condition' => [ 'view!' => 'default' ],
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}}.elementor-view-framed .elementor-icon' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'icon_shadow',
                'selector' => '{{WRAPPER}} .elementor-icon',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'icon_colors_hover',
            [ 'label' => esc_html__('Hover', 'zikzag-core') ]
        );

        $this->add_control(
            'hover_primary_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}.elementor-view-stacked .wgl-infobox:hover .elementor-icon' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.elementor-view-framed .wgl-infobox:hover .elementor-icon, {{WRAPPER}}.elementor-view-default .wgl-infobox:hover .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_secondary_color',
            [
                'label' => esc_html__('Additional Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'condition' => [ 'view!' => 'default' ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}.elementor-view-framed .wgl-infobox:hover .elementor-icon' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.elementor-view-stacked .wgl-infobox:hover .elementor-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'icon_hover_shadow',
                'selector' =>  '{{WRAPPER}} .wgl-infobox:hover .elementor-icon',
            ]
        );

        $this->add_control(
            'hover_animation_icon',
            [
                'label' => esc_html__('Hover Animation', 'zikzag-core'),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'hr_icon_style',
            [ 'type' => Controls_Manager::DIVIDER ]
        );

        $this->add_responsive_control(
            'icon_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
	                'top' => '16',
	                'right' => '30',
	                'bottom' => '0',
	                'left' => '0',
                ],
                'selectors' => [
                    '{{WRAPPER}} .media-wrapper .wgl-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'icon_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
	                'top' => '17',
	                'right' => '17',
	                'bottom' => '17',
	                'left' => '17',
	                'unit' => 'px',
	                'isLinked'  => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .media-wrapper .icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__('Size', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 6, 'max' => 300 ],
                ],
                'condition' => [
	                'icon_fontawesome!' => [
		                'library' => 'wgl_icons',
		                'value' => 'flaticon flaticon-long-next',
	                ],
                ],
                'default' => [ 'size' => 60 ],
                'selectors' => [
                    '{{WRAPPER}} .icon' => 'font-size: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_control(
            'rotate',
            [
                'label' => esc_html__('Rotate', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
	                'px' => [ 'min' => 0, 'max' => 360 ],
                ],
                'default' => [ 'size' => 0, 'unit' => 'px' ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon' => 'transform: rotate({{SIZE}}deg);',
                ],
            ]
        );

        $this->add_control(
            'border_width',
            [
                'label' => esc_html__('Border Width', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'condition' => [ 'view' => 'framed' ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'condition' => [ 'view!' => 'default' ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> IMAGE
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_image',
            [
                'label' => esc_html__('Image', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [ 'icon_type' => 'image' ],
            ]
        );

        $this->add_responsive_control(
            'image_space',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .media-wrapper.img-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_size',
            [
                'label' => esc_html__('Width', 'zikzag-core') . ' (%)',
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%' ],
                'range' => [
                    '%' => [ 'min' => 5, 'max' => 100 ],
                ],
                'default' => [ 'size' => 100, 'unit' => '%' ],
                'tablet_default' => [ 'unit' => '%' ],
                'mobile_default' => [ 'unit' => '%' ],
                'selectors' => [
                    '{{WRAPPER}} .media-wrapper.img-wrapper' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'hover_animation_image',
            [
                'label' => esc_html__('Hover Animation', 'zikzag-core'),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->start_controls_tabs( 'image_effects' );

        $this->start_controls_tab(
            'Idle',
            [ 'label' => esc_html__('Idle', 'zikzag-core') ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} .wgl-image-box_img img',
            ]
        );

        $this->add_control(
            'image_opacity',
            [
                'label' => esc_html__('Opacity', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 0.10, 'max' => 1, 'step' => 0.01 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-image-box_img img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_control(
            'background_hover_transition',
            [
                'label' => esc_html__('Transition Duration', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'default' => [ 'size' => 0.3 ],
                'range' => [
                    'px' => [ 'max' => 3, 'step' => 0.1 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-image-box_img img' => 'transition-duration: {{SIZE}}s',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'hover',
            [ 'label' => esc_html__('Hover', 'zikzag-core') ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters_hover',
                'selector' => '{{WRAPPER}} .wgl-infobox:hover .wgl-image-box_img img',
            ]
        );

        $this->add_control(
            'image_opacity_hover',
            [
                'label' => esc_html__('Opacity', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 0.10, 'max' => 1, 'step' => 0.01 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox:hover .wgl-image-box_img img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> TITLE
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'title_style_section',
            [
                'label' => esc_html__('Title', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
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
                    'div' => '‹div›',
                    'span' => '‹span›',
                ],
                'default' => 'h3',
            ]
        );

        $this->add_responsive_control(
            'title_offset',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'top' => '',
                    'right' => '',
                    'bottom' => '',
                    'left' => '',
                    'unit'  => 'px',
                    'isLinked'  => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'custom_fonts_title',
                'selector' => '{{WRAPPER}} .wgl-infobox_title',
            ]
        );

        $this->start_controls_tabs( 'tabs_title_styles' );

        $this->start_controls_tab(
            'tab_title_idle',
            [ 'label' => esc_html__('Idle', 'zikzag-core') ]
        );

        $this->add_control(
            'title_color_idle',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#232323',
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox_title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_title_hover',
            [ 'label' => esc_html__('Hover' , 'zikzag-core') ]
        );

        $this->add_control(
            'title_color_hover',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox:hover .wgl-infobox_title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> SUBTITLE
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'subtitle_style_section',
            [
                'label' => esc_html__('Subtitle', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [ 'ib_subtitle!' => '' ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'subtitle_typography',
                'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_family']],
                    'font_weight' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_weight']],
                ],
                'selector' => '{{WRAPPER}} .wgl-infobox_subtitle',
            ]
        );

        $this->add_responsive_control(
            'subtitle_offset',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox_subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_subtitle_styles' );

        $this->start_controls_tab(
            'tab_subtitle_idle',
            [ 'label' => esc_html__('Idle' , 'zikzag-core') ]
        );

        $this->add_control(
            'subtitle_color_idle',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#d6d6d6',
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox_subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_subtitle_hover',
            [ 'label' => esc_html__('Hover' , 'zikzag-core') ]
        );

        $this->add_control(
            'subtitle_color_hover',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox:hover .wgl-infobox_subtitle' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> CONTENT
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'content_style_section',
            [
                'label' => esc_html__('Content', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [ 'ib_content!' => '' ],
            ]
        );

        $this->add_control(
            'content_tag',
            [
                'label' => esc_html__('Content Tag', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => '‹h1›',
                    'h2' => '‹h2›',
                    'h3' => '‹h3›',
                    'h4' => '‹h4›',
                    'h5' => '‹h5›',
                    'h6' => '‹h5›',
                    'div' => '‹div›',
                    'span' => '‹span›',
                ],
                'default' => 'div',
            ]
        );

        $this->add_responsive_control(
            'content_offset',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox_content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox_content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'custom_content_mask_color',
                'label' => esc_html__('Background', 'zikzag-core'),
                'types' => [ 'classic', 'gradient' ],
                'condition' => [ 'custom_bg' => 'custom' ],
                'selector' => '{{WRAPPER}} .wgl-infobox_content',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'custom_fonts_content',
                'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_3['font_family']],
                    'font_weight' => ['default' => \Wgl_Addons_Elementor::$typography_3['font_weight']],
                ],
                'selector' => '{{WRAPPER}} .wgl-infobox_content',
            ]
        );

        $this->start_controls_tabs( 'content_color_tab' );

        $this->start_controls_tab(
            'custom_content_color_idle',
            [ 'label' => esc_html__('Idle' , 'zikzag-core') ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox_content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'custom_content_color_hover',
            [ 'label' => esc_html__('Hover' , 'zikzag-core') ]
        );

        $this->add_control(
            'content_color_hover',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox:hover .wgl-infobox_content' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();


	    /*-----------------------------------------------------------------------------------*/
	    /*  STYLE -> ITEM
		/*-----------------------------------------------------------------------------------*/

	    $this->start_controls_section(
		    'wgl_ib_item',
		    [
		    	'label' => esc_html__('Item', 'zikzag-core'),
		        'tab' => Controls_Manager::TAB_STYLE,
			    ]
	    );

	    $this->start_controls_tabs( 'item_bg' );
	    $this->start_controls_tab(
		    'item_bg_idle',
		    [
			    'label' => esc_html__('Idle' , 'zikzag-core'),
		    ]
	    );
	    $this->add_group_control(
		    Group_Control_Background::get_type(),
		    [
			    'name'      => 'bg_image_idle',
			    'label'     => esc_html__( 'Background', 'zikzag-core' ),
			    'types'     => [ 'classic', 'gradient', 'video' ],
			    'default'   => '',
			    'selector'  => '{{WRAPPER}} .wgl-infobox:before',
		    ]
	    );
	    $this->add_group_control(
		    Group_Control_Box_Shadow::get_type(),
		    [
			    'name' => 'item_shadow',
			    'selector' => '{{WRAPPER}} .wgl-infobox:before,{{WRAPPER}} .wgl-infobox:after',
			    'fields_options' => [
				    'box_shadow_type' => [
					    'default' => 'yes'
				    ],
				    'box_shadow' => [
					    'default' => [
						    'horizontal' => 6,
						    'vertical' => 5,
						    'blur' => 30,
						    'spread' => 0,
						    'color' => 'rgba(0,0,0,0.12)',
					    ]
				    ]
			    ]
		    ]
	    );
	    $this->end_controls_tab();
	    $this->start_controls_tab(
		    'item_bg_hover',
		    [
			    'label' => esc_html__('Hover' , 'zikzag-core'),
		    ]
	    );
	    $this->add_group_control(
		    Group_Control_Background::get_type(),
		    [
			    'name'      => 'bg_image_hover',
			    'label'     => esc_html__( 'Background', 'zikzag-core' ),
			    'types'     => [ 'classic', 'gradient', 'video' ],
			    'default'   => '',
			    'selector'  => '{{WRAPPER}} .wgl-infobox:hover:after',
		    ]
	    );
	    $this->add_group_control(
		    Group_Control_Box_Shadow::get_type(),
		    [
			    'name' => 'item_shadow_hover',
			    'selector' => '{{WRAPPER}} .wgl-infobox:hover:before,{{WRAPPER}} .wgl-infobox:hover:after',
		    ]
	    );
	    $this->end_controls_tab();
	    $this->end_controls_tabs();

	    $this->add_responsive_control(
		    'item_padding',
		    [
			    'label' => esc_html__('Padding', 'zikzag-core'),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', 'em', '%' ],
			    'default' => [
				    'top' => '29',
				    'right' => '29',
				    'bottom' => '25',
				    'left' => '29',
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .wgl-infobox' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
		    ]
	    );

	    $this->add_responsive_control(
		    'item_radius',
		    [
			    'label' => esc_html__('Border Radius', 'zikzag-core'),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%' ],
			    'selectors' => [
				    '{{WRAPPER}} .wgl-infobox:before, {{WRAPPER}} .wgl-infobox:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
		    ]
	    );

	    $this->end_controls_section();

        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> BUTTON
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'button_style_section',
            [
                'label' => esc_html__('Button', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [ 'add_read_more!' => '' ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'custom_fonts_button',
                'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_4['font_family']],
                    'font_weight' => ['default' => \Wgl_Addons_Elementor::$typography_4['font_weight']],
                ],
                'selector' => '{{WRAPPER}} .wgl-infobox_button span',
            ]
        );

        $this->add_responsive_control(
            'button_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox-button_wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
	                'top' => '0',
	                'right' => '16',
	                'bottom' => '17',
	                'left' => '0',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'button_color_tab',
            [ 'separator' => 'before' ]
        );

        $this->start_controls_tab(
            'tab_button_idle',
            [ 'label' => esc_html__('Idle' , 'zikzag-core') ]
        );

        $this->add_control(
            'button_color_idle',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#bdbdbd',
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox_button i, {{WRAPPER}} .wgl-infobox_button span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_idle',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox_button' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_idle',
                'label' => esc_html__('Border Type', 'zikzag-core'),
                'selector' => '{{WRAPPER}} .wgl-infobox_button',
                'fields_options' => [
                    'width' => [
                        'selectors' => [
                            '{{WRAPPER}} .wgl-infobox_button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_shadow',
                'selector' =>  '{{WRAPPER}} .wgl-infobox_button',
            ]
        );

        $this->end_controls_tab();

	    $this->start_controls_tab(
		    'tab_button_hover_item',
		    [ 'label' => esc_html__('Hover Item' , 'zikzag-core') ]
	    );

	    $this->add_control(
		    'button_color_hover_item',
		    [
			    'label' => esc_html__('Color', 'zikzag-core'),
			    'type' => Controls_Manager::COLOR,
			    'default' => $primary_color,
			    'selectors' => [
				    '{{WRAPPER}} .wgl-infobox:hover .wgl-infobox_button i, {{WRAPPER}} .wgl-infobox:hover .wgl-infobox_button span' => 'color: {{VALUE}};',
			    ],
		    ]
	    );

	    $this->add_control(
		    'button_bg_hover_item',
		    [
			    'label' => esc_html__('Background Color', 'zikzag-core'),
			    'type' => Controls_Manager::COLOR,
			    'selectors' => [
				    '{{WRAPPER}} .wgl-infobox:hover .wgl-infobox_button' => 'background: {{VALUE}};'
			    ],
		    ]
	    );

	    $this->add_group_control(
		    Group_Control_Border::get_type(),
		    [
			    'name' => 'button_hover_item',
			    'label' => esc_html__('Border Type', 'zikzag-core'),
			    'selector' => '{{WRAPPER}} .wgl-infobox:hover .wgl-infobox_button',
		    ]
	    );

	    $this->add_group_control(
		    Group_Control_Box_Shadow::get_type(),
		    [
			    'name' => 'button_shadow_hover_item',
			    'selector' => '{{WRAPPER}} .wgl-infobox:hover .wgl-infobox_button',
		    ]
	    );

	    $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [ 'label' => esc_html__('Hover Button' , 'zikzag-core') ]
        );

        $this->add_control(
            'button_color_hover',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox .wgl-infobox_button:hover i, {{WRAPPER}} .wgl-infobox .wgl-infobox_button:hover span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_hover',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wgl-infobox .wgl-infobox_button:hover' => 'background: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_hover',
                'label' => esc_html__('Border Type', 'zikzag-core'),
                'selector' => '{{WRAPPER}} .wgl-infobox .wgl-infobox_button:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_shadow_hover',
                'selector' => '{{WRAPPER}} .wgl-infobox .wgl-infobox_button:hover',
            ]
        );

	    $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();

        $info_box = new WglInfoBoxes();
        $info_box->render($this, $atts);
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
