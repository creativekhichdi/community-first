<?php
/*
 * Local Compression
 */

//ini_set('display_errors', 1);
//error_reporting(E_ALL);

class wps_ic_local
{

  private static $uncompressedImages;
  private static $compressedImages;
  private static $allowed_types;

  private static $apiUrl;
  private static $apikey;
  private static $siteUrl;
  private static $parameters;

  private static $defaultParameters;
  private static $imageSizes;

  public function __construct()
  {
    self::$imageSizes = array();
    self::$allowed_types = array('jpg' => 'jpg', 'jpeg' => 'jpeg', 'gif' => 'gif', 'png' => 'png');

    $location = get_option('wps_ic_geo_locate');
    if (empty($location)) {
      $location = $this->geoLocate();
    }

    if (is_object($location)) {
      $location = (array)$location;
    }

    $apiVersion = 'v3';

    if (isset($location) && !empty($location)) {
      if (is_array($location) && !empty($location['server'])) {
        if ($location['continent'] == 'CUSTOM') {
          self::$apiUrl = 'https://' . $location['custom_server'] . '.zapwp.net/local/' . $apiVersion. '/';
        } elseif ($location['continent'] == 'AS' || $location['continent'] == 'IN') {
          self::$apiUrl = 'https://singapore.zapwp.net/local/' . $apiVersion . '/';
        } elseif ($location['continent'] == 'EU') {
          self::$apiUrl = 'https://germany.zapwp.net/local/' . $apiVersion . '/';
        } elseif ($location['continent'] == 'OC') {
          self::$apiUrl = 'https://sydney.zapwp.net/local/' . $apiVersion . '/';
        } elseif ($location['continent'] == 'US' || $location['continent'] == 'NA' || $location['continent'] == 'SA') {
          self::$apiUrl = 'https://nyc.zapwp.net/local/' . $apiVersion . '/';
        } else {
          self::$apiUrl = 'https://germany.zapwp.net/local/' . $apiVersion . '/';
        }
      } else {
        self::$apiUrl = 'https://' . $location->server . '/local/' . $apiVersion . '/';
      }
    } else {
      self::$apiUrl = 'https://germany.zapwp.net/local/' . $apiVersion . '/';
    }

    if (!empty($_GET['debugAPIURL'])) {
      var_dump(self::$apiUrl);
      var_dump($location);
      die();
    }

    // Define default parameters and their values
    self::$defaultParameters = array('webp' => '0', 'quality' => '2', 'retina' => '0', 'exif' => '0');

    // Get All Image Sizes
    self::$imageSizes = $this->getAllThumbSizes();

    /**
     * Is it a multisite?
     */
    if (is_multisite()) {
      $current_blog_id = get_current_blog_id();
      switch_to_blog($current_blog_id);
      self::$apikey = get_option(WPS_IC_OPTIONS)['api_key'];
      #self::$siteUrl = network_site_url();
      self::$siteUrl = site_url();
      self::$parameters = get_option(WPS_IC_SETTINGS);
    } else {
      self::$siteUrl = site_url();
      self::$apikey = get_option(WPS_IC_OPTIONS)['api_key'];
      self::$parameters = get_option(WPS_IC_SETTINGS);
    }

    /**
     * Tranlate Parameters to Latest API
     */
    self::$parameters = $this->translateParameters(self::$parameters);

  }


  public function geoLocate()
  {
    $force_location = get_option('wpc-ic-force-location');
    if (!empty($force_location)) {
      return $force_location;
    }

    $call = wp_remote_get('https://cdn.zapwp.net/?action=geo_locate&domain=' . urlencode(site_url()), array('timeout' => 30, 'sslverify' => false, 'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:20.0) Gecko/20100101 Firefox/20.0'));
    if (wp_remote_retrieve_response_code($call) == 200) {
      $body = wp_remote_retrieve_body($call);
      $body = json_decode($body);

      if ($body->success) {
        update_option('wps_ic_geo_locate', $body->data);

        return $body->data;
      } else {
        update_option('wps_ic_geo_locate', array('country' => 'EU', 'server' => 'frankfurt.zapwp.net'));

        return array('country' => 'EU', 'server' => 'frankfurt.zapwp.net');
      }
    } else {
      update_option('wps_ic_geo_locate', array('country' => 'EU', 'server' => 'frankfurt.zapwp.net'));

      return array('country' => 'EU', 'server' => 'frankfurt.zapwp.net');
    }
  }


  public function getAllThumbSizes()
  {
    global $_wp_additional_image_sizes;

    $default_image_sizes = get_intermediate_image_sizes();

    foreach ($default_image_sizes as $size) {
      $image_sizes[$size]['width'] = intval(get_option("{$size}_size_w"));
      $image_sizes[$size]['height'] = intval(get_option("{$size}_size_h"));
      $image_sizes[$size]['crop'] = get_option("{$size}_crop") ? get_option("{$size}_crop") : false;
    }

    if (isset($_wp_additional_image_sizes) && count($_wp_additional_image_sizes)) {
      $image_sizes = array_merge($image_sizes, $_wp_additional_image_sizes);
    }

    $AdditionalSizes = array('full');
    foreach ($AdditionalSizes as $size) {
      $image_sizes[$size]['width'] = 'full';
    }

    $image_sizes['original']['width'] = 'original';

    return $image_sizes;
  }

  /**
   * Used to translate parameters from old version to new version of API
   * Example: generate_webp gets translated to webp, preserve_exif gets translated to
   * exif...
   * @param $parameters
   * @return void
   */
  public function translateParameters($parameters)
  {
    // Get defaults
    $translatedParameters = $this->getDefaultParameters();

    if (isset($parameters['generate_webp'])) {
      $translatedParameters['webp'] = $parameters['generate_webp'];
    }

    if (isset($parameters['retina'])) {
      $translatedParameters['retina'] = $parameters['retina'];
    }

    if (isset($parameters['qualityLevel'])) {
      $translatedParameters['quality'] = $parameters['qualityLevel'];
    }

    if (isset($parameters['preserve_exif'])) {
      $translatedParameters['exif'] = $parameters['preserve_exif'];
    }

    if (isset($parameters['max_width'])) {
      $translatedParameters['max_width'] = $parameters['max_width'];
    } else {
      $translatedParameters['max_width'] = WPS_IC_MAXWIDTH;
    }

    return $translatedParameters;
  }

  public function getDefaultParameters($override = array())
  {
    foreach (self::$defaultParameters as $index => $value) {
      if (isset($override[$index])) {
        self::$defaultParameters[$index] = $override[$index];
      }
    }

    return self::$defaultParameters;
  }


  public function isBulkRunning()
  {
    $transient = get_transient('wps_ic_bulk_running');
    if (!$transient) return false;

    return true;
  }


  /**
   * Send a stream to API
   * @param $imageArray Array of images
   * @param $parameters Array of parameters from Settings
   * @return void
   */
  public function sendToAPI($imageArray = array(), $parameters = '', $action = '')
  {
    $body = array();
    $body['apikey'] = self::$apikey;
    $body['siteurl'] = self::$siteUrl;

    // Way to track if old items in queue should be removed
    $body['batch'] = sha1(time());

    // NOTE TO SELF!!
    // NO ACTION IS REQUIRED FOR BULK!!! NEEDS TO CREATE QUEUE!!
    if (!empty($action)) {
      $body['action'] = $action;
    }

    if (empty($parameters)) {
      $body['parameters'] = json_encode(self::$parameters);
    } else {
      $body['parameters'] = json_encode($parameters);
    }

    $body['images'] = json_encode($imageArray);

    $call = wp_remote_post(self::$apiUrl, ['timeout' => 300, 'blocking' => true, 'body' => $body, 'sslverify' => false, 'user-agent' => WPS_IC_API_USERAGENT]);

    if (wp_remote_retrieve_response_code($call) == 200) {
      $responseBody = wp_remote_retrieve_body($call);
      $responseBody = json_decode($responseBody);

      if (!$responseBody || $responseBody->success == 'false') {
        if ($responseBody->data == 'invalid-apikey') {
          return array('status' => 'failed', 'status_code' => 200, 'reason' => 'bad-apikey', 'body' => print_r($body,true));
        } else {
          return array('status' => 'failed', 'status_code' => 200, 'reason' => $responseBody->data, 'body' => print_r($body,true));
        }
      } else {
        return array('status' => 'success', 'apiUrl' => self::$apiUrl, 'body' => wp_remote_retrieve_body($call));
      }
    } else {
      return array('status' => 'failed', 'status_code' => wp_remote_retrieve_response_code($call), 'postBody' => $body, 'apiUrl' => self::$apiUrl, 'body' => wp_remote_retrieve_body($call));
    }
  }

  /**
   * Preparing images for restore to send to API
   * @return Array Array of images
   */
  public function prepareRestoreImages()
  {
    self::$uncompressedImages = array();
    self::$compressedImages = array();
    $images = get_posts(array('post_type' => 'attachment', 'posts_per_page' => -1));

    delete_option('wps_ic_parsed_images');
    delete_option('wps_ic_BulkStatus');
    $bulkStatus = get_option('wps_ic_BulkStatus');

    if (!$bulkStatus) $bulkStatus = array();

    $bulkStatus['foundImageCount'] = 0;

    if ($images) {
      $legacy_compress = new wps_local_compress();
      foreach ($images as $image) {
        $stats = get_post_meta($image->ID, 'ic_stats', true);

        if (!empty($stats)) {
          // Not Compressed
          $file_data = get_attached_file($image->ID);
          $type = wp_check_filetype($file_data);

          if (!empty($excluded_list[$image->ID])) {
            continue;
          }

          // Is file extension allowed
          if (!in_array(strtolower($type['ext']), self::$allowed_types)) {
            continue;
          }

          //wp_get_original_image_path
          $fileUrl = wp_get_original_image_url($image->ID);
          set_transient('wps_ic_compress_' . $image->ID, 'compressing', 60);
          $bulkStatus['foundImageCount'] += 1;
          self::$compressedImages[$image->ID] = $fileUrl;

          update_post_meta($image->ID, 'ic_bulk_running', 'true');
        } else {
          // Not Compressed already
          self::$uncompressedImages[$image->ID] = $image->ID;
        }
      }
    }

    update_option('wps_ic_BulkStatus', $bulkStatus);
    return array('compressed' => self::$compressedImages, 'uncompressed' => self::$uncompressedImages);
  }

  /**
   * Preparing images to send to API
   * @return Array Array of images
   */
  public function prepareImages($action = 'compressing', $process = 'count', $limit = '-1')
  {
    self::$uncompressedImages = array();
    self::$compressedImages = array();
    $images = get_posts(array('post_type' => 'attachment', 'posts_per_page' => $limit));

    if ($process != 'count') {
      delete_option('wps_ic_BulkStatus');
    }

    $bulkStatus = get_option('wps_ic_BulkStatus');
    if (!$bulkStatus) $bulkStatus = array();

    $bulkStatus['foundImageCount'] = 0;
    $bulkStatus['foundThumbCount'] = 0;

    if ($images) {
      $legacy_compress = new wps_local_compress();
      foreach ($images as $image) {
        $stats = get_post_meta($image->ID, 'ic_stats', true);

        if (empty($stats)) {
          // Not Compressed
          $file_data = get_attached_file($image->ID);
          $type = wp_check_filetype($file_data);

          if (!empty($excluded_list[$image->ID])) {
            continue;
          }

          // Is file extension allowed
          if (!in_array(strtolower($type['ext']), self::$allowed_types)) {
            continue;
          }

          //wp_get_original_image_path
          if (!empty(self::$imageSizes)) {
            $bulkStatus['foundImageCount'] += 1;

            foreach (self::$imageSizes as $sizeName => $sizeData) {
              if ($sizeName == 'original') {
                $fileUrl = wp_get_original_image_url($image->ID);
              } else {
                $fileUrl = wp_get_attachment_image_url($image->ID, $sizeName);
              }

              $bulkStatus['foundThumbCount'] += 1;

              //set_transient('wps_ic_compress_' . $image->ID, 'compressing');
              self::$uncompressedImages[$image->ID][$sizeName] = $fileUrl;
            }
          }

          if ($process != 'count') {
            set_transient('wps_ic_compress_' . $image->ID, 'true', 60 * 1);
          }
        } else {
          // Compressed already
          self::$compressedImages[$image->ID] = $image->ID;
        }
      }
    }

    if (!empty($_GET['debugBulkPrepare'])) {
      var_dump(self::$uncompressedImages);
      var_dump('---');
      var_dump(self::$compressedImages);
  }

    if ($action == 'compressing' && $process != 'count') {
      update_option('wps_ic_BulkStatus', $bulkStatus);
    }

    return array('compressed' => self::$compressedImages, 'uncompressed' => self::$uncompressedImages);
  }

}