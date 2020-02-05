<?php

namespace KKL\Ligatool\Tasks;

use KKL\Ligatool\ServiceBroker;

class NewGameDay {

  public static function execute() {

    error_log("RUNNING GAMEDAY CHECKER:");

    $gameDayService = ServiceBroker::getGameDayService();
    $seasonService = ServiceBroker::getSeasonService();
    $leagueService = ServiceBroker::getLeagueService();

    $leagues = $leagueService->getAll();
    foreach ($leagues as $league) {
      error_log("checking league: " . utf8_decode($league->getName()));
      $current_season = $seasonService->byId($league->getCurrentSeason());
      if ($current_season && $current_season->isActive()) {
        error_log("- current season is " . utf8_decode($current_season->getName()));
        $current_day = $gameDayService->byId($current_season->getCurrentGameDay());
        if ($current_day) {
          error_log("-- current day is " . $current_day->getNumber());

          $gameDayService = ServiceBroker::getGameDayService();
          $next_day = $gameDayService->getNext($current_day);
          $now = time();
          if ($next_day->getFixture()) {
            error_log("-- next day is " . $next_day->getNumber());
            $fixture = strtotime($next_day->getFixture());
            error_log("--- now: " . $now);
            error_log("--- fixture: " . $fixture);
            error_log("--- diff: " . ($now - $fixture));
            if ($fixture < $now) {
              error_log("--- setting next day");
              $current_season->setCurrentGameDay($next_day->getId());
              $seasonService->createOrUpdate($current_season);
            }
          }
        }
      }
    }
  }
}
