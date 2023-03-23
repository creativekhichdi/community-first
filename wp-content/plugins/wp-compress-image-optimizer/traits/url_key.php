<?php

//Used for combine and critical, cache handles this in its own class

class wps_ic_url_key
{

  public $urlKey;
  public $url;

  public function __construct( $url = '' )
  {

    if ( empty( $url ) ) {
      $url = $_SERVER['REQUEST_URI'];
    }

    if (!is_multisite()) {
      $this->siteUrl       = site_url();
    } else {
      $current_blog_id = get_current_blog_id();
      switch_to_blog($current_blog_id);
      #self::$siteUrl = network_site_url();
      $this->siteUrl = home_url();
    }

    $this->serverRequest = $url;
    #$this->siteUrl       = site_url();
    $this->setup( $this->serverRequest );
    $this->trp_active = 0;

    if (class_exists('TRP_Translate_Press')){
      $this->trp_active = 1;
      $this->trp_settings = get_option('trp_settings');
    }


  }

  public function setup($url, $return = false)
  {
    $this->url = $this->addHomeUrl($url);
    $this->urlKey = $this->createUrlKey($this->url);

    if ($return) {
      return $this->urlKey;
    }
  }


  public function addHomeUrl($url)
  {
    if (strpos($url, $this->siteUrl) === false) {
		//add home url if not present and strip anything before '/?' in $url = REQUEST_URI (needed for wp installs in
	    // folder)
      return $this->siteUrl . strstr($url, '/?');
    }

    return $url;
  }


  //not used for now
  public function removeUrl($url)
  {
    $noUrl = str_replace($this->siteUrl, '', $url);

    //remove our remote trigger from url
    $noUrl = str_replace('&remote_generate_critical=1', '', $noUrl);
    $noUrl = str_replace('&apikey='.get_option( WPS_IC_OPTIONS )['api_key'], '', $noUrl);

    //TranslatePress language remove from url
    //we have to remove only the first occurrence
    if ($this->trp_active) {
      global $TRP_LANGUAGE;

      if ($TRP_LANGUAGE == $this->trp_settings['default-language']) {

        if (isset($this->trp_settings['add-subdirectory-to-default-language']) && $this->trp_settings['add-subdirectory-to-default-language'] == 'yes') {
          //if default language is set to be displayed, do replace
          $pos = strpos($noUrl, $this->trp_settings['url-slugs'][$TRP_LANGUAGE] . '/');
          if ($pos !== false) {
            $noUrl = substr_replace($noUrl, '', $pos, strlen($this->trp_settings['url-slugs'][$TRP_LANGUAGE] . '/'));
          }
        }
      } else {
        //replace for non default languages
        $pos = strpos($noUrl, $this->trp_settings['url-slugs'][$TRP_LANGUAGE] . '/');
        if ($pos !== false) {
          $noUrl = substr_replace($noUrl, '', $pos, strlen($this->trp_settings['url-slugs'][$TRP_LANGUAGE] . '/'));
        }
      }
    }

    return $noUrl;
  }

  public function createUrlKey($url)
  {
	$url = str_replace(['http://', 'https://'], '', $url);
    if (strpos($url, '?testCritical') !== false) {
      $url = explode('?',$url);
      $url = $url[0];
    }

    $url = sanitize_title($url);

    return $url;
  }

}