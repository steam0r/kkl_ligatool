<?php
/*
Plugin Name: KKL Ligatool
Plugin URI: https://www.kickerligakoeln.de
Description: Integration of the KKL Database into Wordpress
Version: 2.15.7
Author: Stephan Maihoefer / Benedikt Scherer
Author URI: http://undev.de
License: MIT
*/

use KKL\Ligatool\Plugin;

require_once(__DIR__.'/vendor/autoload.php');

$pluginFile = __FILE__;
$baseUrl = plugin_dir_url(__FILE__);
$basePath = plugin_dir_path(__FILE__);

$kkl = new Plugin($pluginFile, $baseUrl, $basePath);
$kkl->init();

$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
  'https://github.com/steam0r/kkl_ligatool/',
  __FILE__, //Full path to the main plugin file or functions.php.
  'kkl_ligatool'
);
$myUpdateChecker->setAuthentication('41b8f6711e2b4b094239be5c93ebc2198137afa7');
$myUpdateChecker->getVcsApi()->enableReleaseAssets();

