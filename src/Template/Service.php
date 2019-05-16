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
    static::$engine = $kkl_twig;
  }

}
