<?php
global $zoneName, $cssUrlPath, $cssUrl, $zoneName, $cssPath, $dirName, $siteUrl;

$debug = false;

function pathWalker($path, $find)
{
  $paths = explode('/', $path);
  $foldersUp = substr_count($find, '../');

  $array = array_splice($paths, 0, -$foldersUp);
  $array = implode('/', $array);

  return $array;
}

// Code Here
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
  $protocol = 'https://';
} else {
  $protocol = 'http://';
}

// Get the current site URL
$siteUrl = $protocol . $_SERVER['HTTP_HOST'];

// Decode the URL, because it's encoded in GET
$cssUrl = urldecode($_GET['css']);
// Zone Name
$zoneName = urldecode($_GET['zoneName']);

// Get the filename
$cssFilename = basename($cssUrl);
$cssUrlPath = str_replace($cssFilename, '', $cssUrl);
if (!empty($_GET['debug'])) {
  var_dump($cssFilename);
}

$cssFilename = explode('?', $cssFilename);
if (!empty($_GET['debug'])) {
  var_dump($cssFilename);
}

$cssFilename = $cssFilename[0];
if (!empty($_GET['debug'])) {
  var_dump($cssFilename);
}

// Get the actual path to the file
if (!empty($_GET['debug'])) {
  var_dump($cssUrlPath);
}

// Remove the site URL from the Path to retrieve just the path
$cssPath = str_replace([$siteUrl . '/', 'http://' . $_SERVER['HTTP_HOST'] . '/'], '', $cssUrlPath);
$cssPath = rtrim($cssPath, '/');

// Get clean ABSPATH
$dirName = str_replace('/wp-content/plugins/wp-compress-image-optimizer', '', dirname(__FILE__));
$filePath = $dirName . '/' . $cssPath . '/' . $cssFilename;

if (!empty($_GET['debug'])) {
  var_dump($siteUrl);
  var_dump($cssUrl);
  var_dump($zoneName);
  var_dump($cssFilename);
  var_dump($filePath);
  var_dump('File Exists: ' . print_r(file_exists($filePath), true));
  var_dump(file_exists($filePath));
}

function replaceCSS($matches)
{
  global $zoneName, $cssUrlPath, $cssUrl, $zoneName, $cssPath, $dirName, $siteUrl;

  if (!empty($_GET['dbg_matches'])) {
    return print_r(array($zoneName), true);
  }

  if (!empty($matches)) {
    #foreach ($matches[1] as $k => $match) {
    $foundUrls = $matches[1];

    if (!empty($_GET['dbg_matches_3'])) {
      return print_r(array($foundUrls), true);
    }

    if (strpos($foundUrls, 'data:') !== false) {
      return 'url("' . $foundUrls . '")';
    } else {

      if (!empty($_GET['dbg_matches_2'])) {
        return print_r(array($foundUrls), true);
      }

      $foundUrls = str_replace('("', '', $foundUrls);
      $foundUrls = str_replace("('", '', $foundUrls);
      $foundUrls = str_replace('")', '', $foundUrls);
      $foundUrls = str_replace("')", '', $foundUrls);

      // Remove the wrapping brackets
      $foundUrls = rtrim($foundUrls, ')');
      $foundUrls = ltrim($foundUrls, '(');

      // If the found url has // or http/s, just set on CDN?
      if (strpos($foundUrls, '//') === 0 || strpos($foundUrls, 'http') === 0) {
        // Real URL, leave alone?
        $apiUrl = 'https://' . $zoneName . '/m:0/a:' . $foundUrls;
        #return 'url("' . $apiUrl . '")';
        return 'url("' . $foundUrls . '")';
      } else {

        // Remove the wrapping brackets
        $foundUrls = rtrim($foundUrls, ')');
        $foundUrls = ltrim($foundUrls, '(');

        // If the found url has at least one ../ then do something with it
        if (strpos($foundUrls, '../') !== false) {
          // Count how many ../, that's how many folders up we need to go
          #$countFoldersUp = substr_count($foundUrls, '../');

          // Get just the clean path, without ../
          $removeRelative = str_replace('../', '', $foundUrls);

          // Explode ? so that we remove query var "../whatever.css?ver=12.94#4e0
          $removedQueryVar = explode('?', $removeRelative);
          $removedQueryVar = $removedQueryVar[0];

          // Run walker, this one figures out the actual path depending on number of folders up
          $walker = pathWalker($cssPath, $foundUrls);

          // Add the filename
          $walker .= '/' . $removedQueryVar;

          if (!empty($_GET['debugWalker2'])) {
            return print_r(array($dirName, $walker, $cssPath, $foundUrls), true);
          }

          // Once again, check if the file exists in figured out path
          if (file_exists($dirName . '/' . $walker)) {
            #$apiUrl = 'https://' . $zoneName . '/m:0/a:' . $siteUrl . '/' . $walker;
            #return 'url("' . $apiUrl . '")';
            return 'url("' . $siteUrl . '/' . $walker . '")';
          }
        } elseif (strpos($foundUrls, './') !== false) {

          // Same folder
          $foundUrls = ltrim($foundUrls, '(');
          $foundUrls = rtrim($foundUrls, ')');

          // Get just the clean path, without ../
          $removeRelative = str_replace('./', '', $foundUrls);

          // Once again, check if the file exists in figured out path
          return 'url("' . $cssUrlPath . $removeRelative . '")';
        } elseif (strpos($foundUrls, '/wp-content') !== false && strpos($foundUrls, '/wp-content') == 0) {

          $foundUrls = str_replace('("', '', $foundUrls);
          $foundUrls = str_replace("('", '', $foundUrls);
          $foundUrls = str_replace('")', '', $foundUrls);
          $foundUrls = str_replace("')", '', $foundUrls);
          #$apiUrl = 'https://' . $zoneName . '/m:0/a:' . $siteUrl . '' . $foundUrls;
          #return 'url("' . $apiUrl . '")';
          return 'url("' . $siteUrl . '' . $foundUrls . '")';
        } else {
          // Let's guess it's in root where the file is
          #$foundUrls = explode('?',$foundUrls);
          $cleanUrl = $foundUrls;
          if (!empty($_GET['debug'])) {
            var_dump('----find----');
            var_dump($foundUrls);
            var_dump($cssUrlPath);
            var_dump($cssUrlPath . $foundUrls);
            var_dump('----120----');
            var_dump($cleanUrl);
            var_dump($cssUrlPath);
            var_dump($cleanUrl);
            var_dump('----124---');
          }
          return 'url("' . $cssUrlPath . $foundUrls . '")';
        }
      }
    }
    #}
  }
}

// Check if the CSS file exists, as that way we know we have the correct PATH
if (file_exists($filePath)) {
  $fileContents = file_get_contents($filePath);

  if (!empty($fileContents)) {
    #$fpEdited = preg_match_all("/url\(\s*['\"]?((.+?)\.(woff2|woff|eot|ttf|svg|jpg|jpeg|gif|png).*?)['\"]?\s*\)/i", $fileContents, $matches);
    #$fileContents = preg_replace_callback("/url\(\s*['\"]?((.+?)\.(woff2|woff|eot|ttf|svg|jpg|jpeg|gif|png).*?)['\"]?\s*\)/i", 'replaceCSS', $fileContents);

    // Igniore the error in regexp below!! IDE IS STUPID
    $re = '/url(\(((?:[^()]+|(?1))+)\))/m';
    $fileContents = preg_replace_callback($re, 'replaceCSS', $fileContents);

    $ts = gmdate("D, d M Y H:i:s") . " GMT";
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + ((60 * 60 * 24 * 365)))); // 1 year
    header("Last-Modified: $ts");
    header('Cache-Control:public max-age=84600, s-maxage=84600');
    header('Content-Type:text/css');
    $fileContents = trim($fileContents);
    // TODO: Creates broken CSS on hypereffects.com
    #$fileContents = preg_replace('/\s+/', '', $fileContents);
    echo $fileContents;
    die();
  }
}

if (!empty($_GET['debug'])) {
  die('Debug done');
}

header('Location: ' . $cssUrl, 302);
die();