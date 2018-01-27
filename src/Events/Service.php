<?php
namespace KKL\Ligatool\Events;

class KKL_Events_Service {

  public static $MATCH_FIXTURE_SET = 'kkl_match_fixture_has_been_set';
  public static $NEW_GAMEDAY_UPCOMING = 'kkl_new_gameday_upcoming';

  public static function fireEvent($name, KKL_Event $event) {
    do_action($name, $event);
  }

  public static function registerCallback($eventName, $function) {
    add_action($eventName, $function, 10, 1);
  }

}
