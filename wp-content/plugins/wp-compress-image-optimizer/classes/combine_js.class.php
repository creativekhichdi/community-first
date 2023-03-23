<?php

include_once WPS_IC_DIR . 'traits/url_key.php';

class wps_ic_combine_js
{

  public static $excludes;
  public static $rewrite;
  public $combine;
  public $combined_head_file;
  public $combined_footer_file;
  /**
   * @var void
   */

  public function __construct()
  {

    self::$excludes = new wps_ic_excludes();
    self::$rewrite = new wps_cdn_rewrite();
    $this->url_key_class = new wps_ic_url_key();
    $this->urlKey = $this->url_key_class->urlKey;

    $rand = substr(md5(microtime()), rand(0, 26), 5);
    $this->combine = 1;
    $this->combined_js_head = '';
    $this->combined_js_footer = '';
    $this->combined_head_file = WPS_IC_CACHE . 'wps_combined_head_' . $this->urlKey . '.js';
    $this->combined_footer_file = WPS_IC_CACHE . 'wps_combined_footer_' . $this->urlKey . '.js';
    $this->combined_head_url = WPS_IC_CACHE_URL . 'wps_combined_head_' . $this->urlKey . '.js?rand=' . $rand;
    $this->combined_footer_url = WPS_IC_CACHE_URL . 'wps_combined_footer_' . $this->urlKey . '.js?rand=' . $rand;
    $this->settings = get_option(WPS_IC_SETTINGS);

    $this->all_excludes = self::$excludes->combineJSExcludes();

    if (file_exists($this->combined_head_file) && file_exists($this->combined_footer_file)) {
      $this->combine = 0;
    }

    if (!empty($this->settings['delay-js']) && $this->settings['delay-js'] == '1') {
      //If it shouldn't be delayed, it shouldn't be combined
      $this->all_excludes = array_merge($this->all_excludes, self::$excludes->delayJSExcludes());
    }

    $this->dbg_combine = 0;
    if (isset($_GET['dbg']) && $_GET['dbg'] == 'combine_js') {
      $this->dbg_combine = 1;
      $this->combine = 1;
      $this->dbg_file = WPS_IC_UPLOADS_DIR . '/combine_js_log.txt';
      $this->log('JS Combine Started');
      $this->log('Excludes: ' . print_r($this->all_excludes));
    }

  }

  public function combine_head($head)
  {
    $head = $head[0];
    $head = preg_replace_callback('/<script\b[^>]*>(.*?)<\/script>/si', array($this, 'script_replace_head'), $head);

    if (isset($_GET['dbg']) && $_GET['dbg'] == 'combine_all') {
      $this->log('COMBINED FOOTER SCRIPTS: ' . $this->combined_js_footer);
    }

    if ($this->combine && $this->combined_js_head == '') {
      return $head;
    }

    if ($this->combine) {
      file_put_contents($this->combined_head_file, $this->combined_js_head);
    }

    $head = preg_replace('/<\/head>/', '<script type="text/javascript" src="' . self::$rewrite->adjust_src_url($this->combined_head_url) . '"></script></head>', $head);
    return $head;
  }

  public function combine_footer($footer)
  {
    $footer = $footer[0];
    $footer = preg_replace_callback('/<script\b[^>]*>(.*?)<\/script>/si', array($this, 'script_replace_footer'), $footer);

    if (isset($_GET['dbg']) && $_GET['dbg'] == 'combine_all') {
      $this->log('COMBINED FOOTER SCRIPTS: ' . $this->combined_js_footer);
    }

    if ($this->combine && $this->combined_js_footer == '') {
      return $footer;
    }

    if ($this->combine) {
      file_put_contents($this->combined_footer_file, $this->combined_js_footer);
    }
    $footer = preg_replace('/<\/body>/', '<script type="text/javascript" src="' . self::$rewrite->adjust_src_url($this->combined_footer_url) . '"></script></body>', $footer);
    return $footer;
  }

  public function script_replace_head($tag)
  {

    $tag = $tag[0];
    $original_tag = $tag;
    $src = '';

    if (self::$excludes->strInArray( $tag, $this->all_excludes ) || current_user_can('manage_options')) {
      return $original_tag;
    }

    if ($this->combine) {

      //get only the <script ...> and check for src
      preg_match('/<script(.*?)>/si', $tag, $tag_start);
      $tag_start = $tag_start[0];
      $is_src_set = preg_match('/src=["|\'](.*?)["|\']/si', $tag_start, $src);

      //clean up src
      $src = str_replace('src=', '', $src);
      $src = str_replace(["'", '"'], "", $src);
      $src = $src[0];

      $this->combined_js_head .= "/* SCRIPT : $src */" . PHP_EOL;

      if ($is_src_set == 1) {

        $check = wp_http_validate_url($src);
        if ($check) {
          $content = self::$excludes->getContent($src);
        } else {
          $content = self::$excludes->getContent(get_home_url() . $src);

        }

      } else {
        $content = $tag;
        $content = preg_replace('/<script(.*?)>/', '', $content);
        $content = preg_replace('/<\/script>/', '', $content);

      }

      if ($this->is_problematic($content)) {
        $this->log('PROBLEMATIC');
      }

      $content = $this->parse_content($content);

      $this->combined_js_head .= $content . PHP_EOL;

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
    $original_tag = $tag;
    $src = '';

    if (self::$excludes->strInArray( $tag, $this->all_excludes ) || current_user_can('manage_options')) {
      return $original_tag;
    }

    if ($this->combine) {
      //get only the <script ...> and check for src
      preg_match('/<script(.*?)>/si', $tag, $tag_start);
      $tag_start = $tag_start[0];
      $is_src_set = preg_match('/src=["|\'](.*?)["|\']/si', $tag_start, $src);

      //clean up src
      $src = str_replace('src=', '', $src);
      $src = str_replace(["'", '"'], "", $src);
      $src = $src[0];

      $this->combined_js_footer .= "/* SCRIPT : $src */" . PHP_EOL;

      if ($is_src_set == 1) {

        $check = wp_http_validate_url($src);
        if ($check) {
          $content = self::$excludes->getContent($src);
        } else {
          $content = self::$excludes->getContent(get_home_url() . $src);
        }

      } else {
        $content = $tag;
        $content = preg_replace('/<script(.*?)>/', '', $content);
        $content = preg_replace('/<\/script>/', '', $content);

      }

      if ($this->is_problematic($content)) {
        $this->log('PROBLEMATIC');
      }

      $content = $this->parse_content($content);

      $this->combined_js_footer .= $content . PHP_EOL;


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

  function parse_content($content)
  {

    //sometimes php injects a zero width space char at the start of a new script, this clears it
    $content = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $content);

    return $content;

  }

  function is_problematic($content)
  {

    if (strpos($content, '<') === 0) {
      return true;
    }

    return false;

  }

  function log($msg)
  {

    file_put_contents($this->dbg_file, $msg . PHP_EOL . PHP_EOL, FILE_APPEND);

  }

}