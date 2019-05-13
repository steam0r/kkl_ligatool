<?php

namespace KKL\Ligatool\Utils;

use KKL\Ligatool\Plugin;

class LinkUtils {

  /**
   * @param $type
   * @param $pathValue
   * @param string $customPrefix
   * @return string
   */
  public static function getLink($type, $pathValue, $customPrefix = '') {
    $pathName = $pathValue['pathname'] || '';

    $pluginFile = __FILE__;
    $baseUrl = plugin_dir_url(__FILE__);
    $basePath = plugin_dir_path(__FILE__);
    $plugin = new Plugin($pluginFile, $baseUrl, $basePath);

    switch($type) {
      case 'club':
        $clubsContext = $plugin->getPageTemplateContext('clubs');
        $customPrefix = $clubsContext->post_name . '/';
        $pathName = $pathValue['pathname'];
        break;

      case 'league':
        $clubsContext = $plugin->getPageTemplateContext('ranking');
        $customPrefix = $clubsContext->post_name . '/';

        $pathName = $pathValue['league'];
        if($pathValue['season'])
          $pathName .= "/" . $pathValue['season'];
        if($pathValue['game_day'])
          $pathName .= "/" . $pathValue['game_day'];
        break;

      case 'schedule':
        $customPrefix = 'spielplan/';
        $pathName = $pathValue['league'] . '/' . $pathValue['season'];
        break;

      default:
    }

    return get_site_url() . '/' . $customPrefix . $pathName;
  }
}