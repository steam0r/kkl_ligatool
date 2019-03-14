<?php

namespace KKL\Ligatool\Api;

use KKL\Ligatool\DB;
use WP_REST_Request;
use WP_REST_Server;

class Leagues extends Controller
{

  public function register_routes()
  {
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName(), array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_leagues'), 'args' => array(),));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_league'), 'args' => array('context' => array('default' => 'view',),),));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)/properties', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_properties_for_league'), 'args' => array('context' => array('default' => 'view',),),));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)/seasons', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_seasons_for_league'), 'args' => array('context' => array('default' => 'view',),),));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)/currentseason', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_current_season_for_league'), 'args' => array('context' => array('default' => 'view',),),));

    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<code>[\S]+)/create_season',
      array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback'   => array($this, 'create_season'),
        'args'    => array('context' => array('default' => 'view')),
        'permission_callback' => array($this, 'authenticate_api_key')
      )
    );
  }

  public function getBaseName()
  {
    return 'leagues';
  }


  /**
   * @OA\Get(
   *     path="/leagues",
   *     tags={"leagues"},
   *     summary="Find all Leagues",
   *     description="Returns a list of all leagues",
   *     operationId="getLeagues",
   *     tags={"league"},
   *     produces={"application/json"},
   *     @OA\Response(
   *         response=200,
   *         description="successful operation",
   *         @OA\Schema(type="array", ref="#/definitions/League")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_leagues(WP_REST_Request $request)
  {
    $db = new DB\Api();
    $items = $db->getLeagues();

    return $this->getResponse($request, $items);
  }

  /**
   * @OA\Get(
   *     path="/leagues/:leagueId",
   *     tags={"leagues"},
   *     summary="Find league by ID",
   *     description="Returns a single league",
   *     operationId="getLegueById",
   *     tags={"league"},
   *     produces={"application/json"},
   *     @OA\Parameter(
   *         description="ID of league to return",
   *         in="path",
   *         name="leagueId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="successful operation",
   *         @OA\Schema(ref="#/definitions/League")
   *     ),
   *     @OA\Response(
   *         response="400",
   *         description="Invalid ID supplied"
   *     ),
   *     @OA\Response(
   *         response="404",
   *         description="League not found"
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_league(WP_REST_Request $request)
  {
    $items = array($this->getLeagueFromRequest($request));

    return $this->getResponse($request, $items);
  }

  private function getLeagueFromRequest(WP_REST_Request $request)
  {
    $db = new DB\Api();
    $param = $request->get_param('id');
    if (is_numeric($param)) {
      return $db->getLeague($param);
    } else {
      return $db->getLeagueBySlug($param);
    }
  }

  /**
   * @OA\Get(
   *     path="/leagues/:leagueId/properties",
   *     tags={"leagues"},
   *     summary="Find properties by League",
   *     description="Returns properties for a single league",
   *     operationId="getLeagueProperties",
   *     tags={"league"},
   *     produces={"application/json"},
   *     @OA\Parameter(
   *         description="ID of league to return properties for",
   *         in="path",
   *         name="leagueId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="successful operation",
   *         @OA\Schema(ref="#/definitions/Properties")
   *     ),
   *     @OA\Response(
   *         response="400",
   *         description="Invalid ID supplied"
   *     ),
   *     @OA\Response(
   *         response="404",
   *         description="League not found"
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_properties_for_league(WP_REST_Request $request)
  {
    $db = new DB\Api();
    $league = $this->getLeagueFromRequest($request);
    $items = $db->getLeagueProperties($league->id);

    return $this->getResponse($request, $items);
  }

  /**
   * @OA\Get(
   *     path="/leagues/:leagueId/seasons",
   *     tags={"leagues"},
   *     summary="Find all Seasons for a League",
   *     description="Returns Seasons for a single League",
   *     operationId="getSeasonsByLeague",
   *     tags={"league"},
   *     produces={"application/json"},
   *     @OA\Parameter(
   *         description="ID of league to return seasons for",
   *         in="path",
   *         name="leagueId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="successful operation",
   *         @OA\Schema(type="array", ref="#/definitions/Season")
   *     ),
   *     @OA\Response(
   *         response="400",
   *         description="Invalid ID supplied"
   *     ),
   *     @OA\Response(
   *         response="404",
   *         description="League not found"
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_seasons_for_league(WP_REST_Request $request)
  {
    $db = new DB\Api();
    $league = $this->getLeagueFromRequest($request);
    $items = $db->getSeasonsByLeague($league->id);
    $seasonsEndpoint = new Seasons();

    return $seasonsEndpoint->getResponse($request, $items);
  }

  /**
   * @OA\Get(
   *     path="/leagues/:leagueId/currentseason",
   *     tags={"leagues"},
   *     summary="Find the currently active Seasons for a League",
   *     description="Find the currently active Seasons for a League",
   *     operationId="getCurrentSeason",
   *     tags={"league"},
   *     produces={"application/json"},
   *     @OA\Parameter(
   *         description="ID of league to return season for",
   *         in="path",
   *         name="leagueId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="successful operation",
   *         @OA\Schema(ref="#/definitions/Season")
   *     ),
   *     @OA\Response(
   *         response="400",
   *         description="Invalid ID supplied"
   *     ),
   *     @OA\Response(
   *         response="404",
   *         description="League not found"
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_current_season_for_league(WP_REST_Request $request)
  {
    $db = new DB\Api();
    $league = $this->getLeagueFromRequest($request);
    $items = array($db->getCurrentSeason($league->id));
    $seasonsEndpoint = new Seasons();

    return $seasonsEndpoint->getResponse($request, $items);
  }


  public function create_season(WP_REST_Request $request) {

    $db = new DB\Api();
    $slug = $request->get_param('code');

    $league = $db->getLeagueBySlug($slug);
    $data = json_decode($request->get_body());

    $name = $data->name;
    $start_date = $data->start;
    $end_date = $data->end;
    $days = $data->days;

    if($league && !empty($days)) {
      $season = new \stdClass();
      $season->name = $name . " - " . $league->name;
      $season->start_date = $start_date;
      $season->end_date = $end_date;
      $season->active = true;
      $season->league_id = $league->id;
      $season = $db->createSeason($season);
      $i = 1;
      foreach ($days as $day) {
        $d = new \stdClass();
        $d->number = $i;
        $d->start_date = $day->start;
        $d->end_date = $day->end;
        $d->season_id = $season->id;
        $d = $db->createGameDay($d);
        if ($i == 1) {
          $season->current_game_day = $d->id;
          $season = $db->updateSeason($season);
        }
        $i++;
      }
    }
    return $db->getSeason($season->id);

  }

  protected function getLinks($itemId)
  {
    return array("currentseason" => array("href" => $this->getFullBaseUrl() . '/<id>/currentseason', "embeddable" => array("callback" => function () use ($itemId) {
      $db = new DB\Api();

      return $db->getCurrentSeason($itemId);
    },),), "seasons" => array("href" => $this->getFullBaseUrl() . '/<id>/seasons', "embeddable" => array("table" => "seasons", "field" => "league_id",),), "properties" => array("href" => $this->getFullBaseUrl() . '/<id>/properties', "embeddable" => array("table" => "league_properties", "field" => "objectId",),),);
  }

}
