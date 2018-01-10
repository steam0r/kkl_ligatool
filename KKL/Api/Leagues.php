<?php

class KKL_Api_Leagues extends KKL_Api_Controller {

  public function register_routes() {
    $base = 'leagues';
    register_rest_route($this->getNamespace(), '/' . $base, array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_items'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/seasons', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_seasons_for_league'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_item'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
  }

  /**
   * Get a collection of items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function get_items($request) {
    $db = new KKL_DB();
    $items = $db->getLeagues();
    $data = array();
    foreach ($items as $item) {
      $itemdata = $this->prepare_item_for_response($item, $request);
      $data[] = $this->prepare_response_for_collection($itemdata);
    }

    return new WP_REST_Response($data, 200);
  }

  public function get_seasons_for_league(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getSeasonsByLeague($request->get_param('id'));
    foreach ($items as $item) {
      $itemdata = $this->prepare_item_for_response($item, $request);
      $data[] = $this->prepare_response_for_collection($itemdata);
    }

    return new WP_REST_Response($data, 200);
  }

  public function get_item($request) {
    $db = new KKL_DB();
    $item = $db->getLeague($request->get_param('id'));
    $data = $this->prepare_item_for_response($item, $request);

    //return a response or error based on some conditional
    if (1 == 1) {
      return new WP_REST_Response($data, 200);
    } else {
      return new WP_Error('code', __('message', 'text-domain'));
    }
  }

}
