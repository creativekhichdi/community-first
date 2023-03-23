<?php

include_once WPS_IC_DIR . 'traits/url_key.php';

class wps_criticalCss
{

  static $API_URL = WPS_IC_CRITICAL_API_URL;
  static $API_ASSETS_URL = WPS_IC_CRITICAL_API_ASSETS_URL;
  public static $url;
  public $urlKey;
  private $siteUrl;
  private $trp_active;
  private static $maxRetries = 5;

  public function __construct($url = '')
  {
    if (empty($url)) {
      $url = $_SERVER['REQUEST_URI'];
    }

    self::$url = $url;

    if (!empty($_GET['debugCritical_replace'])) {
      $url = explode('?', $url);
      $url = $url[0];
    }

    $this->serverRequest = $url;

    $this->url_key_class = new wps_ic_url_key();
    $this->urlKey = $this->url_key_class->setup($url, true);
    $this->createDirectory();

  }

  public function createDirectory()
  {
    if (!file_exists(WPS_IC_CRITICAL)) {
      mkdir(WPS_IC_CRITICAL);
    }
  }


  public function maxRetries()
  {
    $running = get_transient('wpc_critical_ajax_' . md5(self::$url));
    if ($running && $running>=self::$maxRetries) {
      return true;
    } else {
      return false;
    }
  }


  public function criticalRunning()
  {
    $running = get_transient('wpc_critical_ajax_' . md5(self::$url));
    if (empty($running) || !$running) {
      return false;
    } else {
      return true;
    }
  }

  public function getCriticalPages()
  {
    $pages = array();

    $posts = get_posts(['posts_per_page' => '-1', 'post_type' => 'page']);
    $totalPosts = count($posts);

    $whatToShow = get_option('show_on_front');
    $homePage = get_option('page_on_front');
    $postsPage = get_option('page_on_posts');

    if ($whatToShow == 'page') {
      if (!empty($homePage)) {
        $post = get_post($homePage);
        $linkFull = get_permalink($post->ID);

        $link = $this->url_key_class->removeUrl($linkFull);
        $link = $this->url_key_class->createUrlKey($link);
        if (empty($link)) {
          $link = 'index';
        }

        $criticalAssets = json_decode(get_post_meta($post->ID, 'wpc_critical_assets', true), true);

        $permalink = get_permalink($post->ID);
        #$key = $this->setup($permalink, true);
        #if ($this->criticalExists($key)) {

        if (file_exists(WPS_IC_CRITICAL . '' . $link . '_critical.css')) {
          $pages[$post->ID]['css'] = 'Done';
        } else {
          $pages[$post->ID]['css'] = 'Not Generated';
        }

        if (!empty($criticalAssets)) {
          $pages[$post->ID]['assets']['img'] = $criticalAssets['img'];
          $pages[$post->ID]['assets']['js'] = $criticalAssets['js'];
          $pages[$post->ID]['assets']['css'] = $criticalAssets['css'];
        } else {
          $pages[$post->ID]['assets']['img'] = '0';
          $pages[$post->ID]['assets']['js'] = '0';
          $pages[$post->ID]['assets']['css'] = '0';
        }

        $pages[$post->ID]['title'] = $post->post_title;
        $pages[$post->ID]['link'] = $permalink;
        $pages[$post->ID]['pageRequest'] = $link;
      }
      if (!empty($postsPage)) {
        $post = get_post($postsPage);
        $linkFull = get_permalink($post->ID);

        $link = $this->url_key_class->removeUrl($linkFull);
        $link = $this->url_key_class->createUrlKey($link);
        if (empty($this->urlKey)) {
          $link = 'index';
        }

        $criticalAssets = json_decode(get_post_meta($post->ID, 'wpc_critical_assets', true), true);

        $permalink = get_permalink($post->ID);
        #$key = $this->setup($permalink, true);
        #if ($this->criticalExists($key)) {

        if (file_exists(WPS_IC_CRITICAL . '' . $link . '_critical.css')) {
          $pages[$post->ID]['css'] = 'Done';
        } else {
          $pages[$post->ID]['css'] = 'Not Generated';
        }

        if (!empty($criticalAssets)) {
          $pages[$post->ID]['assets']['img'] = $criticalAssets['img'];
          $pages[$post->ID]['assets']['js'] = $criticalAssets['js'];
          $pages[$post->ID]['assets']['css'] = $criticalAssets['css'];
        } else {
          $pages[$post->ID]['assets']['img'] = '0';
          $pages[$post->ID]['assets']['js'] = '0';
          $pages[$post->ID]['assets']['css'] = '0';
        }

        $pages[$post->ID]['title'] = $post->post_title;
        $pages[$post->ID]['link'] = $permalink;
        $pages[$post->ID]['pageRequest'] = $link;
      }
    } else {
      $siteUrl = home_url();
      if (!empty($siteUrl)) {
        $linkFull = $siteUrl;
        $link = $this->url_key_class->removeUrl($linkFull);
        $link = $this->url_key_class->createUrlKey($link);
        if (empty($this->urlKey)) {
          $link = 'index';
        }

        $criticalAssets = json_decode(get_option('wpc_critical_assets_home'), true);

        if (file_exists(WPS_IC_CRITICAL . '' . $link . '_critical.css')) {
          $pages['home']['css'] = 'Done';
        } else {
          $pages['home']['css'] = 'Not Generated';
        }

        if (!empty($criticalAssets)) {
          $pages['home']['assets']['img'] = $criticalAssets['img'];
          $pages['home']['assets']['js'] = $criticalAssets['js'];
          $pages['home']['assets']['css'] = $criticalAssets['css'];
        } else {
          $pages['home']['assets']['img'] = '0';
          $pages['home']['assets']['js'] = '0';
          $pages['home']['assets']['css'] = '0';
        }

        $pages['home']['title'] = 'Home Page';
        $pages['home']['link'] = $linkFull;
        $pages['home']['pageRequest'] = $link;
      }
    }

    if (!empty($posts)) {
      foreach ($posts as $post) {
        $linkFull = get_permalink($post->ID);

        if (rtrim($linkFull, '/') == rtrim(home_url())) {
          $link = 'index';
        } else {
          $link = $this->url_key_class->removeUrl($linkFull);
          $link = $this->url_key_class->createUrlKey($link);
        }

        $criticalAssets = json_decode(get_post_meta($post->ID, 'wpc_critical_assets', true), true);

        $permalink = get_permalink($post->ID);
        #$key = $this->setup($permalink, true);
        #if ($this->criticalExists($key)) {

        if (file_exists(WPS_IC_CRITICAL . '' . $link . '_critical.css')) {
          $pages[$post->ID]['css'] = 'Done';
        } else {
          $pages[$post->ID]['css'] = 'Not Generated';
        }

        if (!empty($criticalAssets)) {
          $pages[$post->ID]['assets']['img'] = $criticalAssets['img'];
          $pages[$post->ID]['assets']['js'] = $criticalAssets['js'];
          $pages[$post->ID]['assets']['css'] = $criticalAssets['css'];
        } else {
          $pages[$post->ID]['assets']['img'] = '0';
          $pages[$post->ID]['assets']['js'] = '0';
          $pages[$post->ID]['assets']['css'] = '0';
        }

        if ($homePage == $post->ID) {
          $pageTitle = 'Home Page';
        } elseif ($postsPage == $post->ID) {
          $pageTitle = 'Posts Page';
        } else {
          $pageTitle = $post->post_title;
        }

        $pages[$post->ID]['title'] = $pageTitle;
        $pages[$post->ID]['link'] = $permalink;
        $pages[$post->ID]['pageRequest'] = $link;
      }
    }

    return $pages;
  }

  public function sendCriticalUrl($url = '', $postID = 0)
  {
    $type = 'meta';


    if ($postID === 'home') {
      $url = home_url();
      $type = 'option';
    } elseif (!$postID || $postID == 0) {

      $homePage = get_option('page_on_front');
      $blogPage = get_option('page_for_posts');

      if (!$homePage) {
        $post['post_name'] = 'Home';
        $post = (object)$post;
        $url = site_url();
      } else {
        $post = get_post($homePage);
        $url = get_permalink($homePage);
      }

      $pages[$post->post_name] = $url;

      if ($blogPage !== 0 && $blogPage !== '0' && $blogPage !== $homePage) {
        $post = get_post($blogPage);
        $url = get_permalink($blogPage);
      }

      $pages[$post->post_name] = $url;
    } else {
      $post = get_post($postID);
      $url = get_permalink($postID);
      $pages[$post->post_name] = $url;
    }

    $key = $this->url_key_class->setup($url, true);

//		if ( ! $force ) {
//			$exists = $this->criticalExists( $key );
//			if ( ! empty( $exists ) ) {
//				return;
//			}
//		}

    $args = ['v7' => 'true', 'pages' => json_encode($pages), 'apikey' => get_option(WPS_IC_OPTIONS)['api_key']];

    $call = wp_remote_post(self::$API_URL, ['timeout' => 300, 'body' => $args]);
    $code = wp_remote_retrieve_response_code($call);

    if ($code == 200) {
      $body = wp_remote_retrieve_body($call);
      if (!empty($body) && strlen($body) > 128) {
        $this->saveCriticalCss($this->url_key_class->setup($url, true), $body, $type);
      }
    }
  }

  public function saveCriticalCss_fromBackground($urlKey, $desktop, $mobile, $type = 'meta')
  {
    if ( ! function_exists('download_url')) {
      require_once(ABSPATH . "wp-admin" . '/includes/image.php');
      require_once(ABSPATH . "wp-admin" . '/includes/file.php');
      require_once(ABSPATH . "wp-admin" . '/includes/media.php');
    }

    $cache = new wps_ic_cache_integrations();
    $cache::purgeAll($urlKey, true);

    $desktop = download_url($desktop);
    $mobile = download_url($mobile);

    #$fp = fopen(WPS_IC_CRITICAL . '' . $urlKey . '_critical_desktop.css', 'w+');
    #fwrite($fp, $desktop);
    #fclose($fp);

    copy($desktop, WPS_IC_CRITICAL . '' . $urlKey . '_critical_desktop.css');

    #$fp = fopen(WPS_IC_CRITICAL . '' . $urlKey . '_critical_mobile.css', 'w+');
    #fwrite($fp, $mobile);
    #fclose($fp);

    copy($mobile, WPS_IC_CRITICAL . '' . $urlKey . '_critical_mobile.css');

    if (file_exists(WPS_IC_CRITICAL . '' . $urlKey . '_critical_desktop.css') && filesize(WPS_IC_CRITICAL . '' . $urlKey . '_critical_desktop.css') > 5) {
      if ($type == 'meta') {
        update_post_meta($urlKey, 'wpc_critical_css', WPS_IC_CRITICAL . '' . $urlKey . '_critical.css');
      } else {
        update_option('wps_critical_css_' . $urlKey, WPS_IC_CRITICAL . '' . $urlKey . '_critical.css');
      }
    }
  }


  public function saveCriticalCss($urlKey, $CSS, $type = 'meta')
  {
    $cache = new wps_ic_cache_integrations();
    $cache::purgeAll($urlKey);

    if (empty($CSS)) return false;

    $json = json_decode($CSS, true);

    $desktop = download_url($json['desktop']);
    $mobile = download_url($json['mobile']);

    $fp = fopen(WPS_IC_CRITICAL . '' . $urlKey . '_critical_desktop.css', 'w+');
    fwrite($fp, file_get_contents($desktop));
    fclose($fp);

    $fp = fopen(WPS_IC_CRITICAL . '' . $urlKey . '_critical_mobile.css', 'w+');
    fwrite($fp, file_get_contents($mobile));
    fclose($fp);

    if (file_exists(WPS_IC_CRITICAL . '' . $urlKey . '_critical.css') && filesize(WPS_IC_CRITICAL . '' . $urlKey . '_critical.css') > 5) {
      if ($type == 'meta') {
        update_post_meta($urlKey, 'wpc_critical_css', WPS_IC_CRITICAL . '' . $urlKey . '_critical.css');
      } else {
        update_option('wps_critical_css_' . $urlKey, WPS_IC_CRITICAL . '' . $urlKey . '_critical.css');
      }
    }
  }

  public function sendCriticalUrlNonBlocking($url = '', $postID = 0)
  {
    $type = 'meta';

    if (!empty($url)) {
      $key = $this->url_key_class->setup($url, true);
      $pages[$key] = $url;
    } else {
      if ($postID === 'home') {
        $url = home_url();
        $type = 'option';
      } elseif (!$postID || $postID == 0) {

        $homePage = get_option('page_on_front');
        $blogPage = get_option('page_for_posts');

        if (!$homePage) {
          $post['post_name'] = 'Home';
          $post = (object)$post;
          $url = site_url();
        } else {
          $post = get_post($homePage);
          $url = get_permalink($homePage);
        }

        $pages[$post->post_name] = $url;

        if ($blogPage !== 0 && $blogPage !== '0' && $blogPage !== $homePage) {
          $post = get_post($blogPage);
          $url = get_permalink($blogPage);
        }

        $pages[$post->post_name] = $url;
      } else {
        $post = get_post($postID);
        $url = get_permalink($postID);
        $pages[$post->post_name] = $url;
      }
    }


//		if ( ! $force ) {
//			$exists = $this->criticalExists( $key );
//			if ( ! empty( $exists ) ) {
//				return;
//			}
//		}

    $args = ['pages' => json_encode($pages), 'apikey' => get_option(WPS_IC_OPTIONS)['api_key'], 'background' => 'true'];
    $call = wp_remote_post(self::$API_URL, ['timeout' => 2, 'blocking' => false, 'body' => $args]);
    $body = wp_remote_retrieve_body($call);

  }

  public function criticalExistsAjax($url = '')
  {

    if (!empty($url)) {
      $this->urlKey = $this->url_key_class->setup($url, true);
    }

    if (file_exists(WPS_IC_CRITICAL . '' . $this->urlKey . '_critical_desktop.css')) {
      return WPS_IC_CRITICAL . '' . $this->urlKey . '_critical_desktop.css';
    } else {
      return false;
    }
  }

  public function sendCriticalUrlGetAssets($url = '', $postID = 0)
  {
    global $post;
    $type = 'post_meta';

    if ($postID === 'home') {
      $url = home_url();
      $type = 'option';
    } elseif (!$postID || $postID == 0) {

      $homePage = get_option('page_on_front');
      $blogPage = get_option('page_for_posts');

      if (!$homePage) {
        $post['post_name'] = 'Home';
        $post = (object)$post;
        $url = site_url();
      } else {
        $post = get_post($homePage);
        $url = get_permalink($homePage);
      }

      if ($blogPage !== 0 && $blogPage !== '0' && $blogPage !== $homePage) {
        $post = get_post($blogPage);
        $url = get_permalink($blogPage);
      }
    } else {
      $post = get_post($postID);
      $url = get_permalink($postID);
    }


    $args = ['url' => $url];
    $call = wp_remote_post(self::$API_ASSETS_URL, ['timeout' => 300, 'body' => $args]);

    $body = wp_remote_retrieve_body($call);
    if (!empty($body)) {

      if ($type == 'post_meta') {
        update_post_meta($post->ID, 'wpc_critical_assets', $body);
      } else {
        update_option('wpc_critical_assets_home', $body);
      }

      return $body;
    } else {

      if ($type == 'post_meta') {
        update_post_meta($post->ID, 'wpc_critical_assets', 'unable');
      } else {
        update_option('wpc_critical_assets_home', 'unable');
      }

      return json_encode(['img' => 0, 'js' => 0, 'css' => 0]);
    }
  }

  public function generate_critical_cron()
  {
    $queue = get_option('critical_generator_cron');

    if ($queue) {
      foreach ($queue as $key => $url) {
        $this->serverRequest = $url;
        $this->urlKey = $key;
        if ($this->criticalExists()) {
          unset($queue[$key]);
          update_option('critical_generator_cron', $queue);
          continue;
        }

        $this->generateCriticalAjax();
        unset($queue[$key]);
        update_option('critical_generator_cron', $queue);
      }
    }
  }

  public function criticalExists()
  {
    if (!empty($_GET['debugCritical_replace'])) {
      return array(WPS_IC_CRITICAL, $this->urlKey, 'file' => WPS_IC_CRITICAL . '' . $this->urlKey . '_critical_desktop.css', 'exists' => file_exists(WPS_IC_CRITICAL . '' . $this->urlKey . '_critical_desktop.css'));
    }

    $return = array();

    if (file_exists(WPS_IC_CRITICAL . '' . $this->urlKey . '_critical_desktop.css')) {
      $return['desktop'] = WPS_IC_CRITICAL_URL . '' . $this->urlKey . '_critical_desktop.css';
    }

    if (file_exists(WPS_IC_CRITICAL . '' . $this->urlKey . '_critical_mobile.css')) {
      $return['mobile'] = WPS_IC_CRITICAL_URL . '' . $this->urlKey . '_critical_mobile.css';
    }

    if (!isset($return['mobile']) && !isset($return['desktop'])) {
      return false;
    }

    return $return;
  }

  public function generateCriticalAjax()
  {
    $args = ['pages' => json_encode(['ajax' => $this->serverRequest])];

    $call = wp_remote_post(self::$API_URL, ['timeout' => 300, 'body' => $args]);

    $body = wp_remote_retrieve_body($call);

    if (!empty($body) && strlen($body) > 128) {
      $this->saveCriticalCss($this->urlKey, $body);
    }
  }

}