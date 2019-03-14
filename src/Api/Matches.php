<?php

namespace KKL\Ligatool\Api;

use KKL\Ligatool\DB;
use KKL\Ligatool\Events;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

class Matches extends Controller
{

  public function register_routes()
  {
    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName(),
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_matches'),
        'args' => array()
      )
    );

    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)',
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_match'),
        'args' => array('context' => array('default' => 'view')
        )
      )
    );

    register_rest_route($this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)/fixture',
      array(
        'methods' => 'PATCH',
        'callback' => array($this, 'set_match_fixture'),
        'permission_callback' => array($this, 'is_valid_email_for_match'),
        'args' => array('context' => array('default' => 'view')
        )
      )
    );

    register_rest_route($this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)/properties',
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_properties_for_match'),
        'args' => array('context' => array('default' => 'view')
        )
      )
    );

    register_rest_route($this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)/info',
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_info_for_match'),
        'args' => array('context' => array('default' => 'view')
        )
      )
    );

    register_rest_route($this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)/score',
      array(
        'methods' => 'PATCH',
        'callback' => array($this, 'set_live_result'),
        'permission_callback' => array($this, 'authenticate_api_key'),
        'args' => array('context' => array('default' => 'view')
        )
      )
    );

    register_rest_route($this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)/score',
      array(
        'methods' => 'PUT',
        'callback' => array($this, 'set_final_match_result'),
        'permission_callback' => array($this, 'authenticate_api_key'),
        'args' => array('context' => array('default' => 'view')
        )
      )
    );
  }

  public function getBaseName()
  {
    return 'matches';
  }

  /**
   * @OA\Get(
   *     path="/matches",
   *     tags={"matches"},
   *     @OA\Response(
   *         response=200,
   *         description="successful operation",
   *         @OA\Schema(type="array", ref="#/definitions/Match")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return WP_Error|\WP_REST_Response
   */
  public function get_matches(WP_REST_Request $request)
  {
    $db = new DB\Api();
    $items = $db->getMatches();
    return $this->getResponse($request, $items);
  }

  /**
   * @OA\Get(
   *     path="/matches/:matchId",
   *     tags={"matches"},
   *     @OA\Parameter(
   *         in="path",
   *         name="matchId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="successful operation",
   *         @OA\Schema(ref="#/definitions/Match")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return WP_Error|\WP_REST_Response
   */
  public function get_match(WP_REST_Request $request)
  {
    $db = new DB\Api();
    $items = array($db->getMatch($request->get_param('id')));
    return $this->getResponse($request, $items);
  }


  /**
   * @OA\Get(
   *     path="/matches/:matchId/properties",
   *     tags={"matches"},
   *     @OA\Parameter(
   *         in="path",
   *         name="matchId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="successful operation",
   *         @OA\Schema(type="array", ref="#/definitions/Property")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return WP_Error|\WP_REST_Response
   */
  public function get_properties_for_match(WP_REST_Request $request)
  {
    $db = new DB\Api();
    $items = $db->getMatchProperties($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

  /**
   * @OA\Get(
   *     path="/matches/:matchId/info",
   *     tags={"matches"},
   *     @OA\Parameter(
   *         in="path",
   *         name="matchId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="successful operation",
   *         @OA\Schema(type="array", ref="#/definitions/Property")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return WP_Error|\WP_REST_Response
   */
  public function get_info_for_match(WP_REST_Request $request)
  {
    $db = new DB\Api();
    $items = array($db->getFullMatchInfo($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  /**
   * @param WP_REST_Request $request
   * @return WP_Error|\WP_REST_Response
   */
  public function set_match_fixture(WP_REST_Request $request)
  {
    $db = new DB\Api();
    $match = $db->getMatch($request->get_param('id'));
    $body = json_decode($request->get_body());
    if (!property_exists($body, 'match')) {
      return new WP_Error('missing_match', 'Could not find matchdata in request', array('status' => 400));
    }
    $patch = $body->match;
    if (!property_exists($patch, 'fixture')) {
      return new WP_Error('invalid_date', 'Could not find or parse the given date', array('status' => 400));
    }
    $date = strtotime($patch->fixture);
    if (!$date) {
      return new WP_Error('invalid_date', 'Could not find or parse the given date', array('status' => 400));
    }
    $fixure = strftime('%Y-%m-%d %H:%M:%S', $date);
    $match->fixture = $fixure;
    if (property_exists($patch, 'location') && is_numeric($patch->location)) {
      $location = $db->getLocation($patch->location);
      if (!$location) {
        return new WP_Error('invalid_location', 'Location with the given ID is unknown', array('status' => 400));
      }
      $match->location = $patch->location;
    }
    $db->updateMatch($match);
    Events\Service::fireEvent(Events\Service::$MATCH_FIXTURE_SET, new Events\MatchFixtureUpdatedEvent($match, $body->email));

    $items = array($db->getMatch($request->get_param('id')));
    return $this->getResponse($request, $items, true);
  }

  public function set_live_result(WP_REST_Request $request)
  {
    $db = new DB\Api();
    $match = $db->getMatch($request->get_param('id'));
    $match = $this->updateMatchFromRequest($request, $match);
    $db = new DB\Wordpress();
    $match = $db->createOrUpdateMatch($match);
    return new \WP_REST_Response($match, 200);
  }

  public function set_final_match_result(WP_REST_Request $request)
  {
    $db = new DB\Api();
    $match = $db->getMatch($request->get_param('id'));
    $match = $this->updateMatchFromRequest($request, $match);
    $match->status = 3;
    $db = new DB\Wordpress();
    $match = $db->createOrUpdateMatch($match);
    return new \WP_REST_Response($match, 200);

  }

  public function is_valid_email_for_match(WP_REST_Request $request)
  {
    $body = json_decode($request->get_body());
    if (!property_exists($body, 'email')) {
      return false;
    }
    $requestEmail = strtolower(trim($body->email));
    $matchId = $request->get_param('id');
    $db = new DB\Api();
    $match = $db->getMatch($matchId);
    $homeInfo = $db->getTeamProperties($match->home_team);
    $awayInfo = $db->getTeamProperties($match->away_team);
    if (is_array($homeInfo)) {
      if (isset($homeInfo['captain'])) {
        $homeCaptain = $db->getPlayer($homeInfo['captain']);
        if ($homeCaptain && strtolower(trim($homeCaptain->email)) == $requestEmail) {
          return true;
        }
      }
      if (isset($homeInfo['vice_captain'])) {
        $homeVice = $db->getPlayer($homeInfo['vice_captain']);
        if ($homeVice && strtolower(trim($homeVice->email)) == $requestEmail) {
          return true;
        }
      }
    }
    if (is_array($awayInfo)) {
      if (isset($awayInfo['captain'])) {
        $awayCaptain = $db->getPlayer($homeInfo['captain']);
        if ($awayCaptain && strtolower(trim($awayCaptain->email)) == $requestEmail) {
          return true;
        }
      }
      if (isset($awayInfo['vice_captain'])) {
        $awayVice = $db->getPlayer($homeInfo['vice_captain']);
        if ($awayVice && strtolower(trim($awayVice->email)) == $requestEmail) {
          return true;
        }
      }
    }
    foreach ($db->getLeagueAdmins() as $admin) {
      if ($admin->email == $requestEmail) {
        return true;
      }
    }
    return false;
  }

  protected function getLinks($itemId)
  {
    $teamEndpoint = new Teams();
    $gameDayEndpoint = new GameDays();
    $locationEndpoint = new Locations();
    return array("homeTeam" => array("href" => $teamEndpoint->getFullBaseUrl() . '/<propertyid>', "embeddable" => array("table" => "teams", "field" => "id"), "idFields" => array("homeTeam", "homeid")), "awayTeam" => array("href" => $teamEndpoint->getFullBaseUrl() . '/<propertyid>', "embeddable" => array("table" => "teams", "field" => "id"), "idFields" => array("awayTeam", "awayid")), "location" => array("href" => $locationEndpoint->getFullBaseUrl() . '/<propertyid>', "embeddable" => array("table" => "locations", "field" => "id"), "idFields" => array("location")), "gameDay" => array("href" => $gameDayEndpoint->getFullBaseUrl() . '/<propertyid>', "embeddable" => array("table" => "game_days", "field" => "id"), "idFields" => array("gameDayId")), "properties" => array("href" => $this->getFullBaseUrl() . '/<id>/properties', "embeddable" => array("table" => "match_properties", "field" => "objectId")), "info" => array("href" => $this->getFullBaseUrl() . '/<id>/info'));
  }

  private function updateMatchFromRequest(WP_REST_Request $request, $match)
  {
    $body = json_decode($request->get_body());
    $match->goals_home = $body->goals_home;
    $match->goals_away = $body->goals_away;
    $match->score_home = $body->score_home;
    $match->score_away = $body->score_away;
    $match->notes = $body->notes;
    return $match;
  }

}
