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

class Wgl_Demo_Item extends Widget_Base {

    public function get_name() {
        return 'wgl-demo-item';
    }

    public function get_title() {
        return esc_html__('WGL Demo Item', 'zikzag-core');
    }

    public function get_icon() {
        return 'wgl-demo-item';
    }

    public function get_categories() {
        return [ 'wgl-extensions' ];
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

        $this->start_controls_section('wgl_demo_item_section',
            array(
                'label'         => esc_html__('Demo Item Settings', 'zikzag-core'),
            )
        );

        $this->add_control('demo_title',
            array(
                'label'         => esc_html__('Title', 'zikzag-core'),
                'type'          => Controls_Manager::TEXT,
                'label_block'   => true,
                'default'       => esc_html__('This is the heading​', 'zikzag-core'),
            )
        );

        $this->add_control('title_tag',
            array(
                'label'         => esc_html__('Title Tag', 'zikzag-core'),
                'type'          => Controls_Manager::SELECT,
                'default'       => 'h3',
                'description'   => esc_html__('Choose your tag for demo item title', 'zikzag-core'),
                'options'       => [
                    'h1'      => 'H1',
                    'h2'      => 'H2',
                    'h3'      => 'H3',
                    'h4'      => 'H4',
                    'h5'      => 'H5',
                    'h6'      => 'H6',
                    'div'     => 'DIV',
                    'span'    => 'SPAN',
                ],
            )
        );

        $this->add_control(
            'thumbnail',
            array(
                'label'       => esc_html__('Image', 'zikzag-core'),
                'type'        => Controls_Manager::MEDIA,
                'label_block' => true,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            )
        );

        $this->add_control('coming_soon',
            array(
                'label'        => esc_html__('Coming Soon', 'zikzag-core'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('On', 'zikzag-core'),
                'label_off'    => esc_html__('Off', 'zikzag-core'),
                'return_value' => 'yes',
            )
        );

        $this->add_control('add_button',
            array(
                'label'        => esc_html__('Add Button', 'zikzag-core'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('On', 'zikzag-core'),
                'label_off'    => esc_html__('Off', 'zikzag-core'),
                'return_value' => 'yes',
                'condition'     => [
                    'coming_soon'   => '',
                ]
            )
        );

        $this->add_control('button_title',
            array(
                'label'         => esc_html__('Button Title', 'zikzag-core'),
                'type'          => Controls_Manager::TEXT,
                'label_block'   => true,
                'default'       => esc_html__('View Demo', 'zikzag-core'),
                'condition'     => [
                    'add_button'   => 'yes',
                    'coming_soon'   => '',
                ]
            )
        );

        $this->add_control('demo_link',
            array(
                'label'             => esc_html__('Demo Link', 'zikzag-core'),
                'type'              => Controls_Manager::URL,
                'label_block' => true,
                'condition'     => [
                    'add_button'   => 'yes',
                    'coming_soon'   => '',
                ]
            )
        );

        $this->end_controls_section();

        /*-----------------------------------------------------------------------------------*/
        /*  Carousel styles
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__('Demo Item Styles', 'zikzag-core'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
        );

        $this->add_control(
			'image_border_radius',
			[
				'label' => esc_html__('Image Border Radius', 'zikzag-core'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'default'       => [
                    'top'   => 10,
                    'right' => 10,
                    'bottom'=> 10,
                    'left'  => 10,
                    'unit'  => 'px',
                ],
				'selectors' => [
					'{{WRAPPER}} .demo-item_image, {{WRAPPER}} .demo-item_image-link:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_box_shadow',
				'selector' => '{{WRAPPER}} .demo-item_image',
			]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'custom_fonts_title',
                'selector' => '{{WRAPPER}} .demo-item_title',
            )
        );

        $this->add_responsive_control(
            'title_margin',
            array(
                'label' => esc_html__('Title Margin', 'zikzag-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'default'   => [
                    'top' => 30,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit'  => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .demo-item_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            )
        );

        $this->start_controls_tabs( 'title_color_tab' );

        $this->start_controls_tab(
            'custom_title_color_idle',
            array(
                'label' => esc_html__('Idle' , 'zikzag-core'),
            )
        );

        $this->add_control(
            'title_color',
            array(
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $header_font_color,
                'selectors' => array(
                    '{{WRAPPER}} .demo-item_title' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'custom_title_color_hover',
            array(
                'label' => esc_html__('Hover' , 'zikzag-core'),
            )
        );

        $this->add_control(
            'title_color_hover',
            array(
                'label' => esc_html__('Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => esc_attr($third_color),
                'selectors' => array(
                    '{{WRAPPER}} .demo-item_title:hover' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'coming_color',
            array(
                'label' => esc_html__('Coming Soon Color', 'zikzag-core'),
                'type' => Controls_Manager::COLOR,
                'default' => $third_color,
                'separator' => 'before',
                'selectors' => array(
                    '{{WRAPPER}} .demo-item_label' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('demo', 'class', [
            'wgl-demo-item',
            (bool)$settings['coming_soon'] ? 'coming-soon' : ''
        ]);

        $this->add_render_attribute('demo_button', 'class', [
            'wgl-button',
            'elementor-button',
            'elementor-size-xl',
        ]);
        $this->add_link_attributes('demo_button', $settings['demo_link']);

        $this->add_render_attribute('demo_link', 'class', 'demo-item_image-link');
        if (!empty($settings['demo_link']['url'])) {
            $this->add_link_attributes('demo_link', $settings['demo_link']);
        }

        $this->add_render_attribute('title_link', 'class', 'demo-item_title-link');
        if (!empty($settings['demo_link']['url'])) {
            $this->add_link_attributes('title_link', $settings['demo_link']);
        }

        $this->add_render_attribute('demo_img', [
            'class' => 'demo-item_image',
            'src' => esc_url($settings['thumbnail']['url']),
            'alt' => Control_Media::get_image_alt( $settings['thumbnail'] ),
        ] );

        ?>
        <div <?php echo $this->get_render_attribute_string( 'demo' ); ?>>
            <div class="demo-item_title-wrap"><?php
                if (!empty($settings[ 'thumbnail' ])) {?>
                    <div class="demo-item_image-wrap">
                        <a <?php echo $this->get_render_attribute_string( 'demo_link' ); ?>><img <?php echo $this->get_render_attribute_string( 'demo_img' ); ?> /></a><?php
                        if (!empty($settings[ 'button_title' ])) {?>
                            <a <?php echo $this->get_render_attribute_string( 'demo_button' ); ?>><?php echo esc_html($settings[ 'button_title' ]);?></a><?php
                        }
                        if ((bool)$settings[ 'coming_soon' ]) {?>
                            <h5 class="demo-item_label"><?php echo esc_html__('Coming Soon', 'zikzag-core' ); ?></h5><?php
                        }?>
                    </div><?php
                }
                if (!empty($settings[ 'demo_title' ])) {?>
                    <a <?php echo $this->get_render_attribute_string( 'title_link' ); ?>><<?php echo $settings[ 'title_tag' ]; ?> class="demo-item_title"><?php echo $settings[ 'demo_title' ]; ?></<?php echo $settings[ 'title_tag' ]; ?>></a><?php
                }?>
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