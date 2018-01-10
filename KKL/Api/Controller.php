<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 30.12.17
 * Time: 19:35
 */

abstract class KKL_Api_Controller extends WP_REST_Controller {

  private $reservedQueryParams = array('q', 'sort');

  public function getNamespace() {
    $version = 1;
    $namespace = 'kkl' . '/v' . $version;
    return $namespace;
  }

  protected function toCamelCase($string) {
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
    $newItem = new stdClass();
    foreach ($item as $key => $value) {
      $camelKey = $this->toCamelCase($key);
      $newItem->{$camelKey} = $value;
    }
    return $newItem;
  }

  /**
   * @param WP_REST_Request $request
   * @param array $items
   * @return WP_Error|WP_REST_Response
   */
  protected function getResponse($request, $items) {

    if(!$items || !is_array($items) || empty($items)) {
      return new WP_REST_Response(array(), 404);
    }elseif(!$items[0]) {
      return new WP_REST_Response(array(), 404);
    }

    $data = [];
    $preparedItems = array();
    foreach ($items as $item) {
      $preparedItems[] = $this->prepare_item_for_response($item, $request);
    }

    foreach ($this->filterItemsByRequest($request, $preparedItems) as $item) {
      $data[] = $this->prepare_response_for_collection($item);
    }
    $sortParam = $request->get_param('sort');
    if($sortParam) {
      $modify = substr($sortParam, 0, 1);
      $order = "ASC";
      if($modify === "-") {
        $order = "DESC";
        $sortParam = substr($sortParam, 1);
      }
      usort($data, function($a, $b) use ($sortParam, $order) {
        if(property_exists($a, $sortParam) && property_exists($b, $sortParam)) {
          if ($a->{$sortParam} == $b->{$sortParam}) {
            return 0;
          }
          if($order == "DESC") {
            return ($a->{$sortParam} > $b->{$sortParam}) ? -1 : 1;
          }else{
            return ($a->{$sortParam} < $b->{$sortParam}) ? -1 : 1;
          }
        }else{
          return 0;
        }
      });
    }
    if (count($data) == 1) {
      $data = $data[0];
    }

    //return a response or error based on some conditional
    if (1 == 1) {
      return new WP_REST_Response($data, 200);
    } else {
      return new WP_Error('code', __('message', 'text-domain'));
    }

  }

  /**
   * @param WP_REST_Request $request
   * @param array $items
   * @return array
   */
  private function filterItemsByRequest($request, $items) {
    $searchParams = array_filter($request->get_query_params(), function ($k) {
      return !in_array($k, $this->reservedQueryParams);
    }, ARRAY_FILTER_USE_KEY);
    $filtredItems = array();
    foreach ($items as $item) {
      $keepItem = true;
      foreach ($searchParams as $searchParam => $search) {
        if ($keepItem && (property_exists($item, $searchParam) && $item->{$searchParam} != $search)) {
          $keepItem = false;
        }
      }
      if ($keepItem) {
        $filtredItems[] = $item;
      }
    }
    return $filtredItems;
  }

}
