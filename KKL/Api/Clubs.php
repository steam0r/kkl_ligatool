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
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d\w]+)', array(
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
    $items = array($this->getClub($request));
    return $this->getResponse($request, $items);
  }

  public function get_teams_for_club(WP_REST_Request $request) {
    $db = new KKL_DB();
    $club = $this->getClub($request);
    $items = $db->getTeamsForClub($club->id);
    return $this->getResponse($request, $items);
  }

  public function get_current_team_for_club(WP_REST_Request $request) {
    $db = new KKL_DB();
    $club = $this->getClub($request);
    $items = array($db->getCurrentTeamForClub($club->id));
    return $this->getResponse($request, $items);
  }

  public function get_info_for_club(WP_REST_Request $request) {
    $db = new KKL_DB();
    $club = $this->getClub($request);
    $items = array($db->getClubData($club->id));
    return $this->getResponse($request, $items);
  }

  private function getClub(WP_REST_Request $request) {
    $db = new KKL_DB();
    $param = $request->get_param('id');
    if(is_numeric($param)) {
      return $db->getClub($param);
    }else{
      return $db->getClubByCode($param);
    }
  }

}
