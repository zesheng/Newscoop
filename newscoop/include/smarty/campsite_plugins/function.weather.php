<?php
/**
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Get weather
 *
 * @param array $params
 * @param Smarty_Internal_Template $smarty
 * @return string
 */
function smarty_function_weather(array $params, Smarty_Internal_Template $smarty)
{
    $context = $smarty->getTemplateVars('gimme');

    if (empty($params['hour'])) {
        $hour = date('H');
    } else {
        $hour = $params['hour'];
    }

    $temperature = \SystemPref::get('weather_temperature_'.$params['location'].'_'.$hour);
    $icon = \SystemPref::get('weather_icon_'.$params['location'].'_'.$hour);
      
    $themePath = $context->issue->defined() ? $context->issue->theme_path : $context->publication->theme_path;
    $result = "<img src='/themes/$themePath/assets/img/meteonews/symb/$icon.png' class='mn-symbol-small'>";

    if (isset($params['hour'])) {
        $result .= "<p>" . $temperature . "° </p>";
    } else {
        $result .= "<span>" . $temperature . "°C ".ucfirst($params['location'])."</span>";
    }
    
    return($result);
}
