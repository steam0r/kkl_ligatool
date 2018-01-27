<?php

namespace KKL\Ligatool;

use KKL\Ligatool\Api;

class KKL_Api {

  public static function init() {
    add_action('rest_api_init', function () {
      {
        $controller = new Api\KKL_Api_Documentation();
        $controller->register_routes();
      }
      {
        $controller = new Api\KKL_Api_Clubs();
        $controller->register_routes();
      }
      {
        $controller = new Api\KKL_Api_GameDays();
        $controller->register_routes();
      }
      {
        $controller = new Api\KKL_Api_Leagues();
        $controller->register_routes();
      }
      {
        $controller = new Api\KKL_Api_Locations();
        $controller->register_routes();
      }
      {
        $controller = new Api\KKL_Api_Matches();
        $controller->register_routes();
      }
      {
        $controller = new Api\KKL_Api_Players();
        $controller->register_routes();
      }
      {
        $controller = new Api\KKL_Api_Seasons();
        $controller->register_routes();
      }
      {
        $controller = new Api\KKL_Api_Teams();
        $controller->register_routes();
      }
      {
        $controller = new Api\KKL_Api_Admins();
        $controller->register_routes();
      }
    });
  }

}
