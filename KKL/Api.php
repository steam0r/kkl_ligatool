<?php
require_once('Api/Controller.php');
require_once('Api/Admins.php');
require_once('Api/Clubs.php');
require_once('Api/GameDays.php');
require_once('Api/Leagues.php');
require_once('Api/Locations.php');
require_once('Api/Matches.php');
require_once('Api/Players.php');
require_once('Api/Seasons.php');
require_once('Api/Teams.php');

class KKL_Api {

  public static function init() {
    add_action('rest_api_init', function () {
      {
        $controller = new KKL_Api_Clubs();
        $controller->register_routes();
      }
      {
        $controller = new KKL_Api_GameDays();
        $controller->register_routes();
      }
      {
        $controller = new KKL_Api_Leagues();
        $controller->register_routes();
      }
      {
        $controller = new KKL_Api_Locations();
        $controller->register_routes();
      }
      {
        $controller = new KKL_Api_Matches();
        $controller->register_routes();
      }
      {
        $controller = new KKL_Api_Players();
        $controller->register_routes();
      }
      {
        $controller = new KKL_Api_Seasons();
        $controller->register_routes();
      }
      {
        $controller = new KKL_Api_Teams();
        $controller->register_routes();
      }
      {
        $controller = new KKL_Api_Admins();
        $controller->register_routes();
      }
    });
  }

}
