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
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d\w]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_league'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d\w]+)/seasons', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_seasons_for_league'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d\w]+)/currentseason', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_current_season_for_league'),
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
    $items = array($this->getLeagueFromRequest($request));
    return $this->getResponse($request, $items);
  }

  public function get_seasons_for_league(WP_REST_Request $request) {
    $db = new KKL_DB();
    $league = $this->getLeagueFromRequest($request);
    $items = $db->getSeasonsByLeague($league->id);
    return $this->getResponse($request, $items);
  }

  public function get_current_season_for_league(WP_REST_Request $request) {
    $db = new KKL_DB();
    $league = $this->getLeagueFromRequest($request);
    $items = array($db->getCurrentSeason($league->id));
    return $this->getResponse($request, $items);
  }

  private function getLeagueFromRequest(WP_REST_Request $request) {
    $db = new KKL_DB();
    $param = $request->get_param('id');
    if(is_numeric($param)) {
      return $db->getLeague($param);
    }else{
      return $db->getLeagueBySlug($param);
    }
  }

}
