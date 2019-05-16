<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\DB\Where;
use KKL\Ligatool\Model\Team;
use KKL\Ligatool\ServiceBroker;

class TeamListTable extends ListTable {

  public function getModelService() {
    return ServiceBroker::getTeamService();
  }

  function get_search_fields() {
    return array('name', 'short_name');
  }

  /**
   * Define the columns that are going to be used in the table
   * @return array $columns, the array of columns to use with the table
   */
  function get_display_columns() {
    return $columns = array(
      'ID' => __('id', 'kkl-ligatool'),
      'name' => __('name', 'kkl-ligatool'),
      'club_id' => __('club', 'kkl-ligatool'),
      'season_id' => __('season', 'kkl-ligatool'),
      'short_name' => __('url_code', 'kkl-ligatool')
    );
  }

  function get_filter_sql() {
    if ($this->get_current_season()) {
      return new Where('season_id', $this->get_current_season()->getId(), '=');
    }
    return null;
  }

  /**
   * @param Team $team
   * @return mixed
   */
  function column_club_id($team) {
    $service = ServiceBroker::getClubService();
    $club = $service->byId($team->getClubId());
    return $club->getName();
  }


  /**
   * @param Team $item
   * @return mixed
   */
  function column_name($item) {
    $service = ServiceBroker::getTeamService();
    $team = $service->byId($item->getId());
    return $team->getName();
  }

  /**
   * @param Team $item
   * @return mixed
   */
  function column_season_id($item) {
    $service = ServiceBroker::getSeasonService();
    $season = $service->byId($item->getSeasonId());
    return $season->getName();
  }

  function display() {
    print $this->display_league_filter();
    print $this->display_season_filter();
    parent::display();
    print $this->display_create_link();
  }

}
