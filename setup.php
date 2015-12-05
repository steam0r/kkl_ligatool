<?php

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

require dirname( __FILE__ ) . '/scb/load.php';

require_once('Twig/Autoloader.php');
Twig_Autoloader::register();
$kkl_twig = new Twig_Environment(new Twig_Loader_Filesystem(dirname(__FILE__) . '/templates/'));

require_once('KKL/DB.php');
require_once('KKL/KKL.php');
require_once('KKL/Shortcodes.php');

require_once('KKL/Widget/UpcomingGames.php');
require_once('KKL/Widget/OtherSeasons.php');
require_once('KKL/Widget/OtherLeagues.php');

require_once('KKL/Backend.php');

function scb_framework_init() {

	require_once('KKL/Backend/KKL_List_Table.php');
	require_once('KKL/Backend/KKL_League_List_Table.php');
	require_once('KKL/Backend/KKL_Season_List_Table.php');
	require_once('KKL/Backend/KKL_GameDay_List_Table.php');
	require_once('KKL/Backend/KKL_Match_List_Table.php');
	require_once('KKL/Backend/KKL_Club_List_Table.php');
	require_once('KKL/Backend/KKL_Team_List_Table.php');
	require_once('KKL/Backend/KKL_Location_List_Table.php');

	require_once( dirname( __FILE__ ) . '/KKL/Backend/KKL_Admin_Page.php');
	require_once( dirname( __FILE__ ) . '/KKL/Backend/KKL_League_Admin_Page.php');
	require_once( dirname( __FILE__ ) . '/KKL/Backend/KKL_Club_Admin_Page.php');
	require_once( dirname( __FILE__ ) . '/KKL/Backend/KKL_GameDay_Admin_Page.php');
	require_once( dirname( __FILE__ ) . '/KKL/Backend/KKL_Match_Admin_Page.php');
	require_once( dirname( __FILE__ ) . '/KKL/Backend/KKL_Season_Admin_Page.php');
	require_once( dirname( __FILE__ ) . '/KKL/Backend/KKL_Team_Admin_Page.php');
	require_once( dirname( __FILE__ ) . '/KKL/Backend/KKL_Location_Admin_Page.php');
	require_once( dirname( __FILE__ ) . '/KKL/Tasks/NewGameDay.php');

	$options = array();
    new KKL_League_Admin_Page( __FILE__, $options );
    new KKL_Club_Admin_Page( __FILE__, $options );
    new KKL_GameDay_Admin_Page( __FILE__, $options );
    new KKL_Match_Admin_Page( __FILE__, $options );
    new KKL_Season_Admin_Page( __FILE__, $options );
    new KKL_Team_Admin_Page( __FILE__, $options );
    new KKL_Location_Admin_Page( __FILE__, $options );
}

scb_init( 'scb_framework_init' );

$kkl = new KKL();
$kkl->init();

add_action( 'kkl_update_game_days',  array( 'KKL_Tasks_NewGameDay' ,'execute'));
if (!wp_next_scheduled( 'kkl_update_game_days' ) ) {
	wp_schedule_event( time(), 'hourly', 'kkl_update_game_days' );
}

add_action('plugins_loaded', 'kkl_load_textdomain');
function kkl_load_textdomain() {
	load_plugin_textdomain( 'kkl-ligatool', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

add_filter('set-screen-option', array('KKLBackend', 'set_screen_options'), 10, 3);
