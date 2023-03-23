<?php
/*
 * Plugin name: WP Compress | Image Optimizer
 * Plugin URI: https://www.wpcompress.com
 * Author: WP Compress
 * Author URI: https://www.wpcompress.com
 * Version: 6.00.27
 * Description: Automatically compress and optimize images to shrink image file size, improve  times and boost SEO ranks - all without lifting a finger after setup.
 * Text Domain: wp-compress
 * Domain Path: /langs
 */

if (empty($_GET['disableWPC'])) {
    include_once 'wp-compress-core.php';
}