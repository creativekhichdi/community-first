<?php

namespace WglAddons\Includes;

defined( 'ABSPATH' ) || exit;

use Elementor\Plugin;
use Elementor\Frontend;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use WglAddons\Includes\Wgl_Elementor_Helper;


/**
* Wgl Elementor Carousel Settings
*
*
* @class        Wgl_Carousel_Settings
* @version      1.0
* @category     Class
* @author       WebGeniusLab
*/

if (! class_exists('Wgl_Carousel_Settings')) {
    class Wgl_Carousel_Settings
    {

        private static $instance = null;

        public static function get_instance()
        {
            if ( null == self::$instance ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public static function options($self, $array = [])
        {

            $primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
            $secondary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
            $main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);
            $h_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);

            $desctop_width = get_option('elementor_container_width');
            $desctop_width = $desctop_width ?: '1140';

            $tablet_width = get_option('elementor_viewport_lg');
            $tablet_width = $tablet_width ?: '1025';

            $mobile_width = get_option('elementor_viewport_md');
            $mobile_width = $mobile_width ?: '768';


            $self->start_controls_section(
                'wgl_carousel_section',
                [ 'label' => esc_html__('Carousel Options', 'zikzag-core') ]
            );

            $self->add_control(
                'use_carousel',
                [
                    'label' => esc_html__('Use Carousel','zikzag-core' ),
                    'type' => Controls_Manager::SWITCHER,
                ]
            );

            $self->add_control(
                'autoplay',
                [
                    'label' => esc_html__('Autoplay','zikzag-core' ),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [ 'use_carousel' => 'yes' ],
                    'label_on' => esc_html__( 'On', 'zikzag-core' ),
                    'label_off' => esc_html__( 'Off', 'zikzag-core' ),
                ]
            );

            $self->add_control(
                'autoplay_speed',
                [
                    'label' => esc_html__('Autoplay Speed', 'zikzag-core'),
                    'type' => Controls_Manager::NUMBER,
                    'condition' => [ 'autoplay' => 'yes' ],
                    'min' => 1,
                    'step' => 1,
                    'default' => '3000',
                ]
            );

            $self->add_control(
                'fade_animation',
                [
                    'label' => esc_html__('Fade Animation','zikzag-core' ),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [
                        'use_carousel' => 'yes',
                        'posts_per_line' => '1',
                    ],
                    'label_on' => esc_html__( 'On', 'zikzag-core' ),
                    'label_off' => esc_html__( 'Off', 'zikzag-core' ),
                ]
            );

            $self->add_control(
                'slides_to_scroll',
                [
                    'label' => esc_html__('Slide per single item at a time','zikzag-core' ),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [ 'use_carousel' => 'yes' ],
                    'label_on' => esc_html__( 'On', 'zikzag-core' ),
                    'label_off' => esc_html__( 'Off', 'zikzag-core' ),
                ]
            );

            $self->add_control(
                'infinite',
                [
                    'label' => esc_html__('Infinite Loop Sliding','zikzag-core' ),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [ 'use_carousel' => 'yes' ],
                    'label_on' => esc_html__( 'On', 'zikzag-core' ),
                    'label_off' => esc_html__( 'Off', 'zikzag-core' ),
                ]
            );

            $self->add_control(
                'center_mode',
                [
                    'label' => esc_html__('Center Mode','zikzag-core' ),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [ 'use_carousel' => 'yes' ],
                    'label_on' => esc_html__( 'On', 'zikzag-core' ),
                    'label_off' => esc_html__( 'Off', 'zikzag-core' ),
                ]
            );

            $self->add_control(
                'use_pagination',
                [
                    'label' => esc_html__('Add Pagination control','zikzag-core' ),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [ 'use_carousel' => 'yes' ],
                    'label_on' => esc_html__( 'On', 'zikzag-core' ),
                    'label_off' => esc_html__( 'Off', 'zikzag-core' ),
                ]
            );

            $self->add_control(
                'pag_type',
                [
                    'label' => esc_html__( 'Pagination Type', 'zikzag-core' ),
                    'type' => 'wgl-radio-image',
                    'condition' => [
                        'use_pagination' => 'yes',
                        'use_carousel' => 'yes',
                    ],
                    'options' => [
                        'circle' => [
                            'title'=> esc_html__( 'Circle', 'zikzag-core' ),
                            'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/pag_circle.png',
                        ],
                        'circle_border' => [
                            'title'=> esc_html__( 'Empty Circle', 'zikzag-core' ),
                            'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/pag_circle_border.png',
                        ],
                        'square' => [
                            'title'=> esc_html__( 'Square', 'zikzag-core' ),
                            'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/pag_square.png',
                        ],
                        'square_border' => [
                            'title'=> esc_html__( 'Empty Square', 'zikzag-core' ),
                            'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/pag_square_border.png',
                        ],
                        'line' => [
                            'title'=> esc_html__( 'Line', 'zikzag-core' ),
                            'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/pag_line.png',
                        ],
                        'line_circle' => [
                            'title'=> esc_html__( 'Line - Circle', 'zikzag-core' ),
                            'image' => WGL_ELEMENTOR_ADDONS_URL . 'assets/img/wgl_elementor_addon/icons/pag_line_circle.png',
                        ],
                    ],
                    'default' => 'circle_border',
                ]
            );

            $self->add_control(
                'pag_align',
                [
                    'label' => esc_html__('Pagination Aligning', 'zikzag-core'),
                    'type' => Controls_Manager::SELECT,
                    'condition' => [
                        'use_pagination' => 'yes',
                        'use_carousel' => 'yes',
                    ],
                    'options' => [
                        'left' => esc_html__('Left', 'zikzag-core'),
                        'right' => esc_html__('Right', 'zikzag-core'),
                        'center' => esc_html__('Center', 'zikzag-core'),
                    ],
                    'default' => 'center',
                ]
            );

            $self->add_control(
                'pag_offset',
                [
                    'label' => esc_html__('Pagination Top Offset', 'zikzag-core'),
                    'type' => Controls_Manager::NUMBER,
                    'condition' => [
                        'use_pagination' => 'yes',
                        'use_carousel' => 'yes',
                    ],
                    'min' => -500,
                    'step' => 1,
                    'default' => 70,
                    'selectors' => [
                        '{{WRAPPER}} .wgl-carousel .slick-dots' => 'margin-top: {{VALUE}}px;',
                    ],
                ]
            );

            $self->add_control(
                'custom_pag_color',
                [
                    'label' => esc_html__('Custom Pagination Color','zikzag-core' ),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [
                        'use_pagination' => 'yes',
                        'use_carousel' => 'yes',
                    ],
                    'label_on' => esc_html__( 'On', 'zikzag-core' ),
                    'label_off' => esc_html__( 'Off', 'zikzag-core' ),
                ]
            );

            $self->add_control(
                'pag_color',
                [
                    'label' => esc_html__( 'Color', 'zikzag-core' ),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        'custom_pag_color' => 'yes',
                        'use_carousel' => 'yes',
                    ],
                    'default' => $h_font_color,
                    'selectors' => [
                        '{{WRAPPER}} .slick-dots li button' => 'background: {{VALUE}};',
                        '{{WRAPPER}} .wgl-carousel.pagination_line .slick-dots li button:before' => 'background: {{VALUE}};',
                        '{{WRAPPER}} .wgl-carousel.pagination_square .slick-dots li' => 'background: {{VALUE}};',
                        '{{WRAPPER}} .wgl-carousel.pagination_circle_border .slick-dots li.slick-active button' => 'border-color: {{VALUE}};',
                        '{{WRAPPER}} .wgl-carousel.pagination_circle_border .slick-dots li button:before' => 'background: {{VALUE}};',
                    ],
                ]
            );

            $self->add_control(
                'use_prev_next',
                [
                    'label' => esc_html__('Add Prev/Next buttons','zikzag-core' ),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [ 'use_carousel' => 'yes' ],
                    'label_on' => esc_html__( 'On', 'zikzag-core' ),
                    'label_off' => esc_html__( 'Off', 'zikzag-core' ),
                ]
            );

            $self->add_control(
                'prev_next_position',
                [
                    'label' => esc_html__('Arrows Positioning', 'zikzag-core'),
                    'type' => Controls_Manager::SELECT,
                    'condition' => [
                        'use_prev_next' => 'yes',
                        'use_carousel' => 'yes',
                    ],
                    'options' => [
                        '' => esc_html__('Opposite each other', 'zikzag-core'),
                        'right' => esc_html__('Bottom right corner', 'zikzag-core'),
                    ],
                ]
            );

            $self->add_control(
                'custom_prev_next_color',
                [
                    'label' => esc_html__('Customize Arrows Colors','zikzag-core' ),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [
                        'use_prev_next' => 'yes',
                        'use_carousel' => 'yes',
                    ],
                    'label_on' => esc_html__( 'On', 'zikzag-core' ),
                    'label_off' => esc_html__( 'Off', 'zikzag-core' ),
                ]
            );

            $self->start_controls_tabs( 'arrows_style' );

            $self->start_controls_tab(
                'arrows_button_normal',
                [
                    'label' => esc_html__( 'Normal', 'zikzag-core' ),
                    'condition' => [
                        'custom_prev_next_color' => 'yes',
                        'use_carousel' => 'yes',
                    ],
                ]
            );

            $self->add_control(
                'prev_next_color',
                [
                    'label' => esc_html__( 'Button Color', 'zikzag-core' ),
                    'type' =>  Controls_Manager::COLOR,
                    'condition' => [
                        'custom_prev_next_color' => 'yes',
                        'use_carousel' => 'yes',
                    ],
                    'dynamic' => [ 'active' => true ],
                    'default' => $secondary_color,
                ]
            );

            $self->end_controls_tab();

            $self->start_controls_tab(
                'arrows_button_hover',
                [
                    'label' => esc_html__( 'Hover', 'zikzag-core' ),
                    'condition' => [
                        'custom_prev_next_color' => 'yes',
                        'use_carousel' => 'yes',
                    ],
                ]
            );

            $self->add_control(
                'prev_next_color_hover',
                [
                    'label' => esc_html__( 'Button Color', 'zikzag-core' ),
                    'type' =>  Controls_Manager::COLOR,
                    'condition' => [
                        'custom_prev_next_color' => 'yes',
                        'use_carousel' => 'yes',
                    ],
                    'dynamic' => [ 'active' => true ],
                    'default' => $secondary_color,
                ]
            );

            $self->end_controls_tab();
            $self->end_controls_tabs();

            $self->add_control(
                'custom_resp',
                [
                    'label' => esc_html__('Customize Responsive','zikzag-core' ),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [ 'use_carousel' => 'yes' ],
                    'label_on' => esc_html__( 'On', 'zikzag-core' ),
                    'label_off' => esc_html__( 'Off', 'zikzag-core' ),
                ]
            );

            $self->add_control(
                'heading_desktop',
                [
                    'label' => esc_html__( 'Desktop Settings', 'zikzag-core' ),
                    'type' => Controls_Manager::HEADING,
                    'condition' => [ 'custom_resp' => 'yes' ],
                    'separator' => 'before',
                ]
            );

            $self->add_control(
                'resp_medium',
                [
                    'label' => esc_html__('Desktop Screen Breakpoint', 'zikzag-core'),
                    'type' => Controls_Manager::NUMBER,
                    'condition' => [ 'custom_resp' => 'yes' ],
                    'default' => $desctop_width,
                    'min' => 1,
                ]
            );

            $self->add_control(
                'resp_medium_slides',
                [
                    'label' => esc_html__('Slides to show', 'zikzag-core'),
                    'type' => Controls_Manager::NUMBER,
                    'condition' => [ 'custom_resp' => 'yes' ],
                    'min' => 1,
                ]
            );

            $self->add_control(
                'heading_tablet',
                [
                    'label' => esc_html__( 'Tablet Settings', 'zikzag-core' ),
                    'type' => Controls_Manager::HEADING,
                    'condition' => [ 'custom_resp' => 'yes' ],
                    'separator' => 'before',
                ]
            );

            $self->add_control(
                'resp_tablets',
                [
                    'label' => esc_html__('Tablet Screen Breakpoint', 'zikzag-core'),
                    'type' => Controls_Manager::NUMBER,
                    'condition' => [ 'custom_resp' => 'yes' ],
                    'min' => 1,
                    'step' => 1,
                    'default' => $tablet_width,
                ]
            );

            $self->add_control(
                'resp_tablets_slides',
                [
                    'label' => esc_html__('Slides to show', 'zikzag-core'),
                    'type' => Controls_Manager::NUMBER,
                    'condition' => [ 'custom_resp' => 'yes' ],
                    'min' => 1,
                    'step' => 1,
                ]
            );

            $self->add_control(
                'heading_mobile',
                [
                    'label' => esc_html__( 'Mobile Settings', 'zikzag-core' ),
                    'type' => Controls_Manager::HEADING,
                    'condition' => [ 'custom_resp' => 'yes' ],
                    'separator' => 'before',
                ]
            );

            $self->add_control(
                'resp_mobile',
                [
                    'label' => esc_html__('Mobile Screen Breakpoint', 'zikzag-core'),
                    'type' => Controls_Manager::NUMBER,
                    'condition' => [ 'custom_resp' => 'yes' ],
                    'min' => 1,
                    'step' => 1,
                    'default' => $mobile_width,
                ]
            );

            $self->add_control(
                'resp_mobile_slides',
                [
                    'label' => esc_html__('Slides to show', 'zikzag-core'),
                    'type' => Controls_Manager::NUMBER,
                    'condition' => [ 'custom_resp' => 'yes' ],
                    'min' => 1,
                    'step' => 1,
                ]
            );

            $self->end_controls_section();
        }

        public static function init($atts, $item = [], $templates = false)
        {
            $primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
            $secondary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
            wp_enqueue_script('slick', get_template_directory_uri() . '/js/slick.min.js', [], false, false);

            extract( shortcode_atts( [
                // General
                'slide_to_show' => '1',
                'speed' => '300',
                'autoplay' => true,
                'autoplay_speed' => '3000',
                'slides_to_scroll' => false,
                'infinite' => false,
                'adaptive_height' => false,
                'fade_animation' => false,
                'variable_width' => false,
                'extra_class' => '',
                // Navigation
                'use_pagination' => true,
                'use_navigation' => false,
                'pag_type' => 'circle_border',
                'nav_type' => 'element',
                'pag_offset' => '',
                'pag_align' => 'center',
                'custom_prev_next_offset' => false,
                'prev_next_offset' => '50%',
                'custom_pag_color' => false,
                'pag_color' => $primary_color,
                'center_mode' => false,
                'use_prev_next' => false,
                'prev_next_position' => '',
                'custom_prev_next_color' => false,
                'prev_next_color' => $secondary_color,
                'prev_next_color_hover' => $secondary_color,
                // Responsive
                'custom_resp' => false,
                'resp_medium' => '1025',
                'resp_medium_slides' => '',
                'resp_tablets' => '800',
                'resp_tablets_slides' => '',
                'resp_mobile' => '480',
                'resp_mobile_slides' => '',
            ], $atts));

            if ($custom_pag_color || $custom_prev_next_color || ! empty($pag_offset) || ! empty($prev_next_offset)) {
                $carousel_id = uniqid( "zikzag_carousel_" );
            }
            $carousel_id_attr = isset($carousel_id) ? ' id='.$carousel_id : '';

            // Custom styles
            ob_start();
                if ( $custom_prev_next_color ) {

                    echo "#$carousel_id .slick-arrow:before,
                        #$carousel_id .slick-arrow:after {
                            border-color: ", (! empty($prev_next_color) ? esc_attr($prev_next_color) : 'transparent'),";
                        }
                        #$carousel_id .slick-arrow{
                            background-color: $prev_next_color;
                        }";
                    echo "#$carousel_id .slick-arrow:hover {
                            background-color: $prev_next_color_hover;
                        }
                        #$carousel_id .slick-arrow:hover:after,
                        #$carousel_id .slick-arrow:hover:before {
                            border-color: ", (! empty($prev_next_color_hover) ? esc_attr($prev_next_color_hover) : 'transparent'), ";
                        }";
                }
            $styles = ob_get_clean();
            Wgl_Elementor_Helper::enqueue_css($styles);

            switch ($slide_to_show) {
                case '2':
                    $responsive_medium = 2;
                    $responsive_tablets = 2;
                    $responsive_mobile = 1;
                    break;
                case '3':
                    $responsive_medium = 3;
                    $responsive_tablets = 2;
                    $responsive_mobile = 1;
                    break;
                case '4':
                case '5':
                case '6':
                    $responsive_medium = 4;
                    $responsive_tablets = 2;
                    $responsive_mobile = 1;
                    break;
                default:
                    $responsive_medium = 1;
                    $responsive_tablets = 1;
                    $responsive_mobile = 1;
                    break;
            }

            // If custom responsive
            if ($custom_resp) {
                $responsive_medium = ! empty($resp_medium_slides) ? (int)$resp_medium_slides : $responsive_medium;
                $responsive_tablets = ! empty($resp_tablets_slides) ? (int)$resp_tablets_slides : $responsive_tablets;
                $responsive_mobile = ! empty($resp_mobile_slides) ? (int)$resp_mobile_slides : $responsive_mobile;
            }

            if ( $slides_to_scroll ) {
                $responsive_sltscrl_medium = $responsive_sltscrl_tablets = $responsive_sltscrl_mobile = 1;
            } else {
                $responsive_sltscrl_medium = $responsive_medium;
                $responsive_sltscrl_tablets = $responsive_tablets;
                $responsive_sltscrl_mobile = $responsive_mobile;
            }

            $data_array = [];
            $data_array['slidesToShow'] = (int)$slide_to_show;
            $data_array['slidesToScroll'] = $slides_to_scroll ? 1 : (int)$slide_to_show;
            $data_array['infinite'] = $infinite ? true : false;
            $data_array['variableWidth'] = $variable_width ? true : false;

            $data_array['autoplay'] = $autoplay ? true : false;
            $data_array['autoplaySpeed'] = $autoplay_speed ?: '';
            $data_array['speed'] = $speed ? (int)$speed : '300';
            if ( $center_mode ) {
                $data_array['centerMode'] = $center_mode ? true : false;
                $data_array['centerPadding'] = '0px';
            }

            $data_array['arrows'] = $use_prev_next ? true : false;
            $data_array['dots'] = $use_pagination ? true : false;
            $data_array['adaptiveHeight'] = $adaptive_height ? true : false;

            // Responsive settings
            $data_array['responsive'][0]['breakpoint'] = (int)$resp_medium;
            $data_array['responsive'][0]['settings']['slidesToShow'] = (int)esc_attr($responsive_medium);
            $data_array['responsive'][0]['settings']['slidesToScroll'] = (int)esc_attr($responsive_sltscrl_medium);

            $data_array['responsive'][1]['breakpoint'] = (int)$resp_tablets;
            $data_array['responsive'][1]['settings']['slidesToShow'] = (int)esc_attr($responsive_tablets);
            $data_array['responsive'][1]['settings']['slidesToScroll'] = (int)esc_attr($responsive_sltscrl_tablets);

            $data_array['responsive'][2]['breakpoint'] = (int)$resp_mobile;
            $data_array['responsive'][2]['settings']['slidesToShow'] = (int)esc_attr($responsive_mobile);
            $data_array['responsive'][2]['settings']['slidesToScroll'] = (int)esc_attr($responsive_sltscrl_mobile);

            $prev_next_position_class = $use_prev_next && !empty($prev_next_position) ? ' prev_next_pos_'.$prev_next_position : '';
            $data_attribute = " data-slick='".json_encode($data_array, true)."'";

            // Classes
            $carousel_wrap_classes = $use_pagination ? ' pagination_' . $pag_type : '';
            $carousel_wrap_classes .= $use_navigation ? ' navigation_' . $nav_type : '';
            $carousel_wrap_classes .= $use_pagination ? ' pag_align_' . $pag_align : '';
            $carousel_wrap_classes .= $prev_next_position_class;
            $carousel_wrap_classes .= $extra_class;

            $carousel_classes = $fade_animation ? ' fade_slick' : '';

            // Render
            $output = '<div class="wgl-carousel_wrapper">';
                $output .= '<div'.$carousel_id_attr.' class="wgl-carousel'.esc_attr($carousel_wrap_classes).'">';
                    $output .= '<div class="wgl-carousel_slick'.$carousel_classes.'"'.$data_attribute.'>';

                        if (!empty($templates)) {
	                        if (!empty($item)) {
		                        ob_start();
		                        foreach ($item as $id) if ($id) {
			                        echo '<div class="item">',
			                        (new Frontend)->get_builder_content_for_display($id, true),
			                        '</div>';
		                        }
		                        $output .= ob_get_clean();
	                        }
                        } else {
                            $output .= $item;
                        }

                    $output .= '</div>';
                $output .= '</div>';
            $output .= '</div>';

            return $output;
        }

    }
    new Wgl_Carousel_Settings();
}
