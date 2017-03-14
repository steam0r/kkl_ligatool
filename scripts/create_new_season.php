<?php

require_once('../KKL/DB.php');

$options = get_option('kkl_ligatool');
$kdb = new wpdb($options['db_user'], $options['db_pass'], $options['db_name'], $options['db_host']);

$db = new KKL_DB($kdb);

// DATA
$name = "Saison 2017";
$start_date = '2017-02-06 00:00:00';
$end_date = '2017-12-18 00:00:00';

# $slugs = array('koeln1', 'koeln2a', 'koeln2b', 'koeln3a', 'koeln3b', 'koeln4a', 'koeln4b', 'koeln4c');
$slugs = array('koeln4c');
$teams[] = array(
	'name' => 'TFC Hammondbar 05', 
	'short' => 'tfc05',
);
$teams[] = array(
	'name' => 'Ehrenfeld Asskickers', 
	'short' => 'asskick',
);
$teams[] = array(
	'name' => 'Heute Steil!?', 
	'short' => 'steil',
);
$teams[] = array(
	'name' => 'I ́m Chaos & Disaster', 
	'short' => 'chaos',
);
$teams[] = array(
	'name' => 'Team F.I.K.C', 
	'short' => 'fikc',
);
$teams[] = array(
	'name' => 'Münze des Schrubbsals', 
	'short' => 'schrubb',
);
$teams[] = array(
	'name' => 'Dynamo Tresen', 
	'short' => 'tresen',
);
/*
$teams[] = array(
	'name' => 'Roter Stern Mülheim III', 
	'short' => 'rsm3',
);
 */
$days = array();
$days[] = array('2017-02-06 00:00:00', '2017-02-19 23:59:00');
$days[] = array('2017-02-27 00:00:00', '2017-03-12 23:59:00');
$days[] = array('2017-03-20 00:00:00', '2017-04-02 23:59:00');
$days[] = array('2017-04-17 00:00:00', '2017-04-30 23:59:00');
$days[] = array('2017-05-08 00:00:00', '2017-05-21 23:59:00');
$days[] = array('2017-05-29 00:00:00', '2017-06-11 23:59:00');
$days[] = array('2017-06-19 00:00:00', '2017-07-02 23:59:00');
$days[] = array('2017-07-10 00:00:00', '2017-07-23 23:59:00');
$days[] = array('2017-08-21 00:00:00', '2017-09-03 23:59:00');
$days[] = array('2017-09-11 00:00:00', '2017-09-24 23:59:00');
$days[] = array('2017-10-02 00:00:00', '2017-10-15 23:59:00');
$days[] = array('2017-10-23 00:00:00', '2017-11-05 23:59:00');
$days[] = array('2017-11-13 00:00:00', '2017-11-26 23:59:00');
$days[] = array('2017-12-04 00:00:00', '2017-12-17 23:59:00');

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
	foreach($teams as $team) {
		$t = new stdClass();
		$t->name  = $team['name'];
		$t->short_name = $team['short'];
		$t->season_id = $season->id;
		$team = $db->createTeam($t);
	}
}
