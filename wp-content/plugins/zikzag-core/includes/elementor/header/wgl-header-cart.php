<?php
namespace WglAddons\Widgets;

use WglAddons\Includes\Wgl_Icons;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Plugin;


defined('ABSPATH') || exit; // Abort, If called directly.

class Wgl_Header_Cart extends Widget_Base {

    public function get_name() {
        return 'wgl-header-cart';
    }

    public function get_title() {
        return esc_html__('WooCart', 'zikzag-core' );
    }

    public function get_icon() {
        return 'wgl-header-cart';
    }

    public function get_categories() {
        return [ 'wgl-header-modules' ];
    }

    public function get_script_depends() {
        return [
            'wgl-elementor-extensions-widgets',
        ];
    }

    protected function register_controls() {
        $primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
        $secondary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
        $h_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);
        $main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);

        $this->start_controls_section(
            'section_search_settings',
            [
                'label' => esc_html__( 'Cart Settings', 'zikzag-core' ),
            ]
        );

        $this->add_control(
            'cart_height',
            [
                'label' => esc_html__( 'Cart Icon Height', 'zikzag-core' ),
                'type' => Controls_Manager::NUMBER,
                'separator' => 'before',
                'default' => 50,
                'selectors' => [
                    '{{WRAPPER}} .mini-cart' => 'height: {{VALUE}}px;',
                ],
            ]
        );

        $this->add_control(
            'cart_align',
            [
                'label' => esc_html__( 'Alignment', 'zikzag-core' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'zikzag-core' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'zikzag-core' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'zikzag-core' ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'label_block' => false,
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .wgl-mini-cart_wrapper' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();


        /*-----------------------------------------------------------------------------------*/
        /*  Style Section
        /*-----------------------------------------------------------------------------------*/

        $this->start_controls_section(
            'cart_style_section',
            [
                'label' => esc_html__( 'Cart Style', 'zikzag-core' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'cart_icon_color',
            [
                'label' => esc_html__( 'Icon Idle', 'zikzag-core' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wgl-mini-cart_wrapper .mini-cart a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    public function render()
    {
        if (!class_exists( '\WooCommerce' )) {
            return;
        }
        echo '<div class="wgl-mini-cart_wrapper">';
        echo '<div class="mini-cart woocommerce">'.$this->icon_cart().self::woo_cart().'</div>';
        echo '</div>';
    }

    public function icon_cart() {
        ob_start();
        $link = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : \WooCommerce::instance()->cart->get_cart_url();

        $this->add_render_attribute( 'cart', 'class', ['wgl-cart woo_icon elementor-cart'] );
        $this->add_render_attribute( 'cart', 'role', 'button' );
        $this->add_render_attribute( 'cart', 'title', esc_attr__( 'Click to open Shopping Cart', 'zikzag-core' ) );

        ?>
        <a <?php echo \Zikzag_Theme_Helper::render_html( $this->get_render_attribute_string( 'cart' ) ) ;?> >
            <span class='woo_mini-count flaticon-bag-1'>
                <?php
                if ((!(bool) Plugin::$instance->editor->is_edit_mode())) {
                    echo ((\WooCommerce::instance()->cart->cart_contents_count > 0) ?  '<span>' . esc_html( \WooCommerce::instance()->cart->cart_contents_count ) .'</span>' : '');
                }
                ?>
            </span>
        </a>
        <?php
        return ob_get_clean();
    }

    public static function woo_cart()
    {
        ob_start();
        echo '<div class="wgl-woo_mini_cart">';
        if (!(bool) Plugin::$instance->editor->is_edit_mode() ) {
            woocommerce_mini_cart();
        }
        echo '</div>';
        return ob_get_clean();
    }
}