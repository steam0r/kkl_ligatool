<?php

if (!class_exists('WP_List_Table')) {
  require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

require dirname(__FILE__) . '/scb/load.php';

require_once('Twig/Autoloader.php');
Twig_Autoloader::register();
$kkl_twig = new Twig_Environment(new Twig_Loader_Filesystem(dirname(__FILE__) . '/templates/'), array('debug' => false));
$kkl_twig->addExtension(new Twig_Extension_Debug());

require_once('KKL/Events/Event.php');
require_once('KKL/Events/MatchFixtureUpdatedEvent.php');
require_once('KKL/Events/GameDayReminderEvent.php');
require_once('KKL/Events/Service.php');

require_once('KKL/Mail/EventListener.php');

require_once('KKL/Slack/Slack.php');
require_once('KKL/Slack/EventListener.php');

require_once('KKL/DB.php');
require_once('KKL/DB/Wordpress.php');
require_once('KKL/DB/Api.php');
require_once('KKL/KKL.php');
require_once('KKL/Shortcodes.php');

require_once('KKL/Widget/UpcomingGames.php');
require_once('KKL/Widget/OtherSeasons.php');
require_once('KKL/Widget/OtherLeagues.php');

require_once('KKL/Backend.php');
require_once('KKL/Api.php');

function scb_framework_init() {

  require_once('KKL/Backend/KKL_List_Table.php');
  require_once('KKL/Backend/KKL_League_List_Table.php');
  require_once('KKL/Backend/KKL_Season_List_Table.php');
  require_once('KKL/Backend/KKL_GameDay_List_Table.php');
  require_once('KKL/Backend/KKL_Match_List_Table.php');
  require_once('KKL/Backend/KKL_Club_List_Table.php');
  require_once('KKL/Backend/KKL_Team_List_Table.php');
  require_once('KKL/Backend/KKL_Location_List_Table.php');
  require_once('KKL/Backend/KKL_Player_List_Table.php');

  require_once(dirname(__FILE__) . '/KKL/Backend/KKL_Admin_Page.php');
  require_once(dirname(__FILE__) . '/KKL/Backend/KKL_League_Admin_Page.php');
  require_once(dirname(__FILE__) . '/KKL/Backend/KKL_Club_Admin_Page.php');
  require_once(dirname(__FILE__) . '/KKL/Backend/KKL_GameDay_Admin_Page.php');
  require_once(dirname(__FILE__) . '/KKL/Backend/KKL_Match_Admin_Page.php');
  require_once(dirname(__FILE__) . '/KKL/Backend/KKL_Season_Admin_Page.php');
  require_once(dirname(__FILE__) . '/KKL/Backend/KKL_Team_Admin_Page.php');
  require_once(dirname(__FILE__) . '/KKL/Backend/KKL_Location_Admin_Page.php');
  require_once(dirname(__FILE__) . '/KKL/Backend/KKL_Player_Admin_Page.php');
  require_once(dirname(__FILE__) . '/KKL/Tasks/NewGameDay.php');
  require_once(dirname(__FILE__) . '/KKL/Tasks/GameDayReminder.php');

  $options = array();
  new KKL_League_Admin_Page(__FILE__, $options);
  new KKL_Club_Admin_Page(__FILE__, $options);
  new KKL_GameDay_Admin_Page(__FILE__, $options);
  new KKL_Match_Admin_Page(__FILE__, $options);
  new KKL_Season_Admin_Page(__FILE__, $options);
  new KKL_Team_Admin_Page(__FILE__, $options);
  new KKL_Location_Admin_Page(__FILE__, $options);
  new KKL_Player_Admin_Page(__FILE__, $options);
}

scb_init('scb_framework_init');

$kkl = new KKL();
$kkl->init();

$slackIntegration = new KKL_Slack_EventListener();
$slackIntegration->init();

$mailIntegration = new KKL_Mail_EventListener();
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
