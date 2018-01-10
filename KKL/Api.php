<?php
require_once('Api/Controller.php');
require_once('Api/Leagues.php');
require_once('Api/Seasons.php');

class KKL_Api {

  public static function init() {
    add_action('rest_api_init', function () {
      $controller = new KKL_Api_Leagues();
      $controller->register_routes();
    });
    add_action('rest_api_init', function () {
      $controller = new KKL_Api_Seasons();
      $controller->register_routes();
    });
	}

}
