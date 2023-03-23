<?php


class wps_ic_cache_integrations
{


  public function __construct()
  {
  }


  public static function purgeAll($file = false, $varnish = false)
  {
    self::purgeBreeze();
    self::purgeCacheFiles($file);
    #self::purgeCriticalFiles();

    // Clear cache.
    if (function_exists('rocket_clean_domain')) {
      rocket_clean_domain();
    }

    // WP Optimize
    if (class_exists('WP_Optimize')) {
      WP_Optimize()->get_page_cache()->purge();
    }

    // Purge Combined
    // When plugins have a simple method, add them to the array ('Plugin Name' => 'method_name')
    $others = array(
      'WP Super Cache' => 'wp_cache_clear_cache',
      'W3 Total Cache' => 'w3tc_pgcache_flush',
      'WP Fastest Cache' => 'wpfc_clear_all_cache',
      'WP Rocket' => 'rocket_clean_domain',
      'Cachify' => 'cachify_flush_cache',
      'Comet Cache' => array('comet_cache', 'clear'),
      'SG Optimizer' => 'sg_cachepress_purge_cache',
      'Pantheon' => 'pantheon_wp_clear_edge_all',
      'Zen Cache' => array('zencache', 'clear'),
      'Breeze' => array('Breeze_PurgeCache', 'breeze_cache_flush'),
      'Swift Performance' => array('Swift_Performance_Cache', 'clear_all_cache'),
    );

    foreach ($others as $plugin => $method) {
      if (is_callable($method)) {
        call_user_func($method);
      }
    }

    // Lite Speed
    if (defined('LSCWP_V')) {
      do_action('litespeed_purge_all');
      if (is_callable(array('LiteSpeed_Cache_Tags', 'add_purge_tag'))) {
        LiteSpeed_Cache_Tags::add_purge_tag('*');
      }
    }

    // HummingBird
    if (defined('WPHB_VERSION')) {
      do_action('wphb_clear_page_cache');
    }

    // SG Cache
    if (function_exists('sg_cachepress_purge_cache')) {
      // Purge Everything
      sg_cachepress_purge_cache();
    }

    // Breeze Purge
    if (function_exists('breeze_cache_flush')) {
      Breeze_PurgeCache::breeze_cache_flush();
    }

    // Purge Autoptimize
    if (class_exists('autoptimizeCache')) {
      autoptimizeCache::clearall();
    }

    // Purge W3 Cache
    if( class_exists('W3_Plugin_TotalCacheAdmin') ) {
      $plugin_totalcacheadmin = & w3_instance('W3_Plugin_TotalCacheAdmin');
      $plugin_totalcacheadmin->flush_all();
    }

    // Varnish
    if ($varnish) {
      self::purgeVarnish();
    }

  }

  public static function purgeBreeze()
  {
    if (defined('BREEZE_VERSION')) {
      global $wp_filesystem;
      require_once(ABSPATH . 'wp-admin/includes/file.php');

      WP_Filesystem();

      $cache_path = breeze_get_cache_base_path(is_network_admin(), true);
      $wp_filesystem->rmdir(untrailingslashit($cache_path), true);

      if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
      }
    }
  }

  public static function purgeCacheFiles($file = false)
  {
    $cache_dir = WPS_IC_CACHE;

    if (!$file) {
      self::removeDirectory($cache_dir);
    } else {
      if (file_exists($cache_dir . $file)) {
        unlink($cache_dir . $file);
      }
    }
    return true;
  }

  public static function removeDirectory($path)
  {
    $path = rtrim($path, '/');
    $files = glob($path . '/*');
    if (!empty($files)) {
      foreach ($files as $file) {
        is_dir($file) ? self::removeDirectory($file) : unlink($file);
      }
    }
  }

  public static function purgeCriticalFiles()
  {
    $cache_dir = WPS_IC_CRITICAL;

    self::removeDirectory($cache_dir);

    return true;
  }


  // TODO: Maybe it will cause errors with non SSL sites?
  public static function purgeVarnish()
  {
    global $wpdb, $current_blog;
    $parseUrl = parse_url(site_url());

    // Determine the schema
    $schema = 'http://';
    if ( isset( $parseUrl['scheme'] ) ) {
      $schema = $parseUrl['scheme'] . '://';
    }

    // Flush original WP domain
    wp_remote_request($schema . $current_blog->domain . '/', array('method' => 'PURGE', 'headers' => array('host' => $current_blog->domain, 'X-Purge-Method' => 'default')));
    /*
    // Get table name for mapping
    $wpdb->dmtable = (isset($wpdb->base_prefix) ? $wpdb->base_prefix : $wpdb->prefix) . 'domain_mapping';

    // Flush all mapped domains
    $blog_domains = $wpdb->get_col($wpdb->prepare("
			SELECT dm.domain
			FROM $wpdb->blogs AS b
			INNER JOIN $wpdb->dmtable AS dm ON b.blog_id = dm.blog_id
			WHERE b.blog_id = %d
			", $current_blog->blog_id));

    if (empty($blog_domains)) {
      return false;
    }

    foreach ($blog_domains as $blog_domain) {
      wp_remote_request($schema . $blog_domain . '/', array('method' => 'PURGE', 'headers' => array('host' => $blog_domain, 'X-Purge-Method' => 'default')));
    }*/

    return true;
  }


}