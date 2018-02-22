<?php
/*
Plugin Name: KKL Ligatool
Plugin URI: http://liga.kickerliebe.de
Description: Integration of the KKL Database into Wordpress
Version: 0.1
Author: Stephan Maihoefer / Benedikt Scherer
Author URI: http://undev.de
License: MIT
*/

use KKL\Ligatool\KKL;

require __DIR__.'/vendor/autoload.php';

$kkl = new KKL();
$kkl->init();
