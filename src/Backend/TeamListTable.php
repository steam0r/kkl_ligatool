<?php

namespace KKL\Ligatool\Backend;

class TeamListTable extends ListTable {
  
  function get_table_name() {
    return "teams";
  }
  
  function get_search_fields() {
    return array('name', 'short_name');
  }
  
  /**
   * Define the columns that are going to be used in the table
   * @return array $columns, the array of columns to use with the table
   */
  function get_display_columns() {
    return $columns = array('id' => __('id', 'kkl-ligatool'), 'name' => __('name', 'kkl-ligatool'), 'club_id' => __('club', 'kkl-ligatool'), 'season_id' => __('season', 'kkl-ligatool'), 'short_name' => __('url_code', 'kkl-ligatool'),);
  }
  
  function get_filter_sql() {
    if($this->get_current_season()) {
      return "season_id = '" . $this->get_current_season()->id . "'";
    }
    return " season_id IS NOT NULL";
  }
  
  function column_club_id($item) {
    $clubs = $this->get_clubs();
    $id = $item['club_id'];
    $club = $clubs[$id];
    return $club->name;
  }
  
  function column_name($item) {
    $teams = $this->get_teams();
    $id = $item['id'];
    $team = $teams[$id];
    return $team->name;
  }
  
  function column_season_id($item) {
    $seasons = $this->get_seasons();
    $id = $item['season_id'];
    $season = $seasons[$id];
    return $season->name;
  }
  
  function display() {
    print $this->display_league_filter();
    print $this->display_season_filter();
    parent::display();
    print $this->display_create_link();
  }
  
}
