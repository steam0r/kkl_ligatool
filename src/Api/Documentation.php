<?php

namespace KKL\Ligatool\Api;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use Swagger;

class Documentation extends Controller {

    public function register_routes() {
        register_rest_route($this->getNamespace(), '/'.$this->getBaseName().'/swagger.json', array(
                        'methods'  => WP_REST_Server::READABLE,
                        'callback' => array($this, 'get_swagger_json'),
                        'args'     => array(),
                )
        );
    }

    public function getBaseName() {
        return 'doc';
    }

    /**
     * @SWG\Get(
     *     path="/kkl/v1/doc/swagger.json",
     *     @SWG\Response(response="200", description="An example resource")
     * )
     */
    public function get_swagger_json(WP_REST_Request $request) {
        $controller = dirname(__FILE__);
        $models = $controller . '/../Model/';
        $swagger = Swagger\scan(array($models, $controller));
        return new WP_REST_Response($swagger, 200);
    }

}
