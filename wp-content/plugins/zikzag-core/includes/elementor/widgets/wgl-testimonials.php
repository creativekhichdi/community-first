<?php

namespace WglAddons\Widgets;

defined('ABSPATH') || exit; // Abort, if called directly.

use WglAddons\Includes\Wgl_Icons;
use WglAddons\Includes\Wgl_Carousel_Settings;
use WglAddons\Templates\WglTestimonials;
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
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;


class Wgl_Testimonials extends Widget_Base
{
    public function get_name()
    {
        return 'wgl-testimonials';
    }

    public function get_title()
    {
        return esc_html__('WGL Testimonials', 'zikzag-core');
    }

    public function get_icon()
    {
        return 'wgl-testimonials';
    }

    public function get_script_depends()
    {
        return ['slick'];
    }

    public function get_categories()
    {
        return ['wgl-extensions'];
    }


    protected function register_controls()
    {
        $primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
        $main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);
        $h_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);


        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> GENERAL
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'wgl_testimonials_section',
            ['label' => esc_html__('General', 'zikzag-core')]
        );
        $this->add_control(
            'posts_per_line',
            [
                'label' => esc_html__('Grid Columns Amount', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '1' => esc_html__('One ', 'zikzag-core'),
                    '2' => esc_html__('Two', 'zikzag-core'),
                    '3' => esc_html__('Three', 'zikzag-core'),
                    '4' => esc_html__('Four', 'zikzag-core'),
                    '5' => esc_html__('Five', 'zikzag-core'),
                ],
                'default' => '1',
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'thumbnail',
            [
                'label' => esc_html__('Image', 'zikzag-core'),
                'type' => Controls_Manager::MEDIA,
                'label_block' => true,
                'default' => ['url' => Utils::get_placeholder_image_src()],
            ]
        );

        $repeater->add_control(
            'author_name',
            [
                'label' => esc_html__('Author Name', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true
            ]
        );

        $repeater->add_control(
            'link_author',
            [
                'label' => esc_html__('Link Author', 'zikzag-core'),
                'type' => Controls_Manager::URL,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'author_position',
            [
                'label' => esc_html__('Author Position', 'zikzag-core'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true
            ]
        );

        $repeater->add_control(
            'quote',
            [
                'label' => esc_html__('Quote', 'zikzag-core'),
                'type' => Controls_Manager::WYSIWYG,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'list',
            [
                'label' => esc_html__('Items', 'zikzag-core'),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'author_name' => esc_html__('Tina Johanson', 'zikzag-core'),
                        'author_position' => esc_html__('UI Designer', 'zikzag-core'),
                        'quote' => esc_html__('“Choosing online studies was the best way to do it – the internet is fast, cheap & popular and it’s easy to communicate in social media with native speakers.”', 'zikzag-core'),
                        'thumbnail' => Utils::get_placeholder_image_src(),
                    ],
                ],
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ author_name }}}',
            ]
        );

        $this->add_control(
            'item_type',
            [
                'label' => esc_html__('Layout', 'zikzag-core'),
                'type' => 'wgl-radio-image',
                'options' => [
                    'author_top' => [
                        'title' => esc_html__('Top', 'zikzag-core'),
                        'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/testimonials_1.png',
                    ],
                    'author_bottom' => [
                        'title' => esc_html__('Bottom', 'zikzag-core'),
                        'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/testimonials_4.png',
                    ],
                    'inline_top' => [
                        'title' => esc_html__('Top Inline', 'zikzag-core'),
                        'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/testimonials_2.png',
                    ],
                    'inline_bottom' => [
                        'title' => esc_html__('Bottom Inline', 'zikzag-core'),
                        'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/testimonials_3.png',
                    ],

                ],
                'default' => 'author_top',
            ]
        );

        $this->add_control(
            'item_align',
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
                'default' => 'center',
                'toggle' => true,
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => esc_html__('Enable Hover Animation', 'zikzag-core'),
                'type' => Controls_Manager::SWITCHER,
                'description'  => esc_html__('Lift up the item on hover.', 'zikzag-core'),
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> CAROUSEL OPTIONS
        /*-----------------------------------------------------------------------------------*/

        Wgl_Carousel_Settings::options($this);


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> IMAGE
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_testimonials_image',
            [
                'label' => esc_html__('Image', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'image_size',
            [
                'label' => esc_html__('Image Size', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 20, 'max' => 1000],
                ],
                'default' => ['size' => 80],
            ]
        );

        $this->add_responsive_control(
            'image_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'testimonials_image_shadow',
                'selector' =>  '{{WRAPPER}} .wgl-testimonials_image img',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .wgl-testimonials_image img',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => 50,
                    'left' => 50,
                    'right' => 50,
                    'bottom' => 50,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> QUOTE
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'quote_style_section',
            [
                'label' => esc_html__('Quote', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'quote_tag',
            [
                'label' => esc_html__('Quote tag', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'div' => '‹div›',
                    'span' => '‹span›',
                    'h1' => '‹h1›',
                    'h2' => '‹h2›',
                    'h3' => '‹h3›',
                    'h4' => '‹h4›',
                    'h5' => '‹h5›',
                    'h6' => '‹h6›',
                ],
                'default' => 'div',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'custom_fonts_quote',
                'selector' => '{{WRAPPER}} .wgl-testimonials_quote',
            ]
        );

        $this->add_responsive_control(
            'quote_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_quote' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'quote_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_quote' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .type-inline_bottom .wgl-testimonials_quote:before, {{WRAPPER}} .type-inline_bottom .wgl-testimonials_quote:after' => 'bottom: calc(({{BOTTOM}}{{UNIT}} / -2) + 5px);',
                    '{{WRAPPER}} .type-inline_top .wgl-testimonials_quote:before, {{WRAPPER}} .type-inline_top .wgl-testimonials_quote:after' => 'top: calc(({{TOP}}{{UNIT}} / -2) + 5px);',
                ],
            ]
        );

        $this->add_control(
            'quote_border_radius',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_quote' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'quote_colors',
            [
                'separator' => 'before',
            ]
        );

        $this->start_controls_tab(
            'tab_quote_idle',
            ['label' => esc_html__('Idle', 'zikzag-core')]
        );

        $this->add_control(
            'quote_color_idle',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $main_font_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_quote' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'quote_bg_idle',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_quote, {{WRAPPER}} .wgl-testimonials_quote:before' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'quote_idle',
                'selector' => '{{WRAPPER}} .wgl-testimonials_quote, {{WRAPPER}} .wgl-testimonials_quote:before',

            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_quote_hover',
            ['label' => esc_html__('Hover', 'zikzag-core')]
        );

        $this->add_control(
            'quote_color_hover',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_item:hover .wgl-testimonials_quote' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'quote_bg_hover',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_item:hover .wgl-testimonials_quote' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'quote_hover',
                'selector' =>  '{{WRAPPER}} .wgl-testimonials_item:hover .wgl-testimonials_quote',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'pointer_switch',
            [
                'label' => esc_html__('Enable pointing element', 'zikzag-core'),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'item_type' => ['inline_bottom', 'inline_top']
                ],
                'separator' => 'before',
                'default' => 'yes',
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_quote:before, {{WRAPPER}} .wgl-testimonials_quote:after' => 'content: \'\';'
                ],
            ]
        );

        $this->add_responsive_control(
            'pointer_offset',
            [
                'label' => esc_html__('Pointer Offset', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'condition' => ['pointer_switch!' => ''],
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => ['max' => 350],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_quote:before, {{WRAPPER}} .wgl-testimonials_quote:after' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> NAME
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'author_name_style_section',
            [
                'label' => esc_html__('Name', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'name_tag',
            [
                'label' => esc_html__('HTML tag', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'h3',
                'options' => [
                    'div' => '‹div›',
                    'span' => '‹span›',
                    'h1' => '‹h1›',
                    'h2' => '‹h2›',
                    'h3' => '‹h3›',
                    'h4' => '‹h4›',
                    'h5' => '‹h5›',
                    'h6' => '‹h6›',
                ],
            ]
        );

        $this->add_responsive_control(
            'name_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('name_colors');

        $this->start_controls_tab(
            'tab_name_idle',
            ['label' => esc_html__('Idle', 'zikzag-core')]
        );

        $this->add_control(
            'name_color_idle',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $h_font_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_name' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .quote_icon_svg svg .st1' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_name_hover',
            ['label' => esc_html__('Hover', 'zikzag-core')]
        );

        $this->add_control(
            'name_color_hover',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $h_font_color,
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_name:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'custom_fonts_name',
                'selector' => '{{WRAPPER}} .wgl-testimonials_name',
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> POSITION
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'author_position_style_section',
            [
                'label' => esc_html__('Position', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'position_tag',
            [
                'label' => esc_html__('HTML tag', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'span',
                'options' => [
                    'div' => '‹div›',
                    'span' => '‹span›',
                    'h1' => '‹h1›',
                    'h2' => '‹h2›',
                    'h3' => '‹h3›',
                    'h4' => '‹h4›',
                    'h5' => '‹h5›',
                    'h6' => '‹h6›',
                ],
            ]
        );

        $this->add_responsive_control(
            'position_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => 10,
                    'left' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_position' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('position_colors');

        $this->start_controls_tab(
            'position_color_idle',
            ['label' => esc_html__('Idle', 'zikzag-core')]
        );

        $this->add_control(
            'custom_position_color',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#989898',
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_position' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_position_hover',
            [
                'label' => esc_html__('Hover', 'zikzag-core'),
            ]
        );

        $this->add_control(
            'position_color_hover',
            [
                'label' => esc_html__('Text Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_position:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'custom_fonts_position',
                'selector' => '{{WRAPPER}} .wgl-testimonials_position',
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  STYLE -> ITEM BOX
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_item_box',
            [
                'label' => esc_html__('Item Box', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_item',
                'label' => esc_html__('Background', 'zikzag-core'),
                'types' => ['classic', 'gradient'],
                'fields_options' => [
					'background' => [ 'default' => 'classic' ],
					'color' => [ 'default' => '#ffffff' ],
				],
                'selector' => '{{WRAPPER}} .wgl-testimonials_item',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'testimonials_shadow',
                'selector' => '{{WRAPPER}} .wgl-testimonials_item',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'testimonials_border',
                'label' => esc_html__('Border', 'zikzag-core'),
                'selector' => '{{WRAPPER}} .wgl-testimonials_item',
            ]
        );

        $this->add_responsive_control(
            'item_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => 0,
                    'left' => 6,
                    'right' => 6,
                    'bottom' => 0,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => 4,
                    'left' => 10,
                    'right' => 10,
                    'bottom' => 5,
                    'unit' => '%'
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'item_border_radius',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => 10,
                    'left' => 10,
                    'right' => 10,
                    'bottom' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .wgl-testimonials_item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_general',
            [
                'label' => esc_html__('Carousel', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [ 'use_carousel' => 'yes' ],
            ]
        );

        $this->add_responsive_control(
            'slick_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
                    'top' => 20,
                    'left' => 0,
                    'right' => 0,
                    'bottom' => 40,
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'blog_item_width',
            [
                'label'       => esc_html__('Arrow Top Offset', 'virtus-core'),
                'type'        => Controls_Manager::NUMBER,
                'default'     => '-30',
                'step'        => 1,
                'selectors' => [
                    '{{WRAPPER}} .slick-arrow' => 'margin-top: {{VALUE}}px',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();

        $testimonials = new WglTestimonials();
        echo $testimonials->render($this, $atts);
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
