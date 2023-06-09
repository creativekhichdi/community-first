<?php

defined('ABSPATH') || exit;

if (!class_exists('Zikzag_Footer_Area')) {
    /**
     * Footer area
     *
     *
     * @class		Zikzag_Footer_Area
     * @version		1.0
     * @category	Class
     * @author		WebGeniusLab
     */

    class Zikzag_Footer_Area
    {
        /**
         * @since 1.0
         * @access private
         */

        private $id;
        private $content_type;
        private $footer_full_width;
        private $mb_footer_switch;
        private $mb_copyright_switch;

        function __construct()
        {
            $footer_options = apply_filters( 'zikzag_footer_enable', true);
            extract($footer_options);

            $footer_bg_color = Zikzag_Theme_Helper::get_option('footer_bg_color');
            $style = '';

            // Upper-level variables
            $this->id = get_queried_object_id();
            $this->content_type = Zikzag_Theme_Helper::options_compare('footer_content_type', 'mb_footer_switch', 'on');
            $this->footer_full_width = Zikzag_Theme_Helper::get_option('footer_full_width');

            if (class_exists('RWMB_Loader') && $this->id !== 0) {
                $this->mb_footer_switch = $mb_footer_switch;
                if ($this->mb_footer_switch == 'on') {
                    $footer_bg_color = rwmb_meta('mb_footer_bg');
                    $footer_bg_color = !empty($footer_bg_color['color']) ? $footer_bg_color['color'] : "";
                }

                $this->mb_copyright_switch = $mb_copyright_switch;
            }

            // Container style
            if (
                $this->content_type == 'widgets'
                && ($footer_switch || $copyright_switch)
            ) {
                $style = !empty($footer_bg_color) ? ' background-color :' . esc_attr($footer_bg_color) . ';' : '';
                $style .= Zikzag_Theme_Helper::bg_render('footer', 'mb_footer_switch', 'on');
                $style = $style ? ' style="' . esc_attr($style) . '"' : '';
            }

            /*
	        *
	        * Footer render
	        */
            if ($footer_switch || $copyright_switch) {
                echo "<footer class='footer clearfix'" . $style . " id='footer'>";
                if ($footer_switch) {
                    switch ($this->content_type) {
                        default:
                        case 'widgets':
                            $this->main_footer_html();
                            break;
                        case 'pages':
                            $this->main_footer_get_page();
                            break;
                    }
                }

                if ($copyright_switch && $this->content_type == 'widgets') {
                    $this->copyright_html();
                }
                echo "</footer>";
            }
        }

        private function get_footer_vars($optn_1 = null)
        {
            $footer_options = [];

            // Get options
            $footer_spacing = Zikzag_Theme_Helper::options_compare('footer_spacing', 'mb_footer_switch', 'on');
            $footer_border = Zikzag_Theme_Helper::options_compare('footer_add_border', 'mb_footer_switch', 'on');
            $footer_border_color = Zikzag_Theme_Helper::options_compare('footer_border_color', 'mb_footer_switch', 'on');

            if ($optn_1 == 'widgets') {
                $footer_options['widget_columns'] = Zikzag_Theme_Helper::get_option('widget_columns');
                $footer_options['widget_columns_2'] = Zikzag_Theme_Helper::get_option('widget_columns_2');
                $footer_options['widget_columns_3'] = Zikzag_Theme_Helper::get_option('widget_columns_3');
                $footer_align = Zikzag_Theme_Helper::get_option('footer_align');

                //footer container class
                $footer_options['footer_class'] = ' align-' . esc_attr($footer_align);
            }

            // Footer paddings
            $footer_options['footer_style'] = $footer_options['footer_border_style'] =  '';
            $footer_options['footer_style'] .= !empty($footer_spacing['padding-top']) ? ' padding-top:' . (int) $footer_spacing['padding-top'] . 'px;' : '';
            $footer_options['footer_style'] .= !empty($footer_spacing['padding-bottom']) ? ' padding-bottom:' . (int) $footer_spacing['padding-bottom'] . 'px;' : '';
            $footer_options['footer_style'] .= !empty($footer_spacing['padding-left']) ? ' padding-left:' . (int) $footer_spacing['padding-left'] . 'px;' : '';
            $footer_options['footer_style'] .= !empty($footer_spacing['padding-right']) ? ' padding-right:' . (int) $footer_spacing['padding-right'] . 'px;' : '';
            $footer_options['footer_style'] = !empty($footer_options['footer_style']) ? ' style="' . $footer_options['footer_style'] . '"' : '';

            $footer_options['footer_border_style'] .= $footer_border ? ' style="border-top: 1px solid ' . $footer_border_color . ';"' : '';

            if ($optn_1 == 'widgets') {
                $footer_options['layout'] = array();
                switch ((int) $footer_options['widget_columns']) {
                    case 1:
                        $footer_options['layout'] = array('12');
                        break;
                    case 2:
                        $footer_options['layout'] = explode('-', $footer_options['widget_columns_2']);
                        break;
                    case 3:
                        $footer_options['layout'] = explode('-', $footer_options['widget_columns_3']);
                        break;
                    default:
                    case 4:
                        $footer_options['layout'] = array('3', '3', '3', '3');
                        break;
                }
            }

            return $footer_options;
        }

        private function main_footer_html()
        {
            // Get footer vars
            $footer_vars = $this->get_footer_vars('widgets');
            extract($footer_vars);

            echo "<div class='footer_top-area widgets_area column_" . (int) $widget_columns . $footer_class . "' " . $footer_border_style . ">";

            if (!$this->footer_full_width) echo "<div class='wgl-container'>";

            $sidebar_exists = false;
            $i = 1;
            while ($i < (int) $widget_columns + 1) {
                if (is_active_sidebar('footer_column_' . $i)) {
                    $sidebar_exists = true;
                }
                $i++;
            }
            if ($sidebar_exists) {
                echo "<div class='row'" . $footer_style . ">";
                $i = 1;
                while ($i < (int) $widget_columns + 1) {
                    $columns_number = $i - 1; ?>
                    <div class='wgl_col-<?php echo esc_attr($layout[$columns_number]); ?>'>
                        <?php
                        if (is_active_sidebar('footer_column_' . $i)) dynamic_sidebar('footer_column_' . $i);
                        ?>
                    </div>
                    <?php
                    $i++;
                }
                echo "</div>";
            }

            if (!$this->footer_full_width) echo '</div>';

            echo '</div>';
        }

        private function main_footer_get_page()
        {
            echo '<div class="footer_top-area">';

            echo '<div class="wgl-container">';

            echo '<div class="row-footer">';

            $footer_page_select = Zikzag_Theme_Helper::options_compare('footer_page_select', 'mb_footer_switch', 'on');

            if (!empty($footer_page_select)) {
                $footer_page_select_id = intval($footer_page_select);

                if (class_exists('Polylang') && function_exists('pll_current_language')) {
                    $currentLanguage = pll_current_language();
                    $translations = PLL()->model->post->get_translations($footer_page_select_id);
                    
                    $polylang_footer_id = $translations[$currentLanguage] ?? '';
                    $footer_page_select_id = !empty($polylang_footer_id) ? $polylang_footer_id : $footer_page_select_id;
                }

                if (class_exists('SitePress')) {
                    if (function_exists('icl_object_id')) {
                        $footer_page_select_id = icl_object_id($footer_page_select_id, 'footer', false, ICL_LANGUAGE_CODE);
                    }
                }

                if (did_action('elementor/loaded')) {
                    echo \Elementor\Plugin::$instance->frontend->get_builder_content($footer_page_select_id);
                }
            }

            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        private function copyright_spacing()
        {
            // Get options
            $copyright_spacing = Zikzag_Theme_Helper::options_compare('copyright_spacing', 'mb_copyright_switch', 'on');

            // Copyright style
            $style = '';
            $style .= !empty($copyright_spacing['padding-top']) ? 'padding-top:' . (int) $copyright_spacing['padding-top'] . 'px;' : '';
            $style .= !empty($copyright_spacing['padding-bottom']) ? 'padding-bottom:' . (int) $copyright_spacing['padding-bottom'] . 'px;' : '';
            $style = !empty($style) ? ' style="' . $style . '"' : '';
            return $style;
        }

        // Copyright style
        private function copyright_style()
        {
            $style = '';
            if ($this->content_type == 'widgets') {
                $bg_color = Zikzag_Theme_Helper::options_compare('copyright_bg_color', 'mb_copyright_switch', 'on');

                $style = !empty($bg_color) ? 'background-color: ' . esc_attr($bg_color) . ';' : '';
                $style = $style ? ' style="' . $style . '"' : '';
            }

            return $style;
        }

        private function copyright_html()
        {
            $editor = Zikzag_Theme_Helper::get_option('copyright_editor');

            if ($this->mb_copyright_switch == 'on') {
                $editor = rwmb_meta('mb_copyright_editor');
            }
            ?>
            <div class='copyright' <?php echo Zikzag_Theme_Helper::render_html($this->copyright_style()); ?>>
                <?php if (!$this->footer_full_width) echo "<div class='wgl-container'>"; ?>
                <div class='row' <?php echo Zikzag_Theme_Helper::render_html($this->copyright_spacing()); ?>>
                    <div class='wgl_col-12'>
                        <?php echo do_shortcode($editor); ?>
                    </div>
                </div>
                <?php if (!$this->footer_full_width) echo "</div>"; ?>
            </div>
            <?php
        }
    }

    new Zikzag_Footer_Area();
}
