<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 30.12.17
 * Time: 19:35
 */

abstract class KKL_Api_Controller extends WP_REST_Controller {

  private $reservedQueryParams = array('q', 'order', 'jsonp');

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
    $sortParam = $request->get_param('order');
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

    $reducedItems = array();
    $fieldsParam = $request->get_param('fields');
    if($fieldsParam) {
      $wantedFields = explode(',', $fieldsParam);
      foreach ($data as $item) {
        $reducedItem = new stdClass();
        foreach ($wantedFields as $wantedField) {
          if(property_exists($item, $wantedField)) {
            $reducedItem->{$wantedField} = $item->{$wantedField};
          }
        }
        $reducedItems[] = $reducedItem;
      }
    }else{
      $reducedItems = $data;
    }
    if (count($reducedItems) == 1) {
      $reducedItems = $reducedItems[0];
    }

    //return a response or error based on some conditional
    if (1 == 1) {
      $response = new WP_REST_Response($reducedItems, 200);
      $response->header('X-Total-Count', sizeof($reducedItems));
      $response->header('X-Total-Pages', 1);
      $response->header('X-WP-Total', sizeof($reducedItems));
      $response->header('X-WP-TotalPages', 1);
      // $link = '<https://api.github.com/user/repos?page=3&per_page=100>; rel="next", <https://api.github.com/user/repos?page=50&per_page=100>; rel="last"';
      // $response->header('Link', $link);
      return $response;
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
