<?php
namespace KKL\Ligatool;
use KKL\Ligatool\Backend;
use KKL\Ligatool\Slack;
use KKL\Ligatool\Mail;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;

require __DIR__ . '/vendor/autoload.php';

$kkl_twig = new Twig_Environment(new Twig_Loader_Filesystem(dirname(__FILE__) . '/templates/'), array('debug' => false));
$kkl_twig->addExtension(new Twig_Extension_Debug());

function scb_framework_init() {
  $options = array();
  new Backend\KKL_League_Admin_Page(__FILE__, $options);
  new Backend\KKL_Club_Admin_Page(__FILE__, $options);
  new Backend\KKL_GameDay_Admin_Page(__FILE__, $options);
  new Backend\KKL_Match_Admin_Page(__FILE__, $options);
  new Backend\KKL_Season_Admin_Page(__FILE__, $options);
  new Backend\KKL_Team_Admin_Page(__FILE__, $options);
  new Backend\KKL_Location_Admin_Page(__FILE__, $options);
  new Backend\KKL_Player_Admin_Page(__FILE__, $options);
}

scb_init('scb_framework_init');

$kkl = new KKL();
$kkl->init();

$slackIntegration = new Slack\KKL_Slack_EventListener();
$slackIntegration->init();

$mailIntegration = new Mail\KKL_Mail_EventListener();
$mailIntegration->init();

KKL_Api::init();

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
