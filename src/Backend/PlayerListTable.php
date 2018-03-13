<?php

namespace KKL\Ligatool\Backend;

class PlayerListTable extends ListTable {
  
  function get_table_name() {
    return "players";
  }
  
  function get_search_fields() {
    return array('first_name', 'last_name', 'email');
  }
  
  /**
   * Define the columns that are going to be used in the table
   * @return array $columns, the array of columns to use with the table
   */
  function get_display_columns() {
    return $columns = array('id' => __('id', 'kkl-ligatool'), 'first_name' => __('firstname', 'kkl-ligatool'), 'last_name' => __('lastname', 'kkl-ligatool'), 'email' => __('email', 'kkl-ligatool'),);
  }
  
  function display() {
    parent::display();
    print $this->display_create_link();
  }
  
}
