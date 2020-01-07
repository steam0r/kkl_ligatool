<?php

namespace KKL\Ligatool;

use KKL\Ligatool\Model\GameDay;
use KKL\Ligatool\Model\Season;

require __DIR__ . '../vendor/autoload.php';
$db = new DB\Wordpress();
$seasonService = ServiceBroker::getSeasonService();
$leagueService = ServiceBroker::getLeagueService();
$gameDayService = ServiceBroker::getGameDayService();

// DATA
$name = "Saison 2018";
$start_date = '2018-01-29 00:00:00';
$end_date = '2018-12-17 00:00:00';

//$slugs = array('koeln1', 'koeln2a', 'koeln2b', 'koeln3a', 'koeln3b', 'koeln4a', 'koeln4b', 'koeln4c');
$slugs = array('koeln4d');

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

foreach ($slugs as $slug) {
  $league = $leagueService->bySlug($slug);
  $season = new Season();
  $season->setName($name . " - " . $league->getName());
  $season->setStartDate($start_date);
  $season->setEndDate($end_date);
  $season->setActive(true);
  $season->setLeagueId($league->id);
  $season = $seasonService->save($season);
  $i = 1;
  foreach ($days as $day) {
    $d = new GameDay();
    $d->setNumber($i);
    $d->setStart($day[0]);
    $d->setEnd($day[1]);
    $d->setSeasonId($season->id);
    $d = $gameDayService->save($d);
    if ($i == 1) {
      $season->setCurrentGameDay($d->getId());
      $season = $seasonService->save($season);
    }
    $i++;
  }
}
