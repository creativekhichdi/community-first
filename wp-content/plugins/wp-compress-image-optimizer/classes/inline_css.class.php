<?php

class wps_ic_inline_css
{

  public static $excludes;

  public function __construct()
  {

    self::$excludes = new wps_ic_excludes();

    $this->generate = 0;
    $this->inline_css_file = WPS_IC_CACHE . 'wps_inline_' . get_the_ID() . '.txt';

    //hrvoje, add setting name
    $this->inline_files_url = array();

    if (!file_exists($this->inline_css_file) && $this->inline_files_url) {
      $fp = fopen($this->inline_css_file, 'w+');
      fclose($fp);
      file_put_contents($this->inline_css_file, json_encode(''));
      $this->inline_css_file_content = array();
      $this->generate = 1;
    } else {
      if (file_exists($this->inline_css_file)) {
        $json = file_get_contents($this->inline_css_file);
        $this->inline_css_file_content = json_decode($json, true);
      }
    }

  }

  public function do_inline($html)
  {

    //including all links in case rel != stylesheet
    $html = preg_replace_callback('/<link(.*?)>/si', array($this, 'replace_generate'), $html);

    if ($this->generate) {
      file_put_contents($this->inline_css_file, json_encode($this->inline_css_file_content, JSON_UNESCAPED_SLASHES));
    }

    return $html;
  }

  public function replace_generate($tag)
  {
    $original_tag = $tag[0];
    $tag = $tag[0];

    $src = '';
    $is_src_set = preg_match('/href=["|\'](.*?)["|\']/', $tag, $src);
    $src = str_replace('href=', '', $src);
    $src = str_replace("'", "", $src);
    $src = $src[0];

    if (!$is_src_set || self::$excludes->strInArray($src, $this->inline_files_url) === false) {
      return $original_tag;
    }

    if ($this->generate) {

      $check = wp_http_validate_url($src);
      if ($check) {
        $content = self::$excludes->getContent($src);
      } else {
        $content = self::$excludes->getContent(get_home_url() . $src);
      }

      $this->inline_css_file_content = array_merge($this->inline_css_file_content, [$src => $content]);

    } else {
      $content = $this->inline_css_file_content[$src];
    }

    return '<style class = "wps_inline" type="text/css">' . $content . '</style>';

  }
}