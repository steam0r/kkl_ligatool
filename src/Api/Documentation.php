<?php

namespace KKL\Ligatool\Api;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class KKL_Api_Documentation extends KKL_Api_Controller {

  public function getBaseName() {
    return 'doc';
  }

  public function register_routes() {
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/swagger.json', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_swagger_json'),
        'args' => array(),
      )
    );
  }

  public function get_swagger_json(WP_REST_Request $request) {
    $swagger = file_get_contents(dirname(__FILE__) . '/swagger.json');
    $json = json_decode($swagger);
    return new WP_REST_Response($json, 200);
  }

}
