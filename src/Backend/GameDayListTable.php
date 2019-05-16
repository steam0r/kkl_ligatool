<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\DB\Where;
use KKL\Ligatool\Model\GameDay;
use KKL\Ligatool\ServiceBroker;

class GameDayListTable extends ListTable {

  public function getModelService() {
    return ServiceBroker::getGameDayService();
  }

  function get_search_fields() {
    return array('number');
  }

  function get_filter_sql() {
    if ($this->get_current_season()) {
      return new Where('season_id', $this->get_current_season()->getId(), '=');
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
      'season_id' => __('season', 'kkl-ligatool'),
      'number' => __('number', 'kkl-ligatool'),
      'fixture' => __('start', 'kkl-ligatool'),
      'end' => __('end', 'kkl-ligatool')
    );
  }

  /**
   * @param GameDay $item
   * @return string
   */
  function column_season_id($item) {
    $seasonService = ServiceBroker::getSeasonService();
    $season = $seasonService->byId($item->getSeasonId());
    $leagueService = ServiceBroker::getLeagueService();
    $league = $leagueService->byId($season->getLeagueId());
    return $league->getName() . " - " . $season->getName();

  }

  /**
   * @param GameDay $item
   * @return string
   */
  function column_fixture($item) {
    $fixture = $item->getFixture();
    if ($fixture == '0000-00-00 00:00:00') {
      $fixture = null;
    }
    return $this->column_date($fixture);
  }

  /**
   * @param GameDay $item
   * @return string
   */
  function column_end($item) {
    return $this->column_date($item->getEnd());
  }

  function display() {
    print $this->display_league_filter();
    print $this->display_season_filter();
    parent::display();
    print $this->display_create_link();
  }

}
