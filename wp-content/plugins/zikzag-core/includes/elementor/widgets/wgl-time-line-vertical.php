<?php

namespace WglAddons\Widgets;

defined( 'ABSPATH' ) || exit; // Abort, if called directly.

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


class Wgl_Time_Line_Vertical extends Widget_Base
{
    public function get_name() {
        return 'wgl-time-line-vertical';
    }

    public function get_title() {
        return esc_html__('WGL Time Line Vertical', 'zikzag-core');
    }

    public function get_icon() {
        return 'wgl-time-line-vertical';
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
        $secondary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
        $header_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);
        $main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);


        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> GENERAL
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'wgl_time_line_section',
            [
                'label' => esc_html__('General', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'add_appear',
            [
                'label' => esc_html__('Add Appear Animation', 'zikzag-core'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'line_color',
            [
                'label' => esc_html__('Line Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#d8d8d8',
                'selectors' => [
                    '{{WRAPPER}} .time_line-item:hover .time_line-date:before' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .time_line-date_curve:before' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .time_line-date_curve:after' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> CONTENT
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'zikzag-core'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'thumbnail',
            [
                'label' => esc_html__('Thumbnail', 'zikzag-core'),
                'type' => Controls_Manager::MEDIA,
                'default' => [ 'url' => '' ],
            ]
        );

        $repeater->add_responsive_control(
            'bg_position',
            [
                'label' => esc_html__('Position Image', 'Background Control', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'condition' => [ 'thumbnail[url]!' => '' ],
                'default' => 'center center',
                'responsive' => true,
                'options' => [
                    'top left' => esc_html__('Top Left', 'Background Control', 'zikzag-core'),
                    'top center' => esc_html__('Top Center', 'Background Control', 'zikzag-core'),
                    'top right' => esc_html__('Top Right', 'Background Control', 'zikzag-core'),
                    'center left' => esc_html__('Center Left', 'Background Control', 'zikzag-core'),
                    'center center' => esc_html__('Center Center', 'Background Control', 'zikzag-core'),
                    'center right' => esc_html__('Center Right', 'Background Control', 'zikzag-core'),
                    'bottom left' => esc_html__('Bottom Left', 'Background Control', 'zikzag-core'),
                    'bottom center' => esc_html__('Bottom Center', 'Background Control', 'zikzag-core'),
                    'bottom right' => esc_html__('Bottom Right', 'Background Control', 'zikzag-core'),
                ],
            ]
        );
        $repeater->add_control(
            'bg_size',
            [
                'label' => esc_html__('Size', 'Background Control', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'condition' => [ 'thumbnail[url]!' => '' ],
                'responsive' => true,
                'default' => 'unset',
                'options' => [
                    'auto' => esc_html__('Auto', 'Background Control', 'zikzag-core'),
                    'cover' => esc_html__('Cover', 'Background Control', 'zikzag-core'),
                    'contain' => esc_html__('Contain', 'Background Control', 'zikzag-core'),
                    'unset' => esc_html__('Unset', 'Background Control', 'zikzag-core'),
                ],
            ]
        );
        $repeater->add_control(
            'bg_repeat',
            [
                'label' => esc_html__('Repeat', 'Background Control', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'condition' => [ 'thumbnail[url]!' => '' ],
                'responsive' => true,
                'default' => 'no-repeat',
                'options' => [
                    'no-repeat' => esc_html__('No-repeat', 'Background Control', 'zikzag-core'),
                    'repeat' => esc_html__('Repeat', 'Background Control', 'zikzag-core'),
                    'repeat-y' => esc_html__('Repeat-Y', 'Background Control', 'zikzag-core'),
                    'repeat-x' => esc_html__('Repeat-X', 'Background Control', 'zikzag-core'),
                ],
            ]
        );

        $repeater->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'zikzag-core'),
                'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__('This is the heading​', 'zikzag-core'),
				'placeholder' => esc_html__('This is the heading​', 'zikzag-core'),
                'dynamic' => [ 'active' => true],
            ]
        );

        $repeater->add_control(
            'content',
            [
                'label' => esc_html__('Content', 'zikzag-core'),
                'type' => Controls_Manager::WYSIWYG,
                'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Optio, neque qui velit. Magni dolorum quidem ipsam eligendi, totam, facilis laudantium cum accusamus ullam voluptatibus commodi numquam, error, est. Ea, consequatur.', 'zikzag-core'),
                'dynamic' => [ 'active' => true],
            ]
        );

        $repeater->add_control(
            'date',
            [
                'label' => esc_html__('Date', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
				'default' => '',
            ]
        );

        $this->add_control(
            'items',
            [
                'label' => esc_html__('Layers', 'zikzag-core'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'title' => esc_html__('First heading​', 'zikzag-core'),
                        'date' => esc_html__('2020', 'zikzag-core'),
                    ],
                    [
                        'title' => esc_html__('Second heading​', 'zikzag-core'),
                        'date' => esc_html__('2019', 'zikzag-core'),
                    ],
                ],
                'title_field' => '{{title}}',
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

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typo',
                'selector' => '{{WRAPPER}} .time_line-title',
            ]
        );

        $this->start_controls_tabs( 'title_colors' );

        $this->start_controls_tab(
            'title_colors_idle',
            [
                'label' => esc_html__('Idle', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $header_font_color,
                'selectors' => [
                    '{{WRAPPER}} .time_line-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'title_colors_hover',
            [
                'label' => esc_html__('Hover', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .time_line-item:hover .time_line-title' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .time_line-item:hover .time_line-pointer:before' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> CONTENT
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'content_style_section',
            [
                'label' => esc_html__('Content', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typo',
                'selector' => '{{WRAPPER}} .time_line-text',
            ]
        );

        $this->add_control(
            'content_border_radius',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .time_line-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        $this->start_controls_tabs( 'content_colors' );

        $this->start_controls_tab(
            'content_colors_idle',
            [
                'label' => esc_html__('Idle', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $main_font_color,
                'selectors' => [
                    '{{WRAPPER}} .time_line-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_bg_color',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .time_line-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'content_border',
                'selector' => '{{WRAPPER}} .time_line-content',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'content_shadow',
                'selector' => '{{WRAPPER}} .time_line-content',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'content_colors_hover',
            [
                'label' => esc_html__('Hover', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'content_hover_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .time_line-item:hover .time_line-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_hover_bg_color',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .time_line-item:hover .time_line-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'content_hover_border',
                'selector' => '{{WRAPPER}} .time_line-item:hover .time_line-content',
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'content_hover_shadow',
                'selector' => '{{WRAPPER}} .time_line-item:hover .time_line-content',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> DATE
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'date_style_section',
            [
                'label' => esc_html__('Date', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'date_typo',
                'fields_options' => [
                    'typography' => ['default' => 'yes'],
                    'font_family' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_family']],
                    'font_weight' => ['default' => \Wgl_Addons_Elementor::$typography_1['font_weight']],
                ],
                'selector' => '{{WRAPPER}} .time_line-date',
            ]
        );

        $this->add_control(
            'date_size',
            [
                'label' => esc_html__('Date Size', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'max' => 200 ],
                ],
                'size_units' => [ 'px' ],
                'default' => [ 'size' => 80, 'unit' => 'px' ],
                'description' => esc_html__('Enter button diameter in pixels.', 'zikzag-core'),
                'selectors' => [
                    '{{WRAPPER}} .time_line-date' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .time_line-date:before' => 'width: calc({{SIZE}}{{UNIT}} + 50px); height: calc({{SIZE}}{{UNIT}} + 50px);',
                ],
            ]
        );

        $this->start_controls_tabs( 'date_colors' );

        $this->start_controls_tab(
            'date_colors_idle',
            [
                'label' => esc_html__('Idle', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'date_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .time_line-date' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'date_bg_color',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $secondary_color,
                'selectors' => [
                    '{{WRAPPER}} .time_line-date' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_color',
            [
                'label' => esc_html__('Arrow Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $secondary_color,
                'selectors' => [
                    '{{WRAPPER}} .time_line-content:before, {{WRAPPER}} .time_line-content:after' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'date_shadow',
                'selector' => '{{WRAPPER}} .time_line-date',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'date_colors_hover',
            [
                'label' => esc_html__('Hover', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'date_hover_color',
            [
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .time_line-item:hover .time_line-date' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'date_hover_bg_color',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}} .time_line-item:hover .time_line-date' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_hover_color',
            [
                'label' => esc_html__('Arrow Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $primary_color,
                'selectors' => [
                    '{{WRAPPER}} .time_line-item:hover .time_line-content:before, {{WRAPPER}} .time_line-item:hover .time_line-content:after' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'date_hover_shadow',
                'selector' => '{{WRAPPER}} .time_line-item:hover .time_line-date',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

    }

    protected function render()
    {

        wp_enqueue_script('jquery-appear', get_template_directory_uri() . '/js/jquery.appear.js', [], false, false);

        $settings = $this->get_settings_for_display();

        // HTML tags allowed for rendering
        $allowed_html = [
            'a' => [
                'href' => true,
                'title' => true,
            ],
            'br' => [],
            'em' => [],
            'strong' => [],
            'span' => [
                'class' => true,
                'style' => true,
            ],
            'p' => [
                'class' => true,
                'style' => true,
            ]
        ];

        $this->add_render_attribute( 'timeline-vertical', [
            'class' => [
                'wgl-timeline-vertical',
                ($settings[ 'add_appear' ] ? 'appear_anim' : '' ),
             ],
         ] );

        ?>
        <div <?php echo $this->get_render_attribute_string( 'timeline-vertical' ); ?>>


        <div class="time_line-items_wrap"><?php

        foreach ( $settings[ 'items' ] as $index => $item ) {

            $media = $this->get_repeater_setting_key( 'media', 'items' , $index );
            $this->add_render_attribute(
                $media,
                [
                    'style' => [
                        (!empty($item[ 'thumbnail' ][ 'url' ]) ? 'background-image: url('.esc_url($item[ 'thumbnail' ][ 'url' ]).');' : '' ),
                        ($item[ 'bg_position' ] != '' ? 'background-position: '.esc_attr($item[ 'bg_position' ]).';' : '' ),
                        ($item[ 'bg_size' ] != '' ? 'background-size: '.esc_attr($item[ 'bg_size' ]).';' : '' ),
                        ($item[ 'bg_repeat' ] != '' ? 'background-repeat: '.esc_attr($item[ 'bg_repeat' ]).';' : '' ),
                ]
            ]
        );

            $title = $this->get_repeater_setting_key( 'title', 'items' , $index );
            $this->add_render_attribute(
                $title,
                [
                    'class' => 'time_line-title',
                ]
            );

            $item_wrap = $this->get_repeater_setting_key( 'item_wrap', 'items' , $index );
            $this->add_render_attribute(
                $item_wrap,
                [
                    'class' => 'time_line-item',
                ]
            );

            ?>
            <div <?php echo $this->get_render_attribute_string( $item_wrap ); ?>>
                <div class="time_line-cont"></div>
                <div class="time_line-date_wrap">
                    <div class="time_line-date_curve"></div>
                    <div class="time_line-date"><span><?php echo $item[ 'date' ] ?></span></div>
                    <div class="time_line-date_curve"></div>
                </div>
                <div class="time_line-cont">
                    <div class="time_line-content" <?php echo $this->get_render_attribute_string( $media ); ?>><?php
                        if (!empty($item[ 'content' ]) || !empty($item[ 'title' ])) {
                            if (!empty($item[ 'title' ])) {?>
                                <h3 <?php echo $this->get_render_attribute_string( $title ); ?>><?php echo $item[ 'title' ] ?></h3><?php
                            }
                            if (!empty($item[ 'content' ])) {?>
                                <div class="time_line-text"><?php echo wp_kses( $item[ 'content' ], $allowed_html );?></div><?php
                            }
                        }?>
                    </div>
                </div>
            </div><?php
        }?>
        </div>
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