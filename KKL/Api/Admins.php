<?php

class KKL_Api_Admins extends KKL_Api_Controller {

  public function register_routes() {
    $base = 'admins';
    register_rest_route($this->getNamespace(), '/' . $base, array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_admins'),
        'args' => array(),
      )
    );
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_admin'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
    register_rest_route($this->getNamespace(), '/' . $base . '/(?P<id>[\d]+)/info', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array($this, 'get_info_for_admin'),
      'args' => array(
        'context' => array(
          'default' => 'view',
        ),
      )
    ));
  }

  public function get_admins($request) {
    $db = new KKL_DB();
    $items = $db->getLeagueAdmins();
    return $this->getResponse($request, $items);
  }

  public function get_admin($request) {
    $db = new KKL_DB();
    $items = array($db->getPlayer($request->get_param('id')));
    return $this->getResponse($request, $items);
  }

  public function get_info_for_admin(WP_REST_Request $request) {
    $db = new KKL_DB();
    $items = $db->getPlayerProperties($request->get_param('id'));
    return $this->getResponse($request, $items);
  }

}
