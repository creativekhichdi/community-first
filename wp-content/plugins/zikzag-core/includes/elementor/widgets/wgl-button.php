<?php

namespace WglAddons\Widgets;

defined( 'ABSPATH' ) || exit; // Abort, if called directly.

use WglAddons\Includes\Wgl_Icons;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;


class Wgl_Button extends Widget_Base
{
    public function get_name() {
        return 'wgl-button';
    }

    public function get_title() {
        return esc_html__('WGL Button', 'zikzag-core');
    }

    public function get_icon() {
        return 'wgl-button';
    }

    public function get_categories() {
        return [ 'wgl-extensions' ];
    }

    public static function get_button_sizes()
    {
        return [
            'sm' => esc_html__('Small', 'zikzag-core'),
            'md' => esc_html__('Medium', 'zikzag-core'),
            'lg' => esc_html__('Large', 'zikzag-core'),
            'xl' => esc_html__('Extra Large', 'zikzag-core'),
        ];
    }


    protected function register_controls()
    {
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
            'text',
            [
                'label' => esc_html__('Text', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
                'default' => esc_html__('Load More', 'zikzag-core'),
                'placeholder' => esc_html__('Button Text', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => esc_html__('Link', 'zikzag-core'),
                'type' => Controls_Manager::URL,
                'dynamic' => [ 'active' => true ],
                'placeholder' => esc_html__('https://your-link.com', 'zikzag-core'),
                'default' => [ 'url' => '#' ],
            ]
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
                    'justify' => [
                        'title' => esc_html__('Full Width', 'zikzag-core'),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'prefix_class' => 'a%s',
            ]
        );

        $this->add_control(
            'size',
            [
                'label' => esc_html__('Size', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'lg',
                'options' => self::get_button_sizes(),
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'button_css_id',
            [
                'label' => esc_html__('Button ID', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'title' => esc_html__('Add your custom id WITHOUT the Pound key. e.g: my-id', 'zikzag-core'),
                'separator' => 'before',
                'dynamic' => [ 'active' => true ],
                'label_block' => false,
                'description' => esc_html__('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows [A-z _ 0-9] chars without spaces.', 'zikzag-core'),
                'default' => '',
            ]
        );

        $this->end_controls_section();

        $output[ 'icon_align' ] = [
            'label' => esc_html__('Icon Position', 'zikzag-core'),
            'type' => Controls_Manager::SELECT,
            'condition' => [ 'icon_type!' => '' ],
            'options' => [
                'left' => esc_html__('Before', 'zikzag-core'),
                'right' => esc_html__('After', 'zikzag-core'),
            ],
            'default' => 'left',
        ];

        $output[ 'icon_indent' ] = [
            'label' => esc_html__('Icon Spacing', 'zikzag-core'),
            'type' => Controls_Manager::SLIDER,
            'condition' => [ 'icon_type!' => '' ],
            'range' => [
                'px' => [ 'max' => 50 ],
            ],
            'selectors' => [
                '{{WRAPPER}} .align-icon-right .icon-wrapper .icon' => 'margin-left: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .align-icon-left .icon-wrapper .icon' => 'margin-right: {{SIZE}}{{UNIT}};',
            ],
        ];

        Wgl_Icons::init(
            $this,
            [
                'output' => $output,
                'section' => true,
            ]
        );


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> BUTTON
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_button',
            [
                'label' => esc_html__('Button', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .wgl-button',
            ]
        );

        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'tab_button_idle',
            [ 'label' => esc_html__('Idle', 'zikzag-core') ]
        );

        $this->add_control(
            'button_color_idle',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .wgl-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_idle',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_color_idle',
            [
                'label' => esc_html__('Border Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'condition' => [ 'border_border!' => '' ],
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-button' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .wgl-button',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [ 'label' => esc_html__('Hover', 'zikzag-core') ]
        );

        $this->add_control(
            'button_color_hover',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .wgl-button:hover, {{WRAPPER}} .wgl-button:focus' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_hover',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $secondary_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-button:hover, {{WRAPPER}} .wgl-button:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'condition' => [ 'border_border!' => '' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-button:hover, {{WRAPPER}} .wgl-button:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_hover_box_shadow',
                'selector' => '{{WRAPPER}} .wgl-button:hover',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'separator' => 'before',
                'selector' => '{{WRAPPER}} .wgl-button',
                'fields_options' => [
                    'color' => [ 'type' => Controls_Manager::HIDDEN ],
                ],
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'text_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'separator' => 'before',
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> ICON
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_icon',
            [
                'label' => esc_html__('Icon', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [ 'icon_type!' => '' ],
            ]
        );

        $this->add_responsive_control(
            'icon_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'separator' => 'before',
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_button_style_icon' );

        $this->start_controls_tab(
            'tab_button_normal_icon',
            [ 'label' => esc_html__('Idle', 'zikzag-core') ]
        );

        $this->add_control(
            'color_icon_idle',
            [
                'label' => esc_html__('Icon Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover_icon',
            [ 'label' => esc_html__('Hover', 'zikzag-core') ]
        );

        $this->add_control(
            'color_icon_hover',
            [
                'label' => esc_html__('Icon Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .wgl-button:hover .icon, {{WRAPPER}} .wgl-button:focus .icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'icon_size',
            [
                'label' => esc_html__('Font Size', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'condition' => [
                	'icon_type' => 'font',
	                'icon_fontawesome!' => [
		                'library' => 'wgl_icons',
		                'value' => 'flaticon flaticon-long-next',
	                ],
                ],
                'separator' => 'before',
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [ 'max' => 90 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> ANIMATION
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_animation',
            [
                'label' => esc_html__('Animation', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => esc_html__('Button Hover', 'zikzag-core'),
                'type' => Controls_Manager::HOVER_ANIMATION,
                'separator' => 'after',
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        echo Wgl_Button::init_button($this, $settings);
    }

    public static function init_button($self, $settings)
    {

        $self->add_render_attribute( 'wrapper', 'class', 'button-wrapper' );

        if ( ! empty( $settings[ 'link' ][ 'url' ] ) ) {
	        $self->add_link_attributes( 'button', $settings['link'] );
        }

        $self->add_render_attribute( 'button', 'class', 'wgl-button' );
        $self->add_render_attribute( 'button', 'role', 'button' );

        if ( ! empty( $settings[ 'button_css_id' ] ) ) {
            $self->add_render_attribute( 'button', 'id', $settings[ 'button_css_id' ] );
        }

        if ( ! empty( $settings[ 'size' ] ) ) {
            $self->add_render_attribute( 'button', 'class', 'btn-size-' . $settings[ 'size' ] );
        }

        if ( isset($settings[ 'hover_animation' ]) && !empty($settings[ 'hover_animation' ]) ) {
            $self->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings[ 'hover_animation' ] );
        }

        $settings_icon_align = isset($settings[ 'icon_align' ]) ? 'align-icon-' .$settings[ 'icon_align' ] : '';

        $self->add_render_attribute( [
            'content-wrapper' => [
                'class' => [
                    'button-content-wrapper',
                    $settings_icon_align,
                ]
            ],
            'text' => [
                'class' => 'wgl-button-text',
            ],
        ] );

        echo '<div ', $self->get_render_attribute_string( 'wrapper' ), '>';
        echo '<a  ', $self->get_render_attribute_string( 'button' ), '>';
        if ( !empty($settings[ 'text' ]) || !empty($settings[ 'icon_type' ]) ) {
            echo '<div ', $self->get_render_attribute_string( 'content-wrapper' ), '>';

            if ( ! empty( $settings[ 'icon_type' ] ) ) {
                $icons = new Wgl_Icons;
                $button_icon_out = $icons->build($self, $settings, []);
                echo $button_icon_out;
            }
            echo '<span ', $self->get_render_attribute_string( 'text' ), '>',
                $settings[ 'text' ],
            '</span>';

            echo '</div>';
        }
        echo '</a>';
        echo '</div>';
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