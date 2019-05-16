<?php

namespace KKL\Ligatool\Tasks;

use KKL\Ligatool\DB;

class NewGameDay {

  public static function execute() {

    error_log("RUNNING GAMEDAY CHECKER:");

    $db = new DB\Wordpress();

    $leagues = $db->getLeagues();
    foreach ($leagues as $league) {
      error_log("checking league: " . utf8_decode($league->getName()));
      $current_season = $db->getSeason($league->getCurrentSeason());
      if ($current_season && $current_season->isActive()) {
        error_log("- current season is " . utf8_decode($current_season->getName()));
        $current_day = $db->getGameDay($current_season->getCurrentGameDay());
        if ($current_day) {
          error_log("-- current day is " . $current_day->getNumber());
          $next_day = $db->getNextGameDay($current_day);
          $now = time();
          if ($next_day->getFixture()) {
            error_log("-- next day is " . $next_day->getNumber());
            $fixture = strtotime($next_day->getFixture());
            error_log("--- now: " . $now);
            error_log("--- fixture: " . $fixture);
            error_log("--- diff: " . ($now - $fixture));
            if ($fixture < $now) {
              error_log("--- setting next day");
              $db->setCurrentGameDay($current_season, $next_day);
            }
          }
        }
      }
    }
  }
}
