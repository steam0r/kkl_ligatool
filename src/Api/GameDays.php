<?php

namespace KKL\Ligatool\Api;

use KKL\Ligatool\DB;
use WP_REST_Request;
use WP_REST_Server;

class GameDays extends Controller {

  public function register_routes() {
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName(), array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_gamedays'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_gameday'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/matches', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_matches_for_gameday'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/next', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_next_gameday'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/(?P<id>[\d]+)/previous', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_previous_gameday'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
  }

  public function getBaseName() {
    return 'gamedays';
  }

  public function get_gamedays(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getGameDays();
    return $this->getResponse($request, $items);
  }

  public function get_gameday(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = array($db->getGameDay($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_matches_for_gameday(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = $db->getMatchesByGameDay($request->get_param('id'));
    $matchesEndpoint = new Matches();
    return $matchesEndpoint->getResponse($request, $items);
  }

  public function get_next_gameday(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = array($db->getNextGameDay($db->getGameDay($request->get_param('id'))));
    return $this->getResponse($request, $items);
  }

  public function get_previous_gameday(WP_REST_Request $request) {
    $db = new DB\Api();
    $items = array($db->getPreviousGameDay($db->getGameDay($request->get_param('id'))));
    return $this->getResponse($request, $items);
  }

  protected function getLinks($itemId) {
    $seasonEndpoint = new Seasons();
    return array(
      "season" => array(
        "href" => $seasonEndpoint->getFullBaseUrl() . '/<propertyid>',
        "embeddable" => array(
          "table" => "seasons",
          "field" => "id"
        ),
        "idFields" => array("seasonId")
      ),
      "matches" => array(
        "href" => $this->getFullBaseUrl() . '/<id>/matches',
        "embeddable" => array(
          "table" => "matches",
          "field" => "game_day_id"
        )
      )
    );
  }

}