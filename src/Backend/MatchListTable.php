<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\DB;

class MatchListTable extends ListTable {

  function get_table_name() {
    return "matches";
  }

  function get_filter_sql() {
    if ($this->get_current_game_day()) {
      return "game_day_id = '" . $this->get_current_game_day()->id . "'";
    } elseif ($this->get_current_season()) {
      return "game_day_id = (SELECT current_game_day AS game_day_id FROM seasons WHERE id = '" . $this->get_current_season()->id . "')";
    } elseif ($this->get_current_league()) {
      return "game_day_id = (SELECT current_game_day AS game_day_id FROM seasons WHERE id = (SELECT current_season AS season_id FROM leagues WHERE id = '" . $this->get_current_league()->id . "'))";
    }
    return "";
  }

  /**
   * Define the columns that are going to be used in the table
   * @return array $columns, the array of columns to use with the table
   */
  function get_display_columns() {
    return $columns = array(
      'id' => __('ID', 'kkl-ligatool'),
      'game_day_id' => __('game_day', 'kkl-ligatool'),
      'home_team' => __('home_team', 'kkl-ligatool'),
      'away_team' => __('away_team', 'kkl-ligatool'),
      'fixture' => __('fixture', 'kkl-ligatool'),
      'location' => __('location', 'kkl-ligatool'),
      'score_home' => __('score_home', 'kkl-ligatool'),
      'score_away' => __('score_away', 'kkl-ligatool'),
      'goals_home' => __('goals_home', 'kkl-ligatool'),
      'goals_away' => __('goals_away', 'kkl-ligatool')
    );
  }

  function column_game_day_id($item) {
    $days = $this->get_game_days();
    $day = $days[$item['game_day_id']];
    return $day->number;
  }

  function column_fixture($item) {
    $fixture = $item['fixture'];
    if ($fixture == '0000-00-00 00:00:00') {
      $fixture = null;
    }
    return $this->column_date($fixture);
  }

  function column_home_team($item) {
    $teams = $this->get_teams();
    $id = $item['home_team'];
    $team = $teams[$id];
    return $team->name;
  }

  function column_away_team($item) {
    $teams = $this->get_teams();
    $id = $item['away_team'];
    $team = $teams[$id];
    return $team->name;
  }

  function column_location($item) {
    $db = new DB\Wordpress();
    $location = $db->getLocation($item['location']);
    return $location->title;
  }

  function column_goals_home($item) {
    $db = new DB\Wordpress();
    $match = $db->getMatch($item['id']);
    return $match->goals_home;
  }

  function column_goals_away($item) {
    $db = new DB\Wordpress();
    $match = $db->getMatch($item['id']);
    return $match->goals_away;
  }

  function display() {
    print $this->display_league_filter();
    print $this->display_season_filter();
    print $this->display_game_day_filter();
    parent::display();
    print $this->display_create_link();
  }

  function display_create_link() {
    $game_day = $this->get_current_game_day();
    if ($game_day) {
      $page = $this->get_create_page();
      $link = add_query_arg(array(compact('page', 'id'), 'gameDayId' => $game_day->id), admin_url('admin.php'));
      $html = '<a href="' . $link . '">' . __('create_new', 'kkl-ligatool') . '</a>';
      return $html;
    }
    return "";
  }

}
