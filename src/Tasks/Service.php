<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 22.02.18
 * Time: 11:29
 */

namespace KKL\Ligatool\Tasks;


class Service {

  public static function init() {
    add_action('kkl_update_game_days', array(NewGameDay::class, 'execute'));
    if (!wp_next_scheduled('kkl_update_game_days')) {
      wp_schedule_event(time(), 'hourly', 'kkl_update_game_days');
    }
    add_action('kkl_remind_upcoming_game_day', array(GameDayReminder::class, 'execute'));
    if (!wp_next_scheduled('kkl_remind_upcoming_game_day')) {
      wp_schedule_event(strtotime('16:00:00'), 'daily', 'kkl_remind_upcoming_game_day');
    }
  }

}
