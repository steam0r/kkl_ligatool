<?php

class KKL_Api_Matches extends KKL_Api_Controller {

  public function getBaseName() {
    return 'matches';
  }

  public function register_routes() {
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName(), array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_matches'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_match'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/properties', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_properties_for_match'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/info', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_info_for_match'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
  }

  protected function getLinks() {
    $teamEndpoint = new KKL_Api_Teams();
    $gameDayEndpoint = new KKL_Api_GameDays();
    return array(
      "homeTeam" => array(
        "href" => $teamEndpoint->getFullBaseUrl() . '/<propertyid>',
        "embeddable" => true,
        "idFields" => array("homeTeam", "homeid")
      ),
      "awayTeam" => array(
        "href" => $teamEndpoint->getFullBaseUrl() . '/<propertyid>',
        "embeddable" => true,
        "idFields" => array("awayTeam", "awayid")
      ),
      "gameDay" => array(
        "href" => $gameDayEndpoint->getFullBaseUrl() . '/<propertyid>',
        "embeddable" => true,
        "idFields" => array("gameDayId")
      ),
      "properties" => array(
        "href" => $this->getFullBaseUrl() . '/<id>/properties',
        "embeddable" => true
      ),
      "info" => array(
        "href" => $this->getFullBaseUrl() . '/<id>/info',
        "embeddable" => false
      )
    );
  }

  public function get_matches(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = $db->getMatches();
    return $this->getResponse($request, $items);
  }

  public function get_match(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = array($db->getMatch($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_properties_for_match(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = $db->getFullMatchInfo($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

  public function get_info_for_match(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = $db->getFullMatchInfo($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

}
