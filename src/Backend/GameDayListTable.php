<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\Model\GameDay;

class GameDayListTable extends ListTable {

  public function getModel() {
    return new GameDay();
  }

  function get_search_fields() {
    return array('number');
  }
  
  function get_filter_sql() {
    if($this->get_current_season()) {
      return "season_id = '" . $this->get_current_season()->ID . "'";
    }
    return " season_id IS NOT NULL";
  }
  
  /**
   * Define the columns that are going to be used in the table
   * @return array $columns, the array of columns to use with the table
   */
  function get_display_columns() {
    return $columns = array('ID' => __('id', 'kkl-ligatool'), 'season_id' => __('season', 'kkl-ligatool'), 'number' => __('number', 'kkl-ligatool'), 'fixture' => __('start', 'kkl-ligatool'), 'end' => __('end', 'kkl-ligatool'));
  }
  
  function column_season_id($item) {
    $seasons = $this->get_seasons();
    $leagues = $this->get_leagues();
    $season = $seasons[$item['season_id']];
    $league = $leagues[$season->league_id];
    return $league->name . " - " . $season->name;
    
  }
  
  function column_fixture($item) {
    $fixture = $item['fixture'];
    if($fixture == '0000-00-00 00:00:00') {
      $fixture = null;
    }
    return $this->column_date($fixture);
  }
  
  function column_end($item) {
    return $this->column_date($item['end']);
  }
  
  function display() {
    print $this->display_league_filter();
    print $this->display_season_filter();
    parent::display();
    print $this->display_create_link();
  }
  
}
