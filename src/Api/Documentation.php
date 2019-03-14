<?php

namespace KKL\Ligatool\Api;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class Documentation extends Controller {

  public function register_routes() {
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName(), array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_swagger_json'), 'args' => array(),));
  }

  public function getBaseName() {
    return 'info';
  }

  /**
   * @OA\Info(
   *    title="KKL Ligatool API",
   *    version="0.1"
   * )
   * @OA\Server(url="https://www.kickerligakoeln.de/wp-json/kkl/v1")
   * @OA\SecurityScheme(securityScheme="ApiKeyAuth", type="apiKey", in="header", name="X-API-KEY")
   * @param WP_REST_Request $request
   * @return WP_REST_Response
   */
  public function get_swagger_json(WP_REST_Request $request) {
    $controller = dirname(__FILE__);
    $models = $controller . '/../Model/';

    $openapi = \OpenApi\scan(array($controller, $models));
    return new WP_REST_Response($openapi, 200);
  }

}
