<?php

namespace KKL\Ligatool\Api;

use KKL\Ligatool\DB;
use WP_REST_Request;
use WP_REST_Server;

class Teams extends Controller {
  
  public function register_routes() {
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName(), array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_teams'), 'args' => array(),));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_team'), 'args' => array('context' => array('default' => 'view',),)));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/matches', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_matches_for_team'), 'args' => array('context' => array('default' => 'view',),)));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/properties', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_info_for_team'), 'args' => array('context' => array('default' => 'view',),)));
  }
  
  public function getBaseName() {
    return 'teams';
  }
  
  /**
   * @SWG\Get(
   *     path="/teams",
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(type="array", ref="#/definitions/Team")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_teams(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getTeams();
    return $this->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/teams/{seasonId}",
   *     @SWG\Parameter(
   *         in="path",
   *         name="teamId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(ref="#/definitions/Team")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_team(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = array($db->getTeam($request->get_param('id')));
    return $this->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/teams/{teamId}/matches",
   *     @SWG\Parameter(
   *         in="path",
   *         name="teamId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(type="array", ref="#/definitions/Match")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_matches_for_team(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getMatchesForTeam($request->get_param('id'));
    $matchesEndpoint = new Matches();
    return $matchesEndpoint->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/teams/{teamId}/info",
   *     @SWG\Parameter(
   *         in="path",
   *         name="teamId",
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
  public function get_info_for_team(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getTeamProperties($request->get_param('id'));
    return $this->getResponse($request, $items);
  }
  
  protected function getLinks($itemId) {
    $clubEndpoint = new Clubs();
    $seasonEndpoint = new Seasons();
    return array("matches" => array("href" => $this->getFullBaseUrl() . '/<id>/matches', "embeddable" => array("callback" => function() use ($itemId) {
      $db = new DB\Api();
      return $db->getMatchesForTeam($itemId);
    })), "club" => array("href" => $clubEndpoint->getFullBaseUrl() . '/<propertyid>', "embeddable" => array("table" => "clubs", "field" => "id"), "idFields" => array("clubId")), "season" => array("href" => $seasonEndpoint->getFullBaseUrl() . '/<propertyid>', "embeddable" => array("table" => "seasons", "field" => "id"), "idFields" => array("seasonId")), "properties" => array("href" => $this->getFullBaseUrl() . '/<id>/properties', "embeddable" => array("table" => "team_properties", "field" => "objectId")),);
  }
  
}
