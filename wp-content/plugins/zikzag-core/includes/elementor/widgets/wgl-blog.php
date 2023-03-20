<?php

namespace WglAddons\Widgets;

defined( 'ABSPATH' ) || exit; // Abort, if called directly.

use WglAddons\Includes\Wgl_Loop_Settings;
use WglAddons\Includes\Wgl_Carousel_Settings;
use WglAddons\Templates\WglBlog;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;


class Wgl_Blog extends Widget_Base
{

	public function get_name() {
		return 'wgl-blog';
	}

	public function get_title() {
		return esc_html__('WGL Blog', 'zikzag-core');
	}

	public function get_icon() {
		return 'wgl-blog';
	}

	public function get_script_depends()
	{
		return [
			'slick',
			'jarallax',
			'jarallax-video',
			'imagesloaded',
			'isotope',
			'wgl-elementor-extensions-widgets',
		];
	}

	public function get_categories() {
		return [ 'wgl-extensions' ];
	}


	protected function register_controls()
	{
		$primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
		$secondary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
		$main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);
		$h_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);


		/*-----------------------------------------------------------------------------------*/
		/*  CONTENT -> SETTINGS
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'content_section_settings',
			[ 'label' => esc_html__('Layout', 'zikzag-core') ]
		);

		$this->add_control(
			'blog_title',
			[
				'label' => esc_html__('Title', 'zikzag-core'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [ 'active' => true ]
			]
		);

		$this->add_control(
			'blog_subtitle',
			[
				'label' => esc_html__('Subitle', 'zikzag-core'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [ 'active' => true ]
			]
		);

		$this->add_control(
			'blog_columns',
			[
				'label' => esc_html__('Grid Columns Amount', 'zikzag-core'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'12' => esc_html__('One', 'zikzag-core'),
					'6' => esc_html__('Two', 'zikzag-core'),
					'4' => esc_html__('Three', 'zikzag-core'),
					'3' =>esc_html__('Four', 'zikzag-core')
				],
				'default' => '12',
				'tablet_default' => 'inherit',
				'mobile_default' => '1',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'blog_layout',
			[
				'label' => esc_html__('Layout', 'zikzag-core'),
				'type' => 'wgl-radio-image',
				'options' => [
					'grid' => [
						'title'=> esc_html__('Grid', 'zikzag-core'),
						'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/layout_grid.png',
					],
					'masonry' => [
						'title'=> esc_html__('Masonry', 'zikzag-core'),
						'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/layout_masonry.png',
					],
					'carousel' => [
						'title'=> esc_html__('Carousel', 'zikzag-core'),
						'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/layout_carousel.png',
					],
				],
				'default' => 'grid',
			]
		);

		$this->add_control(
			'blog_navigation',
			[
				'label' => esc_html__('Navigation Type', 'zikzag-core'),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'blog_layout' => [ 'grid', 'masonry' ]
				],
				'options' => [
					'none' => esc_html__('None', 'zikzag-core'),
					'pagination' => esc_html__('Pagination', 'zikzag-core'),
					'load_more' => esc_html__('Load More', 'zikzag-core'),
				],
				'default' => 'none',
			]
		);

		$this->add_control(
			'blog_navigation_align',
			[
				'label' => esc_html__('Navigation\'s Alignment', 'zikzag-core'),
				'type' => Controls_Manager::SELECT,
				'condition' => [ 'blog_navigation' => 'pagination' ],
				'options' => [
					'left' => esc_html__('Left', 'zikzag-core'),
					'center' => esc_html__('Center', 'zikzag-core'),
					'right' => esc_html__('Right', 'zikzag-core'),
				],
				'default' => 'left',
			]
		);

		$this->add_control(
			'spacer_navigation',
			[
				'label' => esc_html__('Navigation Margin Top', 'zikzag-core'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'blog_navigation' => 'pagination',
					'blog_layout' => [ 'grid', 'masonry' ]
				],
				'size_units' => [ 'px', 'em', 'rem'],
				'range' => [
					'px' => [ 'min' => -60, 'max' => 200 ],
				],
				'default' => [ 'size' => 0, 'unit' => 'px' ],
				'selectors' => [
					'{{WRAPPER}} .wgl-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'items_load',
			[
				'label' => esc_html__('Items to be loaded', 'zikzag-core'),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'blog_navigation' => 'load_more',
					'blog_layout' => [ 'grid', 'masonry' ]
				],
				'default' => esc_html__('4', 'zikzag-core'),
			]
		);

		$this->add_control(
			'name_load_more',
			[
				'label' => esc_html__('Button Text', 'zikzag-core'),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'blog_navigation' => 'load_more',
					'blog_layout' => [ 'grid', 'masonry' ]
				],
				'default' => esc_html__('Load More', 'zikzag-core'),
			]
		);

		$this->add_control(
			'spacer_load_more',
			[
				'label' => esc_html__('Button Spacer Top', 'zikzag-core'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'blog_navigation' => 'load_more',
					'blog_layout' => [ 'grid', 'masonry' ]
				],
				'range' => [
					'px' => [ 'min' => -20, 'max' => 200 ],
				],
				'size_units' => [ 'px', 'em', 'rem', 'vw' ],
				'default' => [ 'size' => 0, 'unit' => 'px' ],
				'selectors' => [
					'{{WRAPPER}} .load_more_wrapper' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


		/*-----------------------------------------------------------------------------------*/
		/*  CONTENT -> APPEARANCE
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'content_section_appearance',
			[ 'label' => esc_html__('Appearance', 'zikzag-core') ]
		);

		$this->add_control(
			'hide_media',
			[
				'label' => esc_html__('Hide Media?', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'hide_blog_title',
			[
				'label' => esc_html__('Hide Title?', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'hide_content',
			[
				'label' => esc_html__('Hide Content?', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'hide_postmeta',
			[
				'label' => esc_html__('Hide all post-meta?', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'meta_author',
			[
				'label' => esc_html__('Hide post-meta author?', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [ 'hide_postmeta!' => 'yes' ],
				'default' => 'yes'
			]
		);

		$this->add_control(
			'meta_comments',
			[
				'label' => esc_html__('Hide post-meta comments?', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [ 'hide_postmeta!' => 'yes' ],
				'default' => 'yes'
			]
		);

		$this->add_control(
			'meta_categories',
			[
				'label' => esc_html__('Hide post-meta categories?', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [ 'hide_postmeta!' => 'yes' ],
			]
		);

		$this->add_control(
			'meta_date',
			[
				'label' => esc_html__('Hide post-meta date?', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [ 'hide_postmeta!' => 'yes' ],
			]
		);

		$this->add_control(
			'hide_likes',
			[
				'label' => esc_html__('Hide Likes?', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'hide_share',
			[
				'label' => esc_html__('Hide Post Share?', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'read_more_hide',
			[
				'label' => esc_html__('Hide \'Read More\' button?', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'read_more_text',
			[
				'label' => esc_html__('Read More Text', 'zikzag-core'),
				'type' => Controls_Manager::TEXT,
				'condition' => [ 'read_more_hide' => '' ],
				'dynamic' => [ 'active' => true ],
				'default' => esc_html__('Read More', 'zikzag-core'),
			]
		);

		$this->add_control(
			'content_letter_count',
			[
				'label' => esc_html__('Characters Amount in Content', 'zikzag-core'),
				'type' => Controls_Manager::NUMBER,
				'condition' => [ 'hide_content' => '' ],
				'min' => 1,
				'step' => 1,
				'default' => '95',
			]
		);

		$this->add_control(
			'crop_square_img',
			[
				'label' => esc_html__('Crop Images for Posts List?', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__('For correctly work uploaded image size should be larger than 700px height and width.', 'text-domain' ),
				'default' => 'yes',
			]
		);

		$this->end_controls_section();


		/*-----------------------------------------------------------------------------------*/
		/*  CONTENT -> CAROUSEL OPTIONS
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'content_section_carousel',
			[
				'label' => esc_html__('Carousel Options', 'zikzag-core'),
				'condition' => [ 'blog_layout' => 'carousel' ]
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__('Autoplay', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'zikzag-core'),
				'label_off' => esc_html__('Off', 'zikzag-core'),
			]
		);

		$this->add_control(
			'slides_to_scroll',
			[
				'label' => esc_html__('Slide One Item per time', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => esc_html__('Autoplay Speed', 'zikzag-core'),
				'type' => Controls_Manager::NUMBER,
				'condition' => [ 'autoplay' => 'yes' ],
				'min' => 1,
				'step' => 1,
				'default' => '3000',
			]
		);

		$this->add_control(
			'use_pagination',
			[
				'label' => esc_html__('Add Pagination controls', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'pag_type',
			[
				'label' => esc_html__('Pagination Type', 'zikzag-core'),
				'type' => 'wgl-radio-image',
				'condition' => [ 'use_pagination' => 'yes' ],
				'options' => [
					'circle' => [
						'title'=> esc_html__('Circle', 'zikzag-core'),
						'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/pag_circle.png',
					],
					'circle_border' => [
						'title'=> esc_html__('Empty Circle', 'zikzag-core'),
						'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/pag_circle_border.png',
					],
					'square' => [
						'title'=> esc_html__('Square', 'zikzag-core'),
						'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/pag_square.png',
					],
					'square_border' => [
						'title'=> esc_html__('Empty Square', 'zikzag-core'),
						'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/pag_square_border.png',
					],
					'line' => [
						'title'=> esc_html__('Line', 'zikzag-core'),
						'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/pag_line.png',
					],
					'line_circle' => [
						'title'=> esc_html__('Line - Circle', 'zikzag-core'),
						'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/pag_line_circle.png',
					],
				],
				'default' => 'circle',
			]
		);

		$this->add_control(
			'pag_offset',
			[
				'label' => esc_html__('Top Offset', 'zikzag-core'),
				'type' => Controls_Manager::NUMBER,
				'condition' => [ 'use_pagination' => 'yes' ],
				'min' => -50,
				'max' => 150,
				'default' => -35,
				'selectors' => [
					'{{WRAPPER}} .wgl-carousel .slick-dots' => 'margin-top: {{VALUE}}px;',
				],
			]
		);

		$this->add_control(
			'custom_pag_color',
			[
				'label' => esc_html__('Customize Color', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [ 'use_pagination' => 'yes' ],
			]
		);

		$this->add_control(
			'pag_color',
			[
				'label' => esc_html__('Pagination Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'use_pagination' => 'yes',
					'custom_pag_color' => 'yes',
				],
				'default' => $primary_color,
			]
		);

		$this->add_control(
			'use_navigation',
			[
				'label' => esc_html__('Add Navigation controls', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'custom_resp',
			[
				'label' => esc_html__('Customize Responsive', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'heading_desktop',
			[
				'label' => esc_html__('Desktop Settings', 'zikzag-core'),
				'type' => Controls_Manager::HEADING,
				'condition' => [ 'custom_resp' => 'yes' ],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'resp_medium',
			[
				'label' => esc_html__('Desktop Screen Breakpoint', 'zikzag-core'),
				'type' => Controls_Manager::NUMBER,
				'condition' => [ 'custom_resp' => 'yes' ],
				'min' => 1,
				'default' => '1025',
			]
		);

		$this->add_control(
			'resp_medium_slides',
			[
				'label' => esc_html__('Slides to show', 'zikzag-core'),
				'type' => Controls_Manager::NUMBER,
				'condition' => [ 'custom_resp' => 'yes' ],
				'min' => 1,
			]
		);

		$this->add_control(
			'heading_tablet',
			[
				'label' => esc_html__('Tablet Settings', 'zikzag-core'),
				'type' => Controls_Manager::HEADING,
				'condition' => [ 'custom_resp' => 'yes' ],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'resp_tablets',
			[
				'label' => esc_html__('Tablet Screen Breakpoint', 'zikzag-core'),
				'type' => Controls_Manager::NUMBER,
				'condition' => [ 'custom_resp' => 'yes' ],
				'min' => 1,
				'default' => '800',
			]
		);

		$this->add_control(
			'resp_tablets_slides',
			[
				'label' => esc_html__('Slides to show', 'zikzag-core'),
				'type' => Controls_Manager::NUMBER,
				'condition' => [ 'custom_resp' => 'yes' ],
				'min' => 1,
			]
		);

		$this->add_control(
			'heading_mobile',
			[
				'label' => esc_html__('Mobile Settings', 'zikzag-core'),
				'type' => Controls_Manager::HEADING,
				'condition' => [ 'custom_resp' => 'yes' ],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'resp_mobile',
			[
				'label' => esc_html__('Mobile Screen Breakpoint', 'zikzag-core'),
				'type' => Controls_Manager::NUMBER,
				'condition' => [ 'custom_resp' => 'yes' ],
				'min' => 1,
				'default' => '480',
			]
		);

		$this->add_control(
			'resp_mobile_slides',
			[
				'label' => esc_html__('Slides to show', 'zikzag-core'),
				'type' => Controls_Manager::NUMBER,
				'condition' => [ 'custom_resp' => 'yes' ],
				'min' => 1,
			]
		);

		$this->end_controls_section();


		/*-----------------------------------------------------------------------------------*/
		/*  SETTINGS -> QUERY
		/*-----------------------------------------------------------------------------------*/

		Wgl_Loop_Settings::init(
			$this,
			[ 'post_type' => 'post' ]
		);

		/*-----------------------------------------------------------------------------------*/
		/*  STYLE -> MODULE TITLE
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'section_style_module_title',
			[
				'label' => esc_html__('Module Title', 'zikzag-core'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [ 'blog_title!' => '' ],
			]
		);

		$this->add_control(
			'heading_blog_title',
			[
				'label' => esc_html__('Title', 'zikzag-core'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'module_title',
				'selector' => '{{WRAPPER}} .blog_title',
			]
		);

		$this->add_responsive_control(
			'blog_title_margin',
			[
				'label' => esc_html__('Margin', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .blog_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_blog_subtitle',
			[
				'label' => esc_html__('Title', 'zikzag-core'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'module_subtitle',
				'selector' => '{{WRAPPER}} .blog_subtitle',
			]
		);

		$this->add_responsive_control(
			'blog_subtitle_margin',
			[
				'label' => esc_html__('Margin', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .blog_subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


		/*-----------------------------------------------------------------------------------*/
		/*  STYLE -> HEADINGS
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'section_style_headings',
			[
				'label' => esc_html__('Headings', 'zikzag-core'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_tag',
			[
				'label' => esc_html__('HTML tag', 'zikzag-core'),
				'type' => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => [
					'h1' => '‹h1›',
					'h2' => '‹h2›',
					'h3' => '‹h3›',
					'h4' => '‹h4›',
					'h5' => '‹h5›',
					'h6' => '‹h6›',
				],
			]
		);

		$this->add_responsive_control(
			'heading_margin',
			[
				'label' => esc_html__('Margin', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .blog-post_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_headings_color' );

		$this->start_controls_tab(
			'custom_headings_color_idle',
			[ 'label' => esc_html__('Idle' , 'zikzag-core') ]
		);

		$this->add_control(
			'custom_headings_color',
			[
				'label' => esc_html__('Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'default' => esc_attr($h_font_color),
				'selectors' => [
					'{{WRAPPER}} .blog-post_title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'custom_headings_color_hover',
			[ 'label' => esc_html__('Hover' , 'zikzag-core') ]
		);

		$this->add_control(
			'custom_hover_headings_color',
			[
				'label' => esc_html__('Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'default' => $h_font_color,
				'selectors' => [
					'{{WRAPPER}} .blog-post_title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'custom_fonts_blog_headings',
				'selector' => '{{WRAPPER}} .blog-post_title, {{WRAPPER}} .blog-post_title > a',
			]
		);

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

		$this->add_responsive_control(
			'content_margin',
			[
				'label' => esc_html__('Margin', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .blog-style-standard .blog-post_text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'custom_content_color',
			[
				'label' => esc_html__('Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'default' => $main_font_color,
				'selectors' => [
					'{{WRAPPER}} .blog-post_text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'custom_fonts_blog_content',
				'selector' => '{{WRAPPER}} .blog-post_text',
			]
		);

		$this->end_controls_section();


		/*-----------------------------------------------------------------------------------*/
		/*  STYLE -> META INFO
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'meta_info_style_section',
			[
				'label' => esc_html__('Meta Info', 'zikzag-core'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'meta_info_margin',
			[
				'label' => esc_html__('Margin', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .blog-style-standard .blog-post .blog-post_content > .meta-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .blog-style-standard .blog-post .blog-post_content > .post_meta-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_meta_info' );

		$this->start_controls_tab(
			'tab_meta_info_idle',
			[ 'label' => esc_html__('Idle', 'zikzag-core') ]
		);

		$this->add_control(
			'custom_main_color',
			[
				'label' => esc_html__('Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'default' => '#aeb6c2',
				'selectors' => [
					'{{WRAPPER}} .blog-post_content > .meta-wrapper' => 'color: {{VALUE}};',
					'{{WRAPPER}} .blog-post_content > .meta-wrapper a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .blog-post_likes-wrap .sl-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_meta_hover',
			[ 'label' => esc_html__('Hover', 'zikzag-core') ]
		);

		$this->add_control(
			'custom_main_color_hover',
			[
				'label' => esc_html__('Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'default' => $primary_color,
				'selectors' => [
					'{{WRAPPER}} .blog-post_likes-wrap:hover .sl-count' => 'color: {{VALUE}};',
					'{{WRAPPER}} .blog-post_content > .meta-wrapper a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();


		/*-----------------------------------------------------------------------------------*/
		/*  STYLE -> META INFO
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'section_style_media',
			[
				'label' => esc_html__('Media', 'zikzag-core'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'custom_blog_mask',
			[
				'label' => esc_html__('Add Image Idle Overlay', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'zikzag-core'),
				'label_off' => esc_html__('Off', 'zikzag-core'),
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'custom_image_mask_color',
				'label' => esc_html__('Background', 'zikzag-core'),
				'condition' => [ 'custom_blog_mask' => 'yes' ],
				'types' => [ 'classic', 'gradient', 'video' ],
				'default' => 'rgba(14,21,30,.6)',
				'selector' => '{{WRAPPER}} .blog-post_bg_media:before,
				{{WRAPPER}} .blog-post.format-standard-image .blog-post_media .blog-post_feature-link:before',
			]
		);

		$this->add_control(
			'custom_blog_hover_mask',
			[
				'label' => esc_html__('Add Image Hover Overlay', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'zikzag-core'),
				'label_off' => esc_html__('Off', 'zikzag-core'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'custom_image_hover_mask_color',
				'label' => esc_html__('Background', 'zikzag-core'),
				'condition' => [ 'custom_blog_hover_mask' => 'yes' ],
				'types' => [ 'classic', 'gradient', 'video' ],
				'default' => 'rgba(14,21,30,.6)',
				'selector' => '{{WRAPPER}} .blog-post:hover .blog-post_bg_media:before,
							   {{WRAPPER}} .blog-post.hide_media:hover,
							   {{WRAPPER}} .blog-post.format-standard-image .blog-post_media:hover .blog-post_feature-link:before',
			]
		);

		$this->add_control(
			'custom_blog_bg_item',
			[
				'label' => esc_html__('Add Items Background', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'zikzag-core'),
				'label_off' => esc_html__('Off', 'zikzag-core'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'custom_bg_color',
				'label' => esc_html__('Background', 'zikzag-core'),
				'condition' => [ 'custom_blog_bg_item' => 'yes' ],
				'types' => [ 'classic', 'gradient', 'video' ],
				'default' => 'rgba(19,17,31,1)',
				'selector' => '{{WRAPPER}} .blog-post,
							   {{WRAPPER}} .blog-post .blog-post_content,
							   {{WRAPPER}} .blog-post .blog-post_media .meta-wrapper',
			]
		);

		$this->end_controls_section();

	}


	protected function render()
	{
		$atts = $this->get_settings_for_display();

		$blog = new WglBlog();
		echo $blog->render($atts);
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