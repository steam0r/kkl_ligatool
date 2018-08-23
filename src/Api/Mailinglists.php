<?php

namespace KKL\Ligatool\Api;

use KKL\Ligatool\DB;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class Mailinglists extends Controller {
  
  public function register_routes() {
    register_rest_route(
      $this->getNamespace(), '/' . $this->getBaseName() . '/league',
      array('methods'             => WP_REST_Server::READABLE, 'callback' => array($this, 'get_league_list'),
            'args'                => array(), 'permission_callback' => array($this, 'authenticate_api_key'))
    );
    register_rest_route(
      $this->getNamespace(), '/' . $this->getBaseName() . '/admins',
      array('methods'             => WP_REST_Server::READABLE, 'callback' => array($this, 'get_admin_list'),
            'args'                => array(), 'permission_callback' => array($this, 'authenticate_api_key'))
    );
    register_rest_route(
      $this->getNamespace(), '/' . $this->getBaseName() . '/resolve/(?P<email>[\S]+)',
      array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'resolve_mail_address'),
            'args'    => array(), 'permission_callback' => array($this, 'authenticate_api_key'))
    );
    register_rest_route(
      $this->getNamespace(), '/' . $this->getBaseName() . '/matchinfo/(?P<id>[\d]+)',
      array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'resolve_matchinfo'),
        'args'    => array(), 'permission_callback' => array($this, 'authenticate_api_key'))
    );
  }
  
  public function getBaseName() {
    return 'mailinglists';
  }
  
  /**
   * @SWG\Get(
   *     path="/mailinglists/league",
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation"
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_league_list(WP_REST_Request $request) {
    $db = new DB\Api();
    
    // FIXME: this is wrong, should be only active players
    $query = "SELECT DISTINCT(email) FROM players";
    $results = $db->getDb()->get_results($query);
    
    foreach($results as $email) {
      if(!empty($email->email)) {
        echo $email->email;
        echo "\n";
      }
    }
    echo "ligaleitung@kickerligakoeln.de\n";
    echo "pokal@kickerligakoeln.de\n";
    // FIXME: find a way to not die here but return proper HTML without WP_REST_Response
    die();
    
  }
  
  /**
   * @SWG\Get(
   *     path="/mailinglists/admins",
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation"
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function get_admin_list(WP_REST_Request $request) {
    
    $db = new DB\Api();
    
    // FIXME: this should be moved to db layer...i think
    $query = 'SELECT p.email,pm.value as llcontact FROM `player_properties` as pp ' .
             'JOIN players as p ON pp.objectId = p.id ' .
             'LEFT JOIN player_properties as pm ON p.id = pm.objectId AND pm.property_key = "ligaleitung_address" ' .
             'WHERE pp.property_key = "member_ligaleitung" and pp.value = "true" ';
    $results = $db->getDb()->get_results($query);
    
    foreach($results as $email) {
      if($email->llcontact) {
        echo $email->llcontact;
      } else {
        echo $email->email;
      }
      echo "\n";
    }
    // FIXME: find a way to not die here but return proper HTML without WP_REST_Response
    die();
    
  }
  
  /**
   * @SWG\Get(
   *     path="/mailinglists/resolve",
   *     @SWG\Response(
   *         response=200,
   *         description="successful operation"
   *     )
   * )
   * @param WP_REST_Request $request
   * @return \WP_Error|\WP_REST_Response
   */
  public function resolve_mail_address(WP_REST_Request $request) {
    $query = $request->get_param('email');
    $response = new \stdClass();
    if(isset($query)) {
      $db = new DB\Api();
      $result = $db->getInfoByEmailAddress($query);
      if($result) {
        $response = $result;
      }
    }
    return new WP_REST_Response($response, 200);
  }

  public function resolve_matchinfo(WP_REST_Request $request) {
    $query = $request->get_param('id');
    $response = new \stdClass();
    if(isset($query)) {
      $db = new DB\Api();
      $match = $db->getMatch($query);
      $homeTeam = $db->getTeam($match->home_team);
      $awayTeam = $db->getTeam($match->away_team);
      $day = $db->getGameDay($match->game_day_id);
      $league = $db->getLeagueForGameday($day->id);
      $response->id = $match->id;
      $response->home_name = $homeTeam->name;
      $response->away_name = $awayTeam->name;
      $response->league = $league->name;
    }
    return new WP_REST_Response($response, 200);
  }
  
}
