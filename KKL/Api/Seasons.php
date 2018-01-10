<?php

class KKL_Api_Seasons extends KKL_Api_Controller {

  public function register_routes() {
    $base = 'seasons';
    register_rest_route($this->getNamespace(), '/' . $base, array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_items'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_item'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/leagues', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_leagues_for_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
  }

  public function get_items($request) {
    $db = new KKL_DB();
    $items = $db->getSeasons();
    foreach ($items as $item) {
      $itemdata = $this->prepare_item_for_response($item, $request);
      $data[] = $this->prepare_response_for_collection($itemdata);
    }

    return new WP_REST_Response($data, 200);
  }

  public function get_leagues_for_season(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getLeagueForSeason($request->get_param('id'));
    foreach ($items as $item) {
      $itemdata = $this->prepare_item_for_response($item, $request);
      $data[] = $this->prepare_response_for_collection($itemdata);
    }

    return new WP_REST_Response($data, 200);
  }

  public function get_item($request) {
    $db = new KKL_DB();
    $item = $db->getSeason($request->get_param('id'));
    $data = $this->prepare_item_for_response($item, $request);

    //return a response or error based on some conditional
    if (1 == 1) {
      return new WP_REST_Response($data, 200);
    } else {
      return new WP_Error('code', __('message', 'text-domain'));
    }
  }

}
