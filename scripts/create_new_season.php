<?php

require_once('../KKL/DB.php');
require_once('../KKL/DB/Wordpress.php');
require_once('../KKL/DB/Api.php');

$options = get_option('kkl_ligatool');
$kdb = new wpdb($options['db_user'], $options['db_pass'], $options['db_name'], $options['db_host']);

$db = new KKL_DB_Wordpress($kdb);

// DATA
$name = "Saison 2018";
$start_date = '2018-01-29 00:00:00';
$end_date = '2018-12-17 00:00:00';

$slugs = array('koeln1', 'koeln2a', 'koeln2b', 'koeln3a', 'koeln3b', 'koeln4a', 'koeln4b', 'koeln4c');

$days = array();
$days[] = array('2018-01-29 00:00:00', '2018-02-11 23:59:00');
$days[] = array('2018-02-19 00:00:00', '2018-03-04 23:59:00');
$days[] = array('2018-03-12 00:00:00', '2018-03-25 23:59:00');
$days[] = array('2018-04-03 00:00:00', '2018-04-15 23:59:00');
$days[] = array('2018-04-23 00:00:00', '2018-05-06 23:59:00');
$days[] = array('2018-05-14 00:00:00', '2018-05-27 23:59:00');
$days[] = array('2018-06-04 00:00:00', '2018-06-17 23:59:00');
$days[] = array('2018-07-16 00:00:00', '2018-07-29 23:59:00');
$days[] = array('2018-08-20 00:00:00', '2018-09-02 23:59:00');
$days[] = array('2018-09-10 00:00:00', '2018-09-23 23:59:00');
$days[] = array('2018-10-01 00:00:00', '2018-10-14 23:59:00');
$days[] = array('2018-10-22 00:00:00', '2018-11-04 23:59:00');
$days[] = array('2018-11-12 00:00:00', '2018-11-15 23:59:00');
$days[] = array('2018-12-03 00:00:00', '2018-12-16 23:59:00');

foreach($slugs as $slug) {
	$league = $db->getLeagueBySlug($slug);
	$season = new stdClass();
	$season->name = $name . " - " . $league->name;
	$season->start_date = $start_date;
	$season->end_date = $end_date;
	$season->active = true;
	$season->league_id = $league->id;
	$season = $db->createSeason($season);
	$i = 1;
	foreach($days as $day) {
		$d = new stdClass();
		$d->number = $i;
		$d->start_date = $day[0];
		$d->end_date = $day[1];
		$d->season_id = $season->id;
		$d = $db->createGameDay($d);
		if($i == 1) {
			$season->current_game_day = $d->id;
			$season = $db->updateSeason($season);
		}
		$i++;
	}
}
