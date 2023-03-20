<?php

namespace WglAddons\Templates;

defined('ABSPATH') || exit; // Abort, if called directly.

use Elementor\Plugin;
use Elementor\Frontend;
use Elementor\Icons_Manager;
use WglAddons\Includes\Wgl_Loop_Settings;
use WglAddons\Includes\Wgl_Elementor_Helper;
use WglAddons\Includes\Wgl_Carousel_Settings;
use WglAddons\Widgets\Wgl_Button;

/**
 * WGL Elementor Portfolio Template
 *
 *
 * @class        WglPortfolio
 * @version      1.0
 * @category     Class
 * @author       WebGeniusLab
 */

class WglPortfolio
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null == self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function render($atts, $self = false)
    {
        $this->item = $self;

        extract($atts);

        // Build Query
        list($query_args) = Wgl_Loop_Settings::buildQuery($atts);

        $query_args['paged'] = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $query_args['post_type'] = 'portfolio';

        // Add Query Not In Post in the Related Posts(Metaboxes)
        if (!empty($featured_render)) {
            $query_args['post__not_in'] = [get_the_id()];
        }

        $query_results = new \WP_Query($query_args);

        $atts['post_count'] = $this->post_count = $query_results->post_count;
        $atts['found_posts'] = $query_results->found_posts;
        $atts['query_args'] = $query_args;

        // Add unique id
        $atts['item_id'] = $item_id = uniqid("portfolio_module_");

        // Register CSS
        $this->register_css($atts, $item_id);

        // Metaxobes Related Items
        if (!empty($featured_render)) {
            $portfolio_layout = 'related';
        }
        if (!empty($featured_render) && !empty($mb_pf_carousel_r)) {
            $portfolio_layout = 'carousel';
        }

        if (
            !empty($show_filter)
            || $portfolio_layout == 'masonry2'
            || $portfolio_layout == 'masonry3'
            || $portfolio_layout == 'masonry4'
        ) {
            $portfolio_layout = 'masonry';
        }

        // Classes
        $container_classes = $grid_gap == '0px' ? ' no_gap' : '';
        $container_classes .= $add_animation ? ' appear-animation' : '';
        $container_classes .= $add_animation && !empty($appear_animation) ? ' anim-' . $appear_animation : '';

        $out = '<section class="wgl_cpt_section">';
        $out .= '<div class="wgl-portfolio" id="' . esc_attr($item_id) . '">';

        wp_enqueue_script('imagesloaded');
        if ($add_animation) {
            wp_enqueue_script('jquery-appear', get_template_directory_uri() . '/js/jquery.appear.js', [], false, false);
        }
        if ($portfolio_layout == 'masonry') {
            wp_enqueue_script('isotope', WGL_ELEMENTOR_ADDONS_URL . 'assets/js/isotope.pkgd.min.js');
        }

        if ($show_filter) {
            $filter_class = $portfolio_layout != 'carousel' ? 'isotope-filter' : '';
            $filter_class .= ' filter-' . $filter_align;
            $out .= '<div class="portfolio__filter ' . esc_attr($filter_class) . '">';
            $out .= $this->getCategories($query_args, $query_results);
            $out .= '</div>';
        }

        $style_gap = isset($grid_gap) && !empty($grid_gap) ? ' style="margin-right:-' . ((int) $grid_gap / 2) . 'px; margin-left:-' . ((int) $grid_gap / 2) . 'px; margin-bottom:-' . $grid_gap . ';"' : '';

        $out .= '<div class="wgl-portfolio_wrapper">';
        $out .= '<div class="wgl-portfolio_container container-grid row ' . esc_attr($this->row_class($atts, $portfolio_layout)) . esc_attr($container_classes) . '" ' . $style_gap . '>';
        $out .= $this->output_loop_query($query_results, $atts);
        $out .= '</div>';
        $out .= '</div>';

        wp_reset_postdata();

        if ($navigation == 'pagination') {
            global $paged;
            if (empty($paged)) {
                $paged = (get_query_var('page')) ? get_query_var('page') : 1;
            }

            $out .= \Zikzag_Theme_Helper::pagination('10', $query_results, $nav_align);
        }

        if ($navigation == 'load_more' && ($atts['post_count'] < $atts['found_posts'])) {
            $out .= $this->loadMore($atts, $name_load_more);
        }

        if ($navigation == 'infinite' && ($atts['post_count'] < $atts['found_posts'])) {
            $out .= $this->infinite_more($atts);
        }

        $out .= '</div>';
        $out .= '</section>';

        return $out;
    }

    public function output_loop_query($q, $params)
    {
        extract($params);
        $out = '';
        $count = 0;
        $i = 0;

        switch ($portfolio_layout) {
            default:
            case 'masonry4':
                $max_count = 6;
                break;

            case 'masonry2':
            case 'masonry3':
                $max_count = 4;
                break;
        }
        // Metaxobes Related Items
        if (!empty($featured_render)) {
            $portfolio_layout = 'related';
        }
        if (!empty($featured_render) && !empty($mb_pf_carousel_r)) {
            $portfolio_layout = 'carousel';
        }

        $per_page = $q->query['posts_per_page'];

        if ($q->have_posts()) :
            ob_start();
            if ($portfolio_layout == 'masonry2' || $portfolio_layout == 'masonry3' || $portfolio_layout == 'masonry4') {
                echo '<div class="wgl-portfolio-list_item-size" style="width: 25%;"></div>';
            }

            while ($q->have_posts()) : $q->the_post();

                if ($count < $max_count) $count++;
                else  $count = 1;

                $item_class = $this->grid_class($params, $count);

                switch ($portfolio_layout) {
                    case 'single':
                        echo $this->wgl_portfolio_single_item($params, $item_class);
                        break;

                    default:
                        $i++;
                        if ($navigation === 'custom_link' && $link_position === 'below_items' && $i === 1) {
                            $class = $this->grid_class($params, $i, true);
                            echo $this->wgl_portfolio_item($params, $class, $i, $grid_gap, true);
                        }

                        echo $this->wgl_portfolio_item($params, $item_class, $count, $grid_gap);

                        if ($navigation === 'custom_link' && $link_position === 'after_items' && $i === $per_page) {
                            $class = $this->grid_class($params, $i, true);
                            echo $this->wgl_portfolio_item($params, $class, $i, $grid_gap, true);
                        }
                        break;
                }

            endwhile;
            $render = ob_get_clean();

            $out .= $portfolio_layout == 'carousel' ? $this->wgl_portfolio_carousel_item($params, $item_class, $render) : $render;
        endif;

        return $out;
    }

    public function wgl_portfolio_carousel_item($params, $item_class, $return)
    {
        extract($params);
        $wrap_class = $arrows_center_mode ? ' arrows_center_mode' : '';
        $wrap_class .= (bool)$center_info ? ' center_info' : '';

        $carousel_options = [
            'slide_to_show' => $posts_per_row,
            'autoplay' => $autoplay,
            'autoplay_speed' => $autoplay_speed,
            'use_pagination' => $use_pagination,
            'pag_type' => $pag_type,
            'pag_offset' => $pag_offset,
            'custom_pag_color' => $custom_pag_color,
            'pag_color' => $pag_color,
            'use_prev_next' => $use_prev_next,
            'custom_resp' => $custom_resp,
            'resp_medium' => $resp_medium,
            'resp_medium_slides' => $resp_medium_slides,
            'resp_tablets' => $resp_tablets,
            'resp_tablets_slides' => $resp_tablets_slides,
            'resp_mobile' => $resp_mobile,
            'resp_mobile_slides' => $resp_mobile_slides,
            'infinite' => $c_infinite_loop,
            'slides_to_scroll' => $c_slide_per_single,
            'extra_class' => $wrap_class,
            'adaptive_height' => false,
            'center_mode' => $center_mode,
            'variable_width' => $variable_width,
        ];

        // carousel options
        wp_enqueue_script('slick', get_template_directory_uri() . '/js/slick.min.js', [], false, false);

        ob_start();
        echo Wgl_Carousel_Settings::init($carousel_options, $return, false);
        return ob_get_clean();
    }


    private function register_css($params, $item_id)
    {
        extract($params);

        // Start Custom CSS
        $styles = '';
        ob_start();
        // Gap Fix
        if ((int) $grid_gap == '0') {
            echo "#$item_id .wgl-portfolio-item_image img,
                  #$item_id .inside_image .wgl-portfolio-item_image {
                      border-radius: 0px;
                  }";
        }
        $styles .= ob_get_clean();

        // Register css
        if (!empty($styles)) {
            Wgl_Elementor_Helper::enqueue_css($styles);
        }
    }


    private function row_class($params, $pf_layout)
    {
        extract($params);
        $class = '';
        switch ($pf_layout) {
            case 'carousel':
                $class .= 'carousel';
                break;
            case 'related':
                $class .= !empty($mb_pf_carousel_r) ? 'carousel' : 'isotope';
                break;
            case 'masonry':
                $class .= 'isotope';
                break;
            default:
                $class .= 'grid';
                break;
        }
        if ($posts_per_row) {
            $class .= ' portfolio_columns-' . $posts_per_row . '';
        }

        return $class;
    }


    public function grid_class($params, $count, $link = false)
    {
        $class = '';

        switch ($params['portfolio_layout']) {
            case 'masonry2':
            case 'masonry4':
                if ($count == 1 || $count == 6) $class .= 'wgl_col-6';
                else $class .= 'wgl_col-3';
                break;

            case 'masonry3':
                if ($count == 1 || $count == 2) $class .= 'wgl_col-6';
                else $class .= 'wgl_col-3';
                break;
            default:
                switch ($params['posts_per_row']) {
                    default:
                    case 1:
                        $class .= 'wgl_col-12';
                        break;
                    case 2:
                        $class .= 'wgl_col-6';
                        break;
                    case 3:
                        $class .= 'wgl_col-4';
                        break;
                    case 4:
                        $class .= 'wgl_col-3';
                        break;
                    case 5:
                        $class .= 'wgl_col-1-5';
                        break;
                }
        }

        if (!$link) {
            $class .= $this->post_cats_class();
        }

        return $class;
    }


    private function post_cats_links($cat)
    {
        if (!$cat) return;

        $p_cats = wp_get_post_terms(get_the_id(), 'portfolio-category');
        $p_cats_str = $p_cats_links = '';
        if (!empty($p_cats)) {
            $p_cats_links = '<span class="post_cats">';
            for ($i = 0, $count = count($p_cats); $i < $count; $i++) {
                $p_cat_term = $p_cats[$i];
                $p_cat_name = $p_cat_term->name;
                $p_cats_str .= ' ' . $p_cat_name;
                $p_cats_link = get_category_link($p_cat_term->term_id);
                $p_cats_links .= '<a href=' . esc_html($p_cats_link) . ' class="portfolio-category">' . esc_html($p_cat_name) . '</a>';
            }
            $p_cats_links .= '</span>';
        }
        return $p_cats_links;
    }


    private function post_cats_class()
    {
        $p_cats = wp_get_post_terms(get_the_id(), 'portfolio-category');
        $p_cats_class = '';
        for ($i = 0, $count = count($p_cats); $i < $count; $i++) {
            $p_cat_term = $p_cats[$i];
            $p_cats_class .= ' ' . $p_cat_term->slug;
        }
        return $p_cats_class;
    }


    private function chars_count($cols = null)
    {
        $number = 155;
        switch ($cols) {
            case '1':
                $number = 300;
                break;
            case '2':
                $number = 130;
                break;
            case '3':
                $number = 70;
                break;
            case '4':
                $number = 55;
                break;
        }
        return $number;
    }


    private function post_content($params)
    {
        extract($params);

        if (!$show_content) return;

        $post = get_post(get_the_id());

        $out = $content = '';
        $chars_count = !empty($content_letter_count) ? $content_letter_count : $this->chars_count($posts_per_row);
        $content = !empty($post->post_excerpt) ? $post->post_excerpt : $post->post_content;
        $content = preg_replace('~\[[^\]]+\]~', '', $content);
        $content = strip_tags($content);
        $content = \Zikzag_Theme_Helper::modifier_character($content, $chars_count, "");

        if (!empty($content)) {
            $out .= '<div class="wgl-portfolio-item_content">';
            $out .= sprintf('<div class="content">%s</div>', $content);
            $out .= '</div>';
        }
        return $out;
    }


    public function wgl_portfolio_item($params, $class, $count, $grid_gap, $custom_link = false)
    {
        extract($params);
        $out = $link = '';

        // Post meta
        $post_meta = $this->post_cats_links($show_meta_categories);

        $crop = isset($crop_images) && !empty($crop_images) ? true : false;

        $link_target_blank = isset($link_target) && (bool)$link_target ? ' target="_blank" ' : '';

        $wrapper_class = !empty($info_position) ? ' ' . $info_position : ' inside_image';
        $wrapper_class .= !empty($horizontal_align) ? ' h_align_' . $horizontal_align : '';
        $wrapper_class .= $info_position == 'inside_image' ? ' ' . $image_anim . '_animation' : '';
        $wrapper_class .= !$show_portfolio_title && !$post_meta ? ' gallery_type' : '';

        $style_gap = !empty($grid_gap) ? ' style="padding-right:' . ((int) $grid_gap / 2) . 'px; padding-left:' . ((int) $grid_gap / 2) . 'px; padding-bottom:' . $grid_gap . '"' : '';

        // set post options
        $icon_out = '';

        if ($portfolio_icon_type == 'font') {

            // add icon migration
            if(!empty($portfolio_icon_fontawesome)){
                $migrated = isset( $params['__fa4_migrated'][$params[ 'portfolio_icon_fontawesome' ]] );
                $is_new = Icons_Manager::is_migration_allowed();
                if ( $is_new || $migrated ) {
                    ob_start();
                    Icons_Manager::render_icon( $params[ 'portfolio_icon_fontawesome' ], [ 'aria-hidden' => 'true' ] );
                    $icon_out .= ob_get_clean();
                } else {
                    $icon_out .= '<i class="icon '.esc_attr($icon_font).'"></i>';
                }
            }else{
                $icon_out .= '<i class="icon icon_plus"></i>';
            }
        }

        $attachment_url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()), 'full');

        switch ($img_click_action) {
            case 'popup':
                $link = "<a href='" . $attachment_url . "' class='portfolio_link swipebox' data-elementor-open-lightbox='yes' data-elementor-lightbox-slideshow='".esc_attr($params['item_id'])."'></a>";
                break;

            case 'single':
                $link = "<a href='" . get_permalink() . "'".$link_target_blank." class='portfolio_link single_link'></a>";
                break;

            case 'custom':
                if (rwmb_meta('mb_portfolio_link') == 1) {
                    $mb_custom_url = !empty(rwmb_meta('portfolio_custom_url')) ? rwmb_meta('portfolio_custom_url') : get_permalink();
                    $link = "<a href='" . esc_url($mb_custom_url) . "'".$link_target_blank." class='portfolio_link custom_link'></a>";
                }
                break;
        }

        echo '<article class="wgl-portfolio-list_item item ', esc_attr($class), '" ', $style_gap, '>';

        if ($custom_link) :
            echo $this->custom_link_item($params);

        else :
            echo '<div class="wgl-portfolio-item_wrapper', esc_attr($wrapper_class), '">';
            echo $image_anim == 'offset' ? '<div class="wgl-portfolio-item_offset">' : '';
            echo '<div class="wgl-portfolio-item_image">';
            echo self::getImgUrl($params, $attachment_url, $crop, $count, $grid_gap);

            if ($info_position == 'under_image') {
                echo '<div class="overlay"></div>';

                // Custom Links
                echo $link;
            }
            echo '</div>';

            if ($gallery_mode) {
                echo $this->gallery_mode_enabled($params, $link, $icon_out);
            } else {
                echo $this->standard_mode_enabled($params, $link, $post_meta, $icon_out);
            }

            if ($info_position != 'under_image' && $image_anim != 'sub_layer') {
                echo '<div class="overlay"></div>';
            }

            echo $image_anim == 'sub_layer' ? $link : '';

            echo $image_anim == 'offset' ? '</div>' : '';

            echo '</div>';

        endif;

        echo '</article>';
    }

    public function custom_link_item($params)
    {
        extract($params);

        $this->item->add_render_attribute('link', 'class', 'wgl-portfolio_item_link');

        if (!empty($item_link['url'])) {
            $this->item->add_link_attributes('link', $item_link);
        }

        $wrapper_class = ' align_' . $link_align;

        echo '<div class="wgl-portfolio-link_wrapper', esc_attr($wrapper_class), '">',
            '<a ', $this->item->get_render_attribute_string('link'), '>',
            esc_html($name_load_more),
            '</a>',
            '</div>';
    }

    public function gallery_mode_enabled($params, $link, $icon_out)
    {
        extract($params);

        if (!empty($icon_out)) {
            echo '<div class="wgl-portfolio-item_description">';
            echo '<div class="wgl-portfolio-item_icon wgl-portfolio-item_gallery-icon">';
            if ($info_position != 'under_image' && $image_anim != 'sub_layer') {
                echo $icon_out;
            }
            echo ($info_position != 'under_image' && $image_anim != 'sub_layer') ? $link : '';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="wgl-portfolio-item_description">',
                $info_position != 'under_image' && $image_anim != 'sub_layer' ? $link : '',
                '</div>';
        }
    }

    public function standard_mode_enabled($params, $link, $post_meta, $icon_out)
    {
        extract($params);

        $link_tag = 'span';
        $link_attr = '';
        $p_id = get_the_ID();
        $wp_get_attachment_url = wp_get_attachment_url(get_post_thumbnail_id($p_id), 'full');
        $link_target_blank = isset($link_target) && (bool)$link_target ? ' target="_blank" ' : '';

        if ($single_link_title) {
            switch ($img_click_action) {
                case 'popup':
                    $link_tag = 'a';
                    $link_attr = ' href="' . $wp_get_attachment_url . '" class="swipebox" data-elementor-open-lightbox="yes"';
                    break;

                case 'single':
                    $link_tag = 'a';
                    $link_attr = ' href="' . get_permalink() . '"'.$link_target_blank;
                    break;

                case 'custom':
                    if (rwmb_meta('mb_portfolio_link') == 1) {
                        $mb_custom_url = !empty(rwmb_meta('portfolio_custom_url')) ? ' href="' . rwmb_meta('portfolio_custom_url') . '"' : '';

                        $link_tag = $mb_custom_url ? 'a' : 'span';
                        $link_attr = $mb_custom_url . $link_target_blank;
                    }
                    break;
            }
        }

        echo '<div class="wgl-portfolio-item_description">';

        echo '<div class="portfolio__description">';

        if ($show_portfolio_title) {
            echo '<div class="portfolio__item-title">';
            printf(
                '<h4 class="title"><%1$s%2$s>' . get_the_title() . '</%1$s></h4>',
                $link_tag,
                $link_attr
            );
            echo '</div>';
        }

        if ($post_meta) echo '<div class="portfolio__item-meta">', $post_meta, '</div>';

        if ($show_content) echo $this->post_content($params);

        echo '</div>';

        if (!empty($icon_out)) {
            echo '<div class="wgl-portfolio-item_icon">';

            if ($info_position != 'under_image' && $image_anim != 'sub_layer') {
                echo $single_link_title ? '<a href="' . $title_link . '" target="_blank"' . ($img_click_action === 'popup' ? ' class="swipebox" data-elementor-open-lightbox="yes"' : '') . '>' : '',
                    $icon_out,
                    $single_link_title ? '</a>' : '';
            }

            echo '</div>';
        }

        // Links
        echo ($info_position != 'under_image' && $image_anim != 'sub_layer') ? $link : '';

        echo '</div>'; // wgl-portfolio-item_description
    }


    private function single_post_date()
    {
        if (rwmb_meta('mb_portfolio_single_meta_date') != 'default') {
            $date_enable = rwmb_meta('mb_portfolio_single_meta_date');
        } else {
            $date_enable = \Zikzag_Theme_Helper::get_option('portfolio_single_meta_date');
        }

        if ($date_enable) {
            return '<span class="post_date"><span>' .
                get_the_time('d') .
                '</span>' .
                get_the_time('F') .
                '</span>';
        }
    }


    private function single_post_likes()
    {
        $show_likes = \Zikzag_Theme_Helper::get_option('portfolio_single_meta_likes');
        if ($show_likes && function_exists('wgl_simple_likes')) {
            return wgl_simple_likes()->likes_button(get_the_ID(), 0);
        }
    }


    private function single_post_author()
    {
        $author = \Zikzag_Theme_Helper::get_option('portfolio_single_meta_author');
        if (!empty($author)) {
            return '<span class="post_author">' .
                esc_html__('by ', 'zikzag-core') .
                '<a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' .
                esc_html(get_the_author_meta('display_name')) .
                '</a>' .
                '</span>';
        }
    }


    private function single_post_comments()
    {
        $comments_enabled = \Zikzag_Theme_Helper::get_option('portfolio_single_meta_comments');

        if ($comments_enabled) {
            $comments_num = get_comments_number(get_the_ID());

            return '<span class="comments_post">' .
                '<a href="' . esc_url(get_comments_link()) . '">' .
                esc_html($comments_num) .
                ' ' .
                esc_html(_n('Comment', 'Comments', $comments_num, 'zikzag-core')) .
                '</a>' .
                '</span>';
        }
    }


    private function single_post_cats()
    {
        if (class_exists('RWMB_Loader') && rwmb_meta('mb_portfolio_single_meta_categories') != 'default') {
            $cats_enable = rwmb_meta('mb_portfolio_single_meta_categories');
        } else {
            $cats_enable = \Zikzag_Theme_Helper::get_option('portfolio_single_meta_categories');
        }

        if ($cats_enable && $cats = wp_get_post_terms(get_the_id(), 'portfolio-category')) {
            $result = '<span class="post_meta-categories">';
            for ($i = 0, $count = count($cats); $i < $count; $i++) {
                $term = $cats[$i];
                $name = $term->name;
                $post_cats_link = get_category_link($term->term_id);
                $result .= '<span><a href=' . esc_html($post_cats_link) . ' class="portfolio-category">' . esc_html($name) . '</a></span>';
            }
            $result .= '</span>';

            return $result;
        }
    }


    private function single_portfolio_info()
    {
        $p_info = '';
        $mb_info = rwmb_meta('mb_portfolio_info_items');

        if (!empty($mb_info)) {
            for ($i = 0, $count = count($mb_info); $i < $count; $i++) {
                $info = $mb_info[$i];
                $name = !empty($info['name']) ? $info['name'] : '';
                $description = !empty($info['description']) ? $info['description'] : '';
                $link = !empty($info['link']) ? $info['link'] : '';

                if ($name && $description) {
                    $p_info .= '<div class="portfolio__custom-meta">';
                    $p_info .= '<h5>' . $name . '</h5>';
                    $p_info .= $link ? '<a href="' . esc_url($link) . '">' : '';
                    $p_info .= '<span>' . $description . '</span>';
                    $p_info .= $link ? '</a>' : '';
                    $p_info .= '</div>';
                }
            }
        }
        return $p_info;
    }


    public function wgl_portfolio_single_item($parameters, $item_class = '')
    {
        $social_share = $p_meta = $p_annotation = '';

        // MetaBoxes
        $p_id = get_the_ID();
        $featured_image = $mb_hide_all_meta = $mb_show_title = true;
        $featured_image_replace = false;

        $tags_enable = $shares_enable = '';
        if (class_exists('RWMB_Loader')) {

            $featured_image = \Zikzag_Theme_Helper::options_compare('portfolio_featured_image_type', 'mb_portfolio_featured_image_conditional', 'custom');
            if ($featured_image == 'replace') {
                $featured_image_replace = \Zikzag_Theme_Helper::options_compare('portfolio_featured_image_replace', 'mb_portfolio_featured_image_conditional', 'custom');
            }

            $mb_show_title = rwmb_meta('mb_portfolio_title');
            $mb_info = rwmb_meta('mb_portfolio_info_items');
            $mb_editor = rwmb_meta('mb_portfolio_editor') ?: '';

            $mb_hide_all_meta = \Zikzag_Theme_Helper::get_option('portfolio_single_meta');

            if (rwmb_meta('mb_portfolio_above_content_cats') == 'default') {
                $tags_enable = \Zikzag_Theme_Helper::get_option('portfolio_above_content_cats');
            } else {
                $tags_enable = rwmb_meta('mb_portfolio_above_content_cats');
            }
            if (rwmb_meta('mb_portfolio_above_content_share') != 'default') {
                $shares_enable = rwmb_meta('mb_portfolio_above_content_share');
            } else {
                $shares_enable = \Zikzag_Theme_Helper::get_option('portfolio_above_content_share');
            }
        }

        $single_post_align = \Zikzag_Theme_Helper::options_compare('portfolio_single_align', 'mb_portfolio_post_conditional', 'custom');

        $post_date = $this->single_post_date();
        $post_comments = $this->single_post_comments();
        $post_cats = $this->single_post_cats();
        $post_author = $this->single_post_author();
        $post_likes = $this->single_post_likes();
        $portfolio_info = $this->single_portfolio_info();

        $meta_data = $post_author . $post_comments;

        $wp_get_attachment_url = false;
        if ($featured_image_replace) {
            if (rwmb_meta('mb_portfolio_featured_image_conditional') == 'custom') {
                $image_id = array_values($featured_image_replace);
                $image_id = $image_id[0]['ID'];

                $wp_get_attachment_url = wp_get_attachment_url($image_id, 'full');
            }
        } else {
            $wp_get_attachment_url = wp_get_attachment_url(get_post_thumbnail_id($p_id), 'full');
        }

        // Shares
        if ($shares_enable && function_exists('wgl_theme_helper')) {
            $social_share = '<div class="single_info-share_social-wpapper">' .
                $social_share .= '<div class="single_info-share_social-wpapper">';
            ob_start();
            wgl_theme_helper()->render_post_share($shares_enable);
            $social_share .= ob_get_clean();
            $social_share .= '</div>';
            $social_share .= '</div>';
        }

        // Featured image
        ob_start();
        if ($featured_image != 'off') {
            echo '<div class="wgl-portfolio-item_image">',
                self::getImgUrl($parameters, $wp_get_attachment_url, false, false, false),
                '</div>';
        }
        $p_featured_image = ob_get_clean();

        // Title
        $p_title = $mb_show_title ? '<h1 class="wgl-portfolio-item_title">' . get_the_title() . '</h1>' : '';

        // Meta
        if (!$mb_hide_all_meta && $meta_data) {
            $p_meta = '<div class="post_meta-wrap">';
                $p_meta .= '<div class="meta-wrapper">';
                    $p_meta .= $meta_data;
                $p_meta .= '</div>';
            $p_meta .= '</div>';
        }

        // Build shares and custom meta fields
        if ($mb_editor || $portfolio_info) {
            $p_annotation = '<div class="wgl-portfolio__item-info">';
            if ($mb_editor) {
                $p_annotation .= '<div class="portfolio__custom-desc">' . $mb_editor . '</div>';
            }
            if ($portfolio_info) {
                $p_annotation .= '<div class="portfolio__custom-annotation">' . $portfolio_info . '</div>';
            }
            $p_annotation .= '</div>';
        }

        // Content
        $content = apply_filters('the_content', get_post_field('post_content', get_the_id()));

        // Tags
        $post_tags = $tags_enable ? $this->getTags('<div class="tagcloud-wrapper"><div class="tagcloud">', ' ', '</div></div>') : '';


        // Render
        echo '<article class="wgl-portfolio-single_item">';
        echo '<div class="wgl-portfolio-item_wrapper">';
        echo '<div class="wgl-portfolio-item_title_wrap a', $single_post_align, '">',
            $p_featured_image,
            $post_date,
            $post_cats,
            $p_title,
            $p_meta,
            $p_annotation,
        '</div>';

        if ($content) {
            echo '<div class="wgl-portfolio-item_content">',
                '<div class="content">',
                '<div class="wrapper">',
                $content,
                '</div>',
                '</div>',
            '</div>';
        }

        // Post_info
        if ($tags_enable || $shares_enable || $post_likes) :
            echo '<div class="single_post_info post_info-portfolio">',
                $social_share,
                '<div class="meta-wrapper">',
                    $post_tags,
                    $post_likes,
                '</div>',
                '</div>',
                '<div class="post_info-divider"></div>';
        else :
            echo '<div class="post_info-divider"></div>';
        endif;

        echo '</div>';
        echo '</article>';
    }


    static public function getImgUrl(
        $params,
        $wp_get_attachment_url,
        $crop,
        $count,
        $grid_gap
    ) {
        $featured_image = $masonry_gap = '';

        if (strlen($wp_get_attachment_url)) {
            if ($params['portfolio_layout'] == 'masonry2') :
                switch ($count) {
                    case '2':
                        $wgl_featured_image_url = aq_resize($wp_get_attachment_url, '350', '740', $crop, true, true);
                        $masonry_gap = 'style="margin-top: -' . (33 - (int) $grid_gap) . 'px;"';
                        break;
                    default:
                        $wgl_featured_image_url = aq_resize($wp_get_attachment_url, '740', '740', $crop, true, true);
                } elseif ($params['portfolio_layout'] == 'masonry3') :
                switch ($count) {
                    case '2':
                        $wgl_featured_image_url = aq_resize($wp_get_attachment_url, '740', '350', $crop, true, true);
                        break;
                    default:
                        $wgl_featured_image_url = aq_resize($wp_get_attachment_url, '740', '740', $crop, true, true);
                } elseif ($params['portfolio_layout'] == 'masonry4') :
                switch ($count) {
                    case 1:
                    case 6:
                        $wgl_featured_image_url = aq_resize($wp_get_attachment_url, '1140', '570', $crop, true, true);
                        $masonry_gap = 'style="margin-top: -' . ((int) $grid_gap / 2) . 'px;"';
                        break;
                    default:
                        $wgl_featured_image_url = aq_resize($wp_get_attachment_url, '1140', '1140', $crop, true, true);
                } else :
                if ($params['portfolio_layout'] == 'carousel' && !empty($params['variable_width'])) {
                    $wgl_featured_image_url = aq_resize($wp_get_attachment_url, '1000', '600', $crop, true, true);
                } else {
                    switch ($params['posts_per_row']) { // ratio 0.7872
                        case '1':
                            $wgl_featured_image_url = $wp_get_attachment_url;
                            break;
                        default:
                        case '2':
                            $wgl_featured_image_url = aq_resize($wp_get_attachment_url, '1170', '1170', $crop, true, true);
                            break;
                        case '3':
                            $wgl_featured_image_url = aq_resize($wp_get_attachment_url, '740', '740', $crop, true, true);
                            break;
                        case '4':
                            $wgl_featured_image_url = aq_resize($wp_get_attachment_url, '570', '570', $crop, true, true);
                            break;
                        case '5':
                            $wgl_featured_image_url = aq_resize($wp_get_attachment_url, '390', '390', $crop, true, true);
                            break;
                    }
                }

            endif;

            if (!$wgl_featured_image_url) {
                $wgl_featured_image_url = $wp_get_attachment_url;
            }

            $featured_image = '<img src="' . $wgl_featured_image_url . '" ' . $masonry_gap . ' alt="" />';
        }

        return $featured_image;
    }


    public function getTags($before = null, $sep = ', ', $after = '')
    {
        if (null === $before) $before = __('Tags: ', 'zikzag-core');

        $the_tags = $this->get_the_tag_list($before, $sep, $after);

        if (!is_wp_error($the_tags)) return $the_tags;
    }


    private function get_the_tag_list($before = '', $sep = '', $after = '', $id = 0)
    {
        /**
         * Filters the tags list for a given post.
         */
        global $post;

        return apply_filters('the_tags', get_the_term_list($post->ID, 'portfolio_tag', $before, $sep, $after), $before, $sep, $after, $post->ID);
    }


    public function getCategories($params, $query)
    {
        $data_category = isset($params['tax_query']) ? $params['tax_query'] : [];
        $include = $exclude = [];

        if (!is_tax()) {
            if (!empty($data_category) && isset($data_category[0]) && $data_category[0]['operator'] === 'IN') {
                foreach ($data_category[0]['terms'] as $value) {
                    $idObj = get_term_by('slug', $value, 'portfolio-category');
                    $id_list[] = $idObj->term_id;
                }
                $include = implode(',', $id_list);
            } elseif (!empty($data_category) && isset($data_category[0]) && $data_category[0]['operator'] === 'NOT IN') {
                foreach ($data_category[0]['terms'] as $value) {
                    $idObj = get_term_by('slug', $value, 'portfolio-category');
                    $id_list[] = $idObj->term_id;
                }
                $exclude = implode(',', $id_list);
            }
        }

        $cats = get_terms([
            'taxonomy' => 'portfolio-category',
            'include' => $include,
            'exclude' => $exclude,
            'hide_empty' => true
        ]);

        $out = '<a href="#" data-filter=".item" class="active">' . esc_html__('All Works', 'zikzag-core') . '<span class="number_filter"></span></a>';
        foreach ($cats as $cat) if ($cat->count > 0) {
            $out .= '<a href="' . get_term_link($cat->term_id, 'portfolio-category') . '" data-filter=".' . $cat->slug . '">';
            $out .= $cat->name;
            $out .= '<span class="number_filter"></span>';
            $out .= '</a>';
        }
        return $out;
    }

    public function loadMore($params, $name_load_more)
    {
        if (!empty($name_load_more)) {
            $uniq = uniqid();
            $ajax_data_str = htmlspecialchars(json_encode($params), ENT_QUOTES, 'UTF-8');

            return '<div class="clear"></div>' .
                '<div class="load_more_wrapper">' .
                '<div class="button_wrapper">' .
                '<a href="#" class="load_more_item"><span>' . $name_load_more . '</span></a>' .
                '</div>' .
                '<form class="posts_grid_ajax">' .
                "<input type='hidden' class='ajax_data' name='{$uniq}_ajax_data' value='$ajax_data_str' />" .
                '</form>' .
                '</div>';
        }
    }

    public function infinite_more($params)
    {
        $uniq = uniqid();
        wp_enqueue_script('waypoints');
        $ajax_data_str = htmlspecialchars(json_encode($params), ENT_QUOTES, 'UTF-8');

        return '<div class="clear"></div>' .
            '<div class="text-center load_more_wrapper">' .
            '<div class="infinity_item">' .
            '<span class="wgl-ellipsis">' .
            '<span></span><span></span>' .
            '<span></span><span></span>' .
            '</span>' .
            '</div>' .
            '<form class="posts_grid_ajax">' .
            "<input type='hidden' class='ajax_data' name='{$uniq}_ajax_data' value='${ajax_data_str}' />" .
            '</form>' .
            '</div>';
    }
}
