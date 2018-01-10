<?php

class KKL_Api_Clubs extends KKL_Api_Controller {

  public function register_routes() {
    $base = 'clubs';
    register_rest_route($this->getNamespace(), '/' . $base, array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_clubs'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_club'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/teams', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_teams_for_club'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/currentteam', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_current_team_for_club'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/info', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_info_for_club'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
  }

  public function get_clubs(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getClubs();
    return $this->getResponse($request, $items);
  }

  public function get_club(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = array($db->getClub($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_teams_for_club(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getTeamsForClub($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

  public function get_current_team_for_club(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getTeamsForClub($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

  public function get_info_for_club(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = array($db->getClubData($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

}
