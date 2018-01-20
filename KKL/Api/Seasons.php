<?php

class KKL_Api_Seasons extends KKL_Api_Controller {

  public function getBaseName() {
    return 'seasons';
  }

  public function register_routes() {
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName(), array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_seasons'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/teams', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_teams_for_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/teams', array(
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => array($this, 'add_teams_to_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/gamedays', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_gamedays_for_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/currentgameday', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_current_game_day_for_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/schedule', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_schedule_for_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        )
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/schedule', array(
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => array($this, 'set_schedule_for_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/ranking', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_ranking_for_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/info', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_info_for_season'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
  }

  protected function getLinks() {
    $leagueEndpoint = new KKL_Api_Leagues();
    return array(
      "gameDays" => array(
        "href" => $this->getFullBaseUrl() . '/<id>/gamedays',
        "embeddable" => true
      ),
      "teams" => array(
        "href" => $this->getFullBaseUrl() . '/<id>/teams',
        "embeddable" => true
      ),
      "league" => array(
        "href" => $leagueEndpoint->getFullBaseUrl() . '/<propertyid>',
        "embeddable" => true,
        "idField" => array("leagueId")
      ),
      "currentGameDay" => array(
        "href" => $this->getFullBaseUrl() . '/<id>/currentgameday',
        "embeddable" => true
      ),
      "schedule" => array(
        "href" => $this->getFullBaseUrl() . '/<id>/schedule',
        "embeddable" => false
      ),
      "ranking" => array(
        "href" => $this->getFullBaseUrl() . '/<id>/ranking',
        "embeddable" => false
      ),
      "properties" => array(
        "href" => $this->getFullBaseUrl() . '/<id>/properties',
        "embeddable" => true
      ),
    );
  }

  public function get_seasons(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = $db->getSeasons();
    return $this->getResponse($request, $items);
  }

  public function get_season(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = array($db->getSeason($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_teams_for_season(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = $db->getTeamsForSeason($request->get_param('id'));
    $teamEndpoint = new KKL_Api_Teams();
    return $teamEndpoint->getResponse($request, $items);
  }

  public function add_teams_to_season(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $seasonId = $request->get_param('id');
    foreach (json_decode($request->get_body()) as $newTeam) {
      $team = new stdClass();
      $team->name = $newTeam->name;
      $team->short_name = $newTeam->shortName;
      $team->season_id = $seasonId;
      $club = $db->getClubByCode($newTeam->shortName);
      if ($club) {
        $team->club_id = $club->id;
      }
      $db->createTeam($team);
    }
    return new WP_REST_Response(array(), 200);
  }

  public function set_schedule_for_season(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $seasonId = $request->get_param('id');
    foreach (json_decode($request->get_body()) as $gamedayData) {
      $gameday = $db->getGameDayBySeasonAndPosition($seasonId, $gamedayData->number);
      foreach($gamedayData->matches as $matchData) {
        $awayTeam = $db->getTeamByCodeAndSeason($matchData->home->short_name, $seasonId);
        $homeTeam = $db->getTeamByCodeAndSeason($matchData->away->short_name, $seasonId);
        $match = new stdClass();
        $match->game_day_id = $gameday->id;
        $match->status = -1;
        $match->away_team = $awayTeam->id;
        $match->home_team = $homeTeam->id;
        $db->createMatch($match);
      }
    }
    return new WP_REST_Response(array(), 200);
  }

  public function get_gamedays_for_season(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = $db->getGameDaysForSeason($request->get_param('id'));
    $gameDayEndpoint = new KKL_Api_GameDays();
    return $gameDayEndpoint->getResponse($request, $items);
  }

  public function get_current_game_day_for_season(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = array($db->getCurrentGameDayForSeason($request->get_param('id')));
    $gameDayEndpoint = new KKL_Api_GameDays();
    return $gameDayEndpoint->getResponse($request, $items);
  }

  public function get_info_for_season(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = $db->getSeasonProperties($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

  public function get_schedule_for_season(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = $db->getScheduleForSeason($db->getSeason($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_ranking_for_season(WP_REST_Request $request) {
    $seasonId = $request->get_param('id');
    $db = new KKL_DB_Api();
    $gameDay = $db->getCurrentGameDayForSeason($seasonId);
    $items = $db->getRankingForLeagueAndSeasonAndGameDay(null, $seasonId, $gameDay->id);
    return $this->getResponse($request, $items);
  }

}
