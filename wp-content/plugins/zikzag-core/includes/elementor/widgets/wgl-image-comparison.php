<?php
namespace WglAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use Elementor\Group_Control_Css_Filter;


defined( 'ABSPATH' ) || exit; // Abort, if called directly.

class Wgl_Image_Comparison extends Widget_Base {

    public function get_name() {
        return 'wgl-image-comparison';
    }

    public function get_title() {
        return esc_html__('WGL Image Comparison', 'zikzag-core');
    }

    public function get_icon() {
        return 'wgl-image-comparison';
    }

    public function get_categories() {
        return [ 'wgl-extensions' ];
    }

    public function get_script_depends() {
        return [
            'cocoen',
        ];
    }



    protected function register_controls() {
        $theme_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
        $main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);
        $header_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);

        /*-----------------------------------------------------------------------------------*/
        /*  Content
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section('wgl_image_comparison_section',
            array(
                'label'         => esc_html__('Image Comparison Settings', 'zikzag-core'),
            )
        );

        $this->add_control(
            'before_image',
            array(
                'label'       => esc_html__('Before Image', 'zikzag-core'),
                'type'        => Controls_Manager::MEDIA,
                'label_block' => true,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            )
        );

        $this->add_control(
            'after_image',
            array(
                'label'       => esc_html__('After Image', 'zikzag-core'),
                'type'        => Controls_Manager::MEDIA,
                'label_block' => true,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            )
        );

        $this->end_controls_section();

        /*-----------------------------------------------------------------------------------*/
        /*  Styles options
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__('Slider Bar Styles', 'zikzag-core'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
        );

        $this->add_control(
			'slider',
			[
				'label' => esc_html__('Slider Bar', 'zikzag-core'),
				'type' => Controls_Manager::HEADING,
			]
        );

        $this->add_control(
			'slider_color',
			[
				'label' => esc_html__('Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
                'default' => '#232323',
				'selectors' => [
					'{{WRAPPER}} .cocoen-drag:before, {{WRAPPER}} .cocoen-drag:after' => 'color: {{VALUE}};',
				],
			]
        );

        $this->add_control(
			'slider_bg',
			[
				'label' => esc_html__('Background Color', 'zikzag-core'),
				'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .cocoen-drag, {{WRAPPER}} .cocoen-drag:before, {{WRAPPER}} .cocoen-drag:after' => 'background: {{VALUE}};',
				],
			]
        );

        $this->end_controls_section();

    }

    protected function render() {

        wp_enqueue_script('cocoen', get_template_directory_uri() . '/js/cocoen.min.js', array(), false, false);

        $settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'image_comp_wrapper', [
            'class' => [
                'wgl-image_comparison',
                'cocoen'
            ],
        ] );

        $this->add_render_attribute( 'before_image', [
            'class' => [
                'comp-image_before',
                'comp-image'
            ],
            'src' => esc_url($settings[ 'before_image' ][ 'url' ]),
            'alt' => Control_Media::get_image_alt( $settings[ 'before_image' ] ),
        ] );

        $this->add_render_attribute( 'after_image', [
            'class' => [
                'comp-image_after',
                'comp-image'
            ],
            'src' => esc_url($settings[ 'after_image' ][ 'url' ]),
            'alt' => Control_Media::get_image_alt( $settings[ 'after_image' ] ),
        ] );

        ?><div <?php echo $this->get_render_attribute_string( 'image_comp_wrapper' ); ?>>
            <img <?php echo $this->get_render_attribute_string( 'before_image' ); ?> />
            <img <?php echo $this->get_render_attribute_string( 'after_image' ); ?> />
        </div><?php

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