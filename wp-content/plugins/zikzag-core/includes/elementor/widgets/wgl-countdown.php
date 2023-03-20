<?php

namespace WglAddons\Widgets;

defined('ABSPATH') || exit; // Abort, if called directly.

use WglAddons\Includes\Wgl_Icons;
use WglAddons\Templates\WglCountDown;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;


class Wgl_CountDown extends Widget_Base
{

    public function get_name()
    {
        return 'wgl-countdown';
    }

    public function get_title()
    {
        return esc_html__('WGL Countdown Timer', 'zikzag-core');
    }

    public function get_icon()
    {
        return 'wgl-countdown';
    }

    public function get_categories()
    {
        return ['wgl-extensions'];
    }

    public function get_script_depends()
    {
        return [
            'jquery-coundown',
            'wgl-elementor-extensions-widgets',
        ];
    }


	protected function register_controls() {
		$primary_color     = esc_attr( \Zikzag_Theme_Helper::get_option( 'theme-primary-color' ) );
		$secondary_color   = esc_attr( \Zikzag_Theme_Helper::get_option( 'theme-secondary-color' ) );
		$tertiary_color    = esc_attr( \Zikzag_Theme_Helper::get_option( 'theme-tertiary-color' ) );
		$header_font_color = esc_attr( \Zikzag_Theme_Helper::get_option( 'header-font' )['color'] );
		$main_font_color   = esc_attr( \Zikzag_Theme_Helper::get_option( 'main-font' )['color'] );

		/* Start General Settings Section */
		$this->start_controls_section( 'wgl_countdown_section',
			array(
				'label' => esc_html__( 'Countdown Timer Settings', 'zikzag-core' ),
			)
		);

		$this->add_control( 'countdown_year',
			array(
				'label'       => esc_html__( 'Year', 'zikzag-core' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your title', 'zikzag-core' ),
				'default'     => esc_html__( '2020', 'zikzag-core' ),
				'label_block' => true,
				'description' => esc_html__( 'Example: 2020', 'zikzag-core' ),
			)
		);

		$this->add_control( 'countdown_month',
			array(
				'label'       => esc_html__( 'Month', 'zikzag-core' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( '12', 'zikzag-core' ),
				'default'     => esc_html__( '12', 'zikzag-core' ),
				'label_block' => true,
				'description' => esc_html__( 'Example: 12', 'zikzag-core' ),
			)
		);

		$this->add_control( 'countdown_day',
			array(
				'label'       => esc_html__( 'Day', 'zikzag-core' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( '31', 'zikzag-core' ),
				'default'     => esc_html__( '31', 'zikzag-core' ),
				'label_block' => true,
				'description' => esc_html__( 'Example: 31', 'zikzag-core' ),
			)
		);

		$this->add_control( 'countdown_hours',
			array(
				'label'       => esc_html__( 'Hours', 'zikzag-core' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( '24', 'zikzag-core' ),
				'default'     => esc_html__( '24', 'zikzag-core' ),
				'label_block' => true,
				'description' => esc_html__( 'Example: 24', 'zikzag-core' ),
			)
		);

		$this->add_control( 'countdown_min',
			array(
				'label'       => esc_html__( 'Minutes', 'zikzag-core' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( '59', 'zikzag-core' ),
				'default'     => esc_html__( '59', 'zikzag-core' ),
				'label_block' => true,
				'description' => esc_html__( 'Example: 59', 'zikzag-core' ),
			)
		);

		/*End General Settings Section*/
		$this->end_controls_section();

		/*-----------------------------------------------------------------------------------*/
		/*  Button Section
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section( 'wgl_countdown_content_section',
			array(
				'label' => esc_html__( 'Countdown Timer Content', 'zikzag-core' ),
			)
		);

		$this->add_control( 'hide_day',
			array(
				'label'        => esc_html__( 'Hide Days?', 'zikzag-core' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'zikzag-core' ),
				'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control( 'hide_hours',
			array(
				'label'        => esc_html__( 'Hide Hours?', 'zikzag-core' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'zikzag-core' ),
				'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control( 'hide_minutes',
			array(
				'label'        => esc_html__( 'Hide Minutes?', 'zikzag-core' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'zikzag-core' ),
				'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control( 'hide_seconds',
			array(
				'label'        => esc_html__( 'Hide Seconds?', 'zikzag-core' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'zikzag-core' ),
				'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control( 'show_value_names',
			array(
				'label'        => esc_html__( 'Show Value Names?', 'zikzag-core' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'zikzag-core' ),
				'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'show_value_names-',
			)
		);

		$this->add_control( 'show_separating',
			array(
				'label'        => esc_html__( 'Show Separating?', 'zikzag-core' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'zikzag-core' ),
				'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'show_separating-',
			)
		);
		$this->add_responsive_control(
			'align',
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
				'prefix_class' => 'elementor%s-align-',
				'default' => 'center',
			]
		);

		/*End General Settings Section*/
		$this->end_controls_section();

		$this->start_controls_section(
			'countdown_style_section',
			array(
				'label' => esc_html__( 'Style', 'zikzag-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control( 'size',
			array(
				'label'   => esc_html__( 'Countdown Size', 'zikzag-core' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'large'  => esc_html__( 'Large', 'zikzag-core' ),
					'medium' => esc_html__( 'Medium', 'zikzag-core' ),
					'small'  => esc_html__( 'Small', 'zikzag-core' ),
					'custom' => esc_html__( 'Custom', 'zikzag-core' ),
				],
				'default' => 'large'
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'     => esc_html__( 'Number Typography', 'zikzag-core' ),
				'name'      => 'custom_fonts_number',
				'selector'  => '{{WRAPPER}} .wgl-countdown .countdown-section',
				'condition' => [
					'size' => 'custom'
				]
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'     => esc_html__( 'Text Typography', 'zikzag-core' ),
				'name'      => 'custom_fonts_text',
				'selector'  => '{{WRAPPER}} .wgl-countdown .countdown-section .countdown-period',
				'condition' => [
					'size' => 'custom'
				]
			)
		);

		$this->add_control(
			'number_text_color',
			array(
				'label'     => esc_html__( 'Number Color', 'zikzag-core' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $header_font_color,
				'selectors' => [
					'{{WRAPPER}} .wgl-countdown .countdown-section .countdown-amount' => 'color: {{VALUE}};',
				],
			)
		);

		$this->add_control(
			'period_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'zikzag-core' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $main_font_color,
				'selectors' => [
					'{{WRAPPER}} .wgl-countdown .countdown-section .countdown-period' => 'color: {{VALUE}};',
				],
			)
		);

		$this->add_control(
			'separating_color',
			array(
				'label'     => esc_html__( 'Separate Color', 'zikzag-core' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $primary_color,
				'selectors' => [
					'{{WRAPPER}} .wgl-countdown .countdown-amount:after,
					{{WRAPPER}} .wgl-countdown .countdown-amount:before'  => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'show_separating' => 'yes'
				]
			)
		);

		/*End Style Section*/
		$this->end_controls_section();
	}

    protected function render()
    {
        $atts = $this->get_settings_for_display();

        $countdown = new WglCountDown();
        $countdown->render($this, $atts);
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
