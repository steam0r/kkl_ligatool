<?php

namespace KKL\Ligatool;

class Api {

  public static function init() {
    add_action('rest_api_init', function () {
      {
        $controller = new Api\Documentation();
        $controller->register_routes();
      }
      {
        $controller = new Api\Clubs();
        $controller->register_routes();
      }
      {
        $controller = new Api\GameDays();
        $controller->register_routes();
      }
      {
        $controller = new Api\Leagues();
        $controller->register_routes();
      }
      {
        $controller = new Api\Locations();
        $controller->register_routes();
      }
      {
        $controller = new Api\Matches();
        $controller->register_routes();
      }
      {
        $controller = new Api\Players();
        $controller->register_routes();
      }
      {
        $controller = new Api\Seasons();
        $controller->register_routes();
      }
      {
        $controller = new Api\Teams();
        $controller->register_routes();
      }
      {
        $controller = new Api\Admins();
        $controller->register_routes();
      }
    });
  }

}
