<?php

namespace WglAddons\Widgets;

defined( 'ABSPATH' ) || exit; // Abort, if called directly.

use WglAddons\Includes\Wgl_Loop_Settings;
use WglAddons\Includes\Wgl_Carousel_Settings;
use WglAddons\Includes\Wgl_Elementor_Helper;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Frontend;
use Elementor\Repeater;


class Wgl_Carousel extends Widget_Base {

	public function get_name() {
		return 'wgl-carousel';
	}

	public function get_title() {
		return esc_html__('WGL Carousel', 'zikzag-core');
	}

	public function get_icon() {
		return 'wgl-carousel';
	}

	public function get_script_depends() {
		return [ 'slick' ];
	}

	public function get_categories() {
		return [ 'wgl-extensions' ];
	}


	protected function register_controls()
	{
		$primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
		$self = new REPEATER();

		$this->start_controls_section('wgl_carousel_section',
			[ 'label' => esc_html__('Carousel Settings' , 'zikzag-core') ]
		);

		$self->add_control(
			'content',
			[
				'label' => esc_html__('Content', 'zikzag-core'),
				'type' => Controls_Manager::SELECT2,
				'options' => Wgl_Elementor_Helper::get_instance()->get_elementor_templates(),
			]
		);

		$this->add_control(
			'content_repeater',
			[
				'label' => esc_html__('Templates', 'zikzag-core'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $self->get_controls(),
				'description' => esc_html__('Slider content is a template which you can choose from Elementor library. Each template will be a slider content', 'zikzag-core'),
				'title_field' => 'Template: {{{ content }}}'
			]
		);


		$this->add_control(
			'slide_to_show',
			[
				'label' => esc_html__('Columns Amount', 'zikzag-core'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1' => esc_html__('1', 'zikzag-core'),
					'2' => esc_html__('2', 'zikzag-core'),
					'3' => esc_html__('3', 'zikzag-core'),
					'4' => esc_html__('4', 'zikzag-core'),
					'5' => esc_html__('5', 'zikzag-core'),
					'6' => esc_html__('6', 'zikzag-core'),
				],
				'default' => '1'
			]
		);

		$this->add_control(
			'speed',
			[
				'label' => esc_html__('Animation Speed', 'zikzag-core'),
				'type' => Controls_Manager::NUMBER,
				'default' => '3000',
				'min' => 1,
				'step' => 1,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__('Autoplay', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'zikzag-core'),
				'label_off' => esc_html__('Off', 'zikzag-core'),
				'default' => 'yes',
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
			'slides_to_scroll',
			[
				'label' => esc_html__('Slide One Item per time', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'zikzag-core'),
				'label_off' => esc_html__('Off', 'zikzag-core'),
			]
		);

		$this->add_control(
			'infinite',
			[
				'label' => esc_html__('Infinite loop sliding', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'zikzag-core'),
				'label_off' => esc_html__('Off', 'zikzag-core'),
			]
		);

		$this->add_control(
			'adaptive_height',
			[
				'label' => esc_html__('Adaptive Height', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'zikzag-core'),
				'label_off' => esc_html__('Off', 'zikzag-core'),
			]
		);

		$this->add_control(
			'fade_animation',
			[
				'label' => esc_html__('Fade Animation', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [ 'slide_to_show' => '1' ],
				'label_on' => esc_html__('On', 'zikzag-core'),
				'label_off' => esc_html__('Off', 'zikzag-core'),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'navigation_section',
			[ 'label' => esc_html__('Navigation', 'zikzag-core') ]
		);

		$this->add_control(
			'h_pag_controls',
			[
				'label' => esc_html__('Pagination Controls', 'zikzag-core'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		$this->add_control(
			'use_pagination',
			[
				'label' => esc_html__('Add Pagination control', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'zikzag-core'),
				'label_off' => esc_html__('Off', 'zikzag-core'),
				'default' => 'yes'
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
						'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/pag_circle.png',
					],
				],
				'default' => 'circle',
			]
		);

		$this->add_control(
			'pag_align',
			[
				'label' => esc_html__('Pagination Aligning', 'zikzag-core'),
				'type' => Controls_Manager::SELECT,
				'condition' => [ 'use_pagination' => 'yes' ],
				'options' => [
					'left' => esc_html__('Left', 'zikzag-core'),
					'right' => esc_html__('Right', 'zikzag-core'),
					'center' => esc_html__('Center', 'zikzag-core'),
				],
				'default' => 'center',
			]
		);

		$this->add_control(
			'pag_offset',
			[
				'label' => esc_html__('Pagination Top Offset', 'zikzag-core'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [ 'use_pagination' => 'yes' ],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [ 'min' => 0, 'max' => 1000, 'step' => 5 ],
				],
				'selectors' => [
					'{{WRAPPER}} .wgl-carousel .slick-dots' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]

		);

		$this->add_control(
			'custom_pag_color',
			[
				'label' => esc_html__('Custom Pagination Color', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [ 'use_pagination' => 'yes' ],
				'label_on' => esc_html__('On', 'zikzag-core'),
				'label_off' => esc_html__('Off', 'zikzag-core'),
			]
		);

		$this->add_control(
			'pag_color',
			[
				'label' => esc_html__('Pagination Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'condition' => [ 'custom_pag_color' => 'yes' ],
				'default' => $primary_color,
				'selectors' => [
					'{{WRAPPER}} .pagination_circle .slick-dots li button' => 'background: {{VALUE}}',
					'{{WRAPPER}} .pagination_square .slick-dots li button' => 'background: {{VALUE}}',
					'{{WRAPPER}} .pagination_line .slick-dots li button:before' => 'background: {{VALUE}}'
				],
			]
		);

		$this->add_control(
			'hr_prev_next',
			[ 'type' => Controls_Manager::DIVIDER ]
		);

		$this->add_control(
			'divider_4',
			[
				'label' => esc_html__('Prev/Next Buttons', 'zikzag-core'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'use_prev_next',
			[
				'label' => esc_html__('Add Prev/Next buttons', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
			]
		);
		$this->add_control(
			'custom_prev_next_offset',
			[
				'label' => esc_html__('Custom offset', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [ 'use_prev_next!' => '' ],
				'label_on' => esc_html__('On', 'zikzag-core'),
				'label_off' => esc_html__('Off', 'zikzag-core'),
			]
		);

		$this->add_control(
			'prev_next_offset',
			[
				'label' => esc_html__('Buttons Top Offset', 'zikzag-core'),
				'type' => Controls_Manager::SLIDER,
				'condition' => [ 'use_prev_next!' => '' ],
				'size_units' => [ '%' ],
				'range' => [
					'%' => [ 'min' => 0, 'max' => 1000 ],
				],
				'default' => [ 'size' => 50, 'unit' => '%' ],
				'selectors' => [
					'{{WRAPPER}} .wgl-carousel .slick-next' => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wgl-carousel .slick-prev' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'custom_prev_next_color',
			[
				'label' => esc_html__('Customize Colors', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [ 'use_prev_next!' => '' ],
			]
		);
		$this->add_control(
			'prev_next_color',
			[
				'label' => esc_html__('Prev/Next Buttons Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
				'condition' => [ 'custom_prev_next_color' => 'yes' ],
				'dynamic' => [ 'active' => true ],
				'default' => $primary_color,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'responsive_section',
			[ 'label' => esc_html__('Responsive', 'zikzag-core') ]
		);

		$this->add_control(
			'custom_resp',
			[
				'label' => esc_html__('Customize Responsive', 'zikzag-core'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'zikzag-core'),
				'label_off' => esc_html__('Off', 'zikzag-core'),
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
				'min' => 1,
				'condition' => [ 'custom_resp' => 'yes' ],
			]
		);

		$this->add_control(
			'heading_mobile',
			[
				'label' => esc_html__('Mobile Settings', 'zikzag-core'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [ 'custom_resp' => 'yes' ],
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

	}

	protected function render()
	{
		$atts = $this->get_settings_for_display();
		extract($atts);

		$content = [];

		foreach ($content_repeater as $template) {
			array_push($content, $template[ 'content' ]);
		}
		echo Wgl_Carousel_Settings::init($atts, $content, true);
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