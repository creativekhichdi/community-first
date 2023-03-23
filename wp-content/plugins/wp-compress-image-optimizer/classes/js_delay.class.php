<?php


class wps_ic_js_delay
{

  public static $excludes;

  public function __construct()
  {
    self::$excludes = new wps_ic_excludes();
  }

  public function delay_script_replace($tag)
  {
    if (is_array($tag)) {
      $tag = $tag[0];
    }

    // TODO: Fix, sometimes it's double array? regexp?
    if (is_array($tag)) {
      $tag = $tag[0];
    }

    if (!empty($_GET['disableDelay'])) {
      return $tag;
    }

    if (!empty($_GET['debugCritical'])) {
      return $tag;
    }

    if (!empty($_GET['dbg_delay_js'])) {
      return print_r(array($tag), true);
    }

    if (current_user_can('manage_options')) {
      return $tag;
    }

//    if (strpos(strtolower($tag), 'wp-includes/js/jquery/jquery.min.js') !== false){
//      if (file_exists(WPS_IC_DIR . 'assets/js/jquery.min.js')) {
//        $inlinejQuery = file_get_contents(WPS_IC_DIR . 'assets/js/jquery.min.js');
//        return '<script type="text/javascript" id="jquery-inline">' . $inlinejQuery . '</script>';
//      }
//    }
//
//    if (strpos(strtolower($tag), 'wp-includes/js/jquery/jquery-migrate.min.js') !== false){
//      if (file_exists(WPS_IC_DIR . 'assets/js/jquery-migrate.min.js')) {
//        $inlinejQuery = file_get_contents(WPS_IC_DIR . 'assets/js/jquery-migrate.min.js');
//        return '<script type="text/javascript" id="jquery-migrate-inline">' . $inlinejQuery . '</script>';
//      }
//    }
//
    if (strpos(strtolower($tag), 'tweenmax.min.js') !== false) {
      if (file_exists(WPS_IC_DIR . 'assets/js/tweenmax.min.js')) {
        $inline = file_get_contents(WPS_IC_DIR . 'assets/js/tweenmax.min.js');
        return '<script type="text/javascript" id="tweenmax-inline">' . $inline . '</script>';
      }
    }

    if (strpos(strtolower($tag), 'wp-compress-image-optimizer') !== false) {
      return $tag;
    }

//    if (strpos(strtolower($tag), 'wp-compress-image-optimizer/assets/js/optimizer.min.js') !== false){
//      if (file_exists(WPS_IC_DIR . 'assets/js/optimizer.min.js')) {
//        $inlineOptimizer = file_get_contents(WPS_IC_DIR . 'assets/js/optimizer.min.js');
//        return '<script type="text/javascript">' . $inlineOptimizer . '</script>';
//      }
//    }

    if (self::$excludes->strInArray($tag, self::$excludes->delayJSExcludes())) {
      return $tag;
    }

    if (strpos(strtolower($tag), 'wpc-no-delay') !== false) {
      $tag = str_replace('wpc-no-delay', 'text/javascript', $tag);
      return $tag;
    }

    if (strpos(strtolower($tag), 'tweenmax') !== false || strpos(strtolower($tag), 'fontawesome') !== false) {
      //TODO: Makli smo ovo jer mozda ne treba?
      #$tag = str_replace('<script', '<script type="wpc-delay-timing"', $tag);
      return $tag;
    } elseif (strpos(strtolower($tag), 'type=') === false) {
      $tag = str_replace('<script', '<script type="wpc-delay-script"', $tag);
    } else {
      $tag = str_replace('text/javascript', 'wpc-delay-script', $tag);
    }

    return $tag;
  }


}