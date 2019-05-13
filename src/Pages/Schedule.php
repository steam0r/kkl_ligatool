<?php

namespace KKL\Ligatool\Pages;

use KKL\Ligatool\Plugin;
use KKL\Ligatool\DB;

class Schedule {

  private $db;

  public function __construct() {
    $this->db = new DB\Wordpress();
  }


  /**
   * @param $pageContext
   * @return array
   */
  public function getSingleLeague($pageContext){
    $schedules = array();
    $schedule = $this->db->getScheduleForGameDay($pageContext['game_day']);
    foreach ($schedule->matches as $match) {
      $home_club = $this->db->getClub($match->home->club_id);
      $away_club = $this->db->getClub($match->away->club_id);
      $match->home->link = get_site_url().'/team/'.Plugin::getLink('club', array('club' => $home_club->short_name));
      $match->away->link = get_site_url().'/team/'.Plugin::getLink('club', array('club' => $away_club->short_name));
    }
    $schedule->link = get_site_url().'/spielplan/'.Plugin::getLink('schedule', array('league' => $pageContext['league']->code, 'season' => date('Y', strtotime($pageContext['season']->start_date))));

    $schedules[] = $schedule;

    return $schedules;
  }

}