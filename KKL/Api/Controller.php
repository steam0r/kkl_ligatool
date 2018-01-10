<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 30.12.17
 * Time: 19:35
 */

abstract class KKL_Api_Controller extends WP_REST_Controller {

  public function getNamespace() {
    $version = 1;
    $namespace = 'kkl' . '/v' . $version;
    return $namespace;
  }

  protected  function toCamelCase($string)
  {
    $str = str_replace('_', '', ucwords($string, '_'));
    $str = lcfirst($str);
    return $str;
  }

  /**
   * Prepare the item for the REST response
   *
   * @param mixed $item WordPress representation of the item.
   * @param WP_REST_Request $request Request object.
   * @return mixed
   */
  public function prepare_item_for_response($item, $request) {
    return $item;
  }

}
