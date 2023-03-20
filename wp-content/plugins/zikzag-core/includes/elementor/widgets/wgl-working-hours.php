<?php
namespace WglAddons\Widgets;

use WglAddons\Includes\Wgl_Icons;
use WglAddons\Includes\Wgl_Carousel_Settings;
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
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;


defined( 'ABSPATH' ) || exit; // Abort, if called directly.

class Wgl_Working_Hours extends Widget_Base {

    public function get_name() {
        return 'wgl-working-hours';
    }

    public function get_title() {
        return esc_html__('WGL Working Hours', 'zikzag-core');
    }

    public function get_icon() {
        return 'wgl-working-hours';
    }

    public function get_categories() {
        return [ 'wgl-extensions' ];
    }

    public function get_script_depends() {
        return [ 'jquery-appear' ];
    }



    protected function register_controls() {
        $theme_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
        $second_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
        $third_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-third-color'));
        $header_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);
        $main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);

        /*-----------------------------------------------------------------------------------*/
        /*  Content
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'wgl_working_section',
            array(
                'label' => esc_html__('Content', 'zikzag-core'),
            )
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'working_day',
            array(
                'label' => esc_html__('Day', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Monday', 'zikzag-core'),
                'dynamic' => [ 'active' => true],
            )
        );

        $repeater->add_control(
            'working_hours',
            array(
                'label' => esc_html__('Hours', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('8.00 - 21.00', 'zikzag-core'),
            )
        );

        $repeater->add_control(
            'custom_colors',
            array(
                'label' => esc_html__('Custom Colors', 'zikzag-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', 'zikzag-core'),
                'label_off' => esc_html__('Off', 'zikzag-core'),
                'return_value' => 'yes',
            )
        );

        $repeater->add_control(
            'day_color',
            array(
                'label' => esc_html__('Day Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $main_font_color,
                'condition' => [
                    'custom_colors' => 'yes',
                ],
            )
        );

        $repeater->add_control(
            'hours_color',
            array(
                'label' => esc_html__('Hours Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $second_color,
                'condition' => [
                    'custom_colors' => 'yes',
                ],
            )
        );

        $this->add_control(
            'items',
            array(
                'label' => esc_html__('Days', 'zikzag-core'),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [ 'working_day' => esc_html__('Monday', 'zikzag-core')],
                    [ 'working_day' => esc_html__('Tuesday', 'zikzag-core')],
                ],
                'fields' => $repeater->get_controls(),
                'title_field' => '{{working_day}}',
            )
        );

        $this->end_controls_section();

        /*-----------------------------------------------------------------------------------*/
        /*  Style Section
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'style_section',
            array(
                'label' => esc_html__('Styles', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'item_styles',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__('Item Styles', 'zikzag-core'),
            ]
        );

        $this->add_responsive_control(
			'item_margin',
			[
				'label' => esc_html__('Margin', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .working-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => 0,
                    'left' => 0,
                    'right' => 0,
                    'bottom' => 6,
                    'unit' => 'px',
                ],
                'separator' => 'after'
			]
        );

        $this->add_control(
            'day_styles',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__('Day Styles', 'zikzag-core'),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'day_typo',
                'selector' => '{{WRAPPER}} .working-item_day',
            )
        );

        $this->add_control(
            'day_color',
            array(
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $main_font_color,
                'selectors' => array(
                    '{{WRAPPER}} .working-item_day' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
			'day_margin',
			[
				'label' => esc_html__('Margin', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .working-item_day' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'separator' => 'after'
			]
		);

        $this->add_control(
            'hours_styles',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__('Hours Styles', 'zikzag-core'),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'hours_typo',
                'selector' => '{{WRAPPER}} .working-item_hours',
            )
        );

        $this->add_control(
            'hours_color',
            array(
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $second_color,
                'selectors' => array(
                    '{{WRAPPER}} .working-item_hours' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
			'hours_margin',
			[
				'label' => esc_html__('Margin', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .working-item_hours' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'separator' => 'after'
			]
        );

        $this->add_control(
            'line_color',
            array(
                'label' => esc_html__('Line Between Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#e5e5e5',
                'selectors' => array(
                    '{{WRAPPER}} .working-item:after' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'working-hours', [
			'class' => [
                'wgl-working-hours',
            ],
        ] );

        ?><div <?php echo $this->get_render_attribute_string( 'working-hours' ); ?>><?php

        foreach ( $settings[ 'items' ] as $index => $item ) {

            $working_day = $this->get_repeater_setting_key( 'working_day', 'items' , $index );
            $this->add_render_attribute( $working_day, [
                'class' => [
                    'working-item_day',
                ],
                'style' => [
                    ((bool)$item[ 'custom_colors' ] ? 'color: '.esc_attr($item[ 'day_color' ]).';' : '' ),
                ]
            ] );

            $working_hours = $this->get_repeater_setting_key( 'working_hours', 'items' , $index );
            $this->add_render_attribute( $working_hours, [
                'class' => [
                    'working-item_hours',
                ],
                'style' => [
                    ((bool)$item[ 'custom_colors' ] ? 'color: '.esc_attr($item[ 'hours_color' ]).';' : '' ),
                ]
            ] );

            ?>
            <div class="working-item"><?php
                if (!empty($item[ 'working_day' ])) {
                    ?><div <?php echo $this->get_render_attribute_string( $working_day ); ?>><?php echo esc_html($item[ 'working_day' ]); ?></div><?php
                }
                if (!empty($item[ 'working_hours' ])) {
                    ?><div <?php echo $this->get_render_attribute_string( $working_hours ); ?>><?php echo esc_html($item[ 'working_hours' ]); ?></div><?php
                }?>
            </div><?php

        }

        ?></div><?php

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