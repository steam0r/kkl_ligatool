<?php

namespace KKL\Ligatool\Api;

use KKL\Ligatool\DB;
use WP_REST_Request;
use WP_REST_Server;

class Clubs extends Controller {
  
  public function register_routes() {
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName(),
                        array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_clubs'),
                              'args'    => array(),)
    );
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)',
                        array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_club'),
                              'args'    => array('context' => array('default' => 'view',),))
    );
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)/teams',
                        array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_teams_for_club'),
                              'args'    => array('context' => array('default' => 'view',),))
    );
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)/awards',
                        array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_awards_for_club'),
                              'args'    => array('context' => array('default' => 'view',),))
    );
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)/currentteam',
                        array('methods'  => WP_REST_Server::READABLE,
                              'callback' => array($this, 'get_current_team_for_club'),
                              'args'     => array('context' => array('default' => 'view',),))
    );
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)/properties',
                        array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_info_for_club'),
                              'args'    => array('context' => array('default' => 'view',),))
    );
  }
  
  public function getBaseName() {
    return 'clubs';
  }
  
  /**
   * @SWG\Get(
   *     path="/clubs",
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(type="array", ref="#/definitions/Club")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_clubs(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getClubs();
    return $this->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/club/{clubId}",
   *     @SWG\Parameter(
   *         in="path",
   *         name="clubId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(ref="#/definitions/Club")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_club(WP_REST_Request $request) {
    $items = array($this->getClubFromRequest($request));
    return $this->getResponse($request, $items);
  }
  
  private function getClubFromRequest(WP_REST_Request $request) {
    $db = new DB\Api();
    $param = $request->get_param('id');
    if(is_numeric($param)) {
      return $db->getClub($param);
    } else {
      return $db->getClubByCode($param);
    }
  }
  
  /**
   * @SWG\Get(
   *     path="/club/{clubId}/teams",
   *     @SWG\Parameter(
   *         in="path",
   *         name="clubId",
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
  public function get_teams_for_club(WP_REST_Request $request) {
    $db = new DB\Api();
    $club = $this->getClubFromRequest($request);
    $items = $db->getTeamsForClub($club->id);
    $teamsEndpoint = new Teams();
    return $teamsEndpoint->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/club/{clubId}/awards",
   *     @SWG\Parameter(
   *         in="path",
   *         name="clubId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(type="array", ref="#/definitions/Award")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_awards_for_club(WP_REST_Request $request) {
    $db = new DB\Api();
    $club = $this->getClubFromRequest($request);
    $items = $db->getAwardsForClub($club->id);
    return $this->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/club/{clubId}/currentteam",
   *     @SWG\Parameter(
   *         in="path",
   *         name="clubId",
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
  public function get_current_team_for_club(WP_REST_Request $request) {
    $db = new DB\Api();
    $club = $this->getClubFromRequest($request);
    $items = array($db->getCurrentTeamForClub($club->id));
    $teamsEndpoint = new Teams();
    return $teamsEndpoint->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/club/{clubId}/info",
   *     @SWG\Parameter(
   *         in="path",
   *         name="clubId",
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
  public function get_info_for_club(WP_REST_Request $request) {
    $db = new DB\Api();
    $club = $this->getClubFromRequest($request);
    $items = array($db->getClubProperties($club->id));
    return $this->getResponse($request, $items);
  }
  
  protected function getLinks($itemId) {
    return array("awards"                                                                                             => array("href"       => $this->getFullBaseUrl(
      ) . '/<id>/awards',
                                                                                                                               "embeddable" => array("callback" => function() use ($itemId) {
                                                                                                                                 $db = new DB\Api(
                                                                                                                                 );
                                                                                                                                 return $db->getAwardsForClub(
                                                                                                                                   $itemId
                                                                                                                                 );
                                                                                                                               },)),
                 "teams"                                                                                              => array("href"       => $this->getFullBaseUrl(
                   ) . '/<id>/teams',
                                                                                                                               "embeddable" => array("table" => "teams",
                                                                                                                                                     "field" => "club_id")),
                 "properties"                                                                                         => array("href"       => $this->getFullBaseUrl(
                   ) . '/<id>/properties',
                                                                                                                               "embeddable" => array("table" => "club_properties",
                                                                                                                                                     "field" => "objectId")));
  }
  
}
