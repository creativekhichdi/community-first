<?php

include_once WPS_IC_DIR . 'addons/cdn/cdn-rewrite.php';
include_once WPS_IC_DIR . 'traits/url_key.php';

class wps_ic_combine_css
{

  public static $excludes;
  public static $rewrite;
  public $combine;
  public $combined_head_file;
  public $combined_footer_file;

  public function __construct()
  {
    self::$excludes = new wps_ic_excludes();
    self::$rewrite = new wps_cdn_rewrite();
    $this->url_key_class = new wps_ic_url_key();
    $this->urlKey = $this->url_key_class->urlKey;

    $rand = substr(md5(microtime()), rand(0, 26), 5);
    $this->combine = 1;
    $this->combined_css_head = '';
    $this->combined_css_footer = '';
    $this->combined_head_file = WPS_IC_CACHE . 'wps_combined_head_' . $this->urlKey . '.css';
    $this->combined_footer_file = WPS_IC_CACHE . 'wps_combined_footer_' . $this->urlKey . '.css';
    $this->combined_head_url = WPS_IC_CACHE_URL . 'wps_combined_head_' . $this->urlKey . '.css?rand=' . $rand;
    $this->combined_footer_url = WPS_IC_CACHE_URL . 'wps_combined_footer_' . $this->urlKey . '.css?rand=' . $rand;
    $this->settings = get_option(WPS_IC_SETTINGS);

    $this->patterns = [
      '/<link rel=[\"|\']stylesheet[\"|\'].*?>/si',
      '/<style\b[^>]*>(.*?)<\/style>?/si',
      '/<link\b[^>](.*?)onload=[\"|\']this.rel=[\"|\']stylesheet[\"|\'][\"|\'](.*?)>/' // deferred stylesheets
    ];

    if (file_exists($this->combined_head_file) || file_exists($this->combined_footer_file)) {
      $this->combine = 0;
    }

    if (empty($this->settings['cname']) || !$this->settings['cname']) {
      $this->zone_name = get_option('ic_cdn_zone_name');
    } else {
      $custom_cname = get_option('ic_custom_cname');
      $this->zone_name = $custom_cname;
    }

    $this->dbg_combine = 0;
    if (isset($_GET['dbg']) && $_GET['dbg'] == 'combine_css') {
      $this->dbg_combine = 1;
      $this->combine = 1;
      $this->dbg_file = WPS_IC_UPLOADS_DIR . '/combine_css_log.txt';
      $this->log('CSS Combine Started');
    }

  }

  public function combine_head($head)
  {

    $head = $head[0];
    $head = preg_replace_callback($this->patterns, array($this, 'script_replace_head'), $head);

    if ($this->combine && $this->combined_css_head == '') {
      return $head;
    }

    if ($this->combine) {
      file_put_contents($this->combined_head_file, $this->combined_css_head);
    }

    if (!empty($this->settings['remove-render-blocking']) && $this->settings['remove-render-blocking'] == '1') {
      $head = preg_replace('/<\/head>/', '<link rel="preload" as="style"  onload="this.rel=\'stylesheet\'" defer href="' . $this->combined_head_url . '" type="text/css" media="all"></head>', $head);

    } else {
      $head = preg_replace('/<\/head>/', '<link rel="stylesheet" href="' . self::$rewrite->adjust_src_url($this->combined_head_url) . '" type="text/css" media="all"></head>', $head);
    }

    return $head;
  }

  public function combine_footer($footer)
  {

    $footer = $footer[0];
    $footer = preg_replace_callback($this->patterns, array($this, 'script_replace_footer'), $footer);

    if ($this->combine && $this->combined_css_footer == '') {
      return $footer;
    }

    if ($this->combine) {
      file_put_contents($this->combined_footer_file, $this->combined_css_footer);
    }


    if (!empty($this->settings['remove-render-blocking']) && $this->settings['remove-render-blocking'] == '1') {
      $footer = preg_replace('/<\/body>/', '<link rel="preload" as="style"  onload="this.rel=\'stylesheet\'" defer href="' . $this->combined_footer_url . '" type="text/css" media="all"></body>', $footer);

    } else {
      $footer = preg_replace('/<\/body>/', '<link rel="stylesheet" href="' . self::$rewrite->adjust_src_url($this->combined_footer_url) . '" type="text/css" media="all"></body>', $footer);
    }

    return $footer;
  }

  public function script_replace_head($tag)
  {

    $tag = $tag[0];
    $src = '';

    if (current_user_can('manage_options') ||  self::$excludes->strInArray( $tag, self::$excludes->combineCSSExcludes() )) {
      return $tag;
    }

    if ($this->combine) {
      $is_src_set = preg_match('/href=["|\'](.*?)["|\']/', $tag, $src);
      $src = str_replace('href=', '', $src);
      $src = str_replace("'", "", $src);
      $src = $src[0];

      if ($is_src_set == 1) {

        $check = wp_http_validate_url($src);
        if ($check) {
          $content = self::$excludes->getContent($src);
        } else {
          $content = self::$excludes->getContent(get_home_url() . $src);
        }

      } else {
        $content = $tag;
        $content = preg_replace('/<style(.*?)>/', '', $content, '', $count);
        $content = preg_replace('/<\/style>/', '', $content);

        if (!$count) {
          //no href, and not a <style> tag
          return $tag;
        }
      }

      //replace relative urls
      $this->asset_url = $src;
      $content = preg_replace_callback("/url(\(((?:[^()])+)\))/i", array($this, 'rewrite_relative_url'), $content);

      //sometimes php injects a zero width space char at the start of a new script, this clears it
      $content = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $content);

      $this->combined_css_head .= "/* SCRIPT : $src */" . PHP_EOL;
      $this->combined_css_head .= $content . PHP_EOL;

      if ($this->dbg_combine) {
        if ($src == '') {
          $this->log('SRC: ' . $tag);
          $this->log('CONTENT: ' . $content);
        } else {
          $this->log('SRC: ' . $src);
          $this->log('CONTENT: ' . $content);
        }
      }
    }
    return '';

  }

  public function script_replace_footer($tag)
  {

    $tag = $tag[0];
    $src = '';

    if (current_user_can('manage_options') ||  self::$excludes->strInArray( $tag, self::$excludes->combineCSSExcludes() )) {
      return $tag;
    }

    if ($this->combine) {
      $is_src_set = preg_match('/href=["|\'](.*?)["|\']/', $tag, $src);
      $src = str_replace('href=', '', $src);
      $src = str_replace("'", "", $src);
      $src = $src[0];

      if ($is_src_set == 1) {

        $check = wp_http_validate_url($src);
        if ($check) {
          $content = self::$excludes->getContent($src);
        } else {
          $content = self::$excludes->getContent(get_home_url() . $src);
        }

      } else {
        $content = $tag;
        $content = preg_replace('/<style(.*?)>/', '', $content, '', $count);
        $content = preg_replace('/<\/style>/', '', $content);

        if (!$count) {
          //no href, and not a <style> tag
          return $tag;
        }
      }

      //replace relative urls
      $this->asset_url = $src;
      $content = preg_replace_callback("/url(\(((?:[^()])+)\))/i", array($this, 'rewrite_relative_url'), $content);

      //sometimes php injects a zero width space char at the start of a new script, this clears it
      $content = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $content);

      $this->combined_css_footer .= "/* SCRIPT : $src */" . PHP_EOL;
      $this->combined_css_footer .= $content . PHP_EOL;

      if ($this->dbg_combine) {
        if ($src == '') {
          $this->log('SRC: ' . $tag);
          $this->log('CONTENT: ' . $content);
        } else {
          $this->log('SRC: ' . $src);
          $this->log('CONTENT: ' . $content);
        }
      }
    }

    return '';

  }

  function rewrite_relative_url($url)
  {

    $matched_url = $url[2];
    $asset_url = $this->asset_url;
    $matched_url = str_replace('"', '', $matched_url);
    $matched_url = str_replace("'", '', $matched_url);

    $parsed_url = parse_url($asset_url);
    $path = $parsed_url['path'];
    $path = str_replace(basename($path), '', $path);
    $path = ltrim($path, '/');
    $path = rtrim($path, '/');
    $directories = explode('/', $path);

    $host = $parsed_url['host'];
    $scheme = $parsed_url['scheme'];
    $parsed_homeurl = parse_url(get_home_url());

    if (!$host) {
      //relative asset url
      $host = $parsed_homeurl['host'];
    }

    if (!$scheme) {
      //relative asset url
      $scheme = $parsed_homeurl['scheme'];
    }

    if (strpos($matched_url, $this->zone_name) !== false) {
      return $url[0];
    }

    if (strpos($matched_url, 'google') !== false || strpos($matched_url, 'gstatic') !== false || strpos($matched_url, 'typekit') !== false) {
      return $url[0];
    }

    if (strpos($matched_url, 'data:') !== false) {
      return $url[0];
    }

    $first_char = substr($matched_url, 0, 1);
    if (strpos($matched_url, 'http') === false && ctype_alpha($first_char)) {
      // No,slash.. direct file
      // Same folder
      $relativePath = implode('/', $directories) . '/';
      $matched_url_trim = ltrim($matched_url, './');
      $relativePath .= $matched_url_trim;
      $relativeUrl = $scheme . '://' . $host . '/' . $relativePath;

    } else if (strpos($matched_url, '/') === 0) {
      // Root folder
      $relativePath = '';
      $matched_url_trim = ltrim($matched_url, './');
      $relativePath .= $matched_url_trim;
      $relativeUrl = $scheme . '://' . $host . '/' . $relativePath;

    } else if (strpos($matched_url, './') === 0) {
      // Same folder
      $relativePath = implode('/', $directories) . '/';
      $matched_url_trim = ltrim($matched_url, './');
      $relativePath .= $matched_url_trim;
      $relativeUrl = $scheme . '://' . $host . '/' . $relativePath;

    } else if (strpos($matched_url, '../') === 0) {
      // Are there more directories to go back?
      $exploded_dirs = explode('../', $matched_url);
      array_pop($exploded_dirs);

      foreach ($exploded_dirs as $i => $v) {
        // Back Folder
        array_pop($directories); // Remove 1 last dir
      }
      $relativePath = implode('/', $directories) . '/';
      $matched_url_trim = ltrim($matched_url, '../');
      $relativePath .= $matched_url_trim;

      $relativeUrl = $scheme . '://' . $host . '/' . $relativePath;

    } else {

      // Regular path
      if (strpos($matched_url, 'http://') !== false || strpos($matched_url, 'https://') !== false) {
        // Regular URL
        $replace_url = $matched_url;
      } else {
        // Missing http/s ?
        $replace_url = ltrim($matched_url, '//');
        $replace_url = $scheme . '://' . $replace_url;
      }

      if (strpos($matched_url, '.jpg') !== false || strpos($matched_url, '.png') !== false || strpos($matched_url, '.gif') !== false || strpos($matched_url, '.svg') !== false || strpos($matched_url, '.jpeg') !== false || strpos($matched_url, '.webp') !== false) {
        // Image, put on CDN
        #$relativeUrl = $replace_url . $matched_url;
        $relativeUrl = $replace_url;

      } else if (strpos($matched_url, '.woff') !== false || strpos($matched_url, '.woff2') !== false || strpos($matched_url, '.ttf') !== false || strpos($matched_url, '.eot') !== false) {

        // Font file, put on site
        #$relativeUrl = $replace_url . $matched_url;
        $relativeUrl = $replace_url;
      }
    }

    if (strpos($matched_url, '.eot') !== false || strpos($matched_url, '.woff') !== false || strpos($matched_url, '.woff2') !== false || strpos($matched_url, '.ttf') !== false) {
      $relativeUrl = 'url("' . $relativeUrl . '")';
    } else {
      $relativeUrl = 'url("https://' . $this->zone_name . '/minify:false/asset:' . $relativeUrl . '")';
    }

    return $relativeUrl;

  }

  function log($msg)
  {

    file_put_contents($this->dbg_file, $msg . PHP_EOL . PHP_EOL, FILE_APPEND);

  }

}