<?php

namespace WglAddons\Modules;

use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Base_Data_Control;
use WglAddons\Includes\Wgl_Elementor_Helper;

defined( 'ABSPATH' ) || exit;

/**
* Wgl Elementor Custom Icon Control
*
*
* @class        Wgl_Icons_Library
* @version      1.0
* @category Class
* @author       WebGeniusLab
*/

class Wgl_Icons_Library{

    public function __construct(){

        add_filter( 'elementor/icons_manager/additional_tabs', [ $this, 'extended_icons_library' ] );
    }

    public function extended_icons_library(){

    	return [
    		'wgl_icons' => [
    			'name' => 'wgl_icons',
    			'label' => esc_html__( 'WGL Icons Library', 'zikzag-core' ),
    			//'url' =>  get_template_directory_uri().'/fonts/flaticon/flaticon.css',
    			//'enqueue' => [  get_template_directory_uri().'/fonts/flaticon/flaticon.css' ],
    			'prefix' => 'flaticon-',
    			'displayPrefix' => 'flaticon',
    			'labelIcon' => 'flaticon',
    			//'ver' => '5.9.0',
    			'icons'	=> \WglAddons\Includes\Wgl_Elementor_Helper::get_instance()->get_wgl_icons(),
    			'native' => true,
    		]
    	];
    }
}

?>