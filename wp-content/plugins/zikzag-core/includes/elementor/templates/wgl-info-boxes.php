<?php

namespace WglAddons\Templates;

defined( 'ABSPATH' ) || exit; // Abort, if called directly.

use Elementor\Plugin;
use Elementor\Frontend;
use WglAddons\Includes\Wgl_Loop_Settings;
use WglAddons\Includes\Wgl_Elementor_Helper;
use WglAddons\Includes\Wgl_Carousel_Settings;
use WglAddons\Includes\Wgl_Icons;
use Elementor\Icons_Manager;


/**
 * WGL Elementor Info Boxes Template
 *
 *
 * @class        WglInfoBoxes
 * @version      1.0
 * @category     Class
 * @author       WebGeniusLab
 */

class WglInfoBoxes
{

    private static $instance = null;
    public static function get_instance()
    {
        if (null == self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function render($self, $atts)
    {
        extract($atts);

        $primary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-primary-color'));
        $secondary_color = esc_attr(\Zikzag_Theme_Helper::get_option('theme-secondary-color'));
        $h_font_color = esc_attr(\Zikzag_Theme_Helper::get_option('header-font')['color']);

        $ib_media = $infobox_content = $ib_button = $item_link_html = '';

        // Wrapper classes
        $wrapper_classes = $layout ? ' wgl-layout-' . $layout : '';

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
            ],
            'ul' => [
                'class' => true,
                'style' => true,
            ],
            'li' => [
                'class' => true,
                'style' => true,
            ],
        ];

        // Title
        $infobox_title = '<div class="wgl-infobox-title_wrapper">';
        $infobox_title .= !empty($ib_subtitle) ? '<div class="wgl-infobox_subtitle">' . wp_kses($ib_subtitle, $allowed_html) . '</div>' : '';
        $infobox_title .= !empty($ib_title) ? '<' . esc_attr($title_tag) . ' class="wgl-infobox_title">' . wp_kses($ib_title, $allowed_html) . '</' . esc_attr($title_tag) . '>' : '';
        $infobox_title .= '</div>';

        // Content
        $infobox_content .= !empty($ib_content) ? '<' . esc_attr($content_tag) . ' class="wgl-infobox_content">' . $ib_content . '</' . esc_attr($content_tag) . '>' : '';

        // Media
        if (!empty($icon_type)) {
            $media = new Wgl_Icons;
            $ib_media .= $media->build($self, $atts, []);
        }

        // Link
	    if (!empty($item_link['url'])) {
		    $self->add_link_attributes( 'item_link', $item_link );
	    }

	    if ($add_item_link) {
		    $link_attributes = $self->get_render_attribute_string('item_link');
		    $item_html_start = 'a ' . implode(' ', [$link_attributes]);
		    $item_html_end = 'a';
		    $button_tag = 'div';
	    }else{
		    $item_html_start = $item_html_end ='div';
		    $button_tag = 'a';
	    }

        // Read more button
	    if ( !empty($add_read_more) ) {

            $self->add_render_attribute(
                'button_class',
                [
                    'class' => [
                        'wgl-infobox_button',
                        'button-read-more',
	                    ( !empty($read_more_icon_align) ? 'icon-position-'.esc_attr($read_more_icon_align) : '' )
                    ]
                ]
            );

            $icon_font = $read_more_icon_fontawesome;

            $migrated = isset($atts['__fa4_migrated']['read_more_icon_fontawesome']);
            $is_new = Icons_Manager::is_migration_allowed();
		    $icon_output = '';

		    if ( $is_new || $migrated ) {
			    ob_start();
			    Icons_Manager::render_icon( $atts['read_more_icon_fontawesome'], [ 'aria-hidden' => 'true' ] );
			    $icon_output .= ob_get_clean();
		    } else {
			    $icon_output .= '<i class="icon '.esc_attr($icon_font).'"></i>';
		    }

		    $ib_button .= '<div class="wgl-infobox-button_wrapper">';
            $ib_button .= '<'.$button_tag.' ' . ( !$add_item_link ? $self->get_render_attribute_string('item_link') : '' ) . ' ' . $self->get_render_attribute_string('button_class') . '>';
		    if($read_more_icon_align !== 'right'){
			    $ib_button .= !empty($icon_font) ? $icon_output : '';
		    }
            $ib_button .= !empty($read_more_text) ? '<span>' . esc_html($read_more_text) . '</span>' : '';
		    if($read_more_icon_align === 'right'){
			    $ib_button .= !empty($icon_font) ? $icon_output : '';
		    }
            $ib_button .= '</'.$button_tag.'>';
            $ib_button .= '</div>';
        }

	    // Render
        echo '<'.$item_html_start.' class="wgl-infobox '.esc_attr($wrapper_classes).'">',
            $ib_media,
            '<div class="wgl-infobox_wrapper">',
                $infobox_title,
                '<div class="content_wrapper">',
                    $infobox_content,
                '</div>',
                $ib_button,
            '</div>',
	    '</' . $item_html_end . '>';
    }
}
