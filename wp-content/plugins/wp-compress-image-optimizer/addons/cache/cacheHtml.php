<?php

class wps_cacheHtml
{

  private $siteUrl;
  private $urlKey;
  private $cacheExists = false;
  private $cachedHtml = '';
  
  public function __construct()
  {
    $this->siteUrl = site_url();
    $this->url_key_class = new wps_ic_url_key();
    $this->urlKey = $this->url_key_class->setup($_SERVER['REQUEST_URI'],true);
  }

  /**
   * FrontEnd Editors Detection for various page builders
   * @return bool
   */
  public static function isPageBuilder()
  {
    $page_builders = [
      'run_compress', //wpc
      'run_restore', //wpc
      'elementor-preview', //elementor
      'fl_builder', //beaver builder
      'et_fb', //divi
      'preview', //WP Preview
      'builder', //builder
      'brizy', //brizy
      'fb-edit', //avada
      'bricks', //bricks
      'ct_template', //ct_template
      'ct_builder', //ct_builder
      'tatsu', //tatsu
      'trp-edit-translation', //thrive
      'brizy-edit-iframe', //brizy
      'ct_builder', //oxygen
      'livecomposer_editor', //livecomposer
      'tatsu', //tatsu
      'tatsu-header', //tatsu-header
      'tatsu-footer', //tatsu-footer
      'tve' //thrive
    ];

    if (!empty($_GET['dbg_pagebuilder'])) {
      var_dump($_GET);
      die();
    }

    foreach ($page_builders as $page_builder) {
      if (isset($_GET[$page_builder])) {
        return true;
      }
    }

    return false;
  }


  /**
   * FrontEnd Editors Detection for various page builders
   * @return bool
   */
  public static function isPageBuilderFE()
  {
    if (class_exists('BT_BB_Root')) {
      if (is_user_logged_in() && !is_admin()) {
        return true;
      }
    }

    return false;
  }



  public static function isFEBuilder() {
    if (!empty($_GET['trp-edit-translation']) || !empty($_GET['elementor-preview']) || !empty($_GET['tatsu']) || !empty($_GET['preview']) || !empty($_GET['PageSpeed']) || !empty($_GET['tve']) || !empty($_GET['et_fb']) || (!empty($_GET['fl_builder']) || isset($_GET['fl_builder'])) || !empty($_GET['ct_builder']) || !empty($_GET['fb-edit']) || !empty($_GET['bricks']) || !empty($_GET['brizy-edit-iframe']) || !empty($_GET['brizy-edit']) || (!empty($_SERVER['SCRIPT_URL']) && $_SERVER['SCRIPT_URL'] == "/wp-admin/customize.php") || (!empty($_GET['page']) && $_GET['page'] == 'livecomposer_editor')) {
      return true;
    } else {
      return false;
    }
  }


  public function fetchCache($prefix) {
    // TODO: Is causing fatal errors because it's loading too early and does not have info about logged in user
    #if ($this->userLoggedIn()) {
    #return;
    #}

    // Is cached url?
    $this->urlKey = $this->url_key_class->setup($_SERVER['REQUEST_URI'], true);
    // Check if cache exists
    $isCached = $this->cacheExists($prefix);

    if ($isCached) {
      // Check if cached page expired
      $isCacheExpired = $this->cacheExpired($prefix);
      if ( ! $isCacheExpired) {
        // Fetch the cache
        $this->cachedHtml = $this->getCache($prefix);
        if ( ! empty($this->cachedHtml)) {

          header('wpc-served: true');
          return $this->cachedHtml;
        }
      }
    }

    return false;
  }

  
  public function advancedCache($prefix = '')
  {

    // TODO: Is causing fatal errors because it's loading too early and does not have info about logged in user
    #if ($this->userLoggedIn()) {
      #return;
    #}

    // Is cached url?
    $this->urlKey = $this->url_key_class->setup($_SERVER['REQUEST_URI'], true);
    // Check if cache exists
    $isCached = $this->cacheExists($prefix);


    if (!empty($_GET['dbg_cache'])) {
      var_dump('url_key:' .$this->urlKey);
      var_dump('is prefix ');
      var_dump($prefix);
      var_dump('is cached');
      var_dump($isCached);
      var_dump('is expired ' . print_r($this->cacheExpired($prefix), true));
      var_dump('get cachfe ' . print_r($this->getCache($prefix), true));

    }

    if ($isCached) {
      // Check if cached page expired
      $isCacheExpired = $this->cacheExpired($prefix);
      if ( ! $isCacheExpired) {
        // Fetch the cache
        $this->cachedHtml = $this->getCache($prefix);
        if ( ! empty($this->cachedHtml)) {
          add_action('send_headers', function () {
            header('wpc-served: true');
            echo $this->cachedHtml;
            die();
          });
        }
      }
    }
  }


  public function cacheExpired($prefix = '')
  {
    if (!empty($prefix)) {
      $prefix = $prefix.'_';
    }
    
    $cacheFile = WPS_IC_CACHE.''.$prefix.$this->urlKey;

    if ( ! file_exists($cacheFile)) {
      return true;
    }
    
    $fileModifiedTime = filemtime($cacheFile);
    if ($fileModifiedTime < time() - WPC_IC_CACHE_EXPIRE) {
      unlink($cacheFile);
      
      return true;
    }
    else {
      return false;
    }
  }
  
  
  public function cacheExists($prefix = '')
  {
    if ( ! empty($_GET['disable_cache'])) {
      return false;
    }
    
    if (!empty($prefix)) {
      $prefix = $prefix.'_';
    }

    if (file_exists(WPS_IC_CACHE.''.$prefix.$this->urlKey)) {
      return true;
    }
    else {
      return false;
    }
  }
  
  
  /**
   * Just verify it's not some page test as we don't want those to cache HTML
   * @return void
   */
  public function pageTest()
  {
    return false;
  }
  
  
  public function saveCache($buffer, $prefix = '')
  {
    if ( ! empty($_GET['disable_cache'])) {
      return true;
    }

    if (self::isFEBuilder() || self::isPageBuilder() ||self::isPageBuilderFE()) {
      return true;
    }

  
    if (!empty($prefix)) {
      $prefix = $prefix.'_';
    }
    
//    if ($this->userLoggedIn()) {
//      // Nothing
//    }
//    else {
      $fp = fopen(WPS_IC_CACHE.''.$prefix.$this->urlKey, 'w+');
      fwrite($fp, $buffer);
      fclose($fp);
//    }
    
    return $buffer;
  }
  
  
  public function getCache($prefix = '')
  {
    if (!empty($prefix)) {
      $prefix = $prefix.'_';
    }
    
    return file_get_contents(WPS_IC_CACHE.''.$prefix.$this->urlKey);
  }


  public function removeCacheFiles($post_id)
  {
    $this->urlKey = $this->url_key_class->setup(get_permalink($post_id), true);

    if ($this->cacheExists()) {
      unlink(WPS_IC_CACHE . '/' . $this->urlKey);
    }

    if ($this->cacheExists('mobile')) {
      unlink(WPS_IC_CACHE . '/mobile_' . $this->urlKey);
    }
  }
  
  
  public function userLoggedIn()
  {
    $user = wp_get_current_user();
    if ( ! $user || $user->data->ID == 0) {
      return false;
    }
    else {
      return true;
    }
  }
  
  
}