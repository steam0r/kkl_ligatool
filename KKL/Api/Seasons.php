<?php

class KKL_Api_Seasons extends KKL_Api_Controller {

  public function register_routes() {
    $base = 'seasons';
    register_rest_route($this->getNamespace(), '/' . $base, array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_seasons'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/teams', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_teams_for_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/teams', array(
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => array($this, 'add_teams_to_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/gamedays', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_gamedays_for_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/currentgameday', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_current_game_day_for_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/schedule', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_schedule_for_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/ranking', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_ranking_for_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/info', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_info_for_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
  }

  public function get_seasons(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getSeasons();
    return $this->getResponse($request, $items);
  }

  public function get_season(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = array($db->getSeason($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_teams_for_season(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getTeamsForSeason($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

  public function add_teams_to_season(WP_REST_Request $request) {
    $db = new KKL_DB();
    $seasonId = $request->get_param('id');
    foreach (json_decode($request->get_body()) as $newTeam) {
      $team = new stdClass();
      $team->name = $newTeam->name;
      $team->short_name = $newTeam->short_name;
      $team->season_id = $seasonId;
      $club = $db->getClubByCode($newTeam->short_name);
      if ($club) {
        $team->club_id = 2;
      }
      $db->createTeam($team);
    }
    return new WP_REST_Response(array(), 200);
  }


  public function get_gamedays_for_season(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getGameDaysForSeason($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

  public function get_current_game_day_for_season(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = array($db->getCurrentGameDayForSeason($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_info_for_season(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getSeasonProperties($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

  public function get_schedule_for_season(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getScheduleForSeason($db->getSeason($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_ranking_for_season(WP_REST_Request $request) {
    $seasonId = $request->get_param('id');
    $db = new KKL_DB();
    $gameDay = $db->getCurrentGameDayForSeason($seasonId);
    $items = $db->getRankingForLeagueAndSeasonAndGameDay(null, $seasonId, $gameDay->id);
    return $this->getResponse($request, $items);
  }

}
