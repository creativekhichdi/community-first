<?php

namespace WglAddons\Templates;

defined( 'ABSPATH' ) || exit; // Abort, if called directly.

use Elementor\Plugin;
use Elementor\Frontend;


/**
 * WGL Elementor Countdown Template
 *
 *
 * @class        WglCountDown
 * @version      1.0
 * @category     Class
 * @author       WebGeniusLab
 */

class WglCountDown
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

        wp_enqueue_script('jquery-coundown', get_template_directory_uri() . '/js/jquery.countdown.min.js', array(), false, false);

        $countdown_class = '';

        // Module unique id
        $countdown_id = uniqid( "countdown_" );
        $countdown_attr = ' id='.$countdown_id;


        $countdown_class .= ' cd_'.$size;

        $f = !(bool)$hide_day ? 'd' : '';
        $f .= !(bool)$hide_hours ? 'H' : '';
        $f .= !(bool)$hide_minutes ? 'M' : '';
        $f .= !(bool)$hide_seconds ? 'S' : '';

        // Countdown data attribute http://keith-wood.name/countdown.html
        $data_array = array();

        $data_array['format'] = !empty($f) ? esc_attr($f) : '';

        $data_array['year'] =  esc_attr($countdown_year);
        $data_array['month'] =  esc_attr($countdown_month);
        $data_array['day'] =  esc_attr($countdown_day);
        $data_array['hours'] =  esc_attr($countdown_hours);
        $data_array['minutes'] =  esc_attr($countdown_min);

        $data_array['labels'][]  =  esc_attr( esc_html__( 'Years', 'zikzag-core' ) );
        $data_array['labels'][]  =  esc_attr( esc_html__( 'Months', 'zikzag-core' ) );
        $data_array['labels'][]  =  esc_attr( esc_html__( 'Weeks', 'zikzag-core' ) );
        $data_array['labels'][]  =  esc_attr( esc_html__( 'Days', 'zikzag-core' ) );
        $data_array['labels'][]  =  esc_attr( esc_html__( 'Hours', 'zikzag-core' ) );
        $data_array['labels'][]  =  esc_attr( esc_html__( 'Minutes', 'zikzag-core' ) );
        $data_array['labels'][]  =  esc_attr( esc_html__( 'Seconds', 'zikzag-core' ) );
        $data_array['labels1'][] =  esc_attr( esc_html__( 'Year', 'zikzag-core' ) );
        $data_array['labels1'][] =  esc_attr( esc_html__( 'Month', 'zikzag-core' ) );
        $data_array['labels1'][] =  esc_attr( esc_html__( 'Week', 'zikzag-core' ) );
        $data_array['labels1'][] =  esc_attr( esc_html__( 'Day', 'zikzag-core' ) );
        $data_array['labels1'][] =  esc_attr( esc_html__( 'Hour', 'zikzag-core' ) );
        $data_array['labels1'][] =  esc_attr( esc_html__( 'Minute', 'zikzag-core' ) );
        $data_array['labels1'][] =  esc_attr( esc_html__( 'Second', 'zikzag-core' ) );

        $data_attribute = json_encode($data_array, true);

        $output = '<div'.$countdown_attr.' class="wgl-countdown'.esc_attr($countdown_class).'" data-atts="'.esc_js($data_attribute).'">';
        $output .= '</div>';

        echo \Zikzag_Theme_Helper::render_html($output);

	}

}