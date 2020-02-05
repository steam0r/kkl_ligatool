<?php


namespace KKL\Ligatool\Services;


use KKL\Ligatool\DB\Where;
use KKL\Ligatool\Model\GameDay;
use KKL\Ligatool\Model\Schedule;
use KKL\Ligatool\Model\Season;
use KKL\Ligatool\ServiceBroker;

class ScheduleService {

  /**
   * @param $day GameDay
   * @return Schedule
   */
  public function getScheduleForGameDay($day) {
    $matchService = ServiceBroker::getMatchService();
    $matches = $matchService->find(new Where('game_day_id', $day->getId()));
    $schedule = new Schedule();
    $schedule->setMatches($matches);
    $schedule->setNumber($day->getNumber());
    $schedule->setDay($day);
    $teamService = ServiceBroker::getTeamService();
    foreach ($schedule->getMatches() as $match) {
      $match->home = $teamService->byId($match->getHomeTeam());
      $match->away = $teamService->byId($match->getAwayTeam());
    }

    return $schedule;
  }

  /**
   * @param $season Season
   * @return Schedule[]
   */
  public function getScheduleForSeason($season) {

    $gameDayService = ServiceBroker::getGameDayService();
    $days = $gameDayService->allForSeason($season->getId());
    $schedules = array();
    foreach ($days as $day) {
      $schedules[] = $this->getScheduleForGameDay($day);
    }

    return $schedules;
  }

}