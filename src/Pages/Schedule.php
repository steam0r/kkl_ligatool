<?php

namespace KKL\Ligatool\Pages;

use KKL\Ligatool\DB;
use KKL\Ligatool\Plugin;
use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Utils\LinkUtils;

class Schedule {

  private $db;
  private $plugin;

  public function __construct() {
    $this->db = new DB\Wordpress();

    $pluginFile = __FILE__;
    $baseUrl = plugin_dir_url(__FILE__);
    $basePath = plugin_dir_path(__FILE__);

    $this->plugin = new Plugin($pluginFile, $baseUrl, $basePath);
  }


  /**
   * @param $pageContext
   * @return array
   */
  public function getSingleLeague($pageContext) {
    $schedules = array();
    $schedule = $this->db->getScheduleForGameDay($pageContext['game_day']);

    $clubService = ServiceBroker::getClubService();

    foreach ($schedule->matches as $match) {
      $home_club = $clubService->byId($match->home->club_id);
      $away_club = $clubService->byId($match->away->club_id);
      $match->home->link = LinkUtils::getLink('club', array('pathname' => $home_club->getShortName()));
      $match->away->link = LinkUtils::getLink('club', array('pathname' => $away_club->getShortName()));
    }

    $schedule->link = LinkUtils::getLink('schedule', array(
      'league' => $pageContext['league']->code,
      'season' => date('Y', strtotime($pageContext['season']->start_date))
    ));

    $schedules[] = $schedule;

    return $schedules;
  }


  /**
   * @param $pageContext
   * @return array
   */
  public function getSeason($pageContext) {
    $schedules = $this->db->getScheduleForSeason($pageContext['season']);

    $clubService = ServiceBroker::getClubService();

    foreach ($schedules as $schedule) {
      foreach ($schedule->matches as $match) {
        $home_club = $clubService->byId($match->home->club_id);
        $away_club = $clubService->byId($match->away->club_id);
        $match->home->link = LinkUtils::getLink('clubs', array('pathname' => $home_club->getShortName()));
        $match->away->link = LinkUtils::getLink('clubs', array('pathname' => $away_club->getShortName()));
      }
    }

    return $schedules;
  }


  /**
   * @return array
   */
  public function getCurrentGameday() {
    $schedules = array();

    $leagueService = ServiceBroker::getLeagueService();
    $clubService = ServiceBroker::getClubService();
    $seasonService = ServiceBroker::getSeasonService();
    $dayService = ServiceBroker::getGameDayService();

    foreach ($leagueService->getActive() as $league) {
      $season = $seasonService->byId($league->getCurrentSeason());
      $day = $dayService->byId($season->getCurrentGameDay());

      $schedule = $this->db->getScheduleForGameDay($day);

      foreach ($schedule->matches as $match) {
        $home_club = $clubService->byId($match->home->club_id);
        $away_club = $clubService->byId($match->away->club_id);
        $match->home->link = LinkUtils::getLink('club', array('pathname' => $home_club->getShortName()));
        $match->away->link = LinkUtils::getLink('club', array('pathname' => $away_club->getShortName()));
      }

      $schedule->link = LinkUtils::getLink('schedule', array(
        'league' => $league->getCode(),
        'season' => date('Y', strtotime($season->getStartDate()))
      ));

      $schedules[$league->getId()]['league'] = $league;
      $schedules[$league->getId()]['game_day'] = $day;
      $schedules[$league->getId()]['schedules'] = $schedule;

    }

    return $schedules;
  }
}