<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\ServiceBroker;

class PlayerListTable extends ListTable {

  public function getModelService() {
    return ServiceBroker::getPlayerService();
  }

  function get_search_fields() {
    return array('first_name', 'last_name', 'email');
  }

  /**
   * Define the columns that are going to be used in the table
   * @return array $columns, the array of columns to use with the table
   */
  function get_display_columns() {
    return $columns = array(
      'ID' => __('id', 'kkl-ligatool'),
      'first_name' => __('firstname', 'kkl-ligatool'),
      'last_name' => __('lastname', 'kkl-ligatool'),
      'email' => __('email', 'kkl-ligatool')
    );
  }

  function display() {
    parent::display();
    print $this->display_create_link();
  }

}
