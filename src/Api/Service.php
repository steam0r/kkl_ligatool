<?php

namespace KKL\Ligatool\Api;

class Service {
  
  public static function init() {
    add_action('rest_api_init', function() {
      {
        $controller = new Documentation();
        $controller->init();
      }
      {
        $controller = new Clubs();
        $controller->init();
      }
      {
        $controller = new GameDays();
        $controller->init();
      }
      {
        $controller = new Leagues();
        $controller->init();
      }
      {
        $controller = new Locations();
        $controller->init();
      }
      {
        $controller = new Matches();
        $controller->init();
      }
      {
        $controller = new Players();
        $controller->init();
      }
      {
        $controller = new Seasons();
        $controller->init();
      }
      {
        $controller = new Teams();
        $controller->init();
      }
      {
        $controller = new Admins();
        $controller->init();
      }
      {
        $controller = new ICal();
        $controller->init();
      }
    });
  }
  
}
