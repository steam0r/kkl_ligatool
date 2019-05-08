<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\Model\League;

class LeagueListTable extends ListTable {

  public function getModel() {
    return new League();
  }

  function get_search_fields() {
    return array('name', 'code ');
  }
  
  /**
   * Define the columns that are going to be used in the table
   * @return array $columns, the array of columns to use with the table
   */
  function get_display_columns() {
    return array('ID' => __('id', 'kkl-ligatool'), 'name' => __('name', 'kkl-ligatool'), 'code' => __('url_code', 'kkl-ligatool'), 'active' => __('is_active', 'kkl-ligatool'));
  }
  
  function column_active($item) {
    return $this->column_boolean($item['active']);
  }
  
  function display() {
    parent::display();
    print $this->display_create_link();
  }
  
}
