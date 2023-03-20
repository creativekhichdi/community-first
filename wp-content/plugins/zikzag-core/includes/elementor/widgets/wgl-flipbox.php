<?php
namespace WglAddons\Widgets;

use Elementor\Icons_Manager;
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


defined( 'ABSPATH' ) || exit; // Abort, if called directly.

class Wgl_Flipbox extends Widget_Base {

    public function get_name() {
        return 'wgl-flipbox';
    }

    public function get_title() {
        return esc_html__('WGL Flipbox', 'zikzag-core');
    }

    public function get_icon() {
        return 'wgl-flipbox';
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
            'section_flipbox_settings',
            [ 'label' => esc_html__('General', 'zikzag-core') ]
        );

        $this->add_control(
            'flip_direction',
            [
                'label' => esc_html__('Border Type', 'Border Control', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'flip_right' => esc_html__('Flip to Right', 'zikzag-core'),
                    'flip_left' => esc_html__('Flip to Left', 'zikzag-core'),
                    'flip_top' => esc_html__('Flip to Top', 'zikzag-core'),
                    'flip_bottom' => esc_html__('Flip to Bottom', 'zikzag-core'),
                ],
                'default' => 'flip_right',
            ]
        );

        $this->add_control(
            'alignment',
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
                'default' => 'center',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_wrap' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'flipbox_height',
            [
                'label' => esc_html__('Custom Flipbox Height)', 'zikzag-core'),
                'type' => Controls_Manager::NUMBER,
                'min' => 150,
                'step' => 10,
                'default' => 320,
                'description' => esc_html__('Enter value in pixels', 'zikzag-core'),
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox' => 'height: {{VALUE}}px;',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> ICON
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_flipbox_icon',
            [ 'label' => esc_html__('Icon', 'zikzag-core') ]
        );

        $this->start_controls_tabs( 'flipbox_icon' );

        $this->start_controls_tab(
            'flipbox_front_icon',
            [ 'label' => esc_html__('Front', 'zikzag-core') ]
        );

        Wgl_Icons::init(
            $this,
            [
                'label' => esc_html__('Flipbox ', 'zikzag-core'),
                'output' => '',
                'section' => false,
                'prefix' => 'front_'
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'flipbox_back_icon',
            [
                'label' => esc_html__('Back', 'zikzag-core'),
            ]
        );

        Wgl_Icons::init(
            $this,
            [
                'label' => esc_html__('Flipbox ', 'zikzag-core'),
                'output' => '',
                'section' => false,
                'prefix' => 'back_'
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();


        //*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> CONTENT
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'wgl_ib_content',
            [ 'label' => esc_html__('Content', 'zikzag-core') ]
        );

        $this->start_controls_tabs( 'flipbox_content' );

        $this->start_controls_tab(
            'flipbox_front_content',
            [ 'label' => esc_html__('Front', 'zikzag-core') ]
        );

        $this->add_control(
            'front_title',
            [
                'label' => esc_html__('Title', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => esc_html__('This is the heading​', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'front_content',
            [
                'label' => esc_html__('Flipbox Text', 'zikzag-core'),
                'type' => Controls_Manager::WYSIWYG,
				'dynamic' => [ 'active' => true ],
				'placeholder' => esc_html__('Description Text', 'zikzag-core'),
				'label_block' => true,
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'flipbox_back_content',
            [
                'label' => esc_html__('Back', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'back_title',
            [
                'label' => esc_html__('Title', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => esc_html__('This is the heading​', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'back_content',
            [
                'label' => esc_html__('Flipbox Text', 'zikzag-core'),
                'type' => Controls_Manager::WYSIWYG,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__('Description Text', 'zikzag-core'),
                'label_block' => true,
                'default' => esc_html__('Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'zikzag-core'),
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        /*End General Settings Section*/
        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_link',
            [ 'label' => esc_html__('Flipbox Link', 'zikzag-core') ]
        );

	    $this->add_control(
		    'item_link',
		    [
			    'label' => esc_html__('Link', 'zikzag-core'),
			    'type' => Controls_Manager::URL,
			    'dynamic' => [ 'active' => true ],
			    'label_block' => true,
			    'condition' => [ 'add_item_link' => 'yes' ],
		    ]
	    );

        $this->add_control(
            'add_item_link',
            [
                'label' => esc_html__('Add Link To Whole Item', 'zikzag-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', 'zikzag-core'),
                'label_off' => esc_html__('Off', 'zikzag-core'),
                'return_value' => 'yes',

            ]
        );

        $this->add_control(
            'add_read_more',
            [
                'label' => esc_html__('Add \'Read More\' Button', 'zikzag-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', 'zikzag-core'),
                'label_off' => esc_html__('Off', 'zikzag-core'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'read_more_text',
            [
                'label' => esc_html__('Button Text', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
                'condition' => [
                    'add_read_more' => 'yes',
                ],
            ]
        );

	    $this->add_control(
		    'read_more_icon_align',
		    [
			    'label' => esc_html__('Icon Position', 'zikzag-core'),
			    'type' => Controls_Manager::SELECT,
			    'condition' => [ 'add_read_more' => 'yes' ],
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
			    'condition' => [ 'add_read_more' => 'yes' ],
			    'range' => [
				    'px' => [ 'min' => 0, 'max' => 100 ],
			    ],
			    'default' => [ 'size' => 13, 'unix' => 'px' ],
			    'selectors' => [
				    '{{WRAPPER}} .wgl-flipbox_item-link.icon-position-right i' => 'margin-left: {{SIZE}}{{UNIT}};',
				    '{{WRAPPER}} .wgl-flipbox_item-link.icon-position-left i' => 'margin-right: {{SIZE}}{{UNIT}};',
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
				    'library' => 'flaticon',
				    'value' => 'flaticon-long-next',
			    ],
		    ]
	    );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> GENERAL
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__('General', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'flipbox_style' );

        $this->start_controls_tab(
            'flipbox_front_style',
            [
                'label' => esc_html__('Front', 'zikzag-core'),
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'front_background',
				'label' => esc_html__('Front Background', 'zikzag-core'),
				'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .wgl-flipbox_front',
			]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'flipbox_back_style',
            [
                'label' => esc_html__('Back', 'zikzag-core'),
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'back_background',
				'label' => esc_html__('Back Background', 'zikzag-core'),
				'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .wgl-flipbox_back',
				'fields_options' => [
					'background' => [ 'default' => 'classic' ],
					'color' => [ 'default' => $primary_color ],
				],
			]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'flipbox_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'separator' => 'before',
                'default' => [
                    'top' => 20,
                    'right' => 20,
                    'bottom' => 20,
                    'left' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_front, {{WRAPPER}} .wgl-flipbox_back' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'flipbox_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_front, {{WRAPPER}} .wgl-flipbox_back' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'flipbox_border_radius',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default' => [
                    'top' => 10,
                    'right' => 10,
                    'bottom' => 10,
                    'left' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_front, {{WRAPPER}} .wgl-flipbox_back' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'flipbox_border',
				'selector' => '{{WRAPPER}} .wgl-flipbox_front, {{WRAPPER}} .wgl-flipbox_back',
				'separator' => 'before',
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'flipbox_shadow',
				'selector' => '{{WRAPPER}} .wgl-flipbox_front, {{WRAPPER}} .wgl-flipbox_back',
			]
		);

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> MEDIA
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_icon',
            [
                'label' => esc_html__('Media', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'media_styles' );

        $this->start_controls_tab(
            'front_media_style',
            [ 'label' => esc_html__('Front', 'zikzag-core') ]
        );

        $this->add_responsive_control(
            'front_media_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
	                'top' => 20,
	                'right' => 0,
	                'bottom' => 8,
	                'left' => 0,
	                'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_front .wgl-flipbox_media-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'front_icon_color',
            [
                'label' => esc_html__('Icon Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_front .wgl-icon' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'front_icon_type' => 'font',
                ]
            ]
        );

        $this->add_responsive_control(
            'front_icon_size',
            [
                'label' => esc_html__('Icon Size', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'condition' => [ 'front_icon_type' => 'font' ],
                'range' => [
                    'px' => [ 'min' => 16, 'max' => 100 ],
                ],
                'default' => [ 'size' => 49, 'unit' => 'px' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_front .wgl-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'back_media_style',
            [ 'label' => esc_html__('Back', 'zikzag-core') ]
        );

        $this->add_responsive_control(
            'back_media_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_back .wgl-flipbox_media-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'back_icon_color',
            [
                'label' => esc_html__('Icon Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'condition' => [ 'back_icon_type' => 'font' ],
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_back .wgl-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'back_icon_size',
            [
                'label' => esc_html__('Icon Size', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'condition' => [ 'back_icon_type' => 'font' ],
                'range' => [
                    'px' => [ 'min' => 16, 'max' => 100 ],
                ],
                'default' => [ 'size' => 55, 'unit' => 'px' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_back .wgl-icon' => 'font-size: {{SIZE}}{{UNIT}};',
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
                'default' => 'h3',
                'description' => esc_html__('Choose your tag for flipbox title', 'zikzag-core'),
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'DIV',
                    'span' => 'SPAN',
                ],
            ]
        );

        $this->start_controls_tabs( 'title_styles' );

        $this->start_controls_tab(
            'front_title_style',
            [ 'label' => esc_html__('Front', 'zikzag-core') ]
        );

        $this->add_responsive_control(
            'front_title_offset',
            [
                'label' => esc_html__('Title Offset', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'top' => 15,
                    'right' => 0,
                    'bottom' => 23,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_front .wgl-flipbox_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'custom_front_fonts_title',
                'selector' => '{{WRAPPER}} .wgl-flipbox_front .wgl-flipbox_title',
            ]
        );

        $this->add_control(
            'front_title_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $h_font_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_front .wgl-flipbox_title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'back_title_style',
            [ 'label' => esc_html__('Back', 'zikzag-core') ]
        );

        $this->add_responsive_control(
            'back_title_offset',
            [
                'label' => esc_html__('Title Offset', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'top' => 10,
                    'right' => 0,
                    'bottom' => 8,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_back .wgl-flipbox_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'custom_back_fonts_title',
                'selector' => '{{WRAPPER}} .wgl-flipbox_back .wgl-flipbox_title',
            ]
        );

        $this->add_control(
            'back_title_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $h_font_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_back .wgl-flipbox_title' => 'color: {{VALUE}};',
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
            ]
        );

        $this->start_controls_tabs( 'content_styles' );

        $this->start_controls_tab(
            'front_content_style',
            [ 'label' => esc_html__('Front', 'zikzag-core') ]
        );

        $this->add_responsive_control(
            'front_content_offset',
            [
                'label' => esc_html__('Content Offset', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_front .wgl-flipbox_content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'custom_front_fonts_content',
                'selector' => '{{WRAPPER}} .wgl-flipbox_front .wgl-flipbox_content',
            ]
        );

        $this->add_control(
            'front_content_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $main_font_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_front .wgl-flipbox_content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'back_content_style',
            [ 'label' => esc_html__('Back', 'zikzag-core') ]
        );

        $this->add_responsive_control(
            'back_content_offset',
            [
                'label' => esc_html__('Content Offset', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_back .wgl-flipbox_content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'custom_back_fonts_content',
                'selector' => '{{WRAPPER}} .wgl-flipbox_back .wgl-flipbox_content',
            ]
        );

        $this->add_control(
            'back_content_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_back .wgl-flipbox_content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
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
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_family']],
                    'font_weight' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_weight']],
                ],
                'selector' => '{{WRAPPER}} .wgl-flipbox_item-link',
            ]
        );

        $this->add_responsive_control(
            'custom_button_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_item-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'custom_button_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
                    'top' => 20,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_item-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'custom_button_border',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_item-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        $this->start_controls_tabs( 'button_color_tab' );

        $this->start_controls_tab(
            'custom_button_color_idle',
            [ 'label' => esc_html__('Idle' , 'zikzag-core') ]
        );

        $this->add_control(
            'button_background',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_item-link' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $secondary_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_item-link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'label' => esc_html__('Border Type', 'zikzag-core'),
                'selector' => '{{WRAPPER}} .wgl-flipbox_item-link',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_shadow',
                'selector' => '{{WRAPPER}} .wgl-flipbox_item-link.wgl-button.elementor-button',
            ]
        );


        $this->end_controls_tab();

        $this->start_controls_tab(
            'custom_button_color_hover',
            [ 'label' => esc_html__('Hover' , 'zikzag-core') ]
        );

        $this->add_control(
            'button_background_hover',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_item-link:hover' => 'background: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'button_color_hover',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $secondary_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-flipbox_item-link:hover' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border_hover',
                'label' => esc_html__('Border Type', 'zikzag-core'),
                'selector' => '{{WRAPPER}} .wgl-flipbox_item-link:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_shadow_hover',
                'selector' => '{{WRAPPER}} .wgl-flipbox_item-link:hover',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

    }

    public function render() {

        $settings = $this->get_settings_for_display();

        // HTML tags allowed for rendering
        $allowed_html = [
            'a' => [
                'href' => true, 'title' => true,
                'class' => true, 'style' => true,
            ],
            'br' => [],
            'em' => [],
            'strong' => [],
            'span' => [
                'class' => true,
                'style' => true,
            ],
            'p' => [
                'class' => true,
                'style' => true,
            ]
        ];

        $this->add_render_attribute(
            'flipbox',
            [
    			'class' => [
                    'wgl-flipbox',
                    'type_'.$settings[ 'flip_direction' ],
                ],
            ]
        );

        $this->add_render_attribute(
            'button_class',
            [
                'class' => [
                    'wgl-flipbox_item-link',
                    'button-read-more',
                    'icon-position-'.esc_attr($settings['read_more_icon_align'])
                ],
            ]
        );

	    // Link
	    if (!empty($settings[ 'item_link' ]['url'])) {
		    $this->add_link_attributes( 'item_link', $settings[ 'item_link' ] );
	    }

	    if ($settings[ 'add_item_link' ]) {
		    $link_attributes = $this->get_render_attribute_string('item_link');
		    $item_html_start = 'a ' . implode(' ', [$link_attributes]);
		    $item_html_end = 'a';
	    }else{
		    $item_html_start = $item_html_end ='div';
	    }

        // Icon/Image output
        ob_start();
        if (! empty($settings[ 'front_icon_type' ])) {
            $icons = new Wgl_Icons;
            echo $icons->build($this, $settings, 'front_' );
        }
        $front_media = ob_get_clean();
        // Icon/Image output
        ob_start();
        if (! empty($settings[ 'back_icon_type' ])) {
            $icons = new Wgl_Icons;
            echo $icons->build($this, $settings, 'back_' );
        }
        $back_media = ob_get_clean();
	    $ib_button = '';

	    // Read more button
	    if ( (bool)$settings[ 'add_read_more' ] ) {
		    $icon_font = $settings['read_more_icon_fontawesome'];

		    $migrated = isset($atts['__fa4_migrated']['read_more_icon_fontawesome']);
		    $is_new = Icons_Manager::is_migration_allowed();
		    $icon_output = '';

		    if ( $is_new || $migrated ) {
			    ob_start();
			    Icons_Manager::render_icon( $settings['read_more_icon_fontawesome'], [ 'aria-hidden' => 'true' ] );
			    $icon_output .= ob_get_clean();
		    } else {
			    $icon_output .= '<i class="icon '.esc_attr($icon_font).'"></i>';
		    }

		    $ib_button .= '<div class="wgl-flipbox_button-wrap">';
		    $ib_button .= '<div ' . $this->get_render_attribute_string('button_class') . '>';
		    if($settings['read_more_icon_align'] === 'left'){
			    $ib_button .= !empty($icon_font) ? $icon_output : '';
		    }
		    $ib_button .= '<span>' . esc_html($settings[ 'read_more_text' ]) . '</span>';
		    if($settings['read_more_icon_align'] === 'right'){
			    $ib_button .= !empty($icon_font) ? $icon_output : '';
		    }
		    $ib_button .= '</div>';
		    $ib_button .= '</div>';
	    }

	    ?>
        <div <?php echo $this->get_render_attribute_string( 'flipbox' ); ?>>
            <<?php echo $item_html_start; ?>  class="wgl-flipbox_wrap">
                <div class="wgl-flipbox_front"><?php
                    if ($settings[ 'front_icon_type' ] != '') {?>
                    <div class="wgl-flipbox_media-wrap"><?php
                        if (! empty($front_media)) {
                            echo $front_media;
                        }?>
                    </div><?php
                    }
                    if (! empty($settings[ 'front_title' ])) {?>
                        <<?php echo $settings[ 'title_tag' ]; ?> class="wgl-flipbox_title"><?php echo wp_kses( $settings[ 'front_title' ], $allowed_html );?></<?php echo $settings[ 'title_tag' ]; ?>><?php
                    }
                    if (! empty($settings[ 'front_content' ])) {?>
                        <div class="wgl-flipbox_content"><?php echo wp_kses( $settings[ 'front_content' ], $allowed_html );?></div><?php
                    }

	                echo $ib_button;
                    ?>
                </div>
                <div class="wgl-flipbox_back"><?php
                    if ($settings[ 'back_icon_type' ] != '') {?>
                    <div class="wgl-flipbox_media-wrap"><?php
                        if (! empty($back_media)) {
                            echo $back_media;
                        }?>
                    </div><?php
                    }
                    if (! empty($settings[ 'back_title' ])) {?>
                        <<?php echo $settings[ 'title_tag' ]; ?> class="wgl-flipbox_title"><?php echo wp_kses( $settings[ 'back_title' ], $allowed_html );?></<?php echo $settings[ 'title_tag' ]; ?>><?php
                    }
                    if (! empty($settings[ 'back_content' ])) {?>
                        <div class="wgl-flipbox_content"><?php echo wp_kses( $settings[ 'back_content' ], $allowed_html );?></div><?php
                    }
                    ?>
                </div>
            </<?php echo $item_html_end; ?>>
        </div>

        <?php
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