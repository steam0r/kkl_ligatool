<?php
/*
Plugin Name: KKL Ligatool
Plugin URI: https://www.kickerligakoeln.de
Description: Integration of the KKL Database into Wordpress
Version: 2.2.0
Author: Stephan Maihoefer / Benedikt Scherer
Author URI: http://undev.de
License: MIT
*/

use KKL\Ligatool\KKL;

require __DIR__.'/vendor/autoload.php';

$kkl = new KKL();
$kkl->init();

$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
  'https://ci.undev.de/releases/steam0r/kkl_ligatool/master/release.json',
  __FILE__, //Full path to the main plugin file or functions.php.
  'undev/polyshapes-wpplugin'
);
