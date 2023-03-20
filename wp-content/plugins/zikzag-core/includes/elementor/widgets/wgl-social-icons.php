<?php
namespace WglAddons\Widgets;

use WglAddons\Includes\Wgl_Icons;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Icons_Manager;


if ( ! defined( 'ABSPATH' ) ) exit; // If this file is called directly, abort.

class Wgl_Social_Icons extends Widget_Base {

    public function get_name() {
        return 'wgl-social-icons';
    }

    public function get_title() {
        return esc_html__('WGL Social Icons', 'zikzag-core' );
    }

    public function get_icon() {
        return 'wgl-social-icons';
    }

    public function get_categories() {
        return [ 'wgl-extensions' ];
    }

    public function get_script_depends() {
        return ['jquery-appear'];
    }

	public function get_keywords() {
		return [ 'social', 'icon', 'link' ];
	}

    protected function register_controls() {
        $theme_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-custom-color'));
        $second_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
        $header_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);
        $main_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('main-font')['color']);


        /*-----------------------------------------------------------------------------------*/
        /*  Content
        /*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'section_social_icon',
			[
				'label' => esc_html__( 'Social Icons', 'zikzag-core' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'social_icon_fontawesome',
			array(
				'label' => esc_html__( 'Icon', 'zikzag-core' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'social',
				'label_block' => true,
				'default' => [
					'value' => 'fab fa-wordpress',
					'library' => 'fa-brands',
				],
				'recommended' => [
					'fa-brands' => [
						'android',
						'apple',
						'behance',
						'bitbucket',
						'codepen',
						'delicious',
						'deviantart',
						'digg',
						'dribbble',
						'zikzag-core',
						'facebook',
						'flickr',
						'foursquare',
						'free-code-camp',
						'github',
						'gitlab',
						'globe',
						'houzz',
						'instagram',
						'jsfiddle',
						'linkedin',
						'medium',
						'meetup',
						'mixcloud',
						'odnoklassniki',
						'pinterest',
						'product-hunt',
						'reddit',
						'shopping-cart',
						'skype',
						'slideshare',
						'snapchat',
						'soundcloud',
						'spotify',
						'stack-overflow',
						'steam',
						'stumbleupon',
						'telegram',
						'thumb-tack',
						'tripadvisor',
						'tumblr',
						'twitch',
						'twitter',
						'viber',
						'vimeo',
						'vk',
						'weibo',
						'weixin',
						'whatsapp',
						'wordpress',
						'xing',
						'yelp',
						'youtube',
						'500px',
					],
					'fa-solid' => [
						'envelope',
						'link',
						'rss',
					],
				],
			)
		);

		$repeater->add_control(
			'social_icon_title',
			[
				'label' => esc_html__( 'Title', 'zikzag-core' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Title', 'zikzag-core' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'zikzag-core' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'default' => [
					'is_external' => 'true',
				],
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'zikzag-core' ),
			]
		);

		$repeater->add_control(
			'item_icon_color',
			[
				'label' => esc_html__( 'Color', 'zikzag-core' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Inherit', 'zikzag-core' ),
					'custom' => esc_html__( 'Custom', 'zikzag-core' ),
				],
			]
		);

		$repeater->start_controls_tabs( 'item_icon_style_tab', [
		    'condition' => [
		        'item_icon_color' => 'custom',
		    ],
		] );

        $repeater->start_controls_tab(
            'item_icon_style_normal',
            array(
                'label' => esc_html__( 'Normal' , 'zikzag-core' ),
            )
        );

		$repeater->add_control(
			'item_icon_primary_color',
			[
				'label' => esc_html__( 'Icon Idle', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.elementor-social-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.elementor-social-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'item_icon_secondary_color',
			[
				'label' => esc_html__( 'Background Color', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.elementor-social-icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'item_icon_border_color',
			[
				'label' => esc_html__( 'Border Color', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.elementor-social-icon' => 'border-color: {{VALUE}};',
				],
			]
		);

        $repeater->end_controls_tab();

        $repeater->start_controls_tab(
            'item_icon_style_hover',
            array(
                'label' => esc_html__( 'Hover' , 'zikzag-core' ),
            )
        );

		$repeater->add_control(
			'item_icon_primary_color_hover',
			[
				'label' => esc_html__( 'Icon Idle', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.elementor-social-icon:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.elementor-social-icon:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'item_icon_secondary_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.elementor-social-icon:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'item_icon_border_color_hover',
			[
				'label' => esc_html__( 'Border Color', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.elementor-social-icon:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->add_control(
			'social_icon_list',
			[
				'label' => esc_html__( 'Social Icons', 'zikzag-core' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'social_icon_title' => esc_html__( 'Twitter', 'zikzag-core' ),
						'social_icon_fontawesome' => [
							'value' => 'fab fa-twitter',
							'library' => 'fa-brands',
						],
					],
					[
						'social_icon_title' => esc_html__( 'Facebook', 'zikzag-core' ),
						'social_icon_fontawesome' => [
							'value' => 'fab fa-facebook',
							'library' => 'fa-brands',
						],
					],
					[
						'social_icon_title' => esc_html__( 'Instagram', 'zikzag-core' ),
						'social_icon_fontawesome' => [
							'value' => 'fab fa-instagram',
							'library' => 'fa-brands',
						],
					],
				],
				'title_field' => '{{{ social_icon_title }}}',
			]
		);

		$this->add_control(
			'shape',
			[
				'label' => esc_html__( 'Shape', 'zikzag-core' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'rounded',
				'options' => [
					'rounded' => esc_html__( 'Rounded', 'zikzag-core' ),
					'square' => esc_html__( 'Square', 'zikzag-core' ),
					'circle' => esc_html__( 'Circle', 'zikzag-core' ),
				],
				'prefix_class' => 'elementor-shape-',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'zikzag-core' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'zikzag-core' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'zikzag-core' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'zikzag-core' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => esc_html__( 'View', 'zikzag-core' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_social_style',
			[
				'label' => esc_html__( 'Icon', 'zikzag-core' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Color', 'zikzag-core' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Inherit', 'zikzag-core' ),
					'custom' => esc_html__( 'Custom', 'zikzag-core' ),
				],
			]
		);

		$this->start_controls_tabs( 'icon_style_tab', [
		    'condition' => [
		        'icon_color' => 'custom',
		    ],
		] );

        $this->start_controls_tab(
            'icon_style_normal',
            array(
                'label' => esc_html__( 'Normal' , 'zikzag-core' ),
            )
        );

		$this->add_control(
			'icon_primary_color',
			[
				'label' => esc_html__( 'Icon Idle', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-social-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_secondary_color',
			[
				'label' => esc_html__( 'Background Color', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon' => 'background-color: {{VALUE}};',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'icon_style_hover',
            array(
                'label' => esc_html__( 'Hover' , 'zikzag-core' ),
            )
        );

		$this->add_control(
			'icon_primary_color_hover',
			[
				'label' => esc_html__( 'Icon Idle', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-social-icon:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_secondary_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_border_color_hover',
			[
				'label' => esc_html__( 'Border Color', 'zikzag-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon:hover' => 'border-color: {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'zikzag-core' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'section_social_divider',
            array(
                'type' => Controls_Manager::DIVIDER,
            )
        );

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'zikzag-core' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
            'icon_padding',
            [
                'label' => esc_html__( 'Container Size', 'zikzag-core' ),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-social-icon' => 'padding: {{SIZE}}{{UNIT}};',
                ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
            ]
        );

		$icon_spacing = is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};';

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label' => esc_html__( 'Gap Items', 'zikzag-core' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon:not(:last-child)' => $icon_spacing,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_border', // We know this mistake - TODO: 'icon_border' (for hover control condition also)
				'selector' => '{{WRAPPER}} .elementor-social-icon',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'zikzag-core' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
    }

    protected function render() {
		$settings = $this->get_settings_for_display();
		$fallback_defaults = [
			'fa fa-facebook',
			'fa fa-twitter',
			'fa fa-google-plus',
		];

		$class_animation = '';

		if ( ! empty( $settings['hover_animation'] ) ) {
			$class_animation = ' elementor-animation-' . $settings['hover_animation'];
		}

		$migration_allowed = Icons_Manager::is_migration_allowed();

		?>
		<div class="wgl-social-icons elementor-social-icons-wrapper">
			<?php
			foreach ( $settings['social_icon_list'] as $index => $item ) {

				$migrated = isset( $item['__fa4_migrated'][$item[ 'social_icon_fontawesome' ]] );
				$is_new = $migration_allowed;
				$social = '';

				// add old default
				if ( empty( $item['social'] ) && ! $migration_allowed ) {
					$item['social'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-wordpress';
				}

				if ( ! empty( $item['social'] ) ) {
					$social = str_replace( 'fa fa-', '', $item['social'] );
				}

				if ( ( $is_new || $migrated ) && 'svg' !== $item['social_icon_fontawesome']['library'] ) {
					$social = explode( ' ', $item['social_icon_fontawesome']['value'], 2 );
					if ( empty( $social[1] ) ) {
						$social = '';
					} else {
						$social = str_replace( 'fa-', '', $social[1] );
					}
				}
				if ( 'svg' === $item['social_icon_fontawesome']['library'] ) {
					$social = '';
				}

				$link_key = 'link_' . $index;

				$this->add_render_attribute( $link_key, 'class', [
					'elementor-icon',
					'elementor-social-icon',
					'elementor-social-icon-' . $social . $class_animation,
					'elementor-repeater-item-' . $item['_id'],
				] );

				$this->add_link_attributes($link_key, $item['link']);

				if ( $item['social_icon_title'] ) {
					$this->add_render_attribute( $link_key, 'title', $item['social_icon_title'] );
				}

				?>
				<a <?php echo $this->get_render_attribute_string( $link_key ); ?>>
					<span class="elementor-screen-only"><?php echo ucwords( $social ); ?></span>
					<?php
			        if ( $is_new || $migrated ) {
				        ob_start();
				        Icons_Manager::render_icon( $item['social_icon_fontawesome'] );
				        echo ob_get_clean();
			        } else {
				        echo '<i class="icon '.esc_attr($item['social']).'"></i>';
			        }
					?>
				</a>
			<?php } ?>
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