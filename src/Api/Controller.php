<?php

namespace KKL\Ligatool\Api;

use KKL\Ligatool\DB;
use stdClass;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

/**
 * @SWG\Swagger(
 *   schemes={"http"},
 *   host="www.kickerligakoeln.de",
 *   basePath="/wp-json/kkl/v1",
 *   @SWG\Info(
 *     title="RESTful API für die Kölner Kickerliga",
 *     version="1.0.0"
 *   )
 * )
 */
abstract class Controller extends WP_REST_Controller {

  private $page = 1;
  private $per_page = 100;
  private $offset = 0;
  private $reservedQueryParams = array(
    'fields',
    'order',
    'orderby',
    'page',
    'per_page',
    'offset',
    'embed'
  );

  /**
   * @param WP_REST_Request $request
   * @param array $items
   * @param bool $hideLinks
   * @return WP_Error|WP_REST_Response
   */
  protected function getResponse($request, $items, $hideLinks = false) {

    if (!$items || !is_array($items) || empty($items)) {
      return new WP_Error('not_found', 'Could not find any matching items for your request', array('status' => 404));
    } elseif (count($items) == 0) {
      return new WP_Error('not_found', 'Could not find any matching items for your request', array('status' => 404));
    }

    $data = [];
    $preparedItems = array();
    foreach ($items as $key => $item) {
      $preparedItems[$key] = $this->prepare_item_for_response($item, $request);
    }

    foreach ($this->filterItemsByRequest($request, $preparedItems) as $key => $item) {
      $data[$key] = $this->prepare_response_for_collection($item);
    }

    $sortParam = $request->get_param('orderby');
    $orderParam = $request->get_param('order');
    if ($sortParam) {
      $order = strtoupper($orderParam);
      usort($data, function ($a, $b) use ($sortParam, $order) {
        if (property_exists($a, $sortParam) && property_exists($b, $sortParam)) {
          if ($a->{$sortParam} == $b->{$sortParam}) {
            return 0;
          }
          if ($order == "DESC") {
            return ($a->{$sortParam} > $b->{$sortParam}) ? -1 : 1;
          } else {
            return ($a->{$sortParam} < $b->{$sortParam}) ? -1 : 1;
          }
        } else {
          return 0;
        }
      });
    }

    $reducedItems = array();
    $fieldsParam = $request->get_param('fields');
    if ($fieldsParam) {
      $wantedFields = explode(',', $fieldsParam);
      foreach ($data as $item) {
        $reducedItem = new stdClass();
        foreach ($wantedFields as $wantedField) {
          if (property_exists($item, $wantedField)) {
            $reducedItem->{$wantedField} = $item->{$wantedField};
          }
        }
        $reducedItems[] = $reducedItem;
      }
    } else {
      $reducedItems = $data;
    }
    if (count($reducedItems) == 1) {
      $reducedItems = $reducedItems[0];
    }

    //return a response or error based on some conditional
    if (1 == 1) {
      $totalPages = $this->page;
      $totalItems = count($reducedItems);
      if (is_array($reducedItems)) {
        $reqPage = $request->get_param('page');
        $reqPerPage = $request->get_param('per_page');
        $reqOffset = $request->get_param('offset');
        if (is_numeric($reqPage) && $reqPage > 0) {
          $this->page = $reqPage;
        }
        if (is_numeric($reqPerPage) && $reqPage > 0) {
          $this->per_page = $reqPerPage;
        }
        if (is_numeric($reqOffset) && $reqOffset > 0) {
          $this->offset = $reqOffset;
        }
        $reducedItems = array_slice($reducedItems, $this->offset);
        $reducedItems = array_slice($reducedItems, ($this->per_page * ($this->page - 1)), $this->per_page);
        $totalPages = ceil($totalItems / $this->per_page);

        if (!$hideLinks && (!$fieldsParam || strpos($fieldsParam, '_links') !== false)) {
          foreach ($reducedItems as $key => $reducedItem) {
            if (is_object($reducedItem)) {
              $reducedItems[$key] = $this->addLinks($reducedItem);
            } else {
              $reducedItems[$key] = $reducedItem;
            }
          }
        }
        $reqEmbeds = $request->get_param('embed');
        if ($reqEmbeds) {
          $embeds = explode(',', $reqEmbeds);
          foreach ($reducedItems as $key => $reducedItem) {
            if (is_object($reducedItem)) {
              $reducedItems[$key] = $this->addEmbeddables($reducedItem, $embeds);
            } else {
              $reducedItems[$key] = $reducedItem;
            }
          }
        }
      } else {
        if (!$hideLinks && (!$fieldsParam || strpos($fieldsParam, '_links') !== false)) {
          $reducedItems = $this->addLinks($reducedItems);
        }
        $reqEmbeds = $request->get_param('embed');
        if ($reqEmbeds) {
          $embeds = explode(',', $reqEmbeds);
          $reducedItems = $this->addEmbeddables($reducedItems, $embeds);
        }
      }

      $response = new WP_REST_Response($reducedItems, 200);
      $response->header('X-Total-Count', $totalItems);
      $response->header('X-Total-Pages', $totalPages);
      $response->header('X-WP-Total', $totalItems);
      $response->header('X-WP-TotalPages', $totalPages);
      // $link = '<https://api.github.com/user/repos?page=3&per_page=100>; rel="next", <https://api.github.com/user/repos?page=50&per_page=100>; rel="last"';
      // $response->header('Link', $link);
      return $response;
    } else {
      return new WP_Error('code', __('message', 'text-domain'));
    }

  }

  /**
   * Prepare the item for the REST response
   *
   * @param mixed $item WordPress representation of the item.
   * @param WP_REST_Request $request Request object.
   * @return mixed
   */
  public function prepare_item_for_response($item, $request) {
    return $this->replaceKeys($item);
  }

  private function replaceKeys($item) {
    if (is_array($item)) {
      $newItem = array();
      foreach ($item as $key => $value) {
        $camelKey = $this->toCamelCase($key);
        $newItem[$camelKey] = $this->replaceKeys($value);
      }
      return $newItem;
    } else if (is_object($item)) {
      $newItem = new stdClass();
      foreach ($item as $key => $value) {
        $camelKey = $this->toCamelCase($key);
        $newItem->{$camelKey} = $this->replaceKeys($value);
      }
      return $newItem;
    } else {
      return $item;
    }
  }

  private function toCamelCase($string) {
    $str = str_replace('_', '', ucwords($string, '_'));
    $str = lcfirst($str);
    return $str;
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
    foreach ($items as $key => $item) {
      $keepItem = true;
      foreach ($searchParams as $searchParam => $search) {
        if ($keepItem && (property_exists($item, $searchParam) && $item->{$searchParam} != $search)) {
          $keepItem = false;
        }
      }
      if ($keepItem) {
        $filtredItems[$key] = $item;
      }
    }
    return $filtredItems;
  }

  private function addLinks($item) {
    if (!is_object($item)) {
      return $item;
    }
    $links = array(
      'self' => array(
        'href' => $this->getFullBaseUrl() . '/' . $item->id
      ),
      'collection' => array(
        'href' => $this->getFullBaseUrl()
      )
    );
    foreach ($this->getLinks($item->id) as $key => $value) {
      $href = str_replace('<id>', $item->id, $value['href']);
      $idFields = $value['idFields'];
      if (isset($idFields) && is_array($idFields)) {
        foreach ($idFields as $idField) {
          if (property_exists($item, $idField)) {
            $href = str_replace('<propertyid>', $item->{$idField}, $href);
          }
        }
      }
      $href = str_replace('<propertyid>', $item->{$key}, $href);
      $embeddable = $value['embeddable'];
      $links[$key] = array(
        'href' => $href,
        'embeddable' => is_array($embeddable)
      );
    }
    $item->_links = $links;
    return $item;
  }

  protected function getFullBaseUrl() {
    return $this->getFullBaseUrlFor($this->getBaseName());
  }

  protected function getFullBaseUrlFor($basename) {
    return get_site_url() . '/wp-json/' . $this->getNamespace() . '/' . $basename;
  }

  public function getNamespace() {
    $version = 1;
    $namespace = 'kkl' . '/v' . $version;
    return $namespace;
  }

  protected abstract function getBaseName();

  protected function getLinks($itemId) {
    return array();
  }

  private function addEmbeddables($item, $wantedEmbeds) {
    $links = $this->getLinks($item->id);
    if (empty($links)) {
      return $item;
    }
    $db = new DB\Api();
    $embeddables = array();
    foreach ($wantedEmbeds as $wantedEmbed) {
      if (isset($links[$wantedEmbed]) && isset($links[$wantedEmbed]['embeddable']) && is_array($links[$wantedEmbed]['embeddable'])) {
        $config = $links[$wantedEmbed]['embeddable'];
        if ($config['callback'] && is_callable($config['callback'])) {
          $embeddables[$wantedEmbed] = $config['callback']();
        } else {
          $id = $item->id;
          if ($links[$wantedEmbed]['idFields'] && is_array($links[$wantedEmbed]['idFields'])) {
            foreach ($links[$wantedEmbed]['idFields'] as $idField) {
              if (property_exists($item, $idField)) {
                $id = $item->{$idField};
              }
            }
          }
          $embeddables[$wantedEmbed] = $db->getEmbeddable($config['table'], $config['field'], $id);
        }
      }
    }
    if (!empty($embeddables)) {
      $item->_embedded = $embeddables;
    }
    return $item;
  }
}
