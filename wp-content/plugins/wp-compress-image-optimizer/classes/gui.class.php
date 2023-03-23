<?php


class wpc_gui
{
  
  
  public static $options;
  public static $default;
  public static $safe;
  
  public function __construct()
  {
    $options_class = new wps_ic_options();
    self::$default = $options_class->getDefault();
    self::$safe = $options_class->getSafe();
    self::$options = get_option(WPS_IC_SETTINGS);
    #var_dump(self::$options);
  }
  
  
  public static function checkboxDescription($title = 'Demo', $description = 'Demo', $notify = '', $option = 'default', $locked = false, $value = '1', $configure = false, $tooltip = false, $tooltipPosition = 'left')
  {
    $html = '';
    
    $active = false;
    $circleActive = '';
    
    if ( ! is_array($option)) {
      #$optionName = $option;
      $optionName = 'options['.$option.']';
      $tooltipID = 'option_tooltip_'.$option;
      
      if (isset(self::$options[$option]) && self::$options[$option] == '1') {
        $active = true;
        $circleActive = 'active';
      }

      if (isset(self::$default[$option]) && self::$default[$option] == '1') {
        $default = 1;
      } else {
        $default = 0;
      }

      if (isset(self::$safe[$option]) && self::$safe[$option] == '1') {
        $safe = 1;
      } else {
        $safe = 0;
      }
    }
    else {
      #$optionName = $option[0].','.$option[1];
      $optionName = 'options['.$option[0].']['.$option[1].']';
      $tooltipID = 'option_tooltip_'.$option[0].'_'.$option[1];
      
      if (isset(self::$options[$option[0]][$option[1]]) && self::$options[$option[0]][$option[1]] == '1') {
        $active = true;
        $circleActive = 'active';
      }

      if (isset(self::$default[$option[0]][$option[1]]) && self::$default[$option[0]][$option[1]] == '1') {
        $default = 1;
      } else {
        $default = 0;
      }

      if (isset(self::$safe[$option[0]][$option[1]]) && self::$safe[$option[0]][$option[1]] == '1') {
        $safe = 1;
      } else {
        $safe = 0;
      }
    }
    
    $html .= '<div class="d-flex align-items-top gap-3 option-box">
                <div class="form-check">';
    
    if ($active) {
      $html .= '<input class="form-check-input checkbox mt-0 wpc-ic-settings-v2-checkbox" data-option-name="'.$optionName.'" type="checkbox" checked="checked" value="1" id="'.$optionName.'" name="'.$optionName.'"  data-recommended="'.$default.'" data-safe="'.$safe.'">';
      $html .= '<label for="'.$optionName.'"><span></span></label>';
    }
    else {
      $html .= '<input class="form-check-input checkbox mt-0 wpc-ic-settings-v2-checkbox" data-option-name="'.$optionName.'"  type="checkbox" value="1" id="'.$optionName.'" name="'.$optionName.'"  data-recommended="'.$default.'" data-safe="'.$safe.'">';
      $html .= '<label for="'.$optionName.'"><span></span></label>';
    }
    
    $html .= '</div>

                <div class="left-col">
                  <p class="fs-300 text-dark-300 fw-500 p-inline">'.$title.'</p>';
    
    if ($tooltip) {
      $html .= '<span class="wpc-custom-tooltip" data-tooltip-id="'.$tooltipID.'" data-tooltip-position="left"><i class="tooltip-icon"></i></span>';
    }
    
    if ( ! empty($configure) && $configure !== false) {
      $html .= '<p class="fs-200 text-dark-300 fw-400 p-inline p-float-right"><a href="#" class="wps-ic-configure-popup" data-popup="'.$configure.'" data-popup-width="750">Configure</a></p>';
    }
    
    if ( ! $tooltip) {
      $html .= '<p class="fs-300 text-secondary-400 fw-400">'.$description.'</p>';
    }
    else {
      $html .= '<div id="'.$tooltipID.'" class="wpc-ic-popup wpc-ic-popup-position-' . $tooltipPosition . '" style="display: none;">';
      
      if ( ! empty($title)) {
        $html .= '<div class="pop-header">
                      '.$title.'
                    </div>';
      }
      
      $html .= '<p class="pop-text">
                      '.$description.'
                    </p>
                  </div>';
    }
    
    if ( ! empty($notify)) {
      $html .= '<div class="activate-notification" style="display:none;">
                    <img src="'.WPS_IC_URI.'assets/v2/assets/images/notification.png" alt="">
                    <p>'.$notify.'</p>
                  </div>';
    }
    
    $html .= '</div>
              </div>';
    
    return $html;
  }
  
  

  
  public static function getSetting($name) {
    return self::$options[$name];
  }

  
  
  public static function checkBoxOption($title = 'Demo', $option = 'default', $locked = false, $value = '1', $align = 'right',  $description = '', $tooltip = false, $tooltipPosition = 'top')
  {
    $html = '';
    
    $active = false;
    $circleActive = '';
    
    if ( ! is_array($option)) {
      #$optionName = $option;
      $optionName = 'options['.$option.']';
      $tooltipID = 'option_tooltip_'.$option;
      if (isset(self::$options[$option]) && self::$options[$option] == '1') {
        $active = true;
        $circleActive = 'active';
      }

      if (isset(self::$default[$option]) && self::$default[$option] == '1') {
        $default = 1;
      } else {
        $default = 0;
      }

      if (isset(self::$safe[$option]) && self::$safe[$option] == '1') {
        $safe = 1;
      } else {
        $safe = 0;
      }
    }
    else {
      #$optionName = $option[0].','.$option[1];
      $optionName = 'options['.$option[0].']['.$option[1].']';
      $tooltipID = 'option_tooltip_'.$option[0].'_'.$option[1];
      
      if (isset(self::$options[$option[0]][$option[1]]) && self::$options[$option[0]][$option[1]] == '1') {
        $active = true;
        $circleActive = 'active';
      }

      if (isset(self::$default[$option[0]][$option[1]]) && self::$default[$option[0]][$option[1]] == '1') {
        $default = 1;
      } else {
        $default = 0;
      }

      if (isset(self::$safe[$option[0]][$option[1]]) && self::$safe[$option[0]][$option[1]] == '1') {
        $safe = 1;
      } else {
        $safe = 0;
      }
    }
    
    if ($align == 'right') {
      $html .= '<div class="accordion-item option-item option-box">
                  <h2 class="accordion-header d-flex align-items-center justify-content-between gap-2 fs-400" id="flush-headingOne">
                    <div class="d-flex align-items-center gap-2">
                      <div class="circle-check '.$circleActive.'"></div>
                      <p class="fs-300 text-dark-300">'.$title.'</p>';
  
      if ($tooltip) {
        $html .= '<span class="wpc-custom-tooltip" data-tooltip-id="'.$tooltipID.'" data-tooltip-position="' . $tooltipPosition . '"><i class="tooltip-icon"></i></span>';
      }
      
      $html .= '</div>
                    <div class="form-check">';
      
      if ($locked) {
        $html .= '<input class="form-check-input checkbox mt-0 locked-checkbox" type="checkbox" value="1" id="flexCheckDefault">';
      }
      else {
        if ($active) {
          $html .= '<input class="form-check-input checkbox mt-0 wpc-ic-settings-v2-checkbox" data-option-name="'.$optionName.'" type="checkbox" checked="checked" value="1" id="'.$optionName.'" name="'.$optionName.'" data-recommended="'.$default.'" data-safe="'.$safe.'">';
          $html .= '<label for="'.$optionName.'"><span></span></label>';
        }
        else {
          $html .= '<input class="form-check-input checkbox mt-0 wpc-ic-settings-v2-checkbox" data-option-name="'.$optionName.'"  type="checkbox" value="1" id="'.$optionName.'" name="'.$optionName.'" data-recommended="'.$default.'" data-safe="'.$safe.'">';
          $html .= '<label for="'.$optionName.'"><span></span></label>';
        }
      }
      
      if ($tooltip) {
        
        $html .= '<div id="'.$tooltipID.'" class="wpc-ic-popup wpc-ic-popup-position-' . $tooltipPosition . '" style="display: none;">';
  
        if ( ! empty($title)) {
          $html .= '<div class="pop-header">
                      '.$title.'
                    </div>';
        }
  
        $html .= '<p class="pop-text">
                      '.$description.'
                    </p>
                  </div>';
      }
      
      $html .= '</div>
                  </h2>
                </div>';
    }
    else {
      $html .= '<div class="accordion-item option-item">
                  <h2 class="accordion-header d-flex align-items-center justify-content-between gap-2 fs-400" id="flush-headingOne">
                    <div class="d-flex align-items-center gap-2">';
      
      if ($locked) {
        $html .= '<input class="form-check-input checkbox mt-0 locked-checkbox" type="checkbox" value="1" id="flexCheckDefault">';
      }
      else {
        if ($active) {
          $html .= '<input class="form-check-input checkbox mt-0 wpc-ic-settings-v2-checkbox" data-option-name="'.$optionName.'" type="checkbox" checked="checked" value="1" id="'.$optionName.'" name="'.$optionName.'"  data-recommended="'.$default.'" data-safe="'.$safe.'">';
          $html .= '<label for="'.$optionName.'"><span></span></label>';
        }
        else {
          $html .= '<input class="form-check-input checkbox mt-0 wpc-ic-settings-v2-checkbox" data-option-name="'.$optionName.'"  type="checkbox" value="1" id="'.$optionName.'" name="'.$optionName.'"  data-recommended="'.$default.'" data-safe="'.$safe.'">';
          $html .= '<label for="'.$optionName.'"><span></span></label>';
        }
      }
      
      $html .= '<p class="text-dark-300">'.$title.'</p>';
      $html .= '</div><div class="form-check">';
      
      $html .= '</div>
                  </h2>
                </div>';
    }
    
    return $html;
  }
  
  
}