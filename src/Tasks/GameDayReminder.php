<?php

namespace KKL\Ligatool\Tasks;

use KKL\Ligatool\Events;
use KKL\Ligatool\ServiceBroker;

class GameDayReminder {

  public static function execute() {
    $offset = 6;
    $matches = static::getMatches($offset);
    if (!empty($matches)) {
      $topMatches = static::getTopMatches($matches);
      Events\Service::fireEvent(Events\Service::$NEW_GAMEDAY_UPCOMING, new Events\GameDayReminderEvent($matches, $topMatches, $offset));
    }
  }

  private static function getMatches($days_off) {
    $matchService = ServiceBroker::getMatchService();
    try {
      return $matchService->reminderMatches($days_off);
    } catch (\Exception $e) {
      return [];
    }
  }

  private static function getTopMatches($matches) {
    $matchService = ServiceBroker::getMatchService();
    return $matchService->topMatches($matches);
  }

}
