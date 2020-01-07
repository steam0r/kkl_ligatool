<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\DB\Where;
use KKL\Ligatool\Model\Match;
use KKL\Ligatool\ServiceBroker;

class MatchListTable extends ListTable {

  public function getModelService() {
    return ServiceBroker::getMatchService();
  }

  function get_filter_sql() {
    if ($this->get_current_game_day()) {
      return new Where("game_day_id", $this->get_current_game_day()->getId(), '=');
    } elseif ($this->get_current_season()) {
      return "game_day_id = (SELECT current_game_day AS game_day_id FROM seasons WHERE id = '" . $this->get_current_season()->getId() . "')";
    } elseif ($this->get_current_league()) {
      return "game_day_id = (SELECT current_game_day AS game_day_id FROM seasons WHERE id = (SELECT current_season AS season_id FROM leagues WHERE id = '" . $this->get_current_league()->getId() . "'))";
    }
    return null;
  }

  /**
   * Define the columns that are going to be used in the table
   * @return array $columns, the array of columns to use with the table
   */
  function get_display_columns() {
    return $columns = array(
      'ID' => __('id', 'kkl-ligatool'),
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

  /**
   * @param Match $item
   * @return mixed
   */
  function column_game_day_id($item) {
    $service = ServiceBroker::getGameDayService();
    $day = $service->byId($item->getGameDayId());
    return $day->getNumber();
  }

  /**
   * @param Match $item
   * @return mixed
   */
  function column_fixture($item) {
    $fixture = $item->getFixture();
    if ($fixture == '0000-00-00 00:00:00') {
      $fixture = null;
    }
    return $this->column_date($fixture);
  }

  /**
   * @param Match $item
   * @return mixed
   */
  function column_home_team($item) {
    $teams = $this->get_teams();
    $id = $item->getHomeTeam();
    $team = $teams[$id];
    return $team->getName();
  }


  /**
   * @param Match $item
   * @return mixed
   */
  function column_away_team($item) {
    $teams = $this->get_teams();
    $id = $item->getAwayTeam();
    $team = $teams[$id];
    return $team->getName();
  }


  /**
   * @param Match $item
   * @return mixed
   */
  function column_location($item) {
    $locationService = ServiceBroker::getLocationService();
    $location = $locationService->byId($item->getLocation());
    return $location ? $location->getTitle() : "";
  }

  /**
   * @param Match $item
   * @return mixed
   */
  function column_goals_home($item) {
    $matchService = ServiceBroker::getMatchService();
    $match = $matchService->byId($item->getId());
    return $match->goals_home;
  }

  /**
   * @param Match $item
   * @return mixed
   */
  function column_goals_away($item) {
    $matchService = ServiceBroker::getMatchService();
    $match = $matchService->byId($item->getId());
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
    $page = $this->get_create_page();
    if ($game_day) {
      $link = add_query_arg(array(compact('page', 'id'), 'gameDayId' => $game_day->getId()), admin_url('admin.php'));
    }
    return '<a href="' . $link . '">' . __('create_new', 'kkl-ligatool') . '</a>';
  }

}
