<?php

class KKL_Api_Players extends KKL_Api_Controller {

  public function getBaseName() {
    return 'players';
  }

  public function register_routes() {
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName(), array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_players'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_player'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/properties', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_info_for_player'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
  }

  protected function getLinks($itemId) {
    return array(
      "properties" => array(
        "href" => $this->getFullBaseUrl() . '/<id>/properties',
        "embeddable" => array(
          "table" => "player_properties",
          "field" => "objectId"
        )
      )
    );
  }

  public function get_players(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = $db->getPlayers();
    return $this->getResponse($request, $items);
  }

  public function get_player(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = array($db->getPlayer($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_info_for_player(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = $db->getPlayerProperties($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

}
