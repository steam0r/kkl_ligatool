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

use KKL\Ligatool\Api;
use KKL\Ligatool\Backend;
use KKL\Ligatool\KKL;

require __DIR__ . '/vendor/autoload.php';

$kkl_twig = new Twig_Environment(new Twig_Loader_Filesystem(dirname(__FILE__) . '/templates/'), array('debug' => false));
$kkl_twig->addExtension(new Twig_Extension_Debug());

function scb_framework_init() {
  $options = array();
  new Backend\LeagueAdminPage(__FILE__, $options);
  new Backend\ClubAdminPage(__FILE__, $options);
  new Backend\GameDayAdminPage(__FILE__, $options);
  new Backend\MatchAdminPage(__FILE__, $options);
  new Backend\SeasonAdminPage(__FILE__, $options);
  new Backend\TeamAdminPage(__FILE__, $options);
  new Backend\LocationAdminPage(__FILE__, $options);
  new Backend\PlayerAdminPage(__FILE__, $options);
}

scb_init('scb_framework_init');

$kkl = new KKL();
$kkl->init();

Api::init();

add_action('kkl_update_game_days', array('KKL_Tasks_NewGameDay', 'execute'));
if (!wp_next_scheduled('kkl_update_game_days')) {
  wp_schedule_event(time(), 'hourly', 'kkl_update_game_days');
}
add_action('kkl_remind_upcoming_game_day', array('KKL_Tasks_GameDayReminder', 'execute'));
if (!wp_next_scheduled('kkl_remind_upcoming_game_day')) {
  wp_schedule_event(strtotime('16:00:00'), 'daily', 'kkl_remind_upcoming_game_day');
}

add_action('plugins_loaded', 'kkl_load_textdomain');
function kkl_load_textdomain() {
  load_plugin_textdomain('kkl-ligatool', false, dirname(plugin_basename(__FILE__)) . '/lang/');
}

add_filter('set-screen-option', array('KKLBackend', 'set_screen_options'), 10, 3);

if (is_admin()) {
  Backend::display();
}
