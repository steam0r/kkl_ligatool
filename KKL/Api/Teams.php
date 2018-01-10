<?php

class KKL_Api_Teams extends KKL_Api_Controller {

  public function register_routes() {
    $base = 'teams';
    register_rest_route($this->getNamespace(), '/' . $base, array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_teams'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_team'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/matches', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_matches_for_team'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/info', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_info_for_team'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
  }

  public function get_teams(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getTeams();
    return $this->getResponse($request, $items);
  }

  public function get_team(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = array($db->getTeam($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_matches_for_team(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getGamesForTeam($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

  public function get_info_for_team(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getTeamProperties($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

}
