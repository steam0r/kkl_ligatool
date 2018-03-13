<?php

namespace KKL\Ligatool\Api;

use KKL\Ligatool\DB;
use KKL\Ligatool\Events;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

class Matches extends Controller {
  
  public function register_routes() {
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName(), array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_matches'), 'args' => array(),));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_match'), 'args' => array('context' => array('default' => 'view',),)));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/fixture', array('methods' => 'PATCH', 'callback' => array($this, 'set_match_fixture'), 'permission_callback' => array($this, 'is_valid_email_for_match'), 'args' => array('context' => array('default' => 'view',),)));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/properties', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_properties_for_match'), 'args' => array('context' => array('default' => 'view',),)));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/info', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_info_for_match'), 'args' => array('context' => array('default' => 'view',),)));
  }
  
  public function getBaseName() {
    return 'matches';
  }
  
  /**
   * @SWG\Get(
   *     path="/matches",
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(type="array", ref="#/definitions/Match")
   *     )
   * )
   */
  public function get_matches(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getMatches();
    return $this->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/matches/{matchId}",
   *     @SWG\Parameter(
   *         in="path",
   *         name="matchId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(ref="#/definitions/Match")
   *     )
   * )
   */
  public function get_match(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = array($db->getMatch($request->get_param('id')));
    return $this->getResponse($request, $items);
  }
  
  
  /**
   * @SWG\Get(
   *     path="/matches/{matchId}/properties",
   *     @SWG\Parameter(
   *         in="path",
   *         name="matchId",
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
   */
  public function get_properties_for_match(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getMatchProperties($request->get_param('id'));
    return $this->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/matches/{matchId}/info",
   *     @SWG\Parameter(
   *         in="path",
   *         name="matchId",
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
   */
  public function get_info_for_match(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = array($db->getFullMatchInfo($request->get_param('id')));
    return $this->getResponse($request, $items);
  }
  
  public function set_match_fixture(WP_REST_Request $request) {
    $db = new DB\Api();
    $match = $db->getMatch($request->get_param('id'));
    $body = json_decode($request->get_body());
    if(!property_exists($body, 'match')) {
      return new WP_Error('missing_match', 'Could not find matchdata in request', array('status' => 400));
    }
    $patch = $body->match;
    if(!property_exists($patch, 'fixture')) {
      return new WP_Error('invalid_date', 'Could not find or parse the given date', array('status' => 400));
    }
    $date = strtotime($patch->fixture);
    if(!$date) {
      return new WP_Error('invalid_date', 'Could not find or parse the given date', array('status' => 400));
    }
    $fixure = strftime('%Y-%m-%d %H:%M:%S', $date);
    $match->fixture = $fixure;
    if(property_exists($patch, 'location') && is_numeric($patch->location)) {
      $location = $db->getLocation($patch->location);
      if(!$location) {
        return new WP_Error('invalid_location', 'Location with the given ID is unknown', array('status' => 400));
      }
      $match->location = $patch->location;
    }
    $db->updateMatch($match);
    Events\Service::fireEvent(Events\Service::$MATCH_FIXTURE_SET, new Events\MatchFixtureUpdatedEvent($match, $body->email));
    
    $items = array($db->getMatch($request->get_param('id')));
    return $this->getResponse($request, $items, true);
  }
  
  public function is_valid_email_for_match(WP_REST_Request $request) {
    $body = json_decode($request->get_body());
    if(!property_exists($body, 'email')) {
      return false;
    }
    $requestEmail = strtolower(trim($body->email));
    $matchId = $request->get_param('id');
    $db = new DB\Api();
    $match = $db->getMatch($matchId);
    $homeInfo = $db->getTeamProperties($match->home_team);
    $awayInfo = $db->getTeamProperties($match->away_team);
    if(is_array($homeInfo)) {
      if(isset($homeInfo['captain_email']) && strtolower(trim($homeInfo['captain_email'])) == $requestEmail) {
        return true;
      }
      if(isset($homeInfo['vice_captain_email']) && strtolower(trim($homeInfo['vice_captain_email'])) == $requestEmail) {
        return true;
      }
    }
    if(is_array($awayInfo)) {
      if(isset($awayInfo['captain_email']) && strtolower(trim($awayInfo['captain_email'])) == $requestEmail) {
        return true;
      }
      if(isset($awayInfo['vice_captain_email']) && strtolower(trim($awayInfo['vice_captain_email'])) == $requestEmail) {
        return true;
      }
    }
    foreach($db->getLeagueAdmins() as $admin) {
      if($admin->email == $requestEmail) {
        return true;
      }
    }
    return false;
  }
  
  protected function getLinks($itemId) {
    $teamEndpoint = new Teams();
    $gameDayEndpoint = new GameDays();
    $locationEndpoint = new Locations();
    return array("homeTeam" => array("href" => $teamEndpoint->getFullBaseUrl() . '/<propertyid>', "embeddable" => array("table" => "teams", "field" => "id"), "idFields" => array("homeTeam", "homeid")), "awayTeam" => array("href" => $teamEndpoint->getFullBaseUrl() . '/<propertyid>', "embeddable" => array("table" => "teams", "field" => "id"), "idFields" => array("awayTeam", "awayid")), "location" => array("href" => $locationEndpoint->getFullBaseUrl() . '/<propertyid>', "embeddable" => array("table" => "locations", "field" => "id"), "idFields" => array("location")), "gameDay" => array("href" => $gameDayEndpoint->getFullBaseUrl() . '/<propertyid>', "embeddable" => array("table" => "game_days", "field" => "id"), "idFields" => array("gameDayId")), "properties" => array("href" => $this->getFullBaseUrl() . '/<id>/properties', "embeddable" => array("table" => "match_properties", "field" => "objectId")), "info" => array("href" => $this->getFullBaseUrl() . '/<id>/info'));
  }
  
}
