<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/error.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $notices ) {
	return;
}

?>
<div class="zikzag_module_message_box type_error closable wpb_animate_when_almost_visible wpb_right-to-left right-to-left wpb_start_animation animated"><div class="message_icon_wrap" role="alert"><i class="message_icon "></i></div><div class="message_content"><div class="message_text"> 
<ul class="woocommerce-error" role="alert">
	<?php foreach ( $notices as $notice ) : ?>
		<li<?php echo wc_get_notice_data_attr( $notice ); ?>><?php echo wc_kses_notice( $notice['notice'] ); ?></li>
	<?php endforeach; ?>
</ul>
</div></div><span class="message_close_button"></span></div> 