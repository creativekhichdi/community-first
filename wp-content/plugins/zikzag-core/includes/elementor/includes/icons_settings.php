<?php

namespace WglAddons\Includes;

defined( 'ABSPATH' ) || exit;

use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Icons_Manager;


/**
* Wgl Elementor Icons Settings
*
*
* @class        Wgl_Icons
* @version      1.0
* @category     Class
* @author       WebGeniusLab
*/

if (! class_exists('Wgl_Icons')) {

    class Wgl_Icons
    {
        private static $instance = null;

        public static function get_instance()
        {
            if ( null == self::$instance ) {
                self::$instance = new self();
            }

            return self::$instance;
        }


        public function build($self, $atts, $pref)
        {
            $icon_builder = new Wgl_Icon_Builder();
            return $icon_builder->build( $self, $atts, $pref );
        }


        public static function init($self, $array = [])
        {
            if (! $self) return;

            $label = $array['label'] ?? '';
            $prefix = $array['prefix'] ?? '';

            $default_media_type = $array['default']['media_type'] ?? '';
            $default_icon = $array['default']['icon'] ?? [];
            $extra_media_type = $array['default']['extra_media_type'] ?? false;

            if ($array['section']) {
                $self->start_controls_section(
                    $prefix.'add_icon_image_section',
                    [
                        'label' => sprintf( esc_html__('%s Icon/Image', 'zikzag-core'), $label ),
                    ]
                );
            }

            $media_types_options = [
                '' => [
                    'title' => esc_html__('None', 'zikzag-core'),
                    'icon' => 'fa fa-ban',
                ],
                'font' => [
                    'title' => esc_html__('Icon', 'zikzag-core'),
                    'icon' => 'fa fa-smile-o',
                ],
                'image' => [
                    'title' => esc_html__('Image', 'zikzag-core'),
                    'icon' => 'fa fa-picture-o',
                ],
            ];
            if ($extra_media_type) {
                $media_types_options['morph'] = [
                    'title' => esc_html__('Morph', 'zikzag-core'),
                    'icon' => 'fa fa-superpowers',
                ];
            }

            $self->add_control(
                $prefix.'icon_type',
                [
                    'label' => esc_html__('Media Type', 'zikzag-core'),
                    'type' => Controls_Manager::CHOOSE,
                    'label_block' => false,
                    'options' => $media_types_options,
                    'default' => $default_media_type,
                    'prefix_class' => 'has-',
                ]
            );

            $self->add_control(
                $prefix.'icon_fontawesome',
                [
                    'label' => esc_html__( 'Icon', 'zikzag-core' ),
                    'type' => Controls_Manager::ICONS,
                    'condition' => [ $prefix.'icon_type' => 'font' ],
                    'label_block' => true,
                    'default' => $default_icon,
                ]
            );

            $self->add_control(
                $prefix.'icon_render_class',
                [
                    'label' => esc_html__( 'Icon Class', 'zikzag-core' ),
                    'type' => Controls_Manager::HIDDEN,
                    'condition' => [ $prefix.'icon_type' => 'font' ],
                    'prefix_class' => 'elementor-widget-icon-box ',
                    'default' => 'wgl-icon-box',
                ]
            );

            $self->add_control(
                $prefix.'thumbnail',
                [
                    'label' => esc_html__( 'Image', 'zikzag-core' ),
                    'type' => Controls_Manager::MEDIA,
                    'label_block' => true,
                    'condition' => [ $prefix.'icon_type' => 'image' ],
                    'default' => [ 'url' => Utils::get_placeholder_image_src() ],
                ]
            );

            $self->add_control(
                $prefix.'image_render_class',
                [
                    'label' => esc_html__( 'Image Class', 'zikzag-core' ),
                    'type' => Controls_Manager::HIDDEN,
                    'default' => 'wgl-image-box',
                    'prefix_class' => 'elementor-widget-image-box ',
                    'condition' => [ $prefix.'icon_type' => 'image' ],
                ]
            );

            $self->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'name' => $prefix.'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
                    'default' => 'full',
                    'separator' => 'none',
                    'condition' => [ $prefix.'icon_type' => 'image' ],
                ]
            );

            if (isset($array['output']) && ! empty($array['output'])){
                foreach ($array['output'] as $key => $value) {
                    $self->add_control(
                        $key,
                        $value
                    );
                }
            }
            if ($array['section']) {
                $self->end_controls_section();
            }
        }

    }
    new Wgl_Icons();
}

/**
* Wgl Icon Build
*
*
* @class        Wgl_Icon_Builder
* @version      1.0
* @category     Class
* @author       WebGeniusLab
*/
if (!class_exists('Wgl_Icon_Builder')){
    class Wgl_Icon_Builder
    {
        private static $instance = null;
        public static function get_instance()
        {
            if ( null == self::$instance ) {
                self::$instance = new self();
            }

            return self::$instance;
        }


        public function build($self, $atts, $pref)
        {
            $prefix = $output = '';
            $icon_tag = 'span';

            if (isset($pref) && !empty($pref)) {
                $prefix = $pref;
            }

            $media_type = $atts[$prefix.'icon_type'];
            $icon_fontawesome = $atts[$prefix.'icon_fontawesome'];
            $thumbnail = $atts[$prefix.'thumbnail'];
            $morph_text = $atts['morph_text'] ?? '';

            $self->add_render_attribute( $prefix.'icon', 'class', [ 'wgl-icon' ] );
            if (isset($atts['hover_animation_icon']) && !empty($atts['hover_animation_icon'])) {
                $self->add_render_attribute( $prefix.'icon', 'class', 'elementor-animation-' . $atts['hover_animation_icon'] );
            }

            // Wrapper Class
            $wrapper_class = $atts['wrapper_class'] ?? '';
            if ($media_type === 'image') $wrapper_class .= 'img-wrapper';
            if ($media_type === 'font') $wrapper_class .= 'icon-wrapper';
            if ($media_type === 'morph') $wrapper_class .= 'morph-wrapper';
            $self->add_render_attribute(
                $prefix.'wrapper-icon',
                [
                    'class' => [
                        'media-wrapper',
                        $wrapper_class
                    ]
                ]
            );

            if ( isset($atts['link_t']['url']) && !empty( $atts['link_t']['url'] ) ) {
                $icon_tag = 'a';
                $self->add_link_attributes($prefix.'link_t', $atts['link_t']);
            }

            $icon_attributes = $self->get_render_attribute_string( $prefix.'icon' );
            $link_attributes = $self->get_render_attribute_string( $prefix.'link_t' );


            if (
                $media_type == 'font' && !empty($icon_fontawesome)
                || $media_type == 'image' && !empty($thumbnail)
                || $media_type == 'morph' && $morph_text
            ) {

                $output .= '<div ' . $self->get_render_attribute_string( $prefix.'wrapper-icon' ) . '>';

                    if ($media_type == 'font' && !empty($icon_fontawesome) ) {

                        $icon_font = $icon_fontawesome;

                        $output .= '<';
                            $output .= implode( ' ', [ $icon_tag, $icon_attributes, $link_attributes ] );
                        $output .= '>';

                        // Icon migration
                        $migrated = isset( $atts['__fa4_migrated'][$prefix.'icon_fontawesome'] );
                        $is_new = Icons_Manager::is_migration_allowed();
                        if ( $is_new || $migrated ) {
                            ob_start();
                            Icons_Manager::render_icon( $atts[$prefix.'icon_fontawesome'], [ 'class' => 'icon elementor-icon', 'aria-hidden' => 'true' ] );
                            $output .= ob_get_clean();
                        } else {
                            $output .= '<i class="icon '.esc_attr($icon_font).'"></i>';
                        }

                        $output .= '</'.$icon_tag.'>';
                    }
                    if ($media_type == 'image' && !empty($thumbnail['url'])) {
                        
                        $self->add_render_attribute(
                            'thumbnail',
                            [
                                'src' => $thumbnail['url'],
                                'alt' => Control_Media::get_image_alt( $thumbnail ),
                                'title' => Control_Media::get_image_title( $thumbnail ),
                            ]
                        );

                        if ( isset($atts['hover_animation_image']) ) {
                            $atts['hover_animation'] = $atts['hover_animation_image'];
                        }

                        $output .= '<div class="wgl-image-box_img">';

                        $output .= '<' . $icon_tag . ' ' . $link_attributes . '>';
                            $output .= Group_Control_Image_Size::get_attachment_image_html( $atts, 'thumbnail', $prefix.'thumbnail' );
                        $output .= '</'.$icon_tag.'>';

                        $output .= '</div>';

                    }
                    if ($media_type == 'morph' && $morph_text) {
                        $output .= '<span class="morph_text">';
                        $output .= $morph_text;
                        $output .= '</span>';
                    }

                $output .= '</div>';
            }
            return $output;

        }

    }
}
?>