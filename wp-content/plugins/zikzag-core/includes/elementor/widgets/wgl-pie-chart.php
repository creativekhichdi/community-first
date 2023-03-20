<?php
namespace WglAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // If this file is called directly, abort.

class Wgl_Pie_Chart extends Widget_Base {

	public function get_name() {
		return 'wgl-pie-chart';
	}

	public function get_title() {
		return esc_html__('WGL Pie Chart', 'zikzag-core' );
	}

	public function get_icon() {
		return 'wgl-pie-chart';
	}

	public function get_categories() {
		return [ 'wgl-extensions' ];
	}

	public function get_script_depends() {
		return [
			'jquery-easypiechart', 'jquery-appear'
		];
	}

	// Adding the controls fields for the premium title
	// This will controls the animation, colors and background, dimensions etc
	protected function register_controls() {
		$primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
		$second_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
		$third_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-third-color'));
		$header_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);
		$main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);

		/* Start General Settings Section */
		$this->start_controls_section('wgl_pie_chart_section',
			array(
				'label'         => esc_html__('Pie Chart Settings', 'zikzag-core'),
			)
		);

		$this->add_control(
			'value',
			[
				'label' => esc_html__( 'Value', 'zikzag-core' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 75,
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],

				'label_block' => true,
			]
		);

		$this->add_control(
			'chart_align',
			array(
				'label' => esc_html__( 'Alignment', 'zikzag-core' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'zikzag-core' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'zikzag-core' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'zikzag-core' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'label_block' => false,
				'default' => 'left',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .wgl-pie_chart' => 'text-align: {{VALUE}};',
				],
			)
		);

		$this->add_control(
			'description',
			[
				'label' => esc_html__('Description', 'zikzag-core'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('DESCRIPTION', 'zikzag-core'),
			]
		);

		/*End General Settings Section*/
		$this->end_controls_section();

		/*-----------------------------------------------------------------------------------*/
		/*  Style Section
		/*-----------------------------------------------------------------------------------*/

		/*-----------------------------------------------------------------------------------*/
		/*  Style Section(Value Section)
		/*-----------------------------------------------------------------------------------*/
		$this->start_controls_section(
			'value_style_section',
			array(
				'label'     => esc_html__( 'Value', 'zikzag-core' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'pie_chart_value_typo',
				'selector' => '{{WRAPPER}} .wgl-pie_chart .percent',
			)
		);

		$this->add_control(
			'custom_value_color',
			array(
				'label' => esc_html__( 'Color', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => $header_font_color,
				'selectors' => array(
					'{{WRAPPER}} .wgl-pie_chart .percent' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'custom_value_color_bg',
			array(
				'label' => esc_html__( 'Background Color', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wgl-pie_chart .percent' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		/*-----------------------------------------------------------------------------------*/
		/*  Style Section(Description Section)
		/*-----------------------------------------------------------------------------------*/
		$this->start_controls_section(
			'description_section',
			array(
				'label'     => esc_html__( 'Description', 'zikzag-core' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'pie_chart_desc_typo',
				'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_family']],
                    'font_weight' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_weight']],
                ],
				'selector' => '{{WRAPPER}} .wgl-pie_chart .pie_chart_description',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label' => esc_html__( 'Color', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => $header_font_color,
				'selectors' => array(
					'{{WRAPPER}} .wgl-pie_chart .pie_chart_description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'desc_margin',
			[
				'label' => esc_html__('Margin', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'default' => [
					'top' => '18',
					'left' => '0',
					'right' => '0',
					'bottom' => '0',
					'unit' => 'px',
					'isLinked' => false
				],
				'selectors' => [
					'{{WRAPPER}} .wgl-pie_chart .pie_chart_description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/*-----------------------------------------------------------------------------------*/
		/*  Style Section(Progress Bar Section)
		/*-----------------------------------------------------------------------------------*/
		$this->start_controls_section(
			'bar_style_section',
			array(
				'label'     => esc_html__( 'Pie Chart', 'zikzag-core' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'size_chart',
			[
				'label' => esc_html__( 'Size Chart', 'zikzag-core' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'default' => [
					'size' => 150,
					'unit' => 'px',
				],
				'label_block' => true,
			]
		);

		$this->add_control(
			'track_color',
			array(
				'label' => esc_html__( 'Track Color', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#d6d6d6',
			)
		);

		$this->add_control(
			'bar_color',
			array(
				'label' => esc_html__( 'Bar Color', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => $primary_color,
			)
		);

		$this->add_control(
			'line_width',
			[
				'label' => esc_html__( 'Line Width', 'zikzag-core' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
				'default' => 6,
			]
		);

		$this->end_controls_section();

	}

	public function render(){

		$_s = $this->get_settings_for_display();
		extract($_s);

		wp_enqueue_script('jquery-easypiechart', get_template_directory_uri() . '/js/jquery.easypiechart.min.js', array(), false, false);

		wp_enqueue_script('jquery-appear', get_template_directory_uri() . '/js/jquery.appear.js', array(), false, false);

		$this->add_render_attribute( 'pie_chart_wrapper', [
			'class' => [
				'wgl-pie_chart',
			],
		] );

		$this->add_render_attribute( 'chart', [
			'class' => 'chart',
			'data-percent' => (int) esc_attr($value['size']),
			'data-track-color' => esc_attr($track_color),
			'data-bar-color' => esc_attr($bar_color),
			'data-line-width' => (int) esc_attr($line_width),
			'data-size' => (int) esc_attr($size_chart['size']),
		] );

		?>
		<div <?php echo $this->get_render_attribute_string( 'pie_chart_wrapper' ); ?>>
			<div class="pie-chart_wrap">
				<div <?php echo $this->get_render_attribute_string( 'chart' ); ?>>
                    <span class="percent">
                        0
                    </span>
				</div>
                <?php echo ( isset($description) && $description !== '' ? '<span class="pie_chart_description">'.$description.'</span>' : '' ); ?>
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