<?php

namespace KKL\Ligatool\Api;

class Service {
  
  public static function init() {
    add_action('rest_api_init', function() {
      {
        $controller = new Documentation();
        $controller->register_routes();
      }
      {
        $controller = new Clubs();
        $controller->register_routes();
      }
      {
        $controller = new GameDays();
        $controller->register_routes();
      }
      {
        $controller = new Leagues();
        $controller->register_routes();
      }
      {
        $controller = new Locations();
        $controller->register_routes();
      }
      {
        $controller = new Matches();
        $controller->register_routes();
      }
      {
        $controller = new Players();
        $controller->register_routes();
      }
      {
        $controller = new Seasons();
        $controller->register_routes();
      }
      {
        $controller = new Teams();
        $controller->register_routes();
      }
      {
        $controller = new Admins();
        $controller->register_routes();
      }
      {
        $controller = new Mailinglists();
        $controller->register_routes();
      }
    });
  }
  
}
