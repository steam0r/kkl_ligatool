<?php

namespace KKL\Ligatool\Api;

use KKL\Ligatool\DB;
use WP_REST_Request;
use WP_REST_Server;

class Admins extends Controller {
  
  public function register_routes() {
    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName(),
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_admins'),
        'args' => array(),
        'permission_callback' => array($this, 'authenticate_api_key')
      )
    );
    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)',
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_admin'),
        'args' => array('context' => array('default' => 'view')),
        'permission_callback' => array($this, 'authenticate_api_key')
      )
    );
    register_rest_route(
      $this->getNamespace(),
      '/' . $this->getBaseName() . '/(?P<id>[\d]+)/properties',
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_info_for_admin'),
        'args' => array('context' => array('default' => 'view')),
        'permission_callback' => array($this, 'authenticate_api_key')
      )
    );
  }
  
  public function getBaseName() {
    return 'admins';
  }
  
  /**
   * @OA\Get(
   *     path="/admins",
   *     tags={"admins"},
   *     @OA\Response(
   *         response=200,
   *         description="successful operation",
   *         @OA\Schema(type="array", ref="#/definitions/Player")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_admins(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getLeagueAdmins();
    return $this->getResponse($request, $items);
  }
  
  /**
   * @OA\Get(
   *     path="/admins/:playerId",
   *     tags={"admins"},
   *     @OA\Parameter(
   *         in="path",
   *         name="playerId",
   *         required=true,
   *         type="integer",
   *         format="int64"
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="successful operation",
   *         @OA\Schema(ref="#/definitions/Player")
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_admin(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = array($db->getPlayer($request->get_param('id')));
    return $this->getResponse($request, $items);
  }
  
  /**
   * @OA\Get(
   *     path="/admins/:playerId/info",
   *     tags={"admins"},
   *     @OA\Parameter(
   *         in="path",
   *         name="playerId",
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
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_info_for_admin(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getPlayerProperties($request->get_param('id'));
    return $this->getResponse($request, $items);
  }
  
  protected function getLinks($itemId) {
    return array("properties" => array("href" => $this->getFullBaseUrl() . '/<id>/properties', "embeddable" => array("table" => "player_properties", "field" => "objectId")));
  }
  
}
