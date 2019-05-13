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

}