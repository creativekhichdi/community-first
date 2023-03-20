<?php
namespace WglAddons\Widgets;

defined( 'ABSPATH' ) || exit; // Abort, if called directly.

use WglAddons\Includes\Wgl_Icons;
use WglAddons\Includes\Wgl_Carousel_Settings;
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
use Elementor\Group_Control_Image_Size;


class Wgl_Counter extends Widget_Base
{
    public function get_name() {
        return 'wgl-counter';
    }

    public function get_title() {
        return esc_html__('WGL Counter', 'zikzag-core');
    }

    public function get_icon() {
        return 'wgl-counter';
    }

    public function get_categories() {
        return [ 'wgl-extensions' ];
    }

    public function get_script_depends() {
        return [ 'jquery-appear' ];
    }


    protected function register_controls()
    {
        $primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
        $second_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
        $h_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);
        $main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);


        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> GENERAL
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'wgl_counter_content',
            [ 'label' => esc_html__('General', 'zikzag-core') ]
        );

        Wgl_Icons::init(
            $this,
            [
                'label' => esc_html__('Counter ', 'zikzag-core'),
                'output' => '',
                'section' => false,
            ]
        );

        $this->add_control(
            'layout',
            [
                'label' => esc_html__('Layout', 'zikzag-core'),
                'type' => 'wgl-radio-image',
                'condition' => [ 'icon_type!' => '' ],
                'options' => [
                    'top' => [
                        'title'=> esc_html__('Top', 'zikzag-core'),
                        'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/style_def.png',
                    ],
                    'left' => [
                        'title'=> esc_html__('Left', 'zikzag-core'),
                        'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/style_left.png',
                    ],
                    'right' => [
                        'title'=> esc_html__('Right', 'zikzag-core'),
                        'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/style_right.png',
                    ],
                ],
                'default' => 'top',
            ]
        );

        $this->add_control(
            'counter_title',
            [
                'label' => esc_html__('Title Text', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'separator' => 'before',
                'label_block' => true,
                'default' => esc_html__('This is the heading​', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'title_block',
            [
                'label' => esc_html__('Title Full Width', 'zikzag-core'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'start_value',
            [
                'label' => esc_html__('Start Value', 'zikzag-core'),
                'type' => Controls_Manager::NUMBER,
                'separator' => 'before',
                'min' => 0,
                'step' => 10,
                'default' => 0,
            ]
        );

        $this->add_control(
            'end_value',
            [
                'label' => esc_html__('End Value', 'zikzag-core'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'step' => 10,
                'default' => 120,
            ]
        );

        $this->add_control(
            'prefix',
            [
                'label' => esc_html__('Counter Prefix', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'suffix',
            [
                'label' => esc_html__('Counter Suffix', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__('+', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'speed',
            [
                'label' => esc_html__('Animation Speed', 'zikzag-core'),
                'type' => Controls_Manager::NUMBER,
                'min' => 100,
                'step' => 100,
                'default' => 2000,
            ]
        );

        $this->add_control(
            'alignment',
            [
                'label' => esc_html__('Alignment', 'zikzag-core'),
                'type' => Controls_Manager::CHOOSE,
                'separator' => 'before',
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
                'default' => 'left',
                'toggle' => true,
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> GENERAL
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'counter_style_section',
            [
                'label' => esc_html__('General', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'counter_offset',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-counter' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'counter_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-counter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'counter_border_radius',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-counter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'counter_color_tab' );

        $this->start_controls_tab(
            'custom_counter_color_idle',
            [ 'label' => esc_html__('Idle' , 'zikzag-core') ]
        );

        $this->add_control(
            'bg_counter_color',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wgl-counter' => 'background-color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'counter_border',
                'label' => esc_html__('Border Type', 'zikzag-core'),
                'selector' => '{{WRAPPER}} .wgl-counter',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'counter_shadow',
                'selector' =>  '{{WRAPPER}} .wgl-counter',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'custom_counter_color_hover',
            [ 'label' => esc_html__('Hover' , 'zikzag-core') ]
        );

        $this->add_control(
            'bg_counter_color_hover',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}:hover .wgl-counter' => 'background-color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'counter_border_hover',
                'label' => esc_html__('Border Type', 'zikzag-core'),
                'selector' => '{{WRAPPER}}:hover .wgl-counter',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'counter_shadow_hover',
                'selector' =>  '{{WRAPPER}}:hover .wgl-counter',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> MEDIA
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_icon',
            [
                'label' => esc_html__('Media', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'primary_color',
            [
                'label' => esc_html__('Icon Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'condition' => [ 'icon_type' => 'font' ],
                'default' => '#838383',
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__('Icon Size', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'condition' => [ 'icon_type' => 'font' ],
                'range' => [
                    'px' => [ 'min' => 13, 'max' => 100 ],
                ],
                'default' => [ 'size' => 40 ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-counter_media-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-counter_media-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'counter_icon_border_radius',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-counter_media-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'media_background',
                'label' => esc_html__('Background', 'zikzag-core'),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .wgl-counter_media-wrap',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'counter_icon_border',
                'selector' => '{{WRAPPER}} .wgl-counter_media-wrap'
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'counter_icon_shadow',
                'selector' => '{{WRAPPER}} .wgl-counter_media-wrap',
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> VALUE
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'value_style_section',
            [
                'label' => esc_html__('Value', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'value_offset',
            [
                'label' => esc_html__('Value Offset', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .wgl-counter_value-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'custom_fonts_value',
                'selector' => '{{WRAPPER}} .wgl-counter_value-wrap',
            ]
        );

        $this->add_control(
            'value_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-counter_value-wrap' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'value_prefix_color',
            [
                'label' => esc_html__('Prefix Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wgl-counter__prefix' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'value_suffix_color',
            [
                'label' => esc_html__('Suffix Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#bfbfbf',
                'selectors' => [
                    '{{WRAPPER}} .wgl-counter__suffix' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> TITLE
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'title_style_section',
            [
                'label' => esc_html__('Title', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label' => esc_html__('Title Tag', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => '‹h1›',
                    'h2' => '‹h2›',
                    'h3' => '‹h3›',
                    'h4' => '‹h4›',
                    'h5' => '‹h5›',
                    'h6' => '‹h6›',
                    'div' => '‹div›',
                    'span' => '‹span›',
                ],
                'default' => 'h3',
            ]
        );

        $this->add_responsive_control(
            'title_offset',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'top' => 2,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 18,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-counter_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'custom_fonts_title',
                'selector' => '{{WRAPPER}} .wgl-counter_title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $h_font_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-counter_title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    public function render()
    {
        $_s = $this->get_settings_for_display();

        $this->add_render_attribute(
            [
                'counter' => [
                    'class' => [
                        'wgl-counter',
                        'a'.$_s[ 'alignment' ],
                        $_s[ 'title_block' ] ? 'title-block' : 'title-inline',
                    ],
                ],
                'counter-wrap' => [
                    'class' => [
                        'wgl-counter_wrap',
                        $_s[ 'layout' ] ? 'wgl-layout-' . $_s[ 'layout' ] : '',
                    ],
                ],
                'counter_value' => [
                    'class' => 'wgl-counter__value',
                    'data-start-value' => $_s[ 'start_value' ],
                    'data-end-value' => $_s[ 'end_value' ],
                    'data-speed' => $_s[ 'speed' ],
                ],
            ]
        );

        // Icon/Image
        ob_start();
        if (! empty($_s[ 'icon_type' ])) {
            $icons = new Wgl_Icons;
            echo $icons->build($this, $_s, []);
        }
        $counter_media = ob_get_clean();

        $_s[ 'prefix' ] = !empty($_s[ 'prefix' ]) ? $_s[ 'prefix' ] : '';

        echo '<div ', $this->get_render_attribute_string( 'counter' ), '>';
            echo '<div ', $this->get_render_attribute_string( 'counter-wrap' ), '>';
                if ($_s[ 'icon_type' ] != '' && $counter_media) {
                    echo '<div class="media-wrap">',
                        $counter_media,
                    '</div>';
                }
                ?>
                <div class="content-wrap">
                    <div class="wgl-counter_value-wrap">
                        <?php
                        if ($_s[ 'prefix' ]) {
                            echo '<span class="wgl-counter__prefix">', $_s[ 'prefix' ], '</span>';
                        }
                        if (!empty($_s[ 'end_value' ])) {
                          echo '<div class="wgl-counter__placeholder-wrap">';
                            echo '<span class="wgl-counter__placeholder">',
                                $_s[ 'end_value' ],
                            '</span>';

                            echo '<span ', $this->get_render_attribute_string( 'counter_value' ), '>',
                                $_s[ 'start_value' ],
                            '</span>';
                          echo '</div>';
                        }
                        if (!empty($_s[ 'suffix' ])) {
                            echo '<span class="wgl-counter__suffix">',
                                $_s[ 'suffix' ],
                            '</span>';
                        } ?>
                    </div>
                    <?php
                    if (!empty($_s[ 'counter_title' ])) {
                        echo '<', $_s[ 'title_tag' ], ' class="wgl-counter_title">',
                            $_s[ 'counter_title' ],
                        '</', $_s[ 'title_tag' ], '>';
                    }?>
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