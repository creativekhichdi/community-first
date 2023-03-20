<?php

defined( 'ABSPATH' ) || exit;

if (!class_exists('Zikzag_Header_Sticky')) {
	class Zikzag_Header_Sticky extends Zikzag_Get_Header{

		public function __construct(){
			$this->header_vars();  
			$this->html_render = 'sticky';

	   		if (Zikzag_Theme_Helper::options_compare('header_sticky','mb_customize_header_layout','custom') == '1') {
	   			$header_sticky_style = Zikzag_Theme_Helper::get_option('header_sticky_style');
	   			
	   			echo "<div class='wgl-sticky-header wgl-sticky-element".($this->header_type === 'default' ? ' header_sticky_shadow' : '')."'".(!empty($header_sticky_style) ? ' data-style="'.esc_attr($header_sticky_style).'"' : '').">";

	   				echo "<div class='container-wrapper'>";
	   				
	   					$this->build_header_layout('sticky');
	   				echo "</div>";

	   			echo "</div>";
	   		}
		}
	}

    new Zikzag_Header_Sticky();
}
