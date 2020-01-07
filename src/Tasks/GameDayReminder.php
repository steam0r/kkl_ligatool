<?php

namespace KKL\Ligatool\Tasks;

use KKL\Ligatool\DB;
use KKL\Ligatool\Events;

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
    $db = new DB\Wordpress();
    return $db->getReminderMatches($days_off);
  }

  private static function getTopMatches($matches) {
    $db = new DB\Wordpress();
    return $db->getTopMatches($matches);
  }

}
