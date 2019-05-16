<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\DB\Where;
use KKL\Ligatool\Model\Season;
use KKL\Ligatool\ServiceBroker;

class SeasonListTable extends ListTable {

  public function getModelService() {
    return ServiceBroker::getSeasonService();
  }

  function get_search_fields() {
    return array('name');
  }

  function get_filter_sql() {
    if ($this->get_current_league()) {
      return new Where('league_id', $this->get_current_league()->getId(), '=');
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
      'league_id' => __('league', 'kkl-ligatool'),
      'name' => __('name', 'kkl-ligatool'),
      'start_date' => __('start', 'kkl-ligatool'),
      'end_date' => __('end', 'kkl-ligatool'),
      'active' => __('is_active', 'kkl-ligatool')
    );
  }

  /**
   * @param Season $item
   * @return mixed
   */
  function column_league_id($item) {
    $service = ServiceBroker::getLeagueService();
    return $service->byId($item->getLeagueId())->getName();
  }

  /**
   * @param Season $item
   * @return mixed
   */
  function column_start_date($item) {
    return $this->column_date($item->getStartDate());
  }

  /**
   * @param Season $item
   * @return mixed
   */
  function column_end_date($item) {
    return $this->column_date($item->getEndDate());
  }

  /**
   * @param Season $item
   * @return mixed
   */
  function column_active($item) {
    return $this->column_boolean($item->isActive());
  }

  function display() {
    print $this->display_league_filter();
    parent::display();
    print $this->display_create_link();
  }

}
