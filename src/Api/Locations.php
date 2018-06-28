<?php

namespace KKL\Ligatool\Api;

use KKL\Ligatool\DB;
use WP_REST_Request;
use WP_REST_Server;

class Locations extends Controller {
  
  public function registerRoutes() {
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName(), array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_locations'), 'args' => array(),));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_location'), 'args' => array('context' => array('default' => 'view',),)));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/teams', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_teams_for_location'), 'args' => array('context' => array('default' => 'view',),)));
  }
  
  public function getBaseName() {
    return 'locations';
  }
  
  /**
   * @SWG\Get(
   *     path="/locations",
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(type="array", ref="#/definitions/Location")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_locations(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getLocations();
    return $this->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/locations/{locationId}",
   *     @SWG\Parameter(
   *         in="path",
   *         name="locationId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation",
   *         @SWG\Schema(ref="#/definitions/Location")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_location(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = array($db->getLocation($request->get_param('id')));
    return $this->getResponse($request, $items);
  }
  
  /**
   * @SWG\Get(
   *     path="/locations/{locationId}/teams",
   *     @SWG\Parameter(
   *         in="path",
   *         name="locationId",
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
  public function get_teams_for_location(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getTeamsForLocation($request->get_param('id'));
    return $this->getResponse($request, $items);
  }
  
}
