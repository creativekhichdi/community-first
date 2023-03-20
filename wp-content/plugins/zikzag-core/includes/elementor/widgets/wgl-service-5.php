<?php

namespace WglAddons\Widgets;

use WglAddons\Includes\Wgl_Icons;
use WglAddons\Includes\Wgl_Carousel_Settings;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use Elementor\Repeater;


defined( 'ABSPATH' ) || exit; // Abort, if called directly.

class Wgl_Service_5 extends Widget_Base {

	public function get_name() {
		return 'wgl-service-5';
	}

	public function get_title() {
		return esc_html__( 'WGL Service', 'zikzag-core' );
	}

	public function get_icon() {
		return 'wgl-services-2';
	}

	public function get_categories() {
		return [ 'wgl-extensions' ];
	}

	protected function register_controls() {
		$theme_color       = esc_attr( \Zikzag_Theme_Helper::get_option( 'theme-primary-color' ) );
		$second_color      = esc_attr( \Zikzag_Theme_Helper::get_option( 'theme-secondary-color' ) );
		$third_color       = esc_attr( \Zikzag_Theme_Helper::get_option( 'theme-third-color' ) );
		$header_font_color = esc_attr( \Zikzag_Theme_Helper::get_option( 'header-font' )['color'] );
		$main_font_color   = esc_attr( \Zikzag_Theme_Helper::get_option( 'main-font' )['color'] );

		/*-----------------------------------------------------------------------------------*/
		/*  Content
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section( 'wgl_service_content',
			array(
				'label' => esc_html__( 'Service Content', 'zikzag-core' ),
			)
		);

		$this->add_control( 'service_title',
			array(
				'label'       => esc_html__( 'Title', 'zikzag-core' ),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'default'     => esc_html__( 'This is the heading​', 'zikzag-core' ),
				'dynamic'     => [ 'active' => true ]
			)
		);

		$this->add_control( 'service_bg_text',
			array(
				'label'       => esc_html__( 'Background Text', 'zikzag-core' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( '01', 'zikzag-core' ),
				'dynamic'     => [ 'active' => true ]
			)
		);

		$this->add_control(
			'service_whole_link',
			[
				'label'        => esc_html__( 'Whole Item Link', 'zikzag-core' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'zikzag-core' ),
				'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
				'prefix_class' => 'service_whole_link-',
			]
		);

		$this->add_control(
			'service_link',
			[
				'label'       => esc_html__( 'Link', 'zikzag-core' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		/*-----------------------------------------------------------------------------------*/
		/*  Style Section Title
		/*-----------------------------------------------------------------------------------*/
		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__( 'Title', 'zikzag-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_font',
				'label'    => esc_html__( 'Title Typography', 'zikzag-core' ),
				'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_family']],
                    'font_weight' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_weight']],
                ],
                'selector' => '{{WRAPPER}} .title',
			]
		);
		$this->add_responsive_control(
			'title_offset',
			[
				'label'      => esc_html__( 'Margin', 'zikzag-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => 0,
					'left'     => 18.6,
					'right'    => 0,
					'bottom'   => 0,
					'unit'     => '%',
					'isLinked' => false
				],
				'selectors'  => [
					'{{WRAPPER}} .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => esc_html__( 'Padding', 'zikzag-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs(
			'title_colors'
		);
		$this->start_controls_tab(
			'title_colors_idle',
			[
				'label' => esc_html__( 'Idle', 'zikzag-core' ),
			]
		);
		$this->add_control(
			'title_color_idle',
			[
				'label'     => esc_html__( 'Title Color', 'zikzag-core' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'title_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'zikzag-core' ),
			]
		);
		$this->add_control(
			'title_color_hover',
			[
				'label'     => esc_html__( 'Title Color', 'zikzag-core' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .wgl-service-wrapper:hover .title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		/*-----------------------------------------------------------------------------------*/
		/*  Style Section BG Text
		/*-----------------------------------------------------------------------------------*/
		$this->start_controls_section(
			'section_style_bg_text',
			[
				'label' => esc_html__( 'Background Text', 'zikzag-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'bg_text_font',
				'label'    => esc_html__( 'Typography', 'zikzag-core' ),
				'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_family']],
                    'font_weight' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_weight']],
                ],
                'selector' => '{{WRAPPER}} .bg_text',
			]
		);
		$this->add_responsive_control(
			'bg_text_offset',
			[
				'label'      => esc_html__( 'Margin', 'zikzag-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => 0,
					'left'     => 10.4,
					'right'    => 0,
					'bottom'   => 0,
					'unit'     => '%',
					'isLinked' => false
				],
				'selectors'  => [
					'{{WRAPPER}} .bg_text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bg_text_padding',
			[
				'label'      => esc_html__( 'Padding', 'zikzag-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => 35,
					'left'     => 0,
					'right'    => 0,
					'bottom'   => 0,
					'unit'     => 'px',
					'isLinked' => false
				],
				'selectors'  => [
					'{{WRAPPER}} .bg_text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs(
			'bg_text_colors'
		);
		$this->start_controls_tab(
			'bg_text_colors_idle',
			[
				'label' => esc_html__( 'Idle', 'zikzag-core' ),
			]
		);
		$this->add_control(
			'bg_text_color_idle',
			[
				'label'     => esc_html__( 'BG Text Color', 'zikzag-core' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2a3843',
				'selectors' => [
					'{{WRAPPER}} .bg_text' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'bg_text_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'zikzag-core' ),
			]
		);
		$this->add_control(
			'bg_text_color_hover',
			[
				'label'     => esc_html__( 'BG Text Color', 'zikzag-core' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff673c',
				'selectors' => [
					'{{WRAPPER}} .wgl-service-wrapper:hover .bg_text' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		/*-----------------------------------------------------------------------------------*/
		/*  Style Section Item
		/*-----------------------------------------------------------------------------------*/
		$this->start_controls_section(
			'section_style_item',
			[
				'label' => esc_html__( 'Item', 'zikzag-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__( 'Padding', 'zikzag-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => 52,
					'right'    => 0,
					'bottom'   => 40,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => false
				],
				'selectors'  => [
					'{{WRAPPER}} .wgl-service-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'item_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'zikzag-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'    => 10,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
				],
				'selectors'  => [
					'{{WRAPPER}} .wgl-service-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs(
			'item_colors'
		);
		$this->start_controls_tab(
			'item_colors_idle',
			[
				'label' => esc_html__( 'Idle', 'zikzag-core' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'background_color_idle',
				'label'          => esc_html__( 'Background', 'zikzag-core' ),
				'types'          => [ 'classic', 'gradient', 'video' ],
				'selector'       => '{{WRAPPER}} .wgl-service-wrapper',
				'fields_options' => [
					'background' => [ 'default' => 'classic' ],
					'color'      => [ 'default' => $second_color ],
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'item_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'zikzag-core' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'background_color_hover',
				'label'          => esc_html__( 'Background', 'zikzag-core' ),
				'types'          => [ 'classic', 'gradient', 'video' ],
				'selector'       => '{{WRAPPER}} .wgl-service-wrapper:hover',
				'fields_options' => [
					'background' => [ 'default' => 'classic' ],
					'color'      => [ 'default' => $theme_color ],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		/*-----------------------------------------------------------------------------------*/
		/*  Style Section Icon
		/*-----------------------------------------------------------------------------------*/
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__( 'Icon', 'zikzag-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'icon_offset',
			[
				'label'      => esc_html__( 'Margin', 'zikzag-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => 0,
					'right'    => 9,
					'bottom'   => 0,
					'left'     => 1,
					'unit'     => '%',
					'isLinked' => false
				],
				'selectors'  => [
					'{{WRAPPER}} .wgl-services-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__( 'Padding', 'zikzag-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .wgl-services-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs(
			'icon_colors'
		);
		$this->start_controls_tab(
			'icon_colors_idle',
			[
				'label' => esc_html__( 'Idle', 'zikzag-core' ),
			]
		);
		$this->add_control(
			'icon_color_idle',
			[
				'label'     => esc_html__( 'Icon Color', 'zikzag-core' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .wgl-services-button' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'icon_border_color_idle',
			[
				'label'     => esc_html__( 'Border Color', 'zikzag-core' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,.2)',
				'selectors' => [
					'{{WRAPPER}} .icon-animated' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'icon_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'zikzag-core' ),
			]
		);
		$this->add_control(
			'icon_color_hover',
			[
				'label'     => esc_html__( 'Icon Color', 'zikzag-core' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}}.service_whole_link-yes .wgl-service-wrapper:hover .wgl-services-button' => 'color: {{VALUE}};',
					'{{WRAPPER}}:not(.service_whole_link-yes) .wgl-services-button:hover'                => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'icon_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color', 'zikzag-core' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,.4)',
				'selectors' => [
					'{{WRAPPER}}:hover .icon-animated' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('services', 'class', 'wgl-services-5');

		$this->add_render_attribute('service_link', 'class', 'wgl-services_item-link');
        if ($settings['service_link']['url']) $this->add_link_attributes('service_link', $settings['service_link']);

		$button = '<span class="icon-animated"></span><i class="flaticon-long-next"></i>';
		?>
        <div <?php echo $this->get_render_attribute_string( 'services' ); ?>>
            <div class="wgl-service-wrapper">

				<?php if ( $settings['service_whole_link'] === 'yes' ) { ?>
                    <a <?php echo $this->get_render_attribute_string( 'service_link' ); ?>></a><?php
				} ?>

				<?php if ( ! empty( $settings['service_bg_text'] ) ) { ?>
                    <span class="bg_text"><?php echo esc_html( $settings['service_bg_text'] ); ?></span><?php
				} ?>

				<?php if ( ! empty( $settings['service_title'] ) ) { ?>
                    <h3 class="title"><?php echo esc_html( $settings['service_title'] ); ?></h3><?php
				} ?>

                <div class="wgl-services-button">
					<?php if ( $settings['service_whole_link'] === 'yes' ) { ?>
						<?php echo $button; ?>
					<?php } else { ?>
                        <a <?php echo $this->get_render_attribute_string( 'service_link' ); ?>><?php echo $button; ?></a><?php
					} ?>
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