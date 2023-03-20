<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #main div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Zikzag
 * @since 1.0
 * @version 1.0
 */
?>
	</main>
	<?php

	/**
	* Elementor Pro Footer Render
	*/
	do_action('zikzag_elementor_pro_footer');

	/**
	* Check WGL footer active option
	*/
	$footer = apply_filters( 'zikzag_footer_enable', true);
	$footer_switch = $footer['footer_switch'] ?? '';
	$copyright_switch = $footer['copyright_switch'] ?? '';
	if( $footer_switch || $copyright_switch ){
		get_template_part('templates/section', 'footer');
	}

	/**
	* Runs after main
	*/
	do_action( 'zikzag_after_main_content' );

	wp_footer();
	?>
</body>
</html>