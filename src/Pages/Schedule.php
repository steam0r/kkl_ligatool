<?php

namespace KKL\Ligatool\Pages;

use KKL\Ligatool\Plugin;
use KKL\Ligatool\DB;
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

    foreach($schedule->matches as $match) {
      $home_club = $this->db->getClub($match->home->club_id);
      $away_club = $this->db->getClub($match->away->club_id);
      $match->home->link = LinkUtils::getLink('club', array('pathname' => $home_club->short_name));
      $match->away->link = LinkUtils::getLink('club', array('pathname' => $away_club->short_name));
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

    foreach($schedules as $schedule) {
      foreach($schedule->matches as $match) {
        $home_club = $this->db->getClub($match->home->club_id);
        $away_club = $this->db->getClub($match->away->club_id);
        $match->home->link = LinkUtils::getLink('clubs', array('pathname' => $home_club->short_name));
        $match->away->link = LinkUtils::getLink('clubs', array('pathname' => $away_club->short_name));
      }
    }

    return $schedules;
  }


  /**
   * @return array
   */
  public function getCurrentGameday() {
    $schedules = array();

    foreach($this->db->getActiveLeagues() as $league) {
      $season = $this->db->getSeason($league->current_season);
      $day = $this->db->getGameDay($season->current_game_day);

      $schedule = $this->db->getScheduleForGameDay($day);

      foreach($schedule->matches as $match) {
        $home_club = $this->db->getClub($match->home->club_id);
        $away_club = $this->db->getClub($match->away->club_id);
        $match->home->link = LinkUtils::getLink('club', array('pathname' => $home_club->short_name));
        $match->away->link = LinkUtils::getLink('club', array('pathname' => $away_club->short_name));
      }

      $schedule->link = LinkUtils::getLink('schedule', array(
          'league' => $league->code,
          'season' => date('Y', strtotime($season->start_date))
      ));

      $schedules[$league->id]['league'] = $league;
      $schedules[$league->id]['game_day'] = $day;
      $schedules[$league->id]['schedules'] = $schedule;

    }

    return $schedules;
  }
}