<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="main">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package    WordPress
 * @subpackage Zikzag
 * @since      1.0
 * @version    1.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php
    wp_body_open();

    /**
    * Get Preloader
    *
    * @since 1.0.0
    */
    do_action('zikzag_preloader');

    /**
    * Elementor Pro Header Render
    *
    * @since 1.0.0
    */
    do_action('zikzag_elementor_pro_header');

    /**
    * Check WGL header active option
    *
    * @since 1.0.0
    */
    if (apply_filters('zikzag_header_enable', true)) {
        get_template_part('templates/header/section', 'header');
    }

    /**
    * Check WGL page title active option
    *
    * @since 1.0.0
    */
    $page_title = apply_filters('zikzag_page_title_enable', true);
    if (isset($page_title['page_title_switch']) && $page_title['page_title_switch'] !== 'off') {
        get_template_part('templates/header/section', 'page_title');
    }
    ?>
    <main id="main" class="site-main">