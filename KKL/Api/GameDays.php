<?php

class KKL_Api_GameDays extends KKL_Api_Controller {

  public function register_routes() {
    $base = 'gamedays';
    register_rest_route($this->getNamespace(), '/' . $base, array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_gamedays'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_gameday'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/matches', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_matches_for_gameday'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/next', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_next_gameday'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/previous', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_previous_gameday'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
  }

  public function get_gamedays(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getGameDays();
    return $this->getResponse($request, $items);
  }

  public function get_gameday(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = array($db->getGameDay($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_matches_for_gameday(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getMatchesByGameDay($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

  public function get_next_gameday(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = array($db->getNextGameDay($db->getGameDay($request->get_param('id'))));
    return $this->getResponse($request, $items);
  }

  public function get_previous_gameday(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = array($db->getPreviousGameDay($db->getGameDay($request->get_param('id'))));
    return $this->getResponse($request, $items);
  }

}
