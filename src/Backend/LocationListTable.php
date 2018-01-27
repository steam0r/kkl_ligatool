<?php

namespace KKL\Ligatool\Backend;

class LocationListTable extends ListTable {

  function get_table_name() {
    return "locations";
  }

  function get_search_fields() {
    return array('title', 'description');
  }

  /**
   * Define the columns that are going to be used in the table
   * @return array $columns, the array of columns to use with the table
   */
  function get_display_columns() {
    return $columns = array(
      'id' => __('id', 'kkl-ligatool'),
      'title' => __('title', 'kkl-ligatool'),
      'description' => __('address', 'kkl-ligatool'),
      'lat' => __('latitude', 'kkl-ligatool'),
      'lng' => __('longitude', 'kkl-ligatool'),
    );
  }

  function display() {
    parent::display();
    print $this->display_create_link();
  }

}
