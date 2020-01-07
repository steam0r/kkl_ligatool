<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 22.02.18
 * Time: 11:23
 */

namespace KKL\Ligatool\Template;

use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use Twig_SimpleFilter;

class Service {

  /**
   * @var Engine
   */
  private static $engine = null;

  /**
   * @return Engine
   */
  public static function getTemplateEngine() {
    if (static::$engine == null) {
      static::initTemplateEngine();
    }

    return static::$engine;
  }

  private static function initTemplateEngine($debug = false) {
    $kkl_twig = new Engine(new Twig_Loader_Filesystem(plugin_dir_path(__FILE__) . '../../templates/'), array('debug' => $debug));
    if ($debug) {
      $kkl_twig->addExtension(new Twig_Extension_Debug());
    }

    $filters = self::addTemplateFilters();
    foreach ($filters as $filter) {
      $kkl_twig->addFilter($filter);
    }

    static::$engine = $kkl_twig;
  }


  /**
   * @return array
   */
  private static function addTemplateFilters() {
    $filters = array();

    // usage: {{ '/image.png'|imgSrc }}
    $filters[] = new Twig_SimpleFilter( 'imgSrc', function( $image ) {
      return plugins_url( '../../images' . $image, __FILE__ );
    } );

    // usage: {{ '/teams/rakete'|baseUrl }}
    $filters[] = new Twig_SimpleFilter( 'baseUrl', function( $path, $pre = '' ) {
      return site_url( $pre . $path );
    } );

    return $filters;
  }

}
