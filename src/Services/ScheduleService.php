<?php


namespace KKL\Ligatool\Services;


use KKL\Ligatool\DB\Wordpress;
use KKL\Ligatool\ServiceBroker;

class ScheduleService {

  /**
   * FIXME use orm
   * @param $day
   * @return stdClass
   */
  public function getScheduleForGameDay($day) {
    $sql = "SELECT * FROM " . static::$prefix . "matches WHERE game_day_id = '" . esc_sql($day->ID) . "'";
    $schedule = new stdClass;
    $schedule->matches = $this->getDb()->get_results($sql);
    $schedule->number = $day->number;
    $schedule->day = $day;
    $teamService = ServiceBroker::getTeamService();
    foreach ($schedule->matches as $match) {
      $match->home = $teamService->byId($match->home_team);
      $match->away = $teamService->byId($match->away_team);
    }

    return $schedule;
  }

  /**
   * FIXME use orm
   * @param $season
   * @return array
   */
  public function getScheduleForSeason($season) {

    $gameDayService = ServiceBroker::getGameDayService();
    $days = $gameDayService->allForSeason($season->ID);
    $schedules = array();
    foreach ($days as $day) {
      $schedules[] = $this->getScheduleForGameDay($day);
    }

    return $schedules;
  }


  /**
   * @return Wordpress
   * @deprecated use orm layer
   */
  private function getDb() {
    return new Wordpress();
  }
}