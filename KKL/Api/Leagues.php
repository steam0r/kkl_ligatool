<?php

class KKL_Api_Leagues extends KKL_Api_Controller {

  public function register_routes() {
    $base = 'leagues';
    register_rest_route($this->getNamespace(), '/' . $base, array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_leagues'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_league'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/seasons', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_seasons_for_league'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/currentseason', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_current_season_for_league'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/currentgameday', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_current_game_day_for_league'),
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
  public function get_leagues(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getLeagues();
    return $this->getResponse($request, $items);
  }

  public function get_league(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = array($db->getLeague($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_seasons_for_league(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getSeasonsByLeague($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

  public function get_current_game_day_for_league(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = array($db->getCurrentGameDayForLeague($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_current_season_for_league(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = array($db->getCurrentSeason($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

}
