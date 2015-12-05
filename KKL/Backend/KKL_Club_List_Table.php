<?php

class KKL_Club_List_Table extends KKL_List_Table {

	function get_table_name() {
		return "clubs";
	}

    function get_search_fields() {
        return array('name', 'code ');
    }

	function get_display_columns() {
		return $columns= array(
			'id'=> __('id', 'kkl-ligatool'),
			'name'=> __('name', 'kkl-ligatool'),
			'short_name'=> __('url_code', 'kkl-ligatool'),
		);
	}

	function display() {
		parent::display();
		print $this->display_create_link();
	}

}
