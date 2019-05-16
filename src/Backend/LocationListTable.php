<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\ServiceBroker;

class LocationListTable extends ListTable {

  public function getModelService() {
    return ServiceBroker::getLocationService();
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
      'ID' => __('id', 'kkl-ligatool'),
      'title' => __('title', 'kkl-ligatool'),
      'description' => __('address', 'kkl-ligatool'),
      'lat' => __('latitude', 'kkl-ligatool'),
      'lng' => __('longitude', 'kkl-ligatool')
    );
  }

  function display() {
    parent::display();
    print $this->display_create_link();
  }

}
