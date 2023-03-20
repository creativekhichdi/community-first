<?php

namespace WglAddons\Widgets;

defined( 'ABSPATH' ) || exit; // Abort, If called directly.

use WglAddons\Includes\Wgl_Icons;
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


class Wgl_Header_Side_panel extends Widget_Base
{
	public function get_name() {
		return 'wgl-header-side_panel';
	}

	public function get_title() {
		return esc_html__('WGL Side Panel Button', 'zikzag-core' );
	}

	public function get_icon() {
		return 'wgl-header-side_panel';
	}

	public function get_categories() {
		return [ 'wgl-header-modules' ];
	}

	public function get_script_depends() {
		return [ 'wgl-elementor-extensions-widgets' ];
	}

	protected function register_controls()
	{
		$primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
		$secondary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
		$h_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);
		$main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);


		$this->start_controls_section(
			'section_side_panel_settings',
			[
				'label' => esc_html__( 'Side Panel', 'zikzag-core' ),
			]
		);

		$this->add_responsive_control(
			'sp_width',
			[
				'label' => esc_html__( 'Item Width', 'zikzag-core' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [ 'min' => 30, 'max' => 200 ],
					'%' => [ 'min' => 5, 'max' => 100 ],
				],
				'selectors' => [
					'{{WRAPPER}}' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sp_height',
			[
				'label' => esc_html__( 'Item Height', 'zikzag-core' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [ 'min' => 30, 'max' => 250 ],
					'%' => [ 'min' => 5, 'max' => 100 ],
				],
				'selectors' => [
					'{{WRAPPER}}' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'alignment',
			[
				'label' => esc_html__( 'Alignment', 'zikzag-core' ),
				'type' => Controls_Manager::CHOOSE,
				'condition' => [ 'sp_width!' => 0 ],
				'options' => [
					'margin-right' => [
						'title' => esc_html__( 'Left', 'zikzag-core' ),
						'icon' => 'fa fa-align-left',
					],
					'margin' => [
						'title' => esc_html__( 'Center', 'zikzag-core' ),
						'icon' => 'fa fa-align-center',
					],
					'margin-left' => [
						'title' => esc_html__( 'Right', 'zikzag-core' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}}' => '{{VALUE}}: auto;',
				],

			]
		);

		$this->start_controls_tabs(
			'sp_color_tabs',
			[
				'separator' => 'before',
			]
		);

		$this->start_controls_tab(
			'tab_color_idle',
			[ 'label' => esc_html__('Idle' , 'zikzag-core') ]
		);

		$this->add_control(
			'icon_color_idle',
			[
				'label' => esc_html__( 'Icon Color', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .side_panel' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_bg_idle',
			[
				'label' => esc_html__( 'Item Background', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => $primary_color,
				'selectors' => [
					'{{WRAPPER}} .side_panel' => 'background: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_color_hover',
			[ 'label' => esc_html__('Hover' , 'zikzag-core') ]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label' => esc_html__( 'Icon Color', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}}:hover .side_panel' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_bg_hover',
			[
				'label' => esc_html__( 'Item Background', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:hover .side_panel' => 'background: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}


	public function render()
	{
		echo '<div class="side_panel">',
			'<div class="side_panel_inner">',
				'<a href="#" class="side_panel-toggle">',
					'<span class="side_panel-toggle-inner">',
						'<span></span>',
						'<span></span>',
						'<span></span>',
					'</span>',
				'</a>',
			'</div>',
		'</div>';
	}
}