<?php

class wps_rewriteLogic
{

  public static $settings;
  public static $options;
  public static $siteUrl;
  public static $homeUrl;
  public static $zoneName;
  public static $randomHash;
  public static $siteUrlScheme;
  public static $excludedList;
  public static $lazyExcludeList;
  public static $defaultExcludedList;
  public static $externalUrlEnabled;
  public static $externalUrlExcluded;
  public static $st;
  public static $emojiRemove;
  public static $preloaderAPI;
  public static $replaceAllLinks;

  // CSS / JS Variables
  public static $fonts;
  public static $css;
  public static $cssMinify;
  public static $cssImgUrl;
  public static $js;
  public static $jsMinify;

  // Integrations
  public static $perfMattersActive;
  public static $brizyActive;
  public static $brizyCache;
  public static $revSlider;

  // Lazy Tags
  public static $lazyLoadedImages;
  public static $lazyLoadedImagesLimit;
  public static $loadedImagesSt;
  public static $loadedImagesStLimit;
  public static $lazyOverride;
  public static $delayJsOverride;
  public static $deferJsOverride;

  // Api Params
  public static $apiUrl;
  public static $exif;
  public static $webp;
  public static $isRetina;
  public static $retinaEnabled;
  public static $adaptiveEnabled;
  public static $webpEnabled;
  public static $lazyEnabled;
  public static $removeSrcset;
  public static $isMobile;

  public static $removedCSS;
  public static $excludes;
  public static $excludes_class;
  public static $isAjax;


  public function __construct()
  {
    self::$settings = get_option(WPS_IC_SETTINGS);
    self::$options = get_option(WPS_IC_OPTIONS);
    self::$randomHash = 0;
    self::$preloaderAPI = 0;
    self::$isMobile = false;

    self::$isAjax = (function_exists("wp_doing_ajax") && wp_doing_ajax()) || (defined('DOING_AJAX') && DOING_AJAX);

    if (!self::$isAjax && !empty($_POST)) {
      foreach ($_POST as $key => $value) {
        if (strpos($key, 'ajax') !== false) {
          self::$isAjax = true;
          break;
        }
      }
    }

    // Lazy Limits
    self::$lazyLoadedImages = 0;
    self::$lazyLoadedImagesLimit = 6;

    self::$excludes_class = new wps_ic_excludes();
    self::$excludes = get_option('wpc-excludes');

    /**
     * self::$isAjax was required for Ajax Filtering to work in Precommerce
     */
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'PreloaderAPI') !== false || !empty($_GET['dbg_preload'])) {
      self::$lazyLoadedImagesLimit = 9999;
      self::$preloaderAPI = 1;
      self::$lazyEnabled = 0;
      self::$adaptiveEnabled = 0;
    }

    self::$loadedImagesSt = 0;
    self::$loadedImagesStLimit = 6;

    $this->setupSiteUrl();

    $this->setupExcludes();
    $this->setupApiParams();


    if ($this->isMobile()) {
      $this->setMobile();
    }

    $this->removeEmoji();
    $this->revSliderActive();
    $this->perfMatters();
    $this->Brizy();

    self::$externalUrlEnabled = 'false';

    // External URL Enabled?
    if (!empty(self::$settings['external-url'])) {
      self::$externalUrlEnabled = self::$settings['external-url'];
    }
  }

  public function setupSiteUrl()
  {
    if (!is_multisite()) {
      self::$siteUrl = site_url();
      self::$homeUrl = home_url();
    } else {
      $current_blog_id = get_current_blog_id();
      switch_to_blog($current_blog_id);

      self::$siteUrl = network_site_url();
      self::$homeUrl = home_url();
    }

    $custom_cname = get_option('ic_custom_cname');
    if (empty($custom_cname) || !$custom_cname) {
      self::$zoneName = get_option('ic_cdn_zone_name');
    } else {
      self::$zoneName = $custom_cname;
    }

    self::$siteUrlScheme = parse_url(self::$siteUrl, PHP_URL_SCHEME);
  }

  public function setupExcludes()
  {
    self::$defaultExcludedList = ['redditstatic', 'ai-uncode', 'gtm', 'instagram.com', 'fbcdn.net', 'twitter', 'google', 'coinbase', 'cookie', 'schema', 'recaptcha', 'data:image'];

    self::$lazyExcludeList = get_option('wpc-ic-lazy-exclude');
    self::$excludedList = get_option('wpc-ic-external-url-exclude');

    if (!is_array(self::$excludedList)) {
      self::$externalUrlExcluded = explode("\n", self::$excludedList);
    } else {
      self::$externalUrlExcluded = self::$excludedList;
    }
  }

  public function setupApiParams()
  {
    if (self::is_st()) {
      $this::$st = '1';
    } else {
      $this::$st = '0';
    }

    self::$css = self::$settings['css'];
    self::$cssImgUrl = self::$settings['css_image_urls'];
    self::$cssMinify = self::$settings['css_minify'];
    self::$js = self::$settings['js'];
    self::$jsMinify = self::$settings['js_minify'];
    self::$emojiRemove = self::$settings['emoji-remove'];
    self::$exif = self::$settings['preserve_exif'];

    if (isset(self::$settings['fonts']) && !empty(self::$settings['fonts'])) {
      self::$fonts = self::$settings['fonts'];
    } else {
      self::$fonts = '0';
    }

    self::$isRetina = '0';
    self::$webp = '0';
    self::$externalUrlEnabled = 'false';

    self::$removeSrcset = self::$settings['remove-srcset'];
    self::$lazyEnabled = self::$settings['lazy'];
    self::$adaptiveEnabled = self::$settings['generate_adaptive'];
    self::$webpEnabled = self::$settings['generate_webp'];
    self::$retinaEnabled = self::$settings['retina'];
    self::$replaceAllLinks = self::$settings['replace-all-link'];

    if (strpos($_SERVER['HTTP_USER_AGENT'], 'PreloaderAPI') !== false || !empty($_GET['dbg_preload'])) {
      self::$lazyLoadedImagesLimit = 9999;
      self::$preloaderAPI = 1;
      self::$lazyEnabled = 0;
      self::$adaptiveEnabled = 0;
    }

    if (!empty($_GET['disableLazy'])) {
      self::$lazyEnabled = '0';
    }

    //
    if (!empty(self::$webpEnabled) && self::$webpEnabled == '1') {
      self::$webp = '1';
    } else {
      self::$webp = '0';
    }

    if (!empty(self::$retinaEnabled) && self::$retinaEnabled == '1') {
      if (isset($_COOKIE["ic_pixel_ratio"])) {
        if ($_COOKIE["ic_pixel_ratio"] >= 2) {
          self::$isRetina = '1';
        }
      }
    }

    // If Optimization Quality is Not set...
    if (empty(self::$settings['optimization']) || self::$settings['optimization'] == '' || self::$settings['optimization'] == '0') {
      self::$settings['optimization'] = 'i';
    }

    // Optimization Switch from Legacy
    switch (self::$settings['optimization']) {
      case 'intelligent':
        self::$settings['optimization'] = 'i';
        break;
      case 'ultra':
        self::$settings['optimization'] = 'u';
        break;
      case 'lossless':
        self::$settings['optimization'] = 'l';
        break;
    }

    if (!empty($_GET['dbg']) && $_GET['dbg'] == 'direct') {
      if (!empty($_GET['custom_server'])) {
        self::$zoneName = $_GET['custom_server'] . '/key:' . self::$options['api_key'];
      }
    }

    if (!empty(self::$exif) && self::$exif == '1') {
      self::$apiUrl = 'https://' . self::$zoneName . '/q:' . self::$settings['optimization'] . '/e:1';
    } else {
      self::$apiUrl = 'https://' . self::$zoneName . '/q:' . self::$settings['optimization'] . '';
    }
  }

  public function is_st()
  {
    if (!empty($_GET['ic_lazy'])) {
      return false;
    }

    if (is_admin()) {
      return false;
    }

    $ips = ['208.70.247.157' => '',   // GT Metrix - Vancouver 1.
      '172.255.48.130' => '',   // GT Metrix - Vancouver 2.
      '172.255.48.131' => '',   // GT Metrix - Vancouver 3.
      '172.255.48.132' => '',   // GT Metrix - Vancouver 4.
      '172.255.48.133' => '',   // GT Metrix - Vancouver 5.
      '172.255.48.134' => '',   // GT Metrix - Vancouver 6.
      '172.255.48.135' => '',   // GT Metrix - Vancouver 7.
      '172.255.48.136' => '',   // GT Metrix - Vancouver 8.
      '172.255.48.137' => '',   // GT Metrix - Vancouver 9.
      '172.255.48.138' => '',   // GT Metrix - Vancouver 10.
      '172.255.48.139' => '',   // GT Metrix - Vancouver 11.
      '172.255.48.140' => '',   // GT Metrix - Vancouver 12.
      '172.255.48.141' => '',   // GT Metrix - Vancouver 13.
      '172.255.48.142' => '',   // GT Metrix - Vancouver 14.
      '172.255.48.143' => '',   // GT Metrix - Vancouver 15.
      '172.255.48.144' => '',   // GT Metrix - Vancouver 16.
      '172.255.48.145' => '',   // GT Metrix - Vancouver 17.
      '172.255.48.146' => '',   // GT Metrix - Vancouver 18.
      '172.255.48.147' => '',   // GT Metrix - Vancouver 19.
      '52.229.122.240' => '',   // GT Metrix - Quebec City.
      '104.214.72.101' => '',   // GT Metrix - San Antonio, TX 1.
      '13.66.7.11' => '',   // GT Metrix - San Antonio, TX 2.
      '13.85.24.83' => '',   // GT Metrix - San Antonio, TX 3.
      '13.85.24.90' => '',   // GT Metrix - San Antonio, TX 4.
      '13.85.82.26' => '',   // GT Metrix - San Antonio, TX 5.
      '40.74.242.253' => '',   // GT Metrix - San Antonio, TX 6.
      '40.74.243.13' => '',   // GT Metrix - San Antonio, TX 7.
      '40.74.243.176' => '',   // GT Metrix - San Antonio, TX 8.
      '104.214.48.247' => '',   // GT Metrix - San Antonio, TX 9.
      '157.55.189.189' => '',   // GT Metrix - San Antonio, TX 10.
      '104.214.110.135' => '',   // GT Metrix - San Antonio, TX 11.
      '70.37.83.240' => '',   // GT Metrix - San Antonio, TX 12.
      '65.52.36.250' => '',   // GT Metrix - San Antonio, TX 13.
      '13.78.216.56' => '',   // GT Metrix - Cheyenne, WY.
      '52.162.212.163' => '',   // GT Metrix - Chicago, IL.
      '23.96.34.105' => '',   // GT Metrix - Danville, VA.
      '65.52.113.236' => '',   // GT Metrix - San Francisco, CA.
      '172.255.61.34' => '',   // GT Metrix - London 1.
      '172.255.61.35' => '',   // GT Metrix - London 2.
      '172.255.61.36' => '',   // GT Metrix - London 3.
      '172.255.61.37' => '',   // GT Metrix - London 4.
      '172.255.61.38' => '',   // GT Metrix - London 5.
      '172.255.61.39' => '',   // GT Metrix - London 6.
      '172.255.61.40' => '',   // GT Metrix - London 7.
      '52.237.235.185' => '',   // GT Metrix - Sydney 1.
      '52.237.250.73' => '',   // GT Metrix - Sydney 2.
      '52.237.236.145' => '',   // GT Metrix - Sydney 3.
      '104.41.2.19' => '',   // GT Metrix - São Paulo 1.
      '191.235.98.164' => '',   // GT Metrix - São Paulo 2.
      '191.235.99.221' => '',   // GT Metrix - São Paulo 3.
      '191.232.194.51' => '',   // GT Metrix - São Paulo 4.
      '104.211.143.8' => '',   // GT Metrix - Mumbai 1.
      '104.211.165.53' => '',   // GT Metrix - Mumbai 2.
      '52.172.14.87' => '',   // GT Metrix - Chennai.
      '40.83.89.214' => '',   // GT Metrix - Hong Kong 1.
      '52.175.57.81' => '',   // GT Metrix - Hong Kong 2.
      '20.188.63.151' => '',   // GT Metrix - Paris.
      '20.52.36.49' => '',   // GT Metrix - Frankfurt.
      '52.246.165.153' => '',   // GT Metrix - Tokyo.
      '51.144.102.233' => '',   // GT Metrix - Amsterdam.
      '13.76.97.224' => '',   // GT Metrix - Singapore.
      '102.133.169.66' => '',   // GT Metrix - Johannesburg.
      '52.231.199.170' => '',   // GT Metrix - Busan.
      '13.53.162.7' => '',   // GT Metrix - Stockholm.
      '40.123.218.94' => '',   // GT Metrix - Dubai.
    ];

    if (!empty($this->getIp())) {
      if (isset($ips[$this->getIp()])) {
        return true;
      }
    }

    $user_agent = preg_match('#PingdomPageSpeed|DareBoost|Google|PTST|Chrome-Lighthouse|WP Rocket|gtmetrix#i', $_SERVER['HTTP_USER_AGENT']);
    if ($user_agent) {
      return true;
    }

    // OLD
    $pingdom = strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'pingdom');
    $pingdombot = strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'pingbot');
    $gtmetrix = strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'gtmetrix');
    $pageSpeed = strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'pagespeed');
    $google = strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'google page speed');
    $google_ps = strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'lighthouse');

    if (!empty($_GET['simulate_test'])) {
      return true;
    }

    if ($pingdom !== false) {
      return true;
    }

    if ($pingdombot !== false) {
      return true;
    }

    if ($pageSpeed !== false) {
      return true;
    }

    if ($gtmetrix !== false) {
      return true;
    }

    if ($google !== false) {
      return true;
    }

    if ($google_ps !== false) {
      return true;
    }

    $userIP = $_SERVER['REMOTE_ADDR'];
    if (in_array($userIP, $ips)) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Detect SpeedTest
   * @return bool
   */
  public function getIp()
  {
    $keys = ['HTTP_CF_CONNECTING_IP', // CF = CloudFlare.
      'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_X_REAL_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR',];

    foreach ($keys as $key) {
      if (!isset ($_SERVER[$key])) {
        continue;
      }

      $ip = explode(',', $_SERVER[$key]);
      $ip = end($ip);

      if (false !== filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
      }
    }

    return false;
  }

  public function isMobile()
  {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);

    if (strpos($userAgent, 'lighthouse') !== false) {
      return true;
    }

    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($userAgent, 0, 4))) {
      return true;
    } else {
      return false;
    }
  }

  public function setMobile()
  {
    self::$isMobile = true;
    self::$retinaEnabled = false;
    self::$isRetina = '0';
  }

  public function removeEmoji()
  {
    if (!empty(self::$emojiRemove) && self::$emojiRemove == '1') {
      remove_action('wp_head', 'print_emoji_detection_script', 7);
      remove_action('admin_print_scripts', 'print_emoji_detection_script');
      remove_action('wp_print_styles', 'print_emoji_styles');
      remove_action('admin_print_styles', 'print_emoji_styles');
      remove_filter('the_content_feed', 'wp_staticize_emoji');
      remove_filter('comment_text_rss', 'wp_staticize_emoji');
      remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    }
  }

  public function revSliderActive()
  {
    if (class_exists('RevSliderFront')) {
      self::$revSlider = true;
    }

    self::$revSlider = false;
  }

  public function perfMatters()
  {
    self::$perfMattersActive = false;

    //Perfmatters settings check
    if (function_exists('perfmatters_version_check')) {
      self::$perfMattersActive = self::isPerfMattersLazyActive();

      $perfmatters_options = get_option('perfmatters_options');

      if ($perfmatters_options['assets']['delay_js']) {
        self::$delayJsOverride = 1;
      }

      if ($perfmatters_options['assets']['defer_js']) {
        self::$deferJsOverride = 1;
      }

      if ($perfmatters_options['lazyload']['lazy_loading']) {
        self::$lazyOverride = 1;
      }
    }
  }

  public static function isPerfMattersLazyActive()
  {
    if (defined('PERFMATTERS_ITEM_NAME')) {
      $options = get_option('perfmatters_options');
      if (!empty($options['lazyload']['lazy_loading'])) {
        return true;
      }
    }

    return false;
  }

  public function Brizy()
  {
    if (defined('BRIZY_VERSION')) {
      self::$brizyCache = get_option('wps_ic_brizy_cache');
      self::$brizyActive = true;
    } else {
      self::$brizyActive = false;
    }
  }

  public function removeUnused($html, $handle, $href, $media)
  {
    $unusedCss = self::$options['unused_css'];
    $unusedCss = explode("\n", $unusedCss);
    #return print_r(array($href, $unusedCss),true);
    if (!empty($unusedCss)) {
      $href = explode('?', $href);
      foreach ($unusedCss as $i => $url) {
        if (trim($url) == trim($href[0])) {
          return '';
        }
      }
    }

    //    if ($href == 'https://staging.wpcompress.com/wp-content/plugins/elementor/assets/css/frontend.min.css') {
    //      return print_r(array('pero', $href, in_array($href, $unusedCss), $unusedCss), true);
    //    }

    return $html;
  }

  public function revSliderReplace($html)
  {
    $html = preg_replace_callback('/data-thumb=[\'|"](.*?)[\'|"]/i', [__CLASS__, 'revSlider_Replace_DataThumb'], $html);

    return $html;
  }

  public function revSlider_Replace_DataThumb($image)
  {
    $image_url = $image[1];
    $webp = '/wp:' . self::$webp;
    if (self::isExcludedFrom('webp', $image_url)) {
      $webp = '';
    }

    if (self::isExcludedLink($image_url) || $this->defaultExcluded($image_url)) {
      return $image[0];
    } else {
      $NewSrc = 'https://' . self::$zoneName . '/q:' . self::$settings['optimization'] . '/r:' . self::$isRetina . $webp . '/w:480/u:' . $this->specialChars($image_url);

      return 'data-thumb="' . $NewSrc . '"';
    }

    return $image[0];
  }

  public static function isExcludedFrom($setting, $link)
  {

    if (isset(self::$excludes[$setting])) {
      $excludeList = self::$excludes[$setting];
      if (!empty($excludeList)) {
        foreach ($excludeList as $key => $value) {
          if (strpos($link, $value) !== false && $value != '') {
            return true;
          }
        }
      }
    }


    return false;
  }

  public static function isExcludedLink($link)
  {
    /**
     * Is the link in excluded list?
     */
    if (empty($link)) {
      return false;
    }

    if (strpos($link, '.css') !== false || strpos($link, '.js') !== false) {
      foreach (self::$defaultExcludedList as $i => $excluded_string) {
        if (strpos($link, $excluded_string) !== false) {
          return true;
        }
      }
    }

    if (!empty(self::$excludedList)) {
      foreach (self::$excludedList as $i => $value) {
        if (strpos($link, $value) !== false) {
          // Link is excluded
          return true;
        }
      }
    }

    if (self::isExcludedFrom('cdn', $link)) {
      return true;
    }

    return false;
  }

  public function defaultExcluded($string)
  {
    foreach (self::$defaultExcludedList as $i => $excluded_string) {
      if (strpos($string, $excluded_string) !== false) {
        return true;
      }
    }

    return false;
  }

  public function specialChars($url)
  {
    if (!self::$brizyActive) {
      $url = htmlspecialchars($url);
    }

    return $url;
  }

  public function fonts($html)
  {
    $html = preg_replace_callback('/https?:[^)\'\'"]+\.(woff2|woff|eot|ttf)/i', [__CLASS__, 'replaceFonts'], $html);

    return $html;
  }

  public function replaceFonts($url)
  {
    $url = $url[0];

    if (strpos($url, self::$zoneName) === false) {
      if (strpos($url, '.woff') !== false || strpos($url, '.woff2') !== false || strpos($url, '.eot') !== false || strpos($url, '.ttf') !== false) {
        $newUrl = 'https://' . self::$zoneName . '/m:0/a:' . self::reformatUrl($url);

        return $newUrl;
      }
    }

    return $url;
  }

  public static function reformatUrl($url, $remove_site_url = false)
  {
    $url = trim($url);

    // Check if url is maybe a relative URL (no http or https)
    if (strpos($url, 'http') === false) {
      // Check if url is maybe absolute but without http/s
      if (strpos($url, '//') === 0) {
        // Just needs http/s
        $url = 'https:' . $url;
      } else {
        $url = str_replace('../wp-content', 'wp-content', $url);
        $url_replace = str_replace('/wp-content', 'wp-content', $url);
        $url = self::$siteUrl;
        $url = rtrim($url, '/');
        $url .= '/' . $url_replace;
      }
    }

    $formatted_url = $url;

    if (strpos($formatted_url, '?brizy_media') === false) {
      $formatted_url = explode('?', $formatted_url);
      $formatted_url = $formatted_url[0];
    }

    if ($remove_site_url) {
      $formatted_url = str_replace(self::$siteUrl, '', $formatted_url);
      $formatted_url = str_replace(str_replace(['https://', 'http://'], '', self::$siteUrl), '', $formatted_url);
      $formatted_url = str_replace(addcslashes(self::$siteUrl, '/'), '', $formatted_url);
      $formatted_url = ltrim($formatted_url, '\/');
      $formatted_url = ltrim($formatted_url, '/');
    }

    if (self::$randomHash == 0 && (strpos($formatted_url, '.css') !== false)) {
      $formatted_url .= '?icv=' . WPS_IC_HASH;
    }

    if (self::$randomHash == 0 && strpos($formatted_url, '.js') !== false) {
      $formatted_url .= '?js_icv=' . WPS_IC_JS_HASH;
    }

    return $formatted_url;
  }

  public function allLinks($html)
  {
    $html = preg_replace_callback('/https?:(\/\/[^"\']*\.(?:svg|css|js|ico|icon))/i', [__CLASS__, 'cdnAllLinks'], $html);

    return $html;
  }

  public function cdnAllLinks($image)
  {
    $src_url = $image[0];

    if ($this->defaultExcluded($src_url)) {
      return $src_url;
    }

    if (self::isExcludedFrom('cdn', $src_url)) {
      return $src_url;
    }

    if (strpos($src_url, self::$zoneName) !== false) {
      return $src_url;
    }

    if (!self::isExcludedLink($src_url)) {
      // External is disabled?
      if (self::$externalUrlEnabled == '0' || empty(self::$externalUrlEnabled)) {
        if (!self::imageUrlMatchingSiteUrl($src_url)) {
          return $src_url;
        }
      }

      if (strpos($src_url, self::$zoneName) === false) {
        if (strpos($src_url, '.css') !== false) {
          if (self::$css == "1") {
            $fileMinify = self::$cssMinify;
            if (self::isExcluded('css_minify', $src_url)) {
              $fileMinify = '0';
            }

            $newSrc = 'https://' . self::$zoneName . '/m:' . $fileMinify . '/a:' . self::reformatUrl($src_url);
          }
        } elseif (strpos($src_url, '.js') !== false) {
          if (self::$js == "1") {
            $fileMinify = self::$jsMinify;
            if (self::isExcluded('js_minify', $src_url)) {
              $fileMinify = '0';
            }

            $newSrc = 'https://' . self::$zoneName . '/m:' . $fileMinify . '/a:' . self::reformatUrl($src_url);
          }
        } else {
          $newSrc = 'https://' . self::$zoneName . '/m:0/a:' . self::reformatUrl($src_url);
        }

        return $newSrc;
      }
    }

    return $image[0];
  }

  /**
   * Is link matching the site url?
   *
   * @param $image
   *
   * @return bool
   */
  public static function imageUrlMatchingSiteUrl($image)
  {
    $site_url = self::$siteUrl;
    $image = str_replace(['https://', 'http://'], '', $image);
    $site_url = str_replace(['https://', 'http://'], '', $site_url);

    if (strpos($image, '.css') !== false || strpos($image, '.js') !== false) {
      foreach (self::$defaultExcludedList as $i => $excluded_string) {
        if (strpos($image, $excluded_string) !== false) {
          return false;
        }
      }
    }

    if (strpos($image, $site_url) === false) {
      // Image not on site
      return false;
    } else {
      // Image on site
      return true;
    }
  }

  public static function isExcluded($image_element, $image_link = '')
  {
    $image_path = '';

    if (empty($image_link)) {
      preg_match('@src="([^"]+)"@', $image_element, $match_url);
      if (!empty($match_url)) {
        $image_path = $match_url[1];
        $basename_original = basename($match_url[1]);
      } else {
        $basename_original = basename($image_element);
      }
    } else {
      $image_path = $image_link;
      $basename_original = basename($image_link);
    }

    preg_match("/([0-9]+)x([0-9]+)\.[a-zA-Z0-9]+/", $basename_original, $matches); //the filename suffix way
    if (empty($matches)) {
      // Full Image
      $basename = $basename_original;
    } else {
      // Some thumbnail
      $basename = str_replace('-' . $matches[1] . 'x' . $matches[2], '', $basename_original);
    }

    /**
     * Is this image lazy excluded?
     */
    if (!empty(self::$lazyExcludeList) && !empty(self::$lazyEnabled) && self::$lazyEnabled == '1') {
      //return 'asd';
      foreach (self::$lazyExcludeList as $i => $lazy_excluded) {
        if (strpos($basename, $lazy_excluded) !== false) {
          return true;
        }
      }
    } elseif (!empty(self::$excludedList)) {
      foreach (self::$excludedList as $i => $excluded) {
        if (strpos($basename, $excluded) !== false) {
          return true;
        }
      }
    }

    #$basename = $basename;
    if (!empty(self::$lazyExcludeList) && in_array($basename, self::$lazyExcludeList)) {
      return true;
    }

    if (!empty(self::$excludedList) && in_array($basename, self::$excludedList)) {
      return true;
    }

    return false;
  }

  public function externalUrls($html)
  {
    $html = preg_replace_callback('/https?:[^)\'\'"]+\.(jpg|jpeg|png|gif|svg|css|js|ico|icon)/i', [__CLASS__, 'cdnExternalUrls'], $html);

    return $html;
  }

  public function cdnExternalUrls($image)
  {
    $src_url = $image[0];
    $width = 1;

    if (isset($_GET['wpc_is_amp']) && !empty($_GET['wpc_is_amp'])) {
      $width = 600;
    }

    if (self::isExcludedFrom('cdn', $src_url) || $src_url == 'https://www.ico') {
      return $src_url;
    }

    // Is URL Matching the Site Url?
    if (strpos($src_url, self::$zoneName) !== false) {
      return $src_url;
    }

    $webp = '/wp:' . self::$webp;
    if (self::isExcludedFrom('webp', $src_url)) {
      $webp = '';
    }

    if (self::isExcludedFrom('cdn', $src_url)) {
      return $src_url;
    }

    if (!self::isExcludedLink($src_url)) {
      if (strpos($src_url, self::$zoneName) === false) {
        // Check if the URL is an image, then check if it's instagram etc...
        foreach (self::$defaultExcludedList as $i => $excluded_string) {
          if (strpos($src_url, $excluded_string) !== false) {
            return $src_url;
          }
        }

        $newSrc = $src_url;
        if (strpos($src_url, '.css') !== false) {
          if (self::$css == "1") {
            $newSrc = 'https://' . self::$zoneName . '/m:' . self::$cssMinify . '/a:' . self::reformatUrl($src_url);
          }
        } elseif (strpos($src_url, '.js') !== false) {
          if (self::$js == "1") {
            $newSrc = 'https://' . self::$zoneName . '/m:' . self::$jsMinify . '/a:' . self::reformatUrl($src_url);
          }
        } else {
          if (strpos($src_url, '.svg') !== false) {
            $newSrc = 'https://' . self::$zoneName . '/m:0/a:' . self::reformatUrl($src_url);
          } else {
            $newSrc = self::$apiUrl . '/r:' . self::$isRetina . $webp . '/w:' . $this::getCurrentMaxWidth($width) . '/u:' . self::reformatUrl($src_url);
          }
        }
        return $newSrc;
      }
    }

    return $image[0];
  }

  public static function getCurrentMaxWidth($Width)
  {
    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', strtolower($_SERVER['HTTP_USER_AGENT'])) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr(strtolower($_SERVER['HTTP_USER_AGENT']), 0, 4))) {
      return '320';
    }

    return $Width;
  }

  public function favIcon($html)
  {
    $html = preg_replace_callback('/<link\s+([^>]+[\s\'"])?rel\s*=\s*[\'"]icon[\'"]/is', [__CLASS__, 'checkFavIcon'], $html);

    return $html;
  }

  public function checkFavIcon($html)
  {
    if (empty($html)) {
      return 'no favicon';
    } else {
      return print_r(array($html), true);
    }
  }

  public function runCriticalAjax($html)
  {
    $html = preg_replace_callback('/<\/body>/si', [__CLASS__, 'addCriticalAjax'], $html);
    return $html;
  }

  public function addCriticalAjax($args)
  {
    global $post;

    if (!empty($_GET['test_adding_critical_ajax'])) {
      $script = print_r($post, true);
      $script .= print_r($realUrl = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true);
      return $script;
    }

    $script = '';
    if (isset($post) && !empty($post->ID)) {

      $realUrl = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

      // TODO: Issues if DelayJS is disabled
      #$script .= '<script type="wpc-no-delay">';
      $script .= '<script type="text/javascript">';

      $script .= 'jQuery(document).on("keydown mousedown mousemove touchmove touchstart touchend wheel visibilitychange load", function(){';
      $script .= 'jQuery.post(wpc_vars.ajaxurl, {action: "wpc_send_critical_remote", postID:"' . $post->ID . '", realUrl:"' . $realUrl . '"}, function (response) {
                if (response.success) {
                    console.log("Started Critical Call");
                }';
      $script .= '});';
      $script .= 'jQuery(document).off("keydown mousedown mousemove touchmove touchstart touchend wheel visibilitychange load");';
      $script .= '});';
      $script .= '</script>';
    }
    return $script . '</body>';
  }

  public function addCritical($html)
  {
    $html = preg_replace_callback('/<\/head>/si', [__CLASS__, 'addCriticalCSS'], $html);
    return $html;
  }

  public function addCriticalCSS($html)
  {
    $output = '';
    $criticalCSS = new wps_criticalCss();
    $criticalCSSExists = $criticalCSS->criticalExists();
    if (!empty($criticalCSSExists)) {
      $criticalCSSContent_Desktop = $criticalCSSExists['desktop'];
      $criticalCSSContent_Mobile = $criticalCSSExists['mobile'];


      if (!empty($criticalCSSContent_Desktop)) {
        #$output .= '<link rel="stylesheet" type="text/css" href="' . $criticalCSSContent_Desktop . '" media="all"/>';
        $output .= "\r\n" . '<style type="text/css" id="wpc-critical-css">' . file_get_contents($criticalCSSContent_Desktop) . '</style>';
      }

      if (!empty($criticalCSSContent_Mobile)) {
        #$output .= '<link rel="stylesheet" type="text/css" media="screen and (max-width: 724px)" href="' . $criticalCSSContent_Mobile . '" media="all"/>';
        #$output .= "\r\n".'<style type="text/css" id="wpc-critical-css-mobile">' . file_get_contents($criticalCSSContent_Mobile) . '</style>';
      }
    }

    $output .= '</head>';
    return $output;
  }

  public function lazyCSS($html)
  {
    $html = preg_replace_callback('/<link(.*?)>/si', [__CLASS__, 'cssLinkLazy'], $html);
    return $html;
  }

  public function cssLinkLazy($html)
  {
    $fullTag = $html[0];

    // Not Mobile
    $lazyCss = 'wpc-stylesheet';


    #if (strpos($fullTag, 'elementor-post') !== false || (strpos($fullTag, 'frontend') !== false && strpos($fullTag, '/elementor/') !== false) || strpos($fullTag, 'themes') !== false || strpos($fullTag, 'admin-bar') !== false) {
    if (strpos($fullTag, 'elementor-post') !== false || strpos($fullTag, '/elementor/') !== false || strpos($fullTag, 'admin-bar') !== false) {
      $lazyCss = 'wpc-mobile-stylesheet';
      #return $fullTag;
    } elseif (strpos($fullTag, 'preload') !== false) {
      $lazyCss = 'wpc-mobile-stylesheet';
      #return $fullTag;
    }

    if (self::$excludes_class->strInArray($fullTag, self::$excludes_class->criticalCSSExcludes())) {
      return $fullTag;
    }

    preg_match('/(href)\s*\=["\']?((?:.(?!["\']?\s+(?:\S+)=|\s*\/?[>"\']))+.)["\']?/is', $fullTag, $href);
    if (!empty($href[2])) {

      if (strpos($href[2], 'fonts.googleapis.com/css') !== false) {
        // Google Fonts Hack?
        if (strpos($href[2], 'display=swap') === false) {
          $newHref = $href[2] . '&display=swap';
        } else {
          $newHref = $href[2];
        }
        $gfonts = '<link rel="preload" href="' . $newHref . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"/>';
        return $gfonts;
      } elseif (strpos($href[2], self::$siteUrl) === false || strpos($href[2], 'dynamic') !== false || strpos($href[2], 'layout') !== false) {
        return $fullTag;
      } else {
        $lazyCss = 'wpc-mobile-stylesheet';
      }
    }

    preg_match('/(rel)\s*\=["\']?((?:.(?!["\']?\s+(?:\S+)=|\s*\/?[>"\']))+.)["\']?/is', $fullTag, $linkRel);
    if (!empty($linkRel)) {
      if (!empty($linkRel[2])) {
        $relTag = $linkRel[0]; // rel="stylesheet"
        $relKey = $linkRel[1]; // rel
        $relValue = $linkRel[2]; // stylesheet

        if ($relValue == 'stylesheet') {
          $newTag = str_replace($relValue, $lazyCss, $relTag);
          $fullTag = str_replace($relTag, $newTag, $fullTag);
        }
      }
    }

    preg_match('/(type)\s*\=["\']?((?:.(?!["\']?\s+(?:\S+)=|\s*\/?[>"\']))+.)["\']?/is', $fullTag, $linkType);
    if (!empty($linkType)) {
      if (!empty($linkType[2])) {
        $relTag = $linkType[0]; // type="text/css"
        $relKey = $linkType[1]; // type
        $relValue = $linkType[2]; // text/css

        if ($relValue == 'text/css') {
          $newTag = str_replace($relValue, 'wpc-text/css', $relTag);
          $fullTag = str_replace($relTag, $newTag, $fullTag);
        }
      }
    }

    return $fullTag;
    #}
  }

  public function cssToFooter($html)
  {
    $html = preg_replace_callback('/<\/body>/si', [__CLASS__, 'cssToFooterRender'], $html);

    return $html;
  }

  public function cssToFooterRender($html)
  {
    return self::$removedCSS . '</body>';
  }

  public function encodeIframe($html)
  {
    $html = preg_replace_callback('/<iframe.*?\/iframe>/i', [__CLASS__, 'iframeEncode'], $html);

    return $html;
  }

  public function decodeIframe($html)
  {
    $html = preg_replace_callback('/\[iframe\-wpc\](.*?)\[\/iframe\-wpc\]/i', [__CLASS__, 'iframeDecode'], $html);

    return $html;
  }

  public function iframeEncode($html)
  {
    $html = base64_encode($html[0]);

    if (!empty($_GET['dbg']) && $_GET['dbg'] == 'bas64_encode') {
      return print_r([$html], true);
    }

    return '[iframe-wpc]' . $html . '[/iframe-wpc]';
  }

  public function iframeDecode($html)
  {
    if (!empty($_GET['dbg']) && $_GET['dbg'] == 'bas64_decode') {
      return print_r([$html], true);
    }

    $html = base64_decode($html[1]);

    if (!empty($_GET['dbg']) && $_GET['dbg'] == 'after_base64_decode') {
      return $html;
    }

    return $html;
  }

  public function scriptContent($html)
  {
    $html = preg_replace_callback('/<script\s[^>]*(?<=type=\"text\/template\")*>.*?<\/script>/is', [__CLASS__, 'scriptContentTag'], $html);

    return $html;
  }

  public function scriptContentTag($html)
  {
    if (!empty($_GET['dbg']) && $_GET['dbg'] == 'script') {
      return print_r([$html], true);
    }

    if (strpos($html[0], 'text/template') !== false && strpos($html[0], 'text/x-template') !== false) {
      return $html[0];
    }

    $html = preg_replace_callback('/<img[^>]*>/si', [__CLASS__, 'imageTagAsset'], $html[0]);

    return $html;
  }

  public function imageTagAsset($image)
  {
    $addslashes = false;

    if (strpos($image[0], '$') !== false) {
      return $image[0];
    }

    if (!empty($_GET['dbg']) && $_GET['dbg'] == 'image_asset_array') {
      return print_r([str_replace('<img', 'sad', $image[0])], true);
    }

    if (strpos($image[0], '=\"') !== false || strpos($image[0], "=\'") !== false) {
      $addslashes = true;
      $image[0] = stripslashes($image[0]);
    }

    if (strpos($_SERVER['REQUEST_URI'], 'embed') !== false) {
      $image[0] = $this->maybe_addslashes($image[0], $addslashes);

      return $image[0];
    }

    // File has already been replaced
    if ($this->defaultExcluded($image[0])) {
      $image[0] = $this->maybe_addslashes($image[0], $addslashes);

      return $image[0];
    }

    // File is not an image
    if (!self::isImage($image[0])) {
      $image[0] = $this->maybe_addslashes($image[0], $addslashes);

      return $image[0];
    }

    if ((self::$externalUrlEnabled == 'false' || self::$externalUrlEnabled == '0') && !self::imageUrlMatchingSiteUrl($image[0])) {
      $image[0] = $this->maybe_addslashes($image[0], $addslashes);

      return $image[0];
    }

    //    if (self::isExcludedFrom('cdn', $image_src[1])) {
    //      return $image_src[1];
    //    }

    // File is excluded
    if (self::isExcluded($image[0])) {
      $image[0] = $this->maybe_addslashes($image[0], $addslashes);

      return $image[0];
    }

    $img_tag = $image[0];
    $original_img_tag['original_tags'] = $this->getAllTags($image[0], []);

    preg_match('/src=["|\']([^"]+)["|\']/', $img_tag, $image_src);

    if (strpos($image_src[1], '$') !== false) {
      $image[0] = $this->maybe_addslashes($image[0], $addslashes);

      return $image[0];
    }

    if (!empty($image_src[1])) {
      $NewSrc = 'https://' . self::$zoneName . '/m:0/a:' . $this->specialChars($image_src[1]);
      $img_tag = str_replace($image_src[1], $NewSrc, $img_tag);
    }

    // TODO: Was required for some sites that were having slashes
    $img_tag = $this->maybe_addslashes($img_tag, true);

    return $img_tag;
  }

  public function maybe_addslashes($image, $addslashes = false)
  {
    if ($addslashes) {
      $image = addslashes($image);
    }

    return $image;
  }

  public static function isImage($image)
  {
    if (strpos($image, '.webp') === false && strpos($image, '.jpg') === false && strpos($image, '.jpeg') === false && strpos($image, '.png') === false && strpos($image, '.ico') === false && strpos($image, '.svg') === false && strpos($image, '.gif') === false) {
      return false;
    } else {
      // Serve JPG Enabled?
      if (strpos($image, '.jpg') !== false || strpos($image, '.jpeg') !== false) {
        // is JPEG enabled
        if (empty(self::$settings['serve']['jpg']) || self::$settings['serve']['jpg'] == '0') {
          return false;
        }
      }

      // Serve GIF Enabled?
      if (strpos($image, '.gif') !== false) {
        // is JPEG enabled
        if (empty(self::$settings['serve']['gif']) || self::$settings['serve']['gif'] == '0') {
          return false;
        }
      }

      // Serve PNG Enabled?
      if (strpos($image, '.png') !== false) {
        // is PNG enabled
        if (empty(self::$settings['serve']['png']) || self::$settings['serve']['png'] == '0') {
          return false;
        }
      }

      // Serve SVG Enabled?
      if (strpos($image, '.svg') !== false) {
        // is SVG enabled
        if (empty(self::$settings['serve']['svg']) || self::$settings['serve']['svg'] == '0') {
          return false;
        }
      }

      return true;
    }
  }

  public function getAllTags($image, $ignore_tags = ['src', 'srcset', 'data-src', 'data-srcset'])
  {
    $found_tags = array();
    preg_match_all('/([a-zA-Z\-\_]*)\s*\=["\']?((?:.(?!["\']?\s+(?:\S+)=|\s*\/?[>"\']))+.)["\']?/is', $image, $image_tags);

    if (!empty($image_tags[1])) {
      $tag_value = $image_tags[2];
      foreach ($image_tags[1] as $i => $tag) {
        if (!empty($ignore_tags) && in_array($tag, $ignore_tags)) {
          continue;
        }

        $tag_value[$i] = str_replace(['"', '\''], '', $tag_value[$i]);
        $found_tags[$tag] = $tag_value[$i];
      }
    }

    return $found_tags;
  }

  public function removeWstAssets($html)
  {
    $html = preg_replace_callback('/document\.addEventListener([^>]*)\;/is', [__CLASS__, 'removeEventListener'], $html);

    // Remove Tracking
    $html = preg_replace_callback('/<script type=\"text\/javascript\" src=\"https\:\/\/app\.paykickstart\.com\/tracking\-script\"><\/script>/is', [__CLASS__, 'removeFontAwesome'], $html);

    // Remove fontawesome
    $html = preg_replace_callback('/<script\b[^>]*src=\"https:\/\/kit\.fontawesome.com(.*?)\"><\/script>?/is', [__CLASS__, 'removeFontAwesome'], $html);

    return $html;
  }

  public function removeFontAwesome($html)
  {
    return '';
  }

  public function removeEventListener($html)
  {
    return '';
  }

  public function backgroundSizing($html)
  {
    $html = preg_replace_callback('/style=["\']?(background(-image)?\s*:(.*?)\(\s*(\'|")?(?<image>.*?)\3?\s*\);?)["\']?/i', [__CLASS__, 'replaceBackgroundImage'], $html);
    $html = preg_replace_callback('/<style\b[^>]*>(.*?)<\/style>?/is', [__CLASS__, 'replaceBackgroundImagesInCSS'], $html);
    return $html;
  }

  public function replaceBackgroundImagesInCSS($image)
  {
    $style_content = $image[0];

    $html = preg_replace_callback('~\bbackground(-image)?\s*:(.*?)\(\s*(\'|")?(?<image>.*?)\3?\s*\)~i', [__CLASS__, 'replaceBackgroundImageStyles'], $style_content);

    return $html;
  }

  public function replaceBackgroundImage($image)
  {
    $tag = $image[0];
    $url = $image['image'];
    $original_url = $url;

    if (strpos($url, self::$zoneName) == false) {
      // File has already been replaced
      if ($this->defaultExcluded($url)) {
        return $tag;
      }

      // File is not an image
      if (!self::isImage($url)) {
        return $tag;
      }
    }

    if (self::isExcluded($url)) {
      return $tag;
    }

    if (self::isExcludedFrom('cdn', $url)) {
      return $tag;
    }

    $webp = '/wp:' . self::$webp;
    if (self::isExcludedFrom('webp', $url)) {
      $webp = '';
    }

    $newUrl = self::$apiUrl . '/r:' . self::$isRetina . $webp . '/w:' . $this::getCurrentMaxWidth(1) . '/u:' . self::reformatUrl($url);
    $return_tag = str_replace($original_url, $newUrl, $tag);

    if (self::$lazy_enabled) {
      $return_tag .= 'display:none;';
    }

    return $return_tag;
  }

  public function replaceBackgroundImageStyles($image)
  {
    $tag = $image[0];
    $url = $image['image'];
    $original_url = $url;

    if (strpos($url, self::$zoneName) == false) {
      // File has already been replaced
      if ($this->defaultExcluded($url)) {
        return $tag;
      }

      // File is not an image
      if (!self::isImage($url)) {
        return $tag;
      }

      if (self::isExcluded($url)) {
        return $tag;
      }

      if (self::isExcludedFrom('cdn', $url)) {
        return $tag;
      }

      $webp = '/wp:' . self::$webp;
      if (self::isExcludedFrom('webp', $url)) {
        $webp = '';
      }

      $newUrl = self::$apiUrl . '/r:' . self::$isRetina . $webp . '/w:' . $this::getCurrentMaxWidth(1) . '/u:' . self::reformatUrl($url);
      $return_tag = str_replace($original_url, $newUrl, $tag);

      if (!empty($return_tag)) {
        return $return_tag;
      } else {
        return $tag;
      }
    } else {
      return $tag;
    }
  }

  public function replaceImageTags($html)
  {
    $html = preg_replace_callback('/(?<![\"|\'])<img[^>]*>/i', [__CLASS__, 'replaceImageTagsDo'], $html);

    return $html;
  }


  public function ajaxImage($imageElement)
  {
    $imageElement = stripslashes($imageElement);

    //    if (strpos($imageElement, self::$zoneName) == false) {
    //      // File has already been replaced
    //      if ($this->defaultExcluded($imageElement)) {
    //        return $imageElement;
    //      }
    //
    //      // File is not an image
    //      if (!self::isImage($imageElement)) {
    //        return $imageElement;
    //      }
    //
    //      if ((self::$externalUrlEnabled == 'false' || self::$externalUrlEnabled == '0') && !self::imageUrlMatchingSiteUrl($imageElement)) {
    //        return $imageElement;
    //      }
    //
    //    } else {
    //      // Already has zapwp url, if minify:false/true then it's something
    //      if (strpos($imageElement, 'm:') !== false) {
    //        return $imageElement;
    //      }
    //    }
    //
    //    if (strpos($imageElement, 'cookie') !== false) {
    //      return $imageElement;
    //    }

    $newImageElement = '';
    $original_img_tag =array();
    $original_img_tag['original_tags'] = $this->getAllTags($imageElement, []);

    if (!empty($_GET['ajaxImage'])) {
      return print_r(array($original_img_tag, $imageElement), true);
    }

    if (strpos($original_img_tag['original_tags']['src'], 'data:image') !== false || strpos($original_img_tag['original_tags']['src'], 'blank') !== false) {

      $newImageElement = '<img ';
      // it's placeholder or blank file change something
      foreach ($original_img_tag['original_tags'] as $tag => $value) {
        if ($tag == 'src') {
          // Do nothing
        } elseif ($tag == 'data-src') {
          $src = $value;

          $webp = '/wp:' . self::$webp;
          if (self::isExcludedFrom('webp', $src)) {
            $webp = '/wp:0';
          }

          $src = self::$apiUrl . '/r:' . self::$isRetina . $webp . '/w:' . $this::getCurrentMaxWidth(1) . '/u:' . self::reformatUrl($src);
          $newImageElement .= 'src="' . $src . '" ';
        } else {
          $newImageElement .= $tag . '="' . $value . '" ';
        }
      }
      $newImageElement .= '/>';
    } else {
      $newImageElement = $imageElement;
    }

    $newImageElement = addslashes($newImageElement);
    return $newImageElement;
  }


  public function replaceImageTagsDo($image)
  {
    if (strpos($_SERVER['REQUEST_URI'], 'embed') !== false) {
      return $image[0];
    }

    if (!empty($_GET['dbgAjax'])) {
      return print_r(array($_SERVER, wp_doing_ajax(), self::$isAjax, $image[0]), true);
    }

    if (self::$isAjax) {
      $AjaxImage = $this->ajaxImage($image[0]);
      return $AjaxImage;
    }

    self::$lazyLoadedImages++;

    $isLogo = false;
    $isSlider = false;

    if (strpos($image[0], self::$zoneName) == false) {
      // File has already been replaced
      if ($this->defaultExcluded($image[0])) {
        return $image[0];
      }

      // File is not an image
      if (!self::isImage($image[0])) {
        return $image[0];
      }

      if ((self::$externalUrlEnabled == 'false' || self::$externalUrlEnabled == '0') && !self::imageUrlMatchingSiteUrl($image[0])) {
        return $image[0];
      }

    } else {
      // Already has zapwp url, if minify:false/true then it's something
      if (strpos($image[0], 'm:') !== false) {
        return $image[0];
      }
    }

    if (strpos($image[0], 'cookie') !== false) {
      $image[0] = stripslashes($image[0]);
      return $image[0];
    }


    // Original URL was
    $original_img_tag = array();
    $original_img_tag['original_tags'] = $this->getAllTags($image[0], []);

    if (!empty($_GET['dbg_img'])) {
      return print_r([$original_img_tag['original_tags']], true);
    }

    /**
     * strpos blank is required to make it work when image has placeholder containing "blank" in it.
     */
    if (!empty($original_img_tag['original_tags']['src'])) {
      $image_source = $original_img_tag['original_tags']['src'];
    } else {
      if (!empty($original_img_tag['original_tags']['data-src'])) {
        $image_source = $original_img_tag['original_tags']['data-src'];
      } elseif (!empty($original_img_tag['original_tags']['data-cp-src'])) {
        $image_source = $original_img_tag['original_tags']['data-cp-src'];
      }
    }

    if (self::isExcludedFrom('cdn', $image_source)) {
      return $image[0];
    }

    if (!empty($_GET['dbg_img_src'])) {
      return print_r(array('src_is_empty' => empty($original_img_tag['original_tags']['src']), 'data-src_is_empty' => empty($original_img_tag['original_tags']['data-src']), 'data-cp-src_is_empty' => empty($original_img_tag['original_tags']['data-cp-src']), 'src' => $image_source, 'tags' => $original_img_tag), true);
    }

    $original_img_tag['original_src'] = $image_source;

    /**
     * Fetch image actual size
     */
    if (!empty($original_img_tag['original_tags']['width'])) {
      $size = array();
      $size[0] = $original_img_tag['original_tags']['width'];
      $size[1] = $original_img_tag['original_tags']['height'];
    } else {
      $size = self::get_image_size($image_source);
    }

    // SVG Placeholder
    $source_svg = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="' . $size[0] . '" height="' . $size[1] . '"><path d="M2 2h' . $size[0] . 'v' . $size[1] . 'H2z" fill="#fff" opacity="0"/></svg>');

    $image_source = $this->specialChars($image_source);

    if (isset($_GET['wpc_is_amp']) && !empty($_GET['wpc_is_amp'])) {
      $source_svg = $image_source;
      self::$lazyEnabled = '0';
      self::$adaptiveEnabled = '0';
    }

    if (isset($_GET['preload']) && !empty($_GET['preload'])) {
      $source_svg = $image_source;
      self::$lazyEnabled = '0';
      self::$adaptiveEnabled = '0';
    }

    if (empty($original_img_tag['original_tags']['class']) || !isset($original_img_tag['original_tags']['class'])) {
      $original_img_tag['original_tags']['class'] = '';
    }

    if (empty($original_img_tag['class']) || !isset($original_img_tag['class'])) {
      $original_img_tag['class'] = '';
    }

    if (strpos(strtolower($original_img_tag['original_tags']['class']), 'slide') !== false || strpos(strtolower($original_img_tag['class']), 'slide') !== false) {
      $source_svg = $image_source;
      $isSlider = true;
    }

    $imageUrl = strtolower($image_source);

    if (strpos(strtolower($imageUrl), 'logo') !== false || (!empty($original_img_tag['class']) && strpos(strtolower($original_img_tag['original_tags']['class']), 'logo')) !== false) {
      $isLogo = true;
    }

    if (!empty($original_img_tag['sizes'])) {
      $original_img_tag['additional_tags']['sizes'] = $original_img_tag['sizes'];
    }

    if (!empty($_GET['dbg_logo'])) {
      return print_r([$image_source], true);
    }

    if (!empty($_GET['dbg_tags'])) {
      return print_r(array($original_img_tag), true);
    }


    $webp = '/wp:' . self::$webp;
    if (self::isExcludedFrom('webp', $image_source)) {
      $webp = '/wp:0';
      $original_img_tag['original_tags']['class'] = ' wpc-excluded-webp';
      $original_img_tag['additional_tags']['wpc-data'] = 'excluded-webp ';
    }

    if (self::isExcludedFrom('lazy', $image_source)) {
      #$original_img_tag['original_tags']['class'] = ' wpc-excluded-lazy';
      $original_img_tag['additional_tags']['wpc-data'] = 'excluded-lazy ';
      $isLogo = true;
    }

    $original_img_tag['additional_tags']['data-wpc-loaded'] = 'true';


    // Is LazyLoading enabled in the plugin?
    if (!$isSlider && !empty(self::$lazyEnabled) && self::$lazyEnabled == '1' && !self::$lazyOverride) {
      // if image is logo, then force image url - no lazy loading
      if ($isLogo) {
        $original_img_tag['src'] = $original_img_tag['original_tags']['src'];
        $original_img_tag['additional_tags']['class'] = 'wps-ic-live-cdn wps-ic-logo';

        if ($this::$st) {
          $original_img_tag['additional_tags']['width'] = '230';
          $original_img_tag['additional_tags']['height'] = '50';
        }
      } else {
        if (self::$lazyLoadedImages > self::$lazyLoadedImagesLimit) {
          // We are over lazy limit, load placeholder
          $original_img_tag['src'] = $source_svg;
          $original_img_tag['data-src'] = self::$apiUrl . '/r:' . self::$isRetina . $webp . '/w:' . $this::getCurrentMaxWidth(1) . '/u:' . self::reformatUrl($image_source);
          $original_img_tag['additional_tags']['class'] = 'wps-ic-live-cdn wps-ic-lazy-image';
          $original_img_tag['additional_tags']['loading'] = 'lazy';
        } else {
          // We are under lazy limit, load image
          $original_img_tag['src'] = $source_svg;
          $original_img_tag['data-src'] = self::$apiUrl . '/r:' . self::$isRetina . $webp . '/w:' . $this::getCurrentMaxWidth(1) . '/u:' . self::reformatUrl($image_source);
          $original_img_tag['additional_tags']['class'] = 'wps-ic-live-cdn wps-ic-lazy-image wps-ic-loaded';
        }

        // Data cp-src
        if (!empty($original_img_tag['original_tags']['data-cp-src'])) {
          $original_img_tag['original_tags']['data-cp-src'] = $original_img_tag['data-src'];
        }
      }
    } else {
      if (!$isSlider && !empty(self::$adaptiveEnabled) && self::$adaptiveEnabled == '1') {
        $original_img_tag['src'] = $source_svg;
        $original_img_tag['additional_tags']['class'] = 'wps-ic-cdn';

        /**
         * If current image is logo then force image, don't lazy load
         */
        if ($isLogo || strpos(strtolower($image_source), 'logo') !== false) {
          $original_img_tag['src'] = $original_img_tag['original_tags']['src'];
        } else {
          $original_img_tag['src'] = $source_svg;
          $original_img_tag['data-src'] = self::$apiUrl . '/r:' . self::$isRetina . $webp . '/w:' . $this::getCurrentMaxWidth(1) . '/u:' . self::reformatUrl($image_source);

          // Data cp-src
          if (!empty($original_img_tag['original_tags']['data-cp-src'])) {
            $original_img_tag['original_tags']['data-cp-src'] = $original_img_tag['data-src'];
          }
        }
      } else {
        $original_img_tag['additional_tags']['class'] = 'wps-ic-cdn';

        if (strpos($original_img_tag['original_tags']['class'], 'lazy') !== false) {
          if (!empty($original_img_tag['original_tags']['data-src'])) {
            $original_img_tag['data-src'] = self::$apiUrl . '/r:' . self::$isRetina . $webp . '/w:' . $this::getCurrentMaxWidth(1) . '/u:' . self::reformatUrl($original_img_tag['original_tags']['data-src']);
          } else {
            $original_img_tag['data-src'] = self::$apiUrl . '/r:' . self::$isRetina . $webp . '/w:' . $this::getCurrentMaxWidth(1) . '/u:' . self::reformatUrl($image_source);
          }

          $original_img_tag['original_tags']['src'] = $original_img_tag['data-src'];
          $original_img_tag['original_tags']['data-src'] = $original_img_tag['data-src'];
          $original_img_tag['src'] = $original_img_tag['data-src'];

          // Data cp-src
          if (!empty($original_img_tag['original_tags']['data-cp-src'])) {
            $original_img_tag['original_tags']['data-cp-src'] = $original_img_tag['data-src'];
          }
        } else {
          $original_img_tag['src'] = self::$apiUrl . '/r:' . self::$isRetina . $webp . '/w:' . $this::getCurrentMaxWidth(1) . '/u:' . self::reformatUrl($image_source);

          // Data cp-src
          if (!empty($original_img_tag['original_tags']['data-cp-src'])) {
            $original_img_tag['original_tags']['data-cp-src'] = $original_img_tag['src'];
          }
        }
      }
    }

    if (!empty($_GET['dbg_tag'])) {
      return print_r(array(self::$lazyLoadedImages, self::$lazyLoadedImagesLimit, self::$lazyEnabled, $original_img_tag), true);
    }

    // PerfMatters Fix for lazy loading
    if (self::$perfMattersActive) {
      if (!empty($original_img_tag['data-src'])) {
        $original_img_tag['original_tags']['src'] = $original_img_tag['data-src'];
        $original_img_tag['src'] = $original_img_tag['data-src'];
        unset($original_img_tag['data-src']);
      }
    }

    if (empty($original_img_tag['original_tags']['srcset']) || !isset($original_img_tag['original_tags']['srcset'])) {
      $original_img_tag['original_tags']['srcset'] = '';
    }

    if (!empty($_GET['test_adaptive'])) {

//      if (!empty(self::$adaptiveEnabled) && self::$adaptiveEnabled == '1') {
//        if (!empty($original_img_tag['original_tags']['data-src'])) {
//          $original_img_tag['original_tags']['data-src'] = $source_svg;
//        } else {
//          if (!empty($original_img_tag['data-src'])) {
//            $original_img_tag['data-src'] = $source_svg;
//          } else {
//            $original_img_tag['src'] = $source_svg;
//          }
//        }
//      }

    }

    # if (!empty(self::$adaptiveEnabled) && self::$adaptiveEnabled == '0') {
    if (!self::isExcludedFrom('adaptive', $image_source)) {
      // TODO: Hrvoje testing flickering
      $original_img_tag['original_tags']['data-srcset'] = $this->rewriteSrcset($original_img_tag['original_tags']['srcset']);
      // TODO: Working?
      #$original_img_tag['original_tags']['srcset'] = $this->rewriteSrcset($original_img_tag['original_tags']['srcset']);
    } else {
      #$original_img_tag['original_tags']['class'] = ' wpc-excluded-adaptive';
      $original_img_tag['additional_tags']['wpc-data'] = 'excluded-adaptive ';
    }
    #}

    unset($original_img_tag['original_tags']['srcset']);

    $build_image_tag = '<img ';

    if (!empty($original_img_tag['original_src'])) {
      $original_img_tag['original_src'] = $this->specialChars($original_img_tag['original_src']);
    }

    if (!empty($original_img_tag['src'])) {
      $original_img_tag['src'] = $this->specialChars($original_img_tag['src']);
    }

    if (!empty($original_img_tag['original_tags']['data-src'])) {
      $original_img_tag['original_tags']['data-src'] = $this->specialChars($original_img_tag['original_tags']['data-src']);
    }

    if (!empty($original_img_tag['data-src'])) {
      $original_img_tag['data-src'] = $this->specialChars($original_img_tag['data-src']);
    }

    if (self::isExcluded($original_img_tag['original_src'], $original_img_tag['original_src'])) {
      // Image is excluded
      if (!empty($original_img_tag['original_src'])) {
        $original_img_tag['src'] = $original_img_tag['original_src'];
      } elseif (!empty($original_img_tag['data-src'])) {
        $original_img_tag['src'] = $original_img_tag['data-src'];
      }
    }

    /**
     * Is this image lazy excluded?
     */

    if (!empty(self::$lazyEnabled) && self::$lazyEnabled == '1') {
      if (self::isExcludedFrom('lazy', $image_source)) {
        //Don't add anything if lazy load is off
        $original_img_tag['src'] = $image_source;
      }
    }

    if (!empty(self::$removeSrcset) && self::$removeSrcset == '1') {
      unset($original_img_tag['original_tags']['srcset'],$original_img_tag['original_tags']['data-srcset']);
    }

    if (!empty($_GET['remove_srcset'])) {
      unset($original_img_tag['original_tags']['srcset'],$original_img_tag['original_tags']['data-srcset']);
    }

    if (!empty($_GET['test_adaptive'])) {
      if (!empty(self::$adaptiveEnabled) && self::$adaptiveEnabled == '1') {
        $build_image_tag .= 'data-src="'.$original_img_tag['data-src'].'" ';
        $original_img_tag['original_tags']['data-src'] = $source_svg;
      }
    }

    /**
     * If image contains logo in filename, then it's a logo probably
     */
    if (strpos(strtolower($original_img_tag['original_tags']['class']), 'rs-lazyload') !== false || strpos(strtolower($original_img_tag['original_tags']['class']), 'rs') !== false || strpos(strtolower($image_source), 'logo') !== false || strpos(strtolower($original_img_tag['class']), 'logo') !== false) {
      $build_image_tag .= 'src="' . $original_img_tag['original_tags']['src'] . '" ';
    } else {
      /*
         * if data-src is not empty then we have src as SVG
         */
      if (!empty($original_img_tag['original_tags']['data-src'])) {
        $build_image_tag .= 'src="' . $original_img_tag['original_tags']['data-src'] . '" ';
      } else {
        if (!empty($original_img_tag['data-src'])) {
          $build_image_tag .= 'src="' . $original_img_tag['data-src'] . '" ';
        } else {
          $build_image_tag .= 'src="' . $original_img_tag['src'] . '" ';
        }
      }

    }




    if (!empty($original_img_tag['original_tags'])) {
      foreach ($original_img_tag['original_tags'] as $tag => $value) {
        if (empty($value) || $tag == 'class' || $tag == 'src' || $tag == 'data-src') {
          continue;
        }

        $build_image_tag .= $tag . '="' . $value . '" ';
      }
    }

    // foreach additional image tag
    foreach ($original_img_tag['additional_tags'] as $tag => $value) {
      if ($tag == 'class') {
        $tag = 'class';

        if (strpos($original_img_tag['original_tags']['class'], 'rs-lazyload') !== false || strpos($original_img_tag['original_tags']['class'], 'rs') !== false || (strpos($original_img_tag['original_tags']['class'], 'lazy') !== false && strpos($original_img_tag['original_tags']['class'], 'skip-lazy') === false)) {
          // Leave as is
          $value = $original_img_tag['original_tags']['class'];
        } else {
          $value .= ' ' . $original_img_tag['original_tags']['class'];
        }
      }

      if ($tag == 'src' || $tag == 'data-src' || empty($value)) {
        continue;
      }

      // Check if tag already exists, if so - replace it
      $value = trim($value);
      $build_image_tag .= $tag . '="' . $value . '" ';
    }

    $build_image_tag .= '/>';

    if (!empty($_GET['dbgAjaxEnd'])) {
      return print_r(array($_POST, $_GET, wp_doing_ajax(), self::$isAjax, $image[0]), true);
    }

    if (!empty($_GET['dbg_buildimg'])) {
      return print_r([str_replace('<img', 'mgi', $build_image_tag)], true);
    }

    if (self::$isAjax) {
      $build_image_tag = addslashes($build_image_tag);
    }

    return $build_image_tag;
  }

  public static function get_image_size($url)
  {
    preg_match("/([0-9]+)x([0-9]+)\.[a-zA-Z0-9]+/", $url, $matches); //the filename suffix way
    if (isset($matches[1]) && isset($matches[2])) {
      return [$matches[1], $matches[2]];
    } else { //the file
      return [1024, 1024];
    }
  }

  public function rewriteSrcset($srcset)
  {
    if (!empty($srcset)) {
      $newSrcSet = '';
      preg_match_all('/((https?\:\/\/|\/\/)[^\s]+\S+\.(jpg|jpeg|png|gif|svg|webp))\s(\d{1,5}+[wx])/', $srcset, $srcset_links);

      if (!empty($srcset_links)) {
        foreach ($srcset_links[0] as $i => $srcset) {
          $src = explode(' ', $srcset);
          $srcset_url = $src[0];
          $srcset_width = $src[1];

          $webp = '/wp:' . self::$webp;
          if (self::isExcludedFrom('webp', $srcset_url)) {
            $webp = '';
          }

          if (self::isExcludedLink($srcset_url)) {
            $newSrcSet .= $srcset_url . ' ' . $srcset_width . ',';
          } else {
            if (strpos($srcset_width, 'x') !== false) {
              $width_url = 1;
              $srcset_width = str_replace('x', '', $srcset_width);
              $extension = 'x';
            } else {
              $width_url = $srcset_width = str_replace('w', '', $srcset_width);
              $extension = 'w';
            }

            // Set the maximum width if is_st
            if ($this::$st) {
              $width_url = $srcset_width = $this::getCurrentMaxWidth(420);
            }

            if (strpos($srcset_url, self::$zoneName) !== false) {
              $newSrcSet .= $srcset_url . ' ' . $srcset_width . $extension . ',';
              continue;
            }

            if (strpos($srcset_url, '.svg') !== false) {
              $newSrcSet .= 'https://' . self::$zoneName . '/m:0/a:' . self::reformatUrl($srcset_url) . ' ' . $srcset_width . $extension . ',';
            } else {
              $newSrcSet .= self::$apiUrl . '/r:' . self::$isRetina . $webp . '/w:' . $width_url . '/u:' . self::reformatUrl($srcset_url) . ' ' . $srcset_width . $extension . ',';
            }
          }
        }

        $newSrcSet = rtrim($newSrcSet, ',');
      }

      return $newSrcSet;
    }

    return $srcset;
  }

}