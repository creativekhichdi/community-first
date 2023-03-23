<?php

class wps_ic_excludes
{

  private static $defaultDelayJSExcludes;
  private static $defaultCombineJSExcludes;
  private static $defaultCombineCSSExcludes;
  private static $defaultCriticalCSSExcludes;
  private static $excludesDelayJSOption;
  private static $excludesCombineJSOption;
  private static $excludesCombineCSSOption;
 
  // New
  private static $excludesCriticalCSSOption;
  private static $excludesOption;

  public function __construct()
  {
    self::$excludesOption = get_option('wpc-excludes');

    if (!empty(self::$excludesOption['delay_js'])) {
      self::$excludesDelayJSOption = self::$excludesOption['delay_js'];
    }

    if (!empty(self::$excludesOption['js_combine'])) {
      self::$excludesCombineJSOption = self::$excludesOption['js_combine'];
    }

    if (!empty(self::$excludesOption['css_combine'])) {
      self::$excludesCombineCSSOption = self::$excludesOption['css_combine'];
    }

    if (!empty(self::$excludesOption['critical_css'])) {
      self::$excludesCriticalCSSOption = self::$excludesOption['critical_css'];
    }

    self::$defaultDelayJSExcludes = [
      //Our excludes
      'fontawesome',
      'plus-addon',
      'jquery.min.js',
      'jquery.js',
      'jquery-migrate',
      'lazy.min.js',
      'hooks',
      'lazy',
      'wp-i18',
      'wp.i18',
      'i18',
      'delay-js',
      #'tweenmax',
      'delay-js-script',
      'optimizer',
      //Imported excludes
      'nowprocket',
      '/wp-includes/js/wp-embed.min.js',
      'lazyLoadOptions',
      'lazyLoadThumb',
      'wp-rocket/assets/js/lazyload/',
      'et_core_page_resource_fallback',
      'window.\$us === undefined',
      'js-extra',
      'fusionNavIsCollapsed',
      '/assets/js/smush-lazy-load', // Smush & Smush Pro.
      'eio_lazy_vars',
      '\/lazysizes(\.min|-pre|-post)?\.js', // lazyload library (used in EWWW, Autoptimize, Avada).
      'document\.body\.classList\.remove\("no-js"\)',
      'document\.documentElement\.className\.replace\( \'no-js\', \'js\' \)',
      'et_animation_data',
      'wpforms_settings',
      'var nfForms',
      '//stats.wp.com', // Jetpack Stats.
      '_stq.push', // Jetpack Stats.
      'fluent_form_ff_form_instance_', // Fluent Forms.
      'cpLoadCSS', // Convert Pro.
      'ninja_column_', // Ninja Tables.
      'var rbs_gallery_', // Robo Gallery.
      'var lepopup_', // Green Popup.
      'var billing_additional_field', // Woo Autocomplete Nish.
      'var gtm4wp',
      'var dataLayer_content',
      '/ewww-image-optimizer/includes/load', // EWWW WebP rewrite external script.
      '/ewww-image-optimizer/includes/check-webp', // EWWW WebP check external script.
      'ewww_webp_supported', // EWWW WebP inline scripts.
      '/dist/js/browser-redirect/app.js', // WPML browser redirect script.
      '/perfmatters/js/lazyload.min.js',
      'lazyLoadInstance',
      'scripts.mediavine.com/tags/', // allows mediavine-video schema to be accessible by search engines.
      'initCubePortfolio', // Cube Portfolio show images.
      'simpli.fi', // simpli.fi Advertising Platform scripts.
      'gforms_recaptcha_', // Gravity Forms recaptcha.
      '/jetpack-boost/vendor/automattic/jetpack-lazy-images/', // Jetpack Boost plugin lazyload.
      'jetpack-lazy-images-js-enabled',  // Jetpack Boost plugin lazyload.
      'jetpack-boost-critical-css', // Jetpack Boost plugin critical CSS.
      'wpformsRecaptchaCallback', // WPForms reCAPTCHA v2.
      'booking-suedtirol-js', // bookingsuedtirol.com widgets.
      'wpcp_css_disable_selection', // WP Content Copy Protection & No Right Click.
      '/gravityforms/js/conditional_logic.min.js', // Gravity forms conditions.
      'statcounter.com/counter/counter.js', // StatsCounter.
      'var sc_project', // Statscounter.
      '/jetpack/jetpack_vendor/automattic/jetpack-lazy-images/', // Jetpack plugin lazyload.
      '/themify-builder/themify/js/modules/fallback',
      'handlePixMessage',
      'var corner_video',
      'cdn.pixfuture.com/hb_v2.js',
      'cdn.pixfuture.com/pbix.js',
      'served-by.pixfuture.com/www/delivery/ads.js',
      'served-by.pixfuture.com/www/delivery/headerbid_sticky_refresh.js',
      'serv-vdo.pixfuture.com/vpaid/ads.js',
      'wprRemoveCPCSS',
      'window.jdgmSettings', // Judge.me plugin.
      '/photonic/include/js/front-end/nomodule/photonic-baguettebox.min.js', // Photonic plugin.
      '/photonic/include/ext/baguettebox/baguettebox.min.js', // Photonic plugin.
      'window.wsf_form_json_config', // WSF Form plugin
    ];

    self::$defaultCombineJSExcludes = [
      'jquery.min.js',
      'jquery.js',
      'jquery-migrate',
      'lazy.min.js',
      'wp-i18',
      'wp.i18',
      'i18',
      'hooks',
      'lazy',
      'all',
      'optimizer',
      'delay-js',
      'application/ld+json'
    ];

    self::$defaultCombineCSSExcludes = [
      'wps-inline' //our inline CSS option
    ];

    self::$defaultCriticalCSSExcludes = array();

    //Check if default excludes are disabled
    if (!empty(self::$excludesOption['delay_js_default_excludes_disabled']) && self::$excludesOption['delay_js_default_excludes_disabled'] == '1') {
      self::$defaultDelayJSExcludes = array();
    }

    if (!empty(self::$excludesOption['js_combine_default_excludes_disabled']) && self::$excludesOption['js_combine_default_excludes_disabled'] == '1') {
      self::$defaultCombineJSExcludes = array();
    }
    if (!empty(self::$excludesOption['css_combine_default_excludes_disabled']) && self::$excludesOption['css_combine_default_excludes_disabled'] == '1') {
      self::$defaultCombineCSSExcludes = array();
    }

    if (!empty(self::$excludesOption['critical_css_default_excludes_disabled']) && self::$excludesOption['critical_css_default_excludes_disabled'] == '1') {
      self::$defaultCriticalCSSExcludes = array();
    }
  }


  public function criticalCSSExcludes()
  {
    if (is_array(self::$excludesCriticalCSSOption)) {
      self::$defaultCriticalCSSExcludes = array_merge(self::$defaultCriticalCSSExcludes, self::$excludesCriticalCSSOption);
    }

    return self::$defaultCriticalCSSExcludes;
  }


  public function delayJSExcludes()
  {
    if (is_array(self::$excludesDelayJSOption)) {
      self::$defaultDelayJSExcludes = array_merge(self::$defaultDelayJSExcludes , self::$excludesDelayJSOption);
    }

    if (isset($_GET['exclude_theme_delay'])) {
      array_push(self::$defaultDelayJSExcludes, '/themes/');
    }

    if (isset($_GET['exclude_us_delay'])) {
      //sort out for whitelabel folder name
      array_push(self::$defaultDelayJSExcludes, 'WPS_IC_DIR');
    }

    return self::$defaultDelayJSExcludes;
  }

  public function combineCSSExcludes()
  {
    if (is_array(self::$excludesCombineCSSOption)) {
      self::$defaultCombineCSSExcludes = array_merge(self::$defaultCombineCSSExcludes , self::$excludesCombineCSSOption);
    }

    return self::$defaultCombineCSSExcludes;
  }

  public function combineJSExcludes()
  {
    if (is_array(self::$excludesCombineJSOption)) {
      self::$defaultCombineJSExcludes = array_merge(self::$defaultCombineJSExcludes, self::$excludesCombineJSOption);
    }

    return self::$defaultCombineJSExcludes;
  }

  public function renderBlockingCSSExcludes()
  {
    $excludes = ['wps_inline'];
    $combine_css_excludes = get_option('wpc-excludes');
    $combine_css_excludes = $combine_css_excludes['css_render_blocking'];

    if (is_array($combine_css_excludes)) {
      $excludes = array_merge($excludes, $combine_css_excludes);
    }

    return $excludes;
  }

  public function strInArray($haystack, $needles = [])
  {

    if (empty($needles)) {
      return false;
    }

    $haystack = strtolower($haystack);

    foreach ($needles as $needle) {
      $needle = strtolower(trim($needle));

      if (empty($needle)) continue;

      $res = strpos($haystack, $needle);
      if ($res !== false) {
        return true;
      }
    }

    return false;
  }

  function getContent($url)
  {
    if (strpos($url, '//') === 0) {
      $url = 'https:' . $url;
    }

    $data = wp_remote_get($url);

    if (is_wp_error($data)) {
      return '';
    }

    return wp_remote_retrieve_body($data);
  }
}
