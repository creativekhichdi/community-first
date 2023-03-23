<?php


class wps_minifyHtml
{
  
  public function __construct() {
  }
  
  
  public function minify($buffer)
  {
    $search = [
        '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
        '/[^\S ]+\</s',     // strip whitespaces before tags, except space
        //'/(\s)+/s',         // shorten multiple whitespace sequences
        #'/<!--(.|\s)*?-->/' // Remove HTML comments, not working
    ];
    
    $replace = [
        '>',
        '<',
        //'\\1',
        ''
    ];
    
    $buffer = preg_replace($search, $replace, $buffer);
    
    return $buffer;
  }
  
  
  public function removeComments($buffer) {
    return preg_replace('/(?=<!--)([\s\S]*?)-->/', '', $buffer);
  }
  
  
}