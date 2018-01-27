<?php

namespace KKL\Ligatool\Api;

use KKL\Ligatool\DB;
use WP_REST_Request;
use WP_REST_Server;

class KKL_Api_Clubs extends KKL_Api_Controller {

  public function getBaseName() {
    return 'clubs';
  }

  public function register_routes() {
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName(), array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_clubs'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_club'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)/teams', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_teams_for_club'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)/awards', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_awards_for_club'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)/currentteam', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_current_team_for_club'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d\w]+)/properties', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_info_for_club'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
  }

  protected function getLinks($itemId) {
    return array(
      "awards" => array(
        "href" => $this->getFullBaseUrl() . '/<id>/awards',
        "embeddable" => array(
          "callback" => function () use ($itemId) {
            $db = new DB\KKL_DB_Api();
            return $db->getAwardsForClub($itemId);
          },
        )
      ),
      "teams" => array(
        "href" => $this->getFullBaseUrl() . '/<id>/teams',
        "embeddable" => array(
          "table" => "teams",
          "field" => "club_id"
        )
      ),
      "properties" => array(
        "href" => $this->getFullBaseUrl() . '/<id>/properties',
        "embeddable" => array(
          "table" => "club_properties",
          "field" => "objectId"
        )
      )
    );
  }

  public function get_clubs(WP_REST_Request $request) {
    $db = new DB\KKL_DB_Api();
    $items = $db->getClubs();
    return $this->getResponse($request, $items);
  }

  public function get_club(WP_REST_Request $request) {
    $items = array($this->getClubFromRequest($request));
    return $this->getResponse($request, $items);
  }

  public function get_teams_for_club(WP_REST_Request $request) {
    $db = new DB\KKL_DB_Api();
    $club = $this->getClubFromRequest($request);
    $items = $db->getTeamsForClub($club->id);
    $teamsEndpoint = new KKL_Api_Teams();
    return $teamsEndpoint->getResponse($request, $items);
  }

  public function get_awards_for_club(WP_REST_Request $request) {
    $db = new DB\KKL_DB_Api();
    $club = $this->getClubFromRequest($request);
    $items = $db->getAwardsForClub($club->id);
    return $this->getResponse($request, $items);
  }

  public function get_current_team_for_club(WP_REST_Request $request) {
    $db = new DB\KKL_DB_Api();
    $club = $this->getClubFromRequest($request);
    $items = array($db->getCurrentTeamForClub($club->id));
    $teamsEndpoint = new KKL_Api_Teams();
    return $teamsEndpoint->getResponse($request, $items);
  }

  public function get_info_for_club(WP_REST_Request $request) {
    $db = new DB\KKL_DB_Api();
    $club = $this->getClubFromRequest($request);
    $items = array($db->getClubProperties($club->id));
    return $this->getResponse($request, $items);
  }

  private function getClubFromRequest(WP_REST_Request $request) {
    $db = new DB\KKL_DB_Api();
    $param = $request->get_param('id');
    if (is_numeric($param)) {
      return $db->getClub($param);
    } else {
      return $db->getClubByCode($param);
    }
  }

}
