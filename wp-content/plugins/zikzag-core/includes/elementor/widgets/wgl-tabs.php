<?php

namespace WglAddons\Widgets;

defined( 'ABSPATH' ) || exit; // Abort, if called directly.

use WglAddons\Includes\Wgl_Icons;
use WglAddons\Includes\Wgl_Elementor_Helper;
use Elementor\Frontend;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Repeater;

class Wgl_Tabs extends Widget_Base {

	public function get_name() {
		return 'wgl-tabs';
	}

	public function get_title() {
		return esc_html__('WGL Tabs', 'zikzag-core');
	}

	public function get_icon() {
		return 'wgl-tabs';
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
			'section_content_general',
			[ 'label' => esc_html__('General', 'zikzag-core') ]
		);

		$this->add_responsive_control(
			'tabs_tab_align',
			[
				'label' => esc_html__('Title Alignment', 'zikzag-core'),
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
					'justify' => [
						'title' => esc_html__('Justified', 'zikzag-core'),
						'icon' => 'fa fa-align-justify',
					],
				],
				'default' => 'justify',
			]
		);

		$this->add_control(
			'custom_responsive',
			[
				'label' => esc_html__('Customize Responsive', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'zikzag-core'),
				'label_off' => esc_html__('Off', 'zikzag-core'),
			]
		);

		$this->add_responsive_control(
			'responsive_items',
			[
				'label' => esc_html__('Items per Row', 'zikzag-core'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [ 'custom_responsive' => 'yes' ],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 12,
						'step' => 1,
					],
				],
				'desktop_default' => [
					'size' => 6,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 3,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header_wrap' => 'min-width: calc(100% / {{SIZE}});',
				],
			]
		);

		$this->end_controls_section();


		/*-----------------------------------------------------------------------------------*/
		/*  CONTENT -> CONTENT
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'section_content_content',
			[ 'label' => esc_html__('Content', 'zikzag-core') ]
		);

		$repeater = new Repeater();
        $repeater->add_control(
			'tabs_tab_title',
			[
                'label' => esc_html__('Tab Title', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Tab Title', 'zikzag-core'),
                'dynamic' => ['active' => true],
			]
        );
        $repeater->add_control(
			'tabs_tab_icon_type',
			[
                'label'             => esc_html__('Add Icon/Image', 'zikzag-core'),
                'type'              => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'           => [
                    ''              => [
                        'title' => esc_html__('None', 'zikzag-core'),
                        'icon' => 'fa fa-ban',
                    ],
                    'font'          => [
                        'title' => esc_html__('Icon', 'zikzag-core'),
                        'icon' => 'fa fa-smile-o',
                    ],
                    'image'         => [
                        'title' => esc_html__('Image', 'zikzag-core'),
                        'icon' => 'fa fa-picture-o',
                    ]
                ],
                'default'           => '',
			]
        );
        $repeater->add_control(
			'tabs_tab_icon_fontawesome',
			[
                'label'       => esc_html__( 'Icon', 'zikzag-core' ),
                'type'        => Controls_Manager::ICONS,
                'label_block' => true,
                'condition'     => [
                    'tabs_tab_icon_type'  => 'font',
                ],
                'description' => esc_html__( 'Select icon from Fontawesome library.', 'zikzag-core' ),
			]
        );
        $repeater->add_control(
			'tabs_tab_icon_thumbnail',
			[
                'label'       => esc_html__( 'Image', 'zikzag-core' ),
                'type'        => Controls_Manager::MEDIA,
                'label_block' => true,
                'condition'     => [
                    'tabs_tab_icon_type'   => 'image',
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
			]
        );
        $repeater->add_control(
			'tabs_content_type',
			[
                'label' => esc_html__('Content Type', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'content' => esc_html__('Content', 'zikzag-core'),
                    'template' => esc_html__('Saved Templates', 'zikzag-core'),
                ],
                'default' => 'content',
			]
        );
        $repeater->add_control(
			'tabs_content_templates',
			[
                'label' => esc_html__('Choose Template', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'options' => Wgl_Elementor_Helper::get_instance()->get_elementor_templates(),
                'condition' => [
                    'tabs_content_type' => 'template',
                ],
			]
        );
        $repeater->add_control(
			'tabs_content',
			[
                'label' => esc_html__('Tab Content', 'zikzag-core'),
                'type' => Controls_Manager::WYSIWYG,
                'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Optio, neque qui velit. Magni dolorum quidem ipsam eligendi, totam, facilis laudantium cum accusamus ullam voluptatibus commodi numquam, error, est. Ea, consequatur.', 'zikzag-core'),
                'dynamic' => ['active' => true],
                'condition' => [
                    'tabs_content_type' => 'content',
                ],
			]
        );

		$this->add_control(
			'tabs_tab',
			[
				'type' => Controls_Manager::REPEATER,
				'seperator' => 'before',
				'default' => [
					[ 'tabs_tab_title' => esc_html__('Tab Title 1', 'zikzag-core') ],
					[ 'tabs_tab_title' => esc_html__('Tab Title 2', 'zikzag-core') ],
					[ 'tabs_tab_title' => esc_html__('Tab Title 3', 'zikzag-core') ],
				],
				'fields' => $repeater->get_controls(),
				'title_field' => '{{tabs_tab_title}}',
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
				'name' => 'tabs_title_typo',
				'selector' => '{{WRAPPER}} .wgl-tabs_title',
			]
		);

		$this->add_control(
			'tabs_title_tag',
			[
				'label' => esc_html__('Title HTML Tag', 'zikzag-core'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
				],
				'default' => 'h4',
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label' => esc_html__('Padding', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
				    'top' => 14,
				    'right' => 30,
				    'bottom' => 12,
				    'left' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label' => esc_html__('Margin', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
				    'top' => 0,
				    'right' => 5,
				    'bottom' => 0,
				    'left' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wgl-tabs_headings' => 'margin-left: calc(-1 * {{LEFT}}{{UNIT}});margin-right: calc(-1 * {{RIGHT}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'tabs_title_line',
			[
				'label' => esc_html__('Add Title Bottom Triangle', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'return_value' => 'yes',
			]
		);
		$this->add_responsive_control(
			'tabs_title_triangle_spacing',
			[
				'label' => esc_html__('Triangle Spacing', 'zikzag-core'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 13,
					'unit' => 'px',
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header:after' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->start_controls_tabs( 'tabs_header_tabs' );

		$this->start_controls_tab(
			'tabs_header_idle',
			[ 'label' => esc_html__('Idle', 'zikzag-core') ]
		);

		$this->add_control(
			'title_color_idle',
			[
				'label' => esc_html__('Text Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'default' => $h_font_color,
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_bg_color_idle',
			[
				'label' => esc_html__('Background Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'default' => '#f5f5f5',
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			't_title_border_radius_idle',
			[
				'label' => esc_html__('Border Radius', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => 5,
					'right' => 5,
					'bottom' => 5,
					'left' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tabs_title_border',
				'selector' => '{{WRAPPER}} .wgl-tabs_header',
				'fields_options' => [
					'border' => [ 'default' => 'solid' ],
					'width' => [
						'default' => [
							'top' => 1,
							'right' => 1,
							'bottom' => 1,
							'left' => 1,
						],
					],
					'color' => [ 'default' => '#f5f5f5' ],
				],
			]
		);

		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'tabs_title_shadow',
                'selector' => '{{WRAPPER}} .wgl-tabs_header',
            ]
        );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_header_hover',
			[ 'label' => esc_html__('Hover', 'zikzag-core') ]
		);

		$this->add_control(
			't_title_color_hover',
			[
				'label' => esc_html__('Text Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			't_title_bg_color_hover',
			[
				'label' => esc_html__('Background Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'default' => $secondary_color,
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			't_title_line_color_hover',
			[
				'label' => esc_html__('Title Bottom Triangle Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'condition' => [ 'tabs_title_line' => 'yes' ],
				'default' => $secondary_color,
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header:hover:after' => 'border-right-color: {{VALUE}}; border-bottom-color: {{VALUE}};',
				],

			]
		);

		$this->add_control(
			't_title_border_radius_hover',
			[
				'label' => esc_html__('Border Radius', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 't_title_border_hover',
				'selector' => '{{WRAPPER}} .wgl-tabs_header:hover',
				'fields_options' => [
					'border' => [ 'default' => 'solid' ],
					'width' => [
						'default' => [
							'top' => 1,
							'right' => 1,
							'bottom' => 1,
							'left' => 1,
						],
					],
					'color' => [ 'default' => $secondary_color ],
				],
			]
		);
		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'tabs_title_shadow_hover',
                'selector' => '{{WRAPPER}} .wgl-tabs_header:hover',
            ]
        );

		$this->end_controls_tab();

		$this->start_controls_tab(
			't_header_active',
			[ 'label' => esc_html__('Active', 'zikzag-core') ]
		);

		$this->add_control(
			't_title_color_active',
			[
				'label' => esc_html__('Text Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header_wrap.active .wgl-tabs_header' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			't_title_bg_color_active',
			[
				'label' => esc_html__('Background Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'default' => $secondary_color,
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header_wrap.active .wgl-tabs_header' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			't_title_line_color_active',
			[
				'label' => esc_html__('Title Bottom Triangle Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'condition' => [ 'tabs_title_line' => 'yes' ],
				'default' => $secondary_color,
                'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header_wrap.active  .wgl-tabs_header:after' => 'border-right-color: {{VALUE}}; border-bottom-color: {{VALUE}};',
				],

			]
		);

		$this->add_control(
			't_title_border_radius_active',
			[
				'label' => esc_html__('Border Radius', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header_wrap.active .wgl-tabs_header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 't_title_border_active',
				'selector' => '{{WRAPPER}} .wgl-tabs_header_wrap.active .wgl-tabs_header',
				'fields_options' => [
					'border' => [ 'default' => 'solid' ],
					'width' => [
						'default' => [
							'top' => 1,
							'right' => 1,
							'bottom' => 1,
							'left' => 1,
						],
					],
					'color' => [ 'default' => $secondary_color ],
				],
			]
		);

		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'tabs_title_shadow_active',
                'selector' => '{{WRAPPER}} .wgl-tabs_header_wrap.active .wgl-tabs_header',
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
			]
		);

		$this->add_responsive_control(
			'tabs_icon_size',
			[
				'label' => esc_html__('Icon Size', 'zikzag-core'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 49,
					'unit' => 'px',
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_icon:not(.wgl-tabs_icon-image)' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tabs_icon_position',
			[
				'label' => esc_html__('Icon/Image Position', 'zikzag-core'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'top' => [
						'title' => esc_html__('Top', 'zikzag-core'),
						'icon' => 'eicon-v-align-top',
					],
					'right' => [
						'title' => esc_html__('Right', 'zikzag-core'),
						'icon' => 'eicon-h-align-right',
					],
					'bottom' => [
						'title' => esc_html__('Bottom', 'zikzag-core'),
						'icon' => 'eicon-v-align-bottom',
					],
					'left' => [
						'title' => esc_html__('Left', 'zikzag-core'),
						'icon' => 'eicon-h-align-left',
					]
				],
				'default' => 'top',
			]
		);

		$this->add_responsive_control(
			'tabs_icon_margin',
			[
				'label' => esc_html__('Margin', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 12,
					'left' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_icon_tabs' );

		$this->start_controls_tab(
			'tabs_icon_idle',
			[ 'label' => esc_html__('Idle', 'zikzag-core') ]
		);

		$this->add_control(
			'tabs_icon_color',
			[
				'label'   => esc_html__( 'Icon Color', 'zikzag-core' ),
				'type'    => Controls_Manager::COLOR,
				'default' => $primary_color,
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_icon_hover',
			[ 'label' => esc_html__('Hover', 'zikzag-core') ]
		);

		$this->add_control(
			'tabs_icon_color_hover',
			[
				'label' => esc_html__('Icon Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header:hover .wgl-tabs_icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_icon_active',
			[ 'label' => esc_html__('Active', 'zikzag-core') ]
		);

		$this->add_control(
			'tabs_icon_color_active',
			[
				'label' => esc_html__('Icon Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_header_wrap.active .wgl-tabs_icon' => 'color: {{VALUE}};',
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
			'section_style_content',
			[
				'label' => esc_html__('Content', 'zikzag-core'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tabs_content_typo',
				'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_3['font_family']],
                    'font_weight' => ['default' => \Wgl_Addons_Elementor::$typography_3['font_weight']],
                ],
                'selector' => '{{WRAPPER}} .wgl-tabs_content',
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__('Padding', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => 20,
					'right' => 0,
					'bottom' => 5,
					'left' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tabs_content_margin',
			[
				'label' => esc_html__('Margin', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tabs_content_color',
			[
				'label' => esc_html__('Content Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'default' => $main_font_color,
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tabs_content_bg_color',
			[
				'label' => esc_html__('Content Background Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tabs_content_border_radius',
			[
				'label' => esc_html__('Border Radius', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wgl-tabs_content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tabs_content_border',
				'selector' => '{{WRAPPER}} .wgl-tabs_content',
			]
		);
		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'tabs_content_shadow',
                'selector' => '{{WRAPPER}} .wgl-tabs_content',
            ]
        );

		$this->end_controls_section();

	}

	protected function render() {

		$_s = $this->get_settings_for_display();
		$id_int = substr( $this->get_id_int(), 0, 3 );

		$this->add_render_attribute(
			'tabs',
			[
				'class' => [
					'wgl-tabs',
					'icon_position-'.$_s[ 'tabs_icon_position' ],
					'tabs_align-'.$_s[ 'tabs_tab_align' ],
				],
			]
		);

		?>
		<div <?php echo $this->get_render_attribute_string( 'tabs' ); ?>>

			<div class="wgl-tabs_headings"><?php
				foreach ( $_s[ 'tabs_tab' ] as $index => $item ) :

					$tab_count = $index + 1;
					$tab_title_key = $this->get_repeater_setting_key( 'tabs_tab_title', 'tabs_tab', $index );
					$this->add_render_attribute(
						$tab_title_key,
						[
							'data-tab-id' => 'wgl-tab_' . $id_int . $tab_count,
							'class' => [ 'wgl-tabs_header_wrap' ],
						]
					);
					?>

					<div <?php echo $this->get_render_attribute_string( $tab_title_key ); ?>> <<?php echo $_s[ 'tabs_title_tag' ]; ?> class="wgl-tabs_header">
						<span class="wgl-tabs_title"><?php echo $item[ 'tabs_tab_title' ] ?></span>

						<?php
						// Tab Icon/image
						if ( $item[ 'tabs_tab_icon_type' ] != '' ) {
							if ( $item[ 'tabs_tab_icon_type' ] == 'font' && (!empty( $item[ 'tabs_tab_icon_fontawesome' ] )) ) {

								$icon_font = $item[ 'tabs_tab_icon_fontawesome' ];
								$icon_out = '';
	                            // add icon migration
	                            $migrated = isset( $item['__fa4_migrated'][$item[ 'tabs_tab_icon_fontawesome' ]] );
	                            $is_new = Icons_Manager::is_migration_allowed();
	                            if ( $is_new || $migrated ) {
	                                ob_start();
	                                Icons_Manager::render_icon( $item[ 'tabs_tab_icon_fontawesome' ], [ 'aria-hidden' => 'true' ] );
	                                $icon_out .= ob_get_clean();
	                            } else {
	                                $icon_out .= '<i class="icon '.esc_attr($icon_font).'"></i>';
	                            }

								?>
								<span class="wgl-tabs_icon">
									<?php
                                    	echo $icon_out;
                                	?>
								</span>
								<?php
							 }
							if ( $item[ 'tabs_tab_icon_type' ] == 'image' && !empty( $item[ 'tabs_tab_icon_thumbnail' ] ) ) {
								if ( ! empty( $item[ 'tabs_tab_icon_thumbnail' ][ 'url' ] ) ) {
									$this->add_render_attribute( 'thumbnail', 'src', $item[ 'tabs_tab_icon_thumbnail' ][ 'url' ] );
									$this->add_render_attribute( 'thumbnail', 'alt', Control_Media::get_image_alt( $item[ 'tabs_tab_icon_thumbnail' ] ) );
									$this->add_render_attribute( 'thumbnail', 'title', Control_Media::get_image_title( $item[ 'tabs_tab_icon_thumbnail' ] ) );
									?>
									<span class="wgl-tabs_icon wgl-tabs_icon-image">
									<?php
										echo Group_Control_Image_Size::get_attachment_image_html( $item, 'thumbnail', 'tabs_tab_icon_thumbnail' );
									?>
									</span>
									<?php
								}
							}
						}
						// End Tab Icon/image
						?>

                    </<?php echo $_s[ 'tabs_title_tag' ]; ?>></div>

				<?php endforeach;?>
			</div>

			<div class="wgl-tabs_content-wrap"><?php
				foreach ( $_s[ 'tabs_tab' ] as $index => $item ) :

					$tab_count = $index + 1;
					$tab_content_key = $this->get_repeater_setting_key( 'tab_content', 'tabs_tab', $index );
					$this->add_render_attribute(
						$tab_content_key,
						[
							'data-tab-id' => 'wgl-tab_' . $id_int . $tab_count,
							'class' => [ 'wgl-tabs_content' ],
						]
					);

					?>
					<div <?php echo $this->get_render_attribute_string( $tab_content_key ); ?>>
					<?php
						if ( $item[ 'tabs_content_type' ] == 'content' ) {
							echo do_shortcode( $item[ 'tabs_content' ] );
						} else if ( $item[ 'tabs_content_type' ] == 'template' ) {
							$id = $item[ 'tabs_content_templates' ];
							$wgl_frontend = new Frontend;
							echo $wgl_frontend->get_builder_content_for_display( $id, false );
						}
					?>
					</div>

				<?php endforeach; ?>
			</div>

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