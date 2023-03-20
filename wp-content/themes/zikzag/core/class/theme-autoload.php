<?php

defined( 'ABSPATH' ) || exit;

/**
* Zikzag Theme Autoload
*
*
* @class        Zikzag_Theme_Autoload
* @version      1.0
* @category     Class
* @author       WebGeniusLab
*/

if (!class_exists('Zikzag_Theme_Autoload')) {
	class Zikzag_Theme_Autoload{

		private static $instance = null;
		public static function get_instance( ) {
			if ( null == self::$instance ) {
				self::$instance = new self( );
			}

			return self::$instance;
		}

		public function __construct () {
			#Defaults option theme
			$this->theme_default_option();
			
			#Metabox option 
			$this->metabox_option();
		
			#Theme option 
			$this->theme_option();

			#Customize theme 
			$this->theme_customize();

			#TGM init
			$this->tgm_register();
		}

		public function theme_default_option(){
			require_once( get_theme_file_path('/core/includes/default-options.php') );
		}        

		public function theme_option(){
			require_once( get_theme_file_path('/core/includes/redux/redux-config.php') );
		}        

		public function metabox_option(){
			require_once( get_theme_file_path('/core/includes/metabox/metabox-config.php') );
		}

		public function theme_customize(){
			require_once( get_theme_file_path('/core/class/dynamic-styles.php') );
			require_once( get_theme_file_path('/core/class/theme-support.php') );            
		}

		public function tgm_register(){
			 require_once( get_theme_file_path('/core/tgm/wgl-tgm.php') );
		} 
	}
	new Zikzag_Theme_Autoload();

}
?>