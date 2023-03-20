<?php

/**
 * Template for displaying search forms in Zikzag
 *
 * @package       WordPress
 * @subpackage    Zikzag
 * @since         1.0
 * @version       1.0
 */

$unique_id = uniqid( 'search-form-' );

?>
<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="search-form">
    <input required type="text" id="<?php echo esc_attr($unique_id); ?>" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'zikzag' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
    <input class="search-button" type="submit" value="<?php esc_attr_e('Search', 'zikzag'); ?>">
    <i class="search__icon flaticon-search-3"></i>
</form>