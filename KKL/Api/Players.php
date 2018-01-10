<?php

class KKL_Api_Players extends KKL_Api_Controller {

  public function register_routes() {
    $base = 'players';
    register_rest_route($this->getNamespace(), '/' . $base, array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_players'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_player'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/info', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_info_for_player'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
  }

  public function get_players(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getPlayers();
    return $this->getResponse($request, $items);
  }

  public function get_player(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = array($db->getPlayer($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_info_for_player(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getPlayerProperties($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

}
