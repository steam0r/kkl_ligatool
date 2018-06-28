<?php

namespace KKL\Ligatool\Api;

use Swagger;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class Documentation extends Controller {
  
  public function registerRoutes() {
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName() . '/swagger.json', array('methods' => WP_REST_Server::READABLE, 'callback' => array($this, 'get_swagger_json'), 'args' => array(),));
  }
  
  public function getBaseName() {
    return 'docs';
  }
  
  /**
   * @SWG\Get(
   *     path="/docs/swagger.json",
   *     @SWG\Response(
   *          response="200",
   *          produces={"application/json"},
   *          description="Fetch a Swagger 2.0 specification for this API"
   *     )
   * )
   * @param WP_REST_Request $request
   * @return WP_REST_Response
   */
  public function get_swagger_json(WP_REST_Request $request) {
    $controller = dirname(__FILE__);
    $models = $controller . '/../Model/';
    $swagger = Swagger\scan(array($models, $controller));
    
    return new WP_REST_Response($swagger, 200);
  }
  
}
