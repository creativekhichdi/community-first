<?php
/**
 * Show messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/notice.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version 3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $notices ) {
	return;
}

?>

<?php foreach ( $notices as $notice ) : ?>
	<div class="woocommerce-info zikzag_module_message_box type_info closable wpb_animate_when_almost_visible wpb_left-to-right left-to-right wpb_start_animation animated" <?php echo wc_get_notice_data_attr( $notice ); ?>><div class="message_icon_wrap"><i class="message_icon "></i></div><div class="message_content"><div class="message_text"> <?php echo wc_kses_notice( $notice['notice'] ); ?> </div></div><span class="message_close_button"></span></div>
<?php endforeach; ?>
