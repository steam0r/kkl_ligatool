<?php

class KKL_Api_Teams extends KKL_Api_Controller {

  public function getBaseName() {
    return 'teams';
  }

  public function register_routes() {
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName(), array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_teams'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_team'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/matches', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_matches_for_team'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/properties', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_info_for_team'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
  }

  protected function getLinks($itemId) {
    $clubEndpoint = new KKL_Api_Clubs();
    $seasonEndpoint = new KKL_Api_Seasons();
    return array(
      "matches" => array(
        "href" => $this->getFullBaseUrl() . '/<id>/matches',
        "embeddable" => array(
          "callback" => function() use ($itemId) {
            $db = new KKL_DB_Api();
            return $db->getMatchesForTeam($itemId);
          }
        )
      ),
      "club" => array(
        "href" => $clubEndpoint->getFullBaseUrl() . '/<propertyid>',
        "embeddable" => array(
          "table" => "clubs",
          "field" => "id"
        ),
        "idFields" => array("clubId")
      ),
      "season" => array(
        "href" => $seasonEndpoint->getFullBaseUrl() . '/<propertyid>',
        "embeddable" => array(
          "table" => "seasons",
          "field" => "id"
        ),
        "idFields" => array("seasonId")
      ),
      "properties" => array(
        "href" => $this->getFullBaseUrl() . '/<id>/properties',
        "embeddable" => array(
          "table" => "team_properties",
          "field" => "objectId"
        )
      ),
    );
  }

  public function get_teams(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = $db->getTeams();
    return $this->getResponse($request, $items);
  }

  public function get_team(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = array($db->getTeam($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_matches_for_team(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = $db->getMatchesForTeam($request->get_param('id'));
    $matchesEndpoint = new KKL_Api_Matches();
    return $matchesEndpoint->getResponse($request, $items);
  }

  public function get_info_for_team(WP_REST_Request $request) {
    $db = new KKL_DB_Api();
    $items = $db->getTeamProperties($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

}
