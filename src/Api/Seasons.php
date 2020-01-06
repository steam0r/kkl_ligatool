<?php

namespace KKL\Ligatool\Api;

use KKL\Ligatool\DB;
use stdClass;
use WP_REST_Request;
use WP_REST_Server;

class Seasons extends Controller
{

  public function register_routes()
  {
    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName(),
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_seasons'),
        'args' => array())
    );

    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)',
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_season'),
        'args' => array('context' => array('default' => 'view')
        )
      )
    );

    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)/teams',
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_teams_for_season'),
        'args' => array('context' => array('default' => 'view')
        )
      )
    );

    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)/teams',
      array(
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => array($this, 'add_teams_to_season'),
        'args' => array('context' => array('default' => 'view')),
        'permission_callback' => array($this, 'authenticate_api_key')
      )
    );

    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)/gamedays',
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_gamedays_for_season'),
        'args' => array('context' => array('default' => 'view')
        )
      )
    );

    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)/currentgameday',
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_current_game_day_for_season'),
        'args' => array('context' => array('default' => 'view')
        )
      )
    );

    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)/schedule',
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_schedule_for_season'),
        'args' => array('context' => array('default' => 'view')
        )
      )
    );

    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)/schedule',
      array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => array($this, 'set_schedule_for_season'),
        'args' => array('context' => array('default' => 'view')),
        'permission_callback' => array($this, 'authenticate_api_key')
      )
    );

    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)/ranking',
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_ranking_for_season'),
        'args' => array('context' => array('default' => 'view')
        )
      )
    );

    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)/liveranking',
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_liveranking_for_season'),
        'args' => array('context' => array('default' => 'view')
        )
      )
    );

    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)/info',
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_info_for_season'),
        'args' => array('context' => array('default' => 'view')
        )
      )
    );

    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName() . '/import',
      array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => array($this, 'create_season_from_import'),
        'args' => array('context' => array('default' => 'view')),
        'permission_callback' => array($this, 'authenticate_api_key')
      )
    );

  }

  public function getBaseName()
  {
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
  public function get_seasons(WP_REST_Request $request)
  {
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
  public function get_season(WP_REST_Request $request)
  {
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
  public function get_teams_for_season(WP_REST_Request $request)
  {
    $db = new DB\Api();
    $items = $db->getTeamsForSeason($request->get_param('id'));
    $teamEndpoint = new Teams();

    return $teamEndpoint->getResponse($request, $items);
  }

  /**
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function add_teams_to_season(WP_REST_Request $request)
  {
    $db = new DB\Api();
    $seasonId = $request->get_param('id');
    foreach (json_decode($request->get_body()) as $newTeam) {
      $team = new stdClass();
      $team->name = $newTeam->name;
      $team->short_name = $newTeam->shortName;
      $team->season_id = $seasonId;
      if ($newTeam->clubId) {
        $club = $db->getClub($newTeam->clubId);
      } elseif ($team->shortName) {
        $club = $db->getClubByCode($newTeam->shortName);
      }

      if (!$club) {
        $club = new \stdClass();
        $club->name = $newTeam->name;
        $club->short_name = $newTeam->shortName;
        $club->description = "";
        $club = $db->createClub($club);
      }

      $team->club_id = $club->id;
      $db->createTeam($team);
    }
    $items = $db->getTeamsForSeason($request->get_param('id'));
    $teamEndpoint = new Teams();

    return $teamEndpoint->getResponse($request, $items);
  }

  public function set_schedule_for_season(WP_REST_Request $request)
  {
    $db = new DB\Api();
    $seasonId = $request->get_param('id');
    foreach (json_decode($request->get_body()) as $gamedayData) {
      $gameday = $db->getGameDayBySeasonAndPosition($seasonId, $gamedayData->number);
      foreach ($gamedayData->matches as $matchData) {
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
  public function get_gamedays_for_season(WP_REST_Request $request)
  {
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
  public function get_current_game_day_for_season(WP_REST_Request $request)
  {
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
  public function get_info_for_season(WP_REST_Request $request)
  {
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
  public function get_schedule_for_season(WP_REST_Request $request)
  {
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
  public function get_ranking_for_season(WP_REST_Request $request)
  {
    $seasonId = $request->get_param('id');
    $db = new DB\Api();
    $gameDay = $db->getCurrentGameDayForSeason($seasonId);
    $items = $db->getRankingForLeagueAndSeasonAndGameDay(null, $seasonId, $gameDay->id);

    return $this->getResponse($request, $items, true);
  }

  public function get_liveranking_for_season(WP_REST_Request $request)
  {
    $seasonId = $request->get_param('id');
    $db = new DB\Api();
    $gameDay = $db->getCurrentGameDayForSeason($seasonId);
    $items = $db->getRankingForLeagueAndSeasonAndGameDay(null, $seasonId, $gameDay->id, true);

    return $this->getResponse($request, $items, true);
  }

  public function create_season_from_import(WP_REST_Request $request)
  {

    $db = new DB\Api();
    $data = json_decode($request->get_body());
    $seasons = array();
    foreach ($data as $seasonId => $matches) {
      $season = $db->getSeason($seasonId);
      $seasons[$seasonId] = array();
      foreach ($matches as $match) {

        if($match->team_home !== null) {
          $teamHome = new \stdClass();
          $teamHome->name = $match->team_home->name;
          $teamHome->short_name = $match->team_home->shortName;
          $teamHome->season_id = $season->id;

          $homeClub = null;
          if ($match->team_home->clubId) {
            $homeClub = $db->getClub($match->team_home->clubId);
          } elseif ($match->team_home->shortName) {
            $homeClub = $db->getClubByCode($match->team_home->shortName);
          }

          $homeTeamProperties = [];
          if (!$homeClub) {
            $homeClub = new \stdClass();
            $homeClub->name = $match->team_home->name;
            $homeClub->short_name = $match->team_home->shortName;
            $homeClub->description = "";
            $homeClub = $db->createClub($homeClub);
          }else{
            $teams = $db->getTeamsForClub($homeClub->id);
            if($teams && is_array($teams)) {
              uasort($teams, function ($a, $b) {
                if ($a->id == $b->id) {
                  return 0;
                }
                return ($a->id < $b->id) ? -1;
              });
              if($teams[0]) {
                $prevHomeTeam = $teams[0];
                if(array_key_exists('captain', $prevHomeTeam) && $prevHomeTeam->properties['captain']) {
                  $homeTeamProperties['captain'] = $prevHomeTeam->properties['captain'];
                }
                if(array_key_exists('vice_captain', $prevHomeTeam) && $prevHomeTeam->properties['vice_captain']) {
                  $homeTeamProperties['vice_captain'] = $prevHomeTeam->properties['vice_captain'];
                }
                if(array_key_exists('location', $prevHomeTeam) && $prevHomeTeam->properties['location']) {
                  $homeTeamProperties['location'] = $prevHomeTeam->properties['location'];
                }
              }
            }
          }

          $teamHome->club_id = $homeClub->id;
          $teamHome = $db->createTeam($teamHome);
          if(!empty($homeTeamProperties)) {
            $db->setTeamProperties($teamHome, $homeTeamProperties);
          }

        }

        if($match->team_away->name !== null) {
          $awayClub = null;
          $teamAway = new \stdClass();
          $teamAway->name = $match->team_away->name;
          $teamAway->short_name = $match->team_away->shortName;
          $teamAway->season_id = $season->id;

          $awayTeamProperties = [];
          if ($match->team_away->clubId) {
            $awayClub = $db->getClub($match->team_away->clubId);
          } elseif ($match->team_away->shortName) {
            $awayClub = $db->getClubByCode($match->team_away->shortName);
          }

          if (!$awayClub) {
            $awayClub = new \stdClass();
            $awayClub->name = $match->team_away->name;
            $awayClub->short_name = $match->team_away->shortName;
            $awayClub->description = "";
            $awayClub = $db->createClub($awayClub);
          }else{
            $teams = $db->getTeamsForClub($homeClub->id);
            if($teams && is_array($teams)) {
              uasort($teams, function ($a, $b) {
                if ($a->id == $b->id) {
                  return 0;
                }
                return ($a->id < $b->id) ? -1;
              });
              if($teams[0]) {
                $prevAwayTeam = $teams[0];
                if(array_key_exists('captain', $prevAwayTeam) && $prevHomeTeam->properties['captain']) {
                  $awayTeamProperties['captain'] = $prevAwayTeam->properties['captain'];
                }
                if(array_key_exists('vice_captain', $prevAwayTeam) && $prevAwayTeam->properties['vice_captain']) {
                  $awayTeamProperties['vice_captain'] = $prevAwayTeam->properties['vice_captain'];
                }
                if(array_key_exists('location', $prevAwayTeam) && $prevAwayTeam->properties['location']) {
                  $awayTeamProperties['location'] = $prevAwayTeam->properties['location'];
                }
              }
            }
          }

          $teamAway->club_id = $awayClub->id;
          $teamAway = $db->createTeam($teamAway);
          if(!empty($awayTeamProperties)) {
            $db->setTeamProperties($teamAway, $awayTeamProperties);
          }
        }

        $newMatch = new \stdClass();
        if($teamHome) {
          $newMatch->home = $teamHome->id;
        }else{
          $newMatch->home = null;
        }
        if($teamAway) {
          $newMatch->away = $teamAway->id;
        }else{
          $newMatch->away = null;
        }
        $seasons[$seasonId][] = $newMatch;
      }
    }

    foreach ($seasons as $seasonId => $matches) {
      if (count($matches) == 3) {
        $teams = [$matches[0]->home, $matches[2]->home, $matches[2]->away, $matches[1]->away, $matches[0]->away, $matches[1]->home];
      } elseif (count($matches) == 4) {
        $teams = [$matches[0]->home, $matches[2]->home, $matches[3]->home, $matches[3]->away, $matches[2]->away, $matches[1]->away, $matches[0]->away, $matches[1]->home];
      } else {
        continue;
      }

      $anz = count($teams);      // Anzahl der Teams im Array $teams
      $paare = $anz / 2;            // Anzahl der möglichen Spielpaare
      $tage = $anz - 1;            // Anzahl der Spieltage pro Runde
      $xpos = $anz - 1;            // höchster Key im Array $teams
      $spnr = 0;                  // Zähler für Spielnummer
      $plan = array();

      for ($tag = 0; $tag < $tage; $tag++) {

        array_splice($teams, 1, 1, array(
          array_pop($teams),
          $teams[1]
        ));

        $matchCount = 0;
        for ($sppaar = 0; $sppaar < $paare; $sppaar++) {

          if ($tag % 2 == 0) {
            $hteam = $teams[$sppaar];
            $gteam = $teams[$xpos - $sppaar];
          } else {
            $gteam = $teams[$sppaar];
            $hteam = $teams[$xpos - $sppaar];
          }

          if ($hteam !== null && $gteam !== null) {
            $plan[$tag]["number"] = $tag + 1;
            $plan[$tag]["matches"][$matchCount]['home'] = $hteam;  // für Hin-Runde
            $plan[$tag]["matches"][$matchCount]['away'] = $gteam;  // für Hin-Runde

            $plan[$tag + $tage]["number"] = $tag + $tage + 1;
            $plan[$tag + $tage]["matches"][$matchCount]['home'] = $gteam;  // für Rück-Runde
            $plan[$tag + $tage]["matches"][$matchCount]['away'] = $hteam;  // für Rück-Runde
            $matchCount++;
          }
          $spnr++;
        }
      }
      ksort($plan); /* nach Spieltagen sortieren */

      foreach ($plan as $dayData) {

        $gameDay = $db->getGameDayBySeasonAndPosition($seasonId, $dayData['number']);

        foreach ($dayData['matches'] as $match) {
          $newMatch = new \stdClass();
          $newMatch->game_day_id = $gameDay->id;
          $newMatch->score_away = null;
          $newMatch->score_home = null;
          $newMatch->fixture = null;
          $newMatch->location = null;
          $newMatch->notes = '';
          $newMatch->status = -1;
          $newMatch->home_team = $match['home'];
          $newMatch->away_team = $match['away'];

          $db->createMatch($newMatch);
        }
      }
    }

    return "OK";
  }


  protected function getLinks($itemId)
  {
    $leagueEndpoint = new Leagues();

    return array("gameDays" => array("href" => $this->getFullBaseUrl() . '/<id>/gamedays',
      "embeddable" => array("table" => "game_days",
        "field" => "season_id",),),
      "teams" => array("href" => $this->getFullBaseUrl() . '/<id>/teams',
        "embeddable" => array("table" => "teams",
          "field" => "season_id",),),
      "league" => array("href" => $leagueEndpoint->getFullBaseUrl() . '/<propertyid>',
        "embeddable" => array("table" => "leagues",
          "field" => "id",),
        "idFields" => array("leagueId"),),
      "currentGameDay" => array("href" => $this->getFullBaseUrl() . '/<id>/currentgameday',
        "embeddable" => array("callback" => function () use ($itemId) {
          $db = new DB\Api();

          return $db->getCurrentGameDayForSeason(
            $itemId
          );
        },),),
      "schedule" => array("href" => $this->getFullBaseUrl() . '/<id>/schedule',),
      "ranking" => array("href" => $this->getFullBaseUrl() . '/<id>/ranking',),
      "properties" => array("href" => $this->getFullBaseUrl() . '/<id>/properties',
        "embeddable" => array("table" => "season_properties",
          "field" => "objectId",),),);
  }

}
