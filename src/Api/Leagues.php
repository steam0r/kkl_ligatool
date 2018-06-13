<?php

namespace KKL\Ligatool\Api;

use KKL\Ligatool\DB;
use WP_REST_Request;
use WP_REST_Server;

class Leagues extends Controller {
  
  public function register_routes() {
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName(), array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_leagues'), 'args' => array(),));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_league'), 'args' => array('context' => array('default' => 'view',),),));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)/properties', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_properties_for_league'), 'args' => array('context' => array('default' => 'view',),),));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)/seasons', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_seasons_for_league'), 'args' => array('context' => array('default' => 'view',),),));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)/currentseason', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_current_season_for_league'), 'args' => array('context' => array('default' => 'view',),),));
  }
  
  public function getBaseName() {
    return 'leagues';
  }
  
  
  /**
   * @SWG\Get(
   *     path="/leagues",
   *     summary="Find all Leagues",
   *     description="Returns a list of all leagues",
   *     operationId="getLeagues",
   *     tags={"league"},
   *     produces={"application/json"},
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(type="array", ref="#/definitions/League")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_leagues(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getLeagues();
    
    return $this->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/leagues/{leagueId}",
   *     summary="Find league by ID",
   *     description="Returns a single league",
   *     operationId="getLegueById",
   *     tags={"league"},
   *     produces={"application/json"},
   *     @SWG\Parameter(
   *         description="ID of league to return",
   *         in="path",
   *         name="leagueId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(ref="#/definitions/League")
   *     ),
   *     @SWG\Response(
   *         response="400",
   *         description="Invalid ID supplied"
   *     ),
   *     @SWG\Response(
   *         response="404",
   *         description="League not found"
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_league(WP_REST_Request $request) {
    $items = array($this->getLeagueFromRequest($request));
    
    return $this->getResponse($request, $items);
  }
  
  private function getLeagueFromRequest(WP_REST_Request $request) {
    $db = new DB\Api();
    $param = $request->get_param('id');
    if(is_numeric($param)) {
      return $db->getLeague($param);
    } else {
      return $db->getLeagueBySlug($param);
    }
  }
  
  /**
   * @SWG\Get(
   *     path="/leagues/{leagueId}/properties",
   *     summary="Find properties by League",
   *     description="Returns properties for a single league",
   *     operationId="getLeagueProperties",
   *     tags={"league"},
   *     produces={"application/json"},
   *     @SWG\Parameter(
   *         description="ID of league to return properties for",
   *         in="path",
   *         name="leagueId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(ref="#/definitions/Properties")
   *     ),
   *     @SWG\Response(
   *         response="400",
   *         description="Invalid ID supplied"
   *     ),
   *     @SWG\Response(
   *         response="404",
   *         description="League not found"
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_properties_for_league(WP_REST_Request $request) {
    $db = new DB\Api();
    $league = $this->getLeagueFromRequest($request);
    $items = $db->getLeagueProperties($league->id);
    
    return $this->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/leagues/{leagueId}/seasons",
   *     summary="Find all Seasons for a League",
   *     description="Returns Seasons for a single League",
   *     operationId="getSeasonsByLeague",
   *     tags={"league"},
   *     produces={"application/json"},
   *     @SWG\Parameter(
   *         description="ID of league to return seasons for",
   *         in="path",
   *         name="leagueId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(type="array", ref="#/definitions/Season")
   *     ),
   *     @SWG\Response(
   *         response="400",
   *         description="Invalid ID supplied"
   *     ),
   *     @SWG\Response(
   *         response="404",
   *         description="League not found"
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_seasons_for_league(WP_REST_Request $request) {
    $db = new DB\Api();
    $league = $this->getLeagueFromRequest($request);
    $items = $db->getSeasonsByLeague($league->id);
    $seasonsEndpoint = new Seasons();
    
    return $seasonsEndpoint->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/leagues/{leagueId}/currentseason",
   *     summary="Find the currently active Seasons for a League",
   *     description="Find the currently active Seasons for a League",
   *     operationId="getCurrentSeason",
   *     tags={"league"},
   *     produces={"application/json"},
   *     @SWG\Parameter(
   *         description="ID of league to return season for",
   *         in="path",
   *         name="leagueId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(ref="#/definitions/Season")
   *     ),
   *     @SWG\Response(
   *         response="400",
   *         description="Invalid ID supplied"
   *     ),
   *     @SWG\Response(
   *         response="404",
   *         description="League not found"
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_current_season_for_league(WP_REST_Request $request) {
    $db = new DB\Api();
    $league = $this->getLeagueFromRequest($request);
    $items = array($db->getCurrentSeason($league->id));
    $seasonsEndpoint = new Seasons();
    
    return $seasonsEndpoint->getResponse($request, $items);
  }
  
  protected function getLinks($itemId) {
    return array("currentseason" => array("href" => $this->getFullBaseUrl() . '/<id>/currentseason', "embeddable" => array("callback" => function() use ($itemId) {
      $db = new DB\Api();
      
      return $db->getCurrentSeason($itemId);
    },),), "seasons" => array("href" => $this->getFullBaseUrl() . '/<id>/seasons', "embeddable" => array("table" => "seasons", "field" => "league_id",),), "properties" => array("href" => $this->getFullBaseUrl() . '/<id>/properties', "embeddable" => array("table" => "league_properties", "field" => "objectId",),),);
  }
  
}
