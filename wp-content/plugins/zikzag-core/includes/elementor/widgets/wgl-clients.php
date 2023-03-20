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
use Elementor\Group_Control_Background;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;


class Wgl_Clients extends Widget_Base {

    public function get_name() {
        return 'wgl-clients';
    }

    public function get_title() {
        return esc_html__('WGL Clients', 'zikzag-core');
    }

    public function get_icon() {
        return 'wgl-clients';
    }

    public function get_script_depends() {
        return [ 'slick' ];
    }

    public function get_categories() {
        return [ 'wgl-extensions' ];
    }

    protected function register_controls()
    {
        $primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
        $h_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);


        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> GENERAL
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_content_general',
            [ 'label' => esc_html__('General', 'zikzag-core') ]
        );

        $this->add_control(
            'item_grid',
            [
                'label' => esc_html__('Grid Columns Amount', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '1' => esc_html__('One', 'zikzag-core'),
                    '2' => esc_html__('Two', 'zikzag-core'),
                    '3' => esc_html__('Three', 'zikzag-core'),
                    '4' => esc_html__('Four', 'zikzag-core'),
                    '5' => esc_html__('Five', 'zikzag-core'),
                    '6' => esc_html__('Six', 'zikzag-core'),
                ],
                'default' => '4',
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'thumbnail',
            [
                'label' => esc_html__('Thumbnail', 'zikzag-core'),
                'type' => Controls_Manager::MEDIA,
                'label_block' => true,
                'default' => [ 'url' => Utils::get_placeholder_image_src() ],
            ]
        );

        $repeater->add_control(
            'hover_thumbnail',
            [
                'label' => esc_html__('Hover Thumbnail', 'zikzag-core'),
                'type' => Controls_Manager::MEDIA,
                'label_block' => true,
                'description' => esc_html__('For \'Toggle Image\' animations only.', 'zikzag-core' ),
                'default' => [ 'url' => '' ],
            ]
        );

        $repeater->add_control(
            'client_link',
            [
                'label' => esc_html__('Add Link', 'zikzag-core'),
                'type' => Controls_Manager::URL,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'list',
            [
                'label' => esc_html__('Items', 'zikzag-core'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );

        $this->add_control(
            'item_anim',
            [
                'label' => esc_html__('Thumbnail Animation', 'zikzag-core'),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
                'options' => [
                    'none' => esc_html__('None', 'zikzag-core'),
                    'ex_images' => esc_html__('Toggle Image - Fade', 'zikzag-core'),
                    'ex_images_ver' => esc_html__('Toggle Image - Vertical', 'zikzag-core'),
                    'grayscale' => esc_html__('Grayscale', 'zikzag-core'),
                    'opacity' => esc_html__('Opacity', 'zikzag-core'),
                    'zoom' => esc_html__('Zoom', 'zikzag-core'),
                    'contrast' => esc_html__('Contrast', 'zikzag-core'),
                    'blur-1' => esc_html__('Blur 1', 'zikzag-core'),
                    'blur-2' => esc_html__('Blur 2', 'zikzag-core'),
                    'invert' => esc_html__('Invert', 'zikzag-core'),
                ],
                'default' => 'ex_images',
            ]
        );

        $this->add_control(
            'height',
            [
                'label' => esc_html__('Custom Items Height', 'zikzag-core'),
                'type' => Controls_Manager::SLIDER,
                'condition' => [ 'item_anim' => 'ex_images_bg' ],
                'range' => [
                    'px' => [ 'min' => 50, 'max' => 300 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .clients_image' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'alignment_h',
            [
                'label' => esc_html__('Horizontal Alignment', 'zikzag-core'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => true,
                'toggle' => true,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Left', 'zikzag-core'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'zikzag-core'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Right', 'zikzag-core'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .clients_image' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'alignment_v',
            [
                'label' => esc_html__('Vertical Alignment', 'zikzag-core'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => true,
                'toggle' => true,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Top', 'zikzag-core'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'zikzag-core'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Bottom', 'zikzag-core'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .wgl-clients' => 'align-items: {{VALUE}};',
                    '{{WRAPPER}} .slick-track' => 'align-items: {{VALUE}}; display: flex;',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  CONTENT -> CAROUSEL OPTIONS
        /*-----------------------------------------------------------------------------------*/

        Wgl_Carousel_Settings::options($this);


        /*-----------------------------------------------------------------------------------*/
        /*  STYLES -> ITEMS
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'section_style_items',
            [
                'label' => esc_html__('Items', 'zikzag-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'image_margin',
            [
                'label' => esc_html__('Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .clients_image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_padding',
            [
                'label' => esc_html__('Padding', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .clients_image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => esc_html__('Border Radius', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .clients_image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'tabs_item_styles',
            [ 'separator' => 'before' ]
        );

        $this->start_controls_tab(
            'tab_item_idle',
            [ 'label' => esc_html__('Idle', 'zikzag-core') ]
        );

        $this->add_control(
            'bg_color_idle',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .clients_image' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .clients_image',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_item_hover',
            [ 'label' => esc_html__('Hover', 'zikzag-core') ]
        );

        $this->add_control(
            'bg_color_hover',
            [
                'label' => esc_html__('Background Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .clients_image:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow_hover',
                'selector' => '{{WRAPPER}} .clients_image:hover',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'label' => esc_html__('Border Type', 'zikzag-core'),
                'separator' => 'before',
                'selector' => '{{WRAPPER}} .clients_image',
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
                'selectors' => [
                    '{{WRAPPER}} .slick-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $content = '';
        $carousel_options = [];
        $settings = $this->get_settings_for_display();
        extract($settings);

        if ($use_carousel) {
            $carousel_options = [
                'slide_to_show' => $item_grid,
                'autoplay' => $autoplay,
                'autoplay_speed' => $autoplay_speed,
                'fade_animation' => $fade_animation,
                'slides_to_scroll' => $slides_to_scroll,
                'infinite' => true,
                'use_pagination' => $use_pagination,
                'pag_type' => $pag_type,
                'pag_offset' => $pag_offset,
                'pag_align' => $pag_align,
                'custom_pag_color' => $custom_pag_color,
                'pag_color' => $pag_color,
                // Prev/next
                'use_prev_next' => $use_prev_next,
                'prev_next_position' => $prev_next_position,
                'custom_prev_next_color' => $custom_prev_next_color,
                'prev_next_color' => $prev_next_color,
                'prev_next_color_hover' => $prev_next_color_hover,
                // Responsive
                'custom_resp' => $custom_resp,
                'resp_medium' => $resp_medium,
                'resp_medium_slides' => $resp_medium_slides,
                'resp_tablets' => $resp_tablets,
                'resp_tablets_slides' => $resp_tablets_slides,
                'resp_mobile' => $resp_mobile,
                'resp_mobile_slides' => $resp_mobile_slides,
            ];

            wp_enqueue_script('slick', get_template_directory_uri() . '/js/slick.min.js', [], false, false);
        }

        $this->add_render_attribute(
            'clients',
            [
                'class' => [
                    'wgl-clients',
                    'clearfix',
                    'anim-' . $item_anim,
                    'items-' . $item_grid,
                ],
                'data-carousel' => $use_carousel
            ]
        );

        foreach ( $settings[ 'list' ] as $index => $item ) {

            if (!empty($item['client_link']['url'])) {
                $client_link = $this->get_repeater_setting_key('client_link', 'list' , $index);
                $this->add_render_attribute($client_link, 'class', 'image_link image_wrapper');
                $this->add_link_attributes($client_link, $item['client_link']);
            }

            $client_image = $this->get_repeater_setting_key( 'thumbnail', 'list' , $index );
            $this->add_render_attribute(
                $client_image,
                [
                    'class' => 'main_image',
                    'src' => esc_url($item[ 'thumbnail' ][ 'url' ]),
                    'alt' => Control_Media::get_image_alt( $item[ 'thumbnail' ] ),
                ]
            );

            $client_hover_image = $this->get_repeater_setting_key( 'hover_thumbnail', 'list' , $index );
            $this->add_render_attribute(
                $client_hover_image,
                [
                    'class' => 'hover_image',
                    'src' => esc_url($item[ 'hover_thumbnail' ][ 'url' ]),
                    'alt' => Control_Media::get_image_alt( $item[ 'hover_thumbnail' ] ),
                ]
            );

            ob_start();

            echo '<div class="clients_image">';
                if ( !empty($item[ 'client_link' ][ 'url' ]) ) {
                    echo '<a ', $this->get_render_attribute_string( $client_link ), '>';
                } else {
                    echo '<div class="image_wrapper">';
                }

                    if (!empty($item['hover_thumbnail']['url']) && ($item_anim == 'ex_images' || $item_anim == 'ex_images_bg' || $item_anim == 'ex_images_ver')) {
                        echo '<img ', $this->get_render_attribute_string( $client_hover_image ), ' />';
                    }
                    echo '<img ', $this->get_render_attribute_string( $client_image ), ' />';

                if ( !empty($item[ 'client_link' ][ 'url' ]) ) {
                    echo '</a>';
                } else {
                    echo '</div>';
                }
            echo '</div>';

            $content .= ob_get_clean();
        }

        // Render
        echo '<div ', $this->get_render_attribute_string( 'clients' ), '>';
            if ($use_carousel) {
                echo Wgl_Carousel_Settings::init($carousel_options, $content, false);
            } else {
                echo $content;
            }
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