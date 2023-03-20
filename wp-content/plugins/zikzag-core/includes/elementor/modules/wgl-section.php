<?php
namespace WglAddons\Modules;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Frontend;
use Elementor\Repeater;
use Elementor\Plugin;
use Elementor\Shapes;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
* Wgl Elementor Section
*
*
* @class        Wgl_Section
* @version      1.0
* @category Class
* @author       WebGeniusLab
*/

class Wgl_Section{


    public $sections = array();

    public function __construct(){

        add_action( 'elementor/init', [ $this, 'add_hooks' ] );
    }

    public function add_hooks() {

        // Add WGL extension control section to Section panel
        add_action( 'elementor/element/section/section_typo/after_section_end', [ $this, 'extened_animation' ], 10, 2 );

        //add_action( 'elementor/element/section/section_layout/after_section_end', [ $this, 'extends_header_params' ], 10, 2 );
        add_action( 'elementor/element/column/layout/after_section_end', [ $this, 'extends_column_params' ], 10, 2 );

        add_action( 'elementor/frontend/section/before_render', [ $this, 'extened_row_render' ], 10, 1 );

        add_action( 'elementor/frontend/column/before_render', [ $this, 'extened_column_render' ], 10, 1 );

        add_action('elementor/frontend/before_enqueue_scripts', [ $this, 'enqueue_scripts' ]);

        add_action('elementor/element/wp-post/document_settings/after_section_end', [ $this, 'header_metaboxes' ] , 10 , 1 );
    }


    function header_metaboxes( $page ) {

        if (get_post_type() !== 'header') return;

        $page->start_controls_section(
            'header_options',
            array(
                'label'     => esc_html__( 'WGL Header Options', 'zikzag-core' ),
                'tab'       => Controls_Manager::TAB_SETTINGS
            )
        );

        $page->add_control(
            'use_custom_logo',
            array(
                'label'        => esc_html__('Use Custom Mobile Logo?','zikzag-core' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'On', 'zikzag-core' ),
                'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
                'return_value' => 'yes',
            )
        );

        $page->add_control(
            'custom_logo',
            array(
                'label'       => esc_html__( 'Custom Logo', 'zikzag-core' ),
                'type'        => Controls_Manager::MEDIA,
                'label_block' => true,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition'     => [
                    'use_custom_logo'  => 'yes',
                ]
            )
        );

        $page->add_control(
            'enable_logo_height',
            array(
                'label'        => esc_html__('Enable Logo Height?','zikzag-core' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'On', 'zikzag-core' ),
                'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
                'return_value' => 'yes',
                'condition'     => [
                    'use_custom_logo'  => 'yes',
                ]
            )
        );

        $page->add_control('logo_height',
            array(
                'label'             => esc_html__('Logo Height', 'zikzag-core'),
                'type'              => Controls_Manager::NUMBER,
                'default'           => '',
                'min'               => 1,
                'step'              => 1,
                'condition'         => [
                    'use_custom_logo'     => 'yes',
                    'enable_logo_height'  => 'yes',
                ]
            )
        );

        $page->add_control(
            'hr_mobile_logo',
            array(
                'type' => Controls_Manager::DIVIDER,
            )
        );

        $page->add_control(
            'use_custom_menu_logo',
            array(
                'label'        => esc_html__('Use Custom Mobile Menu Logo?','zikzag-core' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'On', 'zikzag-core' ),
                'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
                'return_value' => 'yes',
            )
        );

        $page->add_control(
            'custom_menu_logo',
            array(
                'label'       => esc_html__( 'Custom Logo', 'zikzag-core' ),
                'type'        => Controls_Manager::MEDIA,
                'label_block' => true,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition'     => [
                    'use_custom_menu_logo'  => 'yes',
                ]
            )
        );

        $page->add_control(
            'enable_menu_logo_height',
            array(
                'label'        => esc_html__('Enable Logo Height?','zikzag-core' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'On', 'zikzag-core' ),
                'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
                'return_value' => 'yes',
                'condition'     => [
                    'use_custom_menu_logo'  => 'yes',
                ]
            )
        );

        $page->add_control('menu_logo_height',
            array(
                'label'             => esc_html__('Logo Height', 'zikzag-core'),
                'type'              => Controls_Manager::NUMBER,
                'default'           => '',
                'min'               => 1,
                'step'              => 1,
                'condition'         => [
                    'use_custom_menu_logo'     => 'yes',
                    'enable_menu_logo_height'  => 'yes',
                ]
            )
        );

        $page->add_control(
            'hr_mobile_menu_logo',
            array(
                'type' => Controls_Manager::DIVIDER,
            )
        );
        $page->add_control(
            'mobile_breakpoint',
            array(
                'label' => esc_html__( 'Mobile Header resolution breakpoint', 'zikzag-core' ),
                'type' => Controls_Manager::NUMBER,
                'step' => 1,
                'min' => 5,
                'max' => 4000,
                'default' => '1200',
            )
        );

        $page->add_control(
            'header_on_bg',
            array(
                'label'        => esc_html__('Over content?','zikzag-core' ),
                'description' => esc_html__( 'Set Header to display over content.', 'zikzag-core' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'On', 'zikzag-core' ),
                'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
                'return_value' => 'yes',
            )
        );

        $page->end_controls_section();


    }

    public function extened_row_render( \Elementor\Element_Base $element ){

        if( 'section' !== $element->get_name() ) {
            return;
        }

        $settings = $element->get_settings();
        $data     = $element->get_data();

        if(isset($settings['add_background_text']) && !empty($settings['add_background_text'])){

            wp_enqueue_script('jquery-appear', esc_url( get_template_directory_uri() . '/js/jquery.appear.js' ), array(), false, false);
            wp_enqueue_script('anime', esc_url( get_template_directory_uri() . '/js/anime.min.js' ), array(), false, false);
        }

        if(isset($settings['add_background_animation']) && !empty($settings['add_background_animation'])){
            if(!(bool) Plugin::$instance->editor->is_edit_mode()){
                wp_enqueue_script('parallax', esc_url( get_template_directory_uri() . '/js/parallax.min.js' ), array(), false, false);
                wp_enqueue_script('jquery-paroller', esc_url( get_template_directory_uri() . '/js/jquery.paroller.min.js' ), array(), false, false);
                wp_enqueue_style('animate', esc_url( get_template_directory_uri() . '/css/animate.css' ) );
            }
        }

        $this->sections[ $data['id'] ] = $settings;

    }

    public function extened_column_render( \Elementor\Element_Base $element ){

        if( 'column' !== $element->get_name() ) {
            return;
        }

        $settings = $element->get_settings();
        $data     = $element->get_data();

        if(isset($settings['apply_sticky_column']) && !empty($settings['apply_sticky_column'])){

            wp_enqueue_script('theia-sticky-sidebar', get_template_directory_uri() . '/js/theia-sticky-sidebar.min.js', array(), false, false);

        }
    }

    public function enqueue_scripts(){

        if((bool) Plugin::$instance->preview->is_preview_mode()){
            wp_enqueue_style('animate', esc_url( get_template_directory_uri() . '/css/animate.css' ));

            wp_enqueue_script('parallax', esc_url( get_template_directory_uri() . '/js/parallax.min.js' ), array(), false, false);
            wp_enqueue_script('jquery-paroller', esc_url( get_template_directory_uri() . '/js/jquery.paroller.min.js' ), array(), false, false);

            wp_enqueue_script('theia-sticky-sidebar', get_template_directory_uri() . '/js/theia-sticky-sidebar.min.js', array(), false, false);
        }

        //Add options in the section
        wp_enqueue_script( 'wgl-parallax', esc_url( WGL_ELEMENTOR_ADDONS_URL . 'assets/js/wgl_elementor_sections.js' ), array('jquery'), false, true );

        //Add options in the column
        wp_enqueue_script( 'wgl-column', esc_url( WGL_ELEMENTOR_ADDONS_URL . 'assets/js/wgl_elementor_column.js' ), array('jquery'), false, true );

        wp_localize_script(
            'wgl-parallax',
            'wgl_parallax_settings',
            array(
                $this->sections,
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'svgURL'  => esc_url( WGL_ELEMENTOR_ADDONS_URL . 'assets/shapes/' ),
            )
        );
    }

    public function extened_animation( $widget, $args ){
        $widget->start_controls_section(
            'extened_animation',
            array(
                'label'     => esc_html__( 'WGL Background Text', 'zikzag-core' ),
                'tab'       => Controls_Manager::TAB_STYLE
            )
        );

        $widget->add_control(
            'add_background_text',
            array(
                'label'        => esc_html__('Add Background Text?','zikzag-core' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'On', 'zikzag-core' ),
                'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
                'return_value' => 'add-background-text',
                'prefix_class' => 'wgl-',
            )
        );

        $widget->add_control('background_text',
            array(
                'label'             => esc_html__('Background Text', 'zikzag-core'),
                'type'              => Controls_Manager::TEXTAREA,
                'label_block' => true,
                'default' =>  esc_html__('Text', 'zikzag-core'),
                'selectors' => array(
                    '{{WRAPPER}}.wgl-add-background-text:before' => 'content:"{{VALUE}}"',
                    '{{WRAPPER}} .wgl-background-text' => 'content:"{{VALUE}}"',
                ),
                'condition' => [
                    'add_background_text' => 'add-background-text',
                ],
            )
        );

        $widget->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'background_text_typo',
                'selector' => '{{WRAPPER}}.wgl-add-background-text:before, {{WRAPPER}} .wgl-background-text',
                'condition' => [
                    'add_background_text' => 'add-background-text',
                ],
            )
        );

        $widget->add_responsive_control(
            'background_text_indent',
            [
                'label' => esc_html__( 'Text Indent', 'zikzag-core' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'vw' ],
                'selectors' => [
                    '{{WRAPPER}}.wgl-add-background-text:before' => 'margin-left: calc({{SIZE}}{{UNIT}} / 2);',
                    '{{WRAPPER}} .wgl-background-text .letter:last-child' => 'margin-right: -{{SIZE}}{{UNIT}};',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 250,
                    ],
                    'vw' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'default' => [
                    'unit' => 'vw',
                    'size' => 8.9,
                ],
                'condition' => [
                    'add_background_text' => 'add-background-text',
                ],
            ]
        );

        $widget->add_control(
            'background_text_color',
            array(
                'label' => esc_html__( 'Color', 'zikzag-core' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#f1f1f1',
                'selectors' => array(
                    '{{WRAPPER}}.wgl-add-background-text:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wgl-background-text' => 'color: {{VALUE}};',
                ),
                'condition' => [
                    'add_background_text' => 'add-background-text',
                ],
            )
        );

        $widget->add_responsive_control(
            'background_text_spacing',
            [
                'label' => esc_html__( 'Top Spacing', 'zikzag-core' ),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}}.wgl-add-background-text:before' => 'margin-top: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .wgl-background-text' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 400,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'condition' => [
                    'add_background_text' => 'add-background-text',
                ],
            ]
        );

        $widget->add_control(
            'apply_animation_background_text',
            array(
                'label'        => esc_html__('Apply Animation?','zikzag-core' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'On', 'zikzag-core' ),
                'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
                'return_value' => 'animation-background-text',
                'default'      => 'animation-background-text',
                'prefix_class' => 'wgl-',
                'condition' => [
                    'add_background_text' => 'add-background-text',
                ],
            )
        );

        $widget->end_controls_section();

        $widget->start_controls_section(
            'extened_parallax',
            array(
                'label'     => esc_html__( 'WGL Parallax', 'zikzag-core' ),
                'tab'       => Controls_Manager::TAB_STYLE
            )
        );

        $widget->add_control(
            'add_background_animation',
            array(
                'label'        => esc_html__('Add Extended Background Animation?','zikzag-core' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'On', 'zikzag-core' ),
                'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
                'return_value' => 'yes',
            )
        );

        $repeater = new Repeater();

        $repeater->add_control('image_effect',
            array(
                'label'             => esc_html__('Parallax Effect', 'zikzag-core'),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                    'scroll'        => esc_html__('Scroll', 'zikzag-core'),
                    'mouse'         => esc_html__('Mouse', 'zikzag-core'),
                    'css_animation' => esc_html__('CSS Animation', 'zikzag-core'),
                ],
                'default' => 'scroll',
            )
        );

        $repeater->add_responsive_control(
            'animation_name',
            array(
                'label' => esc_html__( 'Animation', 'zikzag-core' ),
                'type' => Controls_Manager::SELECT2,
                'default' => 'fadeIn',
                'options' => [
                    'bounce' => 'bounce',
                    'flash' => 'flash',
                    'pulse' => 'pulse',
                    'rubberBand' => 'rubberBand',
                    'shake' => 'shake',
                    'swing' => 'swing',
                    'tada' => 'tada',
                    'wobble' => 'wobble',
                    'jello' => 'jello',
                    'bounceIn' => 'bounceIn',
                    'bounceInDown' => 'bounceInDown',
                    'bounceInUp' => 'bounceInUp',
                    'bounceOut' => 'bounceOut',
                    'bounceOutDown' => 'bounceOutDown',
                    'bounceOutLeft' => 'bounceOutLeft',
                    'bounceOutRight' => 'bounceOutRight',
                    'bounceOutUp' => 'bounceOutUp',
                    'fadeIn' => 'fadeIn',
                    'fadeInDown' => 'fadeInDown',
                    'fadeInDownBig' => 'fadeInDownBig',
                    'fadeInLeft' => 'fadeInLeft',
                    'fadeInLeftBig' => 'fadeInLeftBig',
                    'fadeInRightBig' => 'fadeInRightBig',
                    'fadeInUp' => 'fadeInUp',
                    'fadeInUpBig' => 'fadeInUpBig',
                    'fadeOut' => 'fadeOut',
                    'fadeOutDown' => 'fadeOutDown',
                    'fadeOutDownBig' => 'fadeOutDownBig',
                    'fadeOutLeft' => 'fadeOutLeft',
                    'fadeOutLeftBig' => 'fadeOutLeftBig',
                    'fadeOutRightBig' => 'fadeOutRightBig',
                    'fadeOutUp' => 'fadeOutUp',
                    'fadeOutUpBig' => 'fadeOutUpBig',
                    'flip' => 'flip',
                    'flipInX' => 'flipInX',
                    'flipInY' => 'flipInY',
                    'flipOutX' => 'flipOutX',
                    'flipOutY' => 'flipOutY',
                    'fadeOutDown' => 'fadeOutDown',
                    'lightSpeedIn' => 'lightSpeedIn',
                    'lightSpeedOut' => 'lightSpeedOut',
                    'rotateIn' => 'rotateIn',
                    'rotateInDownLeft' => 'rotateInDownLeft',
                    'rotateInDownRight' => 'rotateInDownRight',
                    'rotateInUpLeft' => 'rotateInUpLeft',
                    'rotateInUpRight' => 'rotateInUpRight',
                    'rotateOut' => 'rotateOut',
                    'rotateOutDownLeft' => 'rotateOutDownLeft',
                    'rotateOutDownRight' => 'rotateOutDownRight',
                    'rotateOutUpLeft' => 'rotateOutUpLeft',
                    'rotateOutUpRight' => 'rotateOutUpRight',
                    'slideInUp' => 'slideInUp',
                    'slideInDown' => 'slideInDown',
                    'slideInLeft' => 'slideInLeft',
                    'slideInRight' => 'slideInRight',
                    'slideOutUp' => 'slideOutUp',
                    'slideOutDown' => 'slideOutDown',
                    'slideOutLeft' => 'slideOutLeft',
                    'slideOutRight' => 'slideOutRight',
                    'zoomIn' => 'zoomIn',
                    'zoomInDown' => 'zoomInDown',
                    'zoomInLeft' => 'zoomInLeft',
                    'zoomInRight' => 'zoomInRight',
                    'zoomInUp' => 'zoomInUp',
                    'zoomOut' => 'zoomOut',
                    'zoomOutDown' => 'zoomOutDown',
                    'zoomOutLeft' => 'zoomOutLeft',
                    'zoomOutUp' => 'zoomOutUp',
                    'hinge' => 'hinge',
                    'rollIn' => 'rollIn',
                    'rollOut' => 'rollOut'
                ],
                'condition' => [
                    'image_effect' => 'css_animation',
                ],
            )
        );
        $repeater->add_control(
            'animation_name_iteration_count',
            [
                'label' => esc_html__( 'Animation Iteration Count', 'zikzag-core' ),
                'type' => Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    'infinite' => esc_html__( 'Infinite', 'zikzag-core' ),
                    '1' => esc_html__( '1', 'zikzag-core' ),
                ],
                'condition' => [
                    'image_effect' => 'css_animation',
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'animation-iteration-count:{{UNIT}}'
                ],
            ]
        );
        $repeater->add_control(
            'animation_name_speed',
            array(
                'label' => esc_html__( 'Animation speed', 'zikzag-core' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'step' => 100,
                'default' => '1',
                'condition' => [
                    'image_effect' => 'css_animation',
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'animation-duration:{{UNIT}}s'
                ],
            )
        );
        $repeater->add_control(
            'animation_name_direction',
            [
                'label' => esc_html__( 'Animation Direction', 'zikzag-core' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'normal',
                'options' => [
                    'normal' => esc_html__( 'Normal', 'zikzag-core' ),
                    'reverse' => esc_html__( 'Reverse', 'zikzag-core' ),
                    'alternate' => esc_html__( 'Alternate', 'zikzag-core' ),
                ],
                'condition' => [
                    'image_effect' => 'css_animation',
                ],
                'selectors' => [
                    "{{WRAPPER}} {{CURRENT_ITEM}}" => 'animation-direction:{{UNIT}}'
                ],
            ]
        );
        $repeater->add_control(
            'image_bg',
            array(
                'label' => esc_html__( 'Parallax Image', 'zikzag-core' ),
                'type' => Controls_Manager::MEDIA,
                'label_block' => true,
                'default' => [
                    'url' => '',
                ],
            )
        );


        $repeater->add_control('parallax_dir',
            array(
                'label'             => esc_html__('Parallax Direction', 'zikzag-core'),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                    'vertical'    => esc_html__('Vertical', 'zikzag-core'),
                    'horizontal'     => esc_html__('Horizontal', 'zikzag-core'),
                ],
                'condition' => [
                    'image_effect' => 'scroll',
                ],
                'default' => 'vertical',
            )
        );

        $repeater->add_control(
            'parallax_factor',
            array(
                'label' => esc_html__( 'Parallax Factor', 'zikzag-core' ),
                'type' => Controls_Manager::NUMBER,
                'min' => -3,
                'max' => 3,
                'step' => 0.01,
                'default' => 0.03,
                'description' => esc_html__( 'Set elements offset and speed. It can be positive (0.3) or negative (-0.3). Less means slower.', 'zikzag-core' ),
            )
        );

        $repeater->add_responsive_control(
            'position_top',
            [
                'label' => esc_html__( 'Top Offset', 'zikzag-core' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px' ],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => -200,
                        'max' => 1000,
                        'step' => 5,
                    ],
                ],
                'default'   => [
                    'unit' => '%',
                    'size' => 0,
                ],
                'description' => esc_html__( 'Set figure vertical offset from top border.', 'zikzag-core' ),
                'selectors' => [
                    "{{WRAPPER}} {{CURRENT_ITEM}}" => 'top: {{SIZE}}{{UNIT}}',

                ],
            ]
        );

        $repeater->add_responsive_control(
            'position_left',
            [
                'label' => esc_html__( 'Left Offset', 'zikzag-core' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px'  ],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => -200,
                        'max' => 1000,
                        'step' => 5,
                    ],
                ],
                'default'   => [
                    'unit' => '%',
                    'size' => 0,
                ],
                'description' => esc_html__( 'Set figure horizontal offset from left border.', 'zikzag-core' ),
                'selectors' => [
                    "{{WRAPPER}} {{CURRENT_ITEM}}" => 'left: {{SIZE}}{{UNIT}}',

                ],
            ]
        );

        $repeater->add_control(
            'image_index',
            array(
                'label' => esc_html__( 'Image z-index', 'zikzag-core' ),
                'type' => Controls_Manager::NUMBER,
                'step' => 1,
                'default' => -1,
                'selectors' => [
                    "{{WRAPPER}} {{CURRENT_ITEM}}" => 'z-index: {{UNIT}}',
                ],
            )
        );

        $repeater->add_control(
            'hide_on_mobile',
            array(
                'label'        => esc_html__('Hide On Mobile?','zikzag-core' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'On', 'zikzag-core' ),
                'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
                'return_value' => 'yes',
            )
        );
        $repeater->add_control(
            'hide_mobile_resolution',
            array(
                'label' => esc_html__( 'Screen Resolution', 'zikzag-core' ),
                'type' => Controls_Manager::NUMBER,
                'step' => 1,
                'default' => 768,
                'condition' => [
                    'hide_on_mobile' => 'yes',
                ],
            )
        );

        $widget->add_control(
            'items_parallax',
            array(
                'label' => esc_html__( 'Layers', 'zikzag-core' ),
                'type' => Controls_Manager::REPEATER,
                'condition' => [
                    'add_background_animation' => 'yes',
                ],
                'fields' => $repeater->get_controls(),
            )
        );

        $widget->end_controls_section();

        $widget->start_controls_section(
            'extened_shape',
            array(
                'label'     => esc_html__( 'WGL Shape Divider', 'zikzag-core' ),
                'tab'       => Controls_Manager::TAB_STYLE
            )
        );

        $widget->start_controls_tabs( 'tabs_wgl_shape_dividers' );

        $shapes_options = [
            '' => esc_html__( 'None', 'zikzag-core' ),
            'torn_line' => esc_html__( 'Torn Line', 'zikzag-core' ),
        ];

        foreach ( [
            'top' => esc_html__( 'Top', 'zikzag-core' ),
            'bottom' => esc_html__( 'Bottom', 'zikzag-core' ),
        ] as $side => $side_label ) {
            $base_control_key = "wgl_shape_divider_$side";

            $widget->start_controls_tab(
                "tab_$base_control_key",
                [
                    'label' => $side_label,
                ]
            );

            $widget->add_control(
                $base_control_key,
                [
                    'label' => esc_html__( 'Type', 'zikzag-core' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => $shapes_options,
                ]
            );


            $widget->add_control(
                $base_control_key . '_color',
                [
                    'label' => esc_html__( 'Color', 'zikzag-core' ),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        "wgl_shape_divider_$side!" => '',
                    ],
                    'selectors' => [
                        "{{WRAPPER}} > .wgl-elementor-shape-$side path" => 'fill: {{UNIT}};',
                    ],
                ]
            );

            $widget->add_responsive_control(
                $base_control_key . '_height',
                [
                    'label' => esc_html__( 'Height', 'zikzag-core' ),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'max' => 500,
                        ],
                    ],
                    'condition' => [
                        "wgl_shape_divider_$side!" => '',
                    ],
                    'selectors' => [
                        "{{WRAPPER}} > .wgl-elementor-shape-$side svg" => 'height: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $widget->add_control(
                $base_control_key . '_flip',
                [
                    'label' => __( 'Flip', 'zikzag-core' ),
                    'type' => Controls_Manager::SWITCHER,
                    'selectors' => [
                        "{{WRAPPER}} > .wgl-elementor-shape-$side svg" => 'transform: translateX(-50%) rotateY(180deg)',
                    ],
                    'condition' => [
                        "wgl_shape_divider_$side!" => '',
                    ],
                ]
            );

            $widget->add_control(
                $base_control_key . '_invert',
                [
                    'label' => __( 'Invert', 'zikzag-core' ),
                    'type' => Controls_Manager::SWITCHER,
                    'selectors' => [
                        "{{WRAPPER}} > .wgl-elementor-shape-$side" => 'transform: rotate(180deg)',
                    ],
                    'condition' => [
                        "wgl_shape_divider_$side!" => '',
                    ],
                ]
            );

            $widget->add_control(
                $base_control_key . '_above_content',
                array(
                    'label' => esc_html__( 'Z-index', 'zikzag-core' ),
                    'type' => Controls_Manager::NUMBER,
                    'step' => 1,
                    'default' => 0,
                    'selectors' => [
                        "{{WRAPPER}} > .wgl-elementor-shape-$side" => 'z-index: {{UNIT}}',
                    ],
                    'condition' => [
                        "wgl_shape_divider_$side!" => '',
                    ],
                )
            );

            $widget->end_controls_tab();
        }

        $widget->end_controls_tabs();

        $widget->end_controls_section();
    }

    public function extends_header_params($widget, $args) {

        $widget->start_controls_section(
            'extened_header',
            array(
                'label'     => esc_html__( 'WGL Header Layout', 'zikzag-core' ),
                'tab'       => Controls_Manager::TAB_LAYOUT
            )
        );

        $widget->add_control(
            'apply_sticky_row',
            array(
                'label'        => esc_html__('Apply Sticky?','zikzag-core' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'On', 'zikzag-core' ),
                'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
                'return_value' => 'sticky-on',
                'prefix_class' => 'wgl-',
            )
        );

        $widget->end_controls_section();

    }

    public function extends_column_params($widget, $args) {

        $widget->start_controls_section(
            'extened_header',
            array(
                'label'     => esc_html__( 'WGL Column Options', 'zikzag-core' ),
                'tab'       => Controls_Manager::TAB_LAYOUT
            )
        );

        $widget->add_control(
            'apply_sticky_column',
            array(
                'label'        => esc_html__('Enable Sticky?','zikzag-core' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'On', 'zikzag-core' ),
                'label_off'    => esc_html__( 'Off', 'zikzag-core' ),
                'return_value' => 'sidebar',
                'prefix_class' => 'sticky-',
            )
        );

        $widget->end_controls_section();

    }
}

?>