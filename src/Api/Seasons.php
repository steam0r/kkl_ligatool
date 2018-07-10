<?php

namespace KKL\Ligatool\Api;

use KKL\Ligatool\DB;
use stdClass;
use WP_REST_Request;
use WP_REST_Server;

class Seasons extends Controller {
  
  public function register_routes() {
    register_rest_route(
      $this->getNamespace(), '/' . $this->getBaseName(),
      array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_seasons'), 'args' => array(),)
    );
    register_rest_route(
      $this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)',
      array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_season'),
            'args'    => array('context' => array('default' => 'view',),),)
    );
    register_rest_route(
      $this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/teams',
      array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_teams_for_season'),
            'args'    => array('context' => array('default' => 'view',),),)
    );
    register_rest_route(
      $this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/teams',
      array('methods' => WP_REST_Server::CREATABLE, 'permission_callback' => function() {
        return is_user_logged_in();
      }, 'callback'   => array($this, 'add_teams_to_season'),
            'args'    => array('context' => array('default' => 'view',),),)
    );
    register_rest_route(
      $this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/gamedays',
      array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_gamedays_for_season'),
            'args'    => array('context' => array('default' => 'view',),),)
    );
    register_rest_route(
      $this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/currentgameday',
      array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_current_game_day_for_season'),
            'args'    => array('context' => array('default' => 'view',),),)
    );
    register_rest_route(
      $this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/schedule',
      array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_schedule_for_season'),
            'args'    => array('context' => array('default' => 'view',),),)
    );
    register_rest_route(
      $this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/schedule',
      array('methods' => WP_REST_Server::CREATABLE, 'permission_callback' => function() {
        return is_user_logged_in();
      }, 'callback'   => array($this, 'set_schedule_for_season'),
            'args'    => array('context' => array('default' => 'view',),),)
    );
    register_rest_route(
      $this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/ranking',
      array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_ranking_for_season'),
            'args'    => array('context' => array('default' => 'view',),),)
    );
    register_rest_route(
      $this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/liveranking',
      array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_liveranking_for_season'),
            'args'    => array('context' => array('default' => 'view',),),)
    );
    register_rest_route(
      $this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/info',
      array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_info_for_season'),
            'args'    => array('context' => array('default' => 'view',),),)
    );
  }
  
  public function getBaseName() {
    return 'seasons';
  }
  
  
  /**
   * @SWG\Get(
   *     path="/seasons",
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(type="array", ref="#/definitions/Season")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_seasons(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getSeasons();
    
    return $this->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/seasons/{seasonId}",
   *     @SWG\Parameter(
   *         in="path",
   *         name="seasonId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(ref="#/definitions/Season")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_season(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = array($db->getSeason($request->get_param('id')));
    
    return $this->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/seasons/{seasonId}/teams",
   *     @SWG\Parameter(
   *         in="path",
   *         name="seasonId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(type="array", ref="#/definitions/Team")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_teams_for_season(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getTeamsForSeason($request->get_param('id'));
    $teamEndpoint = new Teams();
    
    return $teamEndpoint->getResponse($request, $items);
  }
  
  /**
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function add_teams_to_season(WP_REST_Request $request) {
    $db = new DB\Api();
    $seasonId = $request->get_param('id');
    foreach(json_decode($request->get_body()) as $newTeam) {
      $team = new stdClass();
      $team->name = $newTeam->name;
      $team->short_name = $newTeam->shortName;
      $team->season_id = $seasonId;
      $club = $db->getClubByCode($newTeam->shortName);
      if($club) {
        $team->club_id = $club->id;
      }
      $db->createTeam($team);
    }
    $items = $db->getTeamsForSeason($request->get_param('id'));
    $teamEndpoint = new Teams();
    
    return $teamEndpoint->getResponse($request, $items);
  }
  
  public function set_schedule_for_season(WP_REST_Request $request) {
    $db = new DB\Api();
    $seasonId = $request->get_param('id');
    foreach(json_decode($request->get_body()) as $gamedayData) {
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
    $items = $db->getScheduleForSeason($db->getSeason($request->get_param('id')));
    
    return $this->getResponse($request, $items, true);
  }
  
  /**
   * @SWG\Get(
   *     path="/seasons/{seasonId}/gamedays",
   *     @SWG\Parameter(
   *         in="path",
   *         name="seasonId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(type="array", ref="#/definitions/GameDay")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_gamedays_for_season(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getGameDaysForSeason($request->get_param('id'));
    $gameDayEndpoint = new GameDays();
    
    return $gameDayEndpoint->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/seasons/{seasonId}/currentgameday",
   *     @SWG\Parameter(
   *         in="path",
   *         name="seasonId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(type="array", ref="#/definitions/GameDay")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_current_game_day_for_season(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = array($db->getCurrentGameDayForSeason($request->get_param('id')));
    $gameDayEndpoint = new GameDays();
    
    return $gameDayEndpoint->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/seasons/{seasonId}/info",
   *     @SWG\Parameter(
   *         in="path",
   *         name="seasonId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(type="array", ref="#/definitions/Property")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_info_for_season(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getSeasonProperties($request->get_param('id'));
    
    return $this->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/seasons/{seasonId}/teams",
   *     @SWG\Parameter(
   *         in="path",
   *         name="seasonId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(type="array", ref="#/definitions/Schedule")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_schedule_for_season(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getScheduleForSeason($db->getSeason($request->get_param('id')));
    
    return $this->getResponse($request, $items, true);
  }
  
  /**
   * @SWG\Get(
   *     path="/seasons/{seasonId}/teams",
   *     @SWG\Parameter(
   *         in="path",
   *         name="seasonId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(type="array", ref="#/definitions/Ranking")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_ranking_for_season(WP_REST_Request $request) {
    $seasonId = $request->get_param('id');
    $db = new DB\Api();
    $gameDay = $db->getCurrentGameDayForSeason($seasonId);
    $items = $db->getRankingForLeagueAndSeasonAndGameDay(null, $seasonId, $gameDay->id);
    
    return $this->getResponse($request, $items, true);
  }
  
  public function get_liveranking_for_season(WP_REST_Request $request) {
    $seasonId = $request->get_param('id');
    $db = new DB\Api();
    $gameDay = $db->getCurrentGameDayForSeason($seasonId);
    $items = $db->getRankingForLeagueAndSeasonAndGameDay(null, $seasonId, $gameDay->id, true);
    
    return $this->getResponse($request, $items, true);
  }
  
  
  protected function getLinks($itemId) {
    $leagueEndpoint = new Leagues();
    
    return array("gameDays"                                                                                         => array("href"       => $this->getFullBaseUrl(
      ) . '/<id>/gamedays',
                                                                                                                             "embeddable" => array("table" => "game_days",
                                                                                                                                                   "field" => "season_id",),),
                 "teams"                                                                                            => array("href"       => $this->getFullBaseUrl(
                   ) . '/<id>/teams',
                                                                                                                             "embeddable" => array("table" => "teams",
                                                                                                                                                   "field" => "season_id",),),
                 "league"                                                                                           => array("href"       => $leagueEndpoint->getFullBaseUrl(
                   ) . '/<propertyid>',
                                                                                                                             "embeddable" => array("table" => "leagues",
                                                                                                                                                   "field" => "id",),
                                                                                                                             "idFields"   => array("leagueId"),),
                 "currentGameDay"                                                                                   => array("href"       => $this->getFullBaseUrl(
                   ) . '/<id>/currentgameday',
                                                                                                                             "embeddable" => array("callback" => function() use ($itemId) {
                                                                                                                               $db = new DB\Api(
                                                                                                                               );
      
                                                                                                                               return $db->getCurrentGameDayForSeason(
                                                                                                                                 $itemId
                                                                                                                               );
                                                                                                                             },),),
                 "schedule"                                                                                         => array("href" => $this->getFullBaseUrl(
                   ) . '/<id>/schedule',),
                 "ranking"                                                                                          => array("href" => $this->getFullBaseUrl(
                   ) . '/<id>/ranking',),
                 "properties"                                                                                       => array("href"       => $this->getFullBaseUrl(
                   ) . '/<id>/properties',
                                                                                                                             "embeddable" => array("table" => "season_properties",
                                                                                                                                                   "field" => "objectId",),),);
  }
  
}
