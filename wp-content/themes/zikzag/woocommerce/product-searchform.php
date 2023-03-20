<?php

defined( 'ABSPATH' ) || exit;

/**
 * The template for displaying product search form
 *
 * This template is overridden by WebGeniusLab team for fine customizing.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version    7.0.1
 */


?>
<form role="search" method="get" class="woocommerce-product-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="woocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>"><?php esc_html_e( 'Search for:', 'zikzag' ); ?></label>
	<input type="search" id="woocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>" class="search-field" placeholder="<?php echo esc_attr__( 'Search products&hellip;', 'zikzag' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	<button class="search-button" type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'zikzag' ); ?>"><?php echo esc_html_x( 'Search', 'submit button', 'zikzag' ); ?></button>
	<i class="search__icon flaticon-search-3"></i>
	<input type="hidden" name="post_type" value="product" />
</form>
