<?php

namespace WglAddons\Widgets;

defined( 'ABSPATH' ) || exit; // Abort, if called directly.

use WglAddons\Includes\Wgl_Icons;
use WglAddons\Templates\WglProgressBar;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;


class Wgl_Progress_Bar extends Widget_Base
{

    public function get_name() {
        return 'wgl-progress-bar';
    }

    public function get_title() {
        return esc_html__('WGL Progress Bar', 'zikzag-core');
    }

    public function get_icon() {
        return 'wgl-progress-bar';
    }

    public function get_categories() {
        return [ 'wgl-extensions' ];
    }

    public function get_script_depends() {
        return [ 'jquery-appear' ];
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

        $this->add_control(
            'progress_title',
            [
                'label' => esc_html__('Title', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
				'placeholder' => esc_html__('Enter your title', 'zikzag-core'),
				'default' => esc_html__('My Skill', 'zikzag-core'),
				'label_block' => true,
            ]
        );

		$this->add_control(
			'value',
			[
				'label' => esc_html__('Value', 'zikzag-core'),
				'type' => Controls_Manager::SLIDER,
				'default' => [ 'size' => 50, 'unit' => '%' ],
				'label_block' => true,
			]
        );

        $this->add_control(
            'units',
            [
                'label' => esc_html__('Units', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
				'placeholder' => esc_html__('Enter your units', 'zikzag-core'),
				'description' => esc_html__('Enter measurement units (Example: %, px, points, etc.)', 'zikzag-core'),
				'default' => esc_html__('%', 'zikzag-core'),
				'label_block' => true,
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
                'name' => 'progress_title_typo',
                'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_3['font_family']],
                ],
                'selector' => '{{WRAPPER}} .progress_label',
            ]
        );

        $this->add_control(
			'progress_title_tag',
			[
                'label' => esc_html__('HTML Tag', 'zikzag-core'),
				'type' => Controls_Manager::SELECT,
				'options' => [
                    'h1' => '‹h1›',
                    'h2' => '‹h2›',
                    'h3' => '‹h3›',
                    'h4' => '‹h4›',
                    'h5' => '‹h5›',
                    'h6' => '‹h6›',
                    'div' => '‹div›',
				],
				'default' => 'div',
			]
		);

        $this->add_control(
            'custom_title_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => esc_attr($h_font_color),
                'selectors' => [
                    '{{WRAPPER}} .progress_label' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .progress_label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> VALUE
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_value',
            [
                'label' => esc_html__('Value', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'progress_value_typo',
                'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_3['font_family']],
                ],
                'selector' => '{{WRAPPER}} .progress_value_wrap',
            ]
        );

        $this->add_control(
            'custom_value_color',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $h_font_color,
                'selectors' => [
                    '{{WRAPPER}} .progress_value_wrap' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'custom_value_color_bg',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .progress_value_wrap' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'value_margin',
			[
				'label' => esc_html__('Margin', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .progress_value_wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

		$this->add_control(
			'value_border_radius',
			[
				'label' => esc_html__('Border Radius', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                ],
				'selectors' => [
					'{{WRAPPER}} .progress_value_wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'value_pos',
			[
				'label' => esc_html__('Value Position', 'zikzag-core'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'fixed' => esc_html__('Fixed', 'zikzag-core'),
					'dynamic' => esc_html__('Dynamic', 'zikzag-core'),
				],
				'default' => 'fixed',
			]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> PROGRESS BAR
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_bar',
            [
                'label' => esc_html__('Bar', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_control(
			'bar_height_filled',
			[
				'label' => esc_html__('Filled Bar Height', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'label_block' => true,
                'range' => [
                    'px' => [ 'min' => 1, 'max' => 50 ],
                ],
				'default' => [ 'size' => 4 ],
                'selectors' => [
                    '{{WRAPPER}} .progress_bar_wrap .progress_bar' => 'height: {{SIZE}}{{UNIT}};',
                ],
			]
        );

		$this->add_control(
			'bar_height_empty',
			[
				'label' => esc_html__('Empty Bar Height', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'label_block' => true,
                'range' => [
                    'px' => [ 'min' => 1, 'max' => 50 ],
                ],
				'default' => [ 'size' => 1 ],
                'selectors' => [
                    '{{WRAPPER}} .progress_bar_wrap' => 'height: {{SIZE}}{{UNIT}};',
                ],
			]
        );

        $this->add_control(
            'bar_bg_color',
            [
                'label' => esc_html__('Empty Bar Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .progress_bar_wrap' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'bar_color',
            [
                'label' => esc_html__('Filled Bar Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}} .progress_bar' => 'background-color: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'bar_padding',
			[
				'label' => esc_html__('Padding', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 11,
                    'left' => 0,
                ],
				'selectors' => [
					'{{WRAPPER}} .progress_bar_wrap-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'bar_margin',
			[
				'label' => esc_html__('Margin', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
                    'top' => 13,
                    'right' => 0,
                    'bottom' => 8,
                    'left' => 0,
                ],
				'selectors' => [
					'{{WRAPPER}} .progress_bar_wrap-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

		$this->add_control(
			'bar_border_radius',
			[
				'label' => esc_html__('Border Radius', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'default' => [
                    'top' => 2,
                    'right' => 2,
                    'bottom' => 2,
                    'left' => 2,
                ],
				'selectors' => [
					'{{WRAPPER}} .progress_bar_wrap, {{WRAPPER}} .progress_bar, {{WRAPPER}} .progress_bar_wrap-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bar_box_shadow',
				'selector' => '{{WRAPPER}} .progress_bar_wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .progress_bar_wrap-wrap',
                'separator' => 'before',
			]
		);

        $this->end_controls_section();

    }

    public function render() {

        $settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'progress_bar', [
			'class' => [
                'wgl-progress_bar',
                ($settings[ 'value_pos' ] == 'dynamic' ? 'dynamic-value' : '' ),
            ],
        ] );

        $this->add_render_attribute( 'bar', [
			'class' => 'progress_bar',
			'data-width' => esc_attr((int)$settings[ 'value' ][ 'size' ]),
        ] );

        $this->add_render_attribute( 'label', [
			'class' => 'progress_label',
        ] );

        ?>
        <div <?php echo $this->get_render_attribute_string( 'progress_bar' ); ?>>
            <div class="progress_wrap">
                <div class="progress_label_wrap">
                    <?php if (!empty($settings[ 'progress_title' ])) { ?>
                        <<?php echo esc_attr($settings[ 'progress_title_tag' ]); ?> <?php echo $this->get_render_attribute_string( 'label' ); ?>><?php
                            echo esc_html($settings[ 'progress_title' ]);
                        ?></<?php echo esc_attr($settings[ 'progress_title_tag' ]); ?>>
                    <?php } ?>
                    <div class="progress_value_wrap">
                        <?php if (!empty($settings[ 'value' ][ 'size' ])) { ?>
                            <span class="progress_value"><?php echo esc_html((int)$settings[ 'value' ][ 'size' ]); ?></span>
                        <?php } ?>
                        <?php if (!empty($settings[ 'units' ])) { ?>
                            <span class="progress_units"><?php echo esc_html($settings[ 'units' ]); ?></span>
                        <?php } ?>
                    </div>
                </div>
                <div class="progress_bar_wrap-wrap">
                    <div class="progress_bar_wrap">
                        <div <?php echo $this->get_render_attribute_string( 'bar' ); ?>></div>
                    </div>
                </div>
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