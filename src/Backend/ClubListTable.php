<?php

namespace KKL\Ligatool\Backend;

class ClubListTable extends ListTable {
  
  function get_table_name() {
    return "clubs";
  }
  
  function get_search_fields() {
    return array('name', 'short_name ');
  }
  
  function get_display_columns() {
    return $columns = array('id' => __('id', 'kkl-ligatool'), 'name' => __('name', 'kkl-ligatool'), 'short_name' => __('url_code', 'kkl-ligatool'),);
  }
  
  function display() {
    parent::display();
    print $this->display_create_link();
  }
  
}
