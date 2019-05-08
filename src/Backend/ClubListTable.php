<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\Model\Club;

class ClubListTable extends ListTable {

  function get_search_fields() {
    return array('name', 'short_name ');
  }
  
  function get_display_columns() {
    return $columns = array('ID' => __('id', 'kkl-ligatool'), 'name' => __('name', 'kkl-ligatool'), 'short_name' => __('url_code', 'kkl-ligatool'),);
  }
  
  function display() {
    parent::display();
    print $this->display_create_link();
  }

  public function getModel() {
    return new Club();
  }
}
