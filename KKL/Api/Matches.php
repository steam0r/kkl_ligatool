<?php

class KKL_Api_Matches extends KKL_Api_Controller {

  public function register_routes() {
    $base = 'matches';
    register_rest_route($this->getNamespace(), '/' . $base, array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_matches'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_match'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/info', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_info_for_match'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
  }

  public function get_matches(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getMatches();
    return $this->getResponse($request, $items);
  }

  public function get_match(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = array($db->getMatch($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_info_for_match(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getFullMatchInfo($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

}
