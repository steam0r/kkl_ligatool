<?php

class KKL_Season_List_Table extends KKL_List_Table {

    function get_table_name() {
		return "seasons";
	}

    function get_search_fields() {
        return array('name');
    }

    function get_filter_sql() {
        if($this->get_current_league()) {
            return " league_id = '". $this->get_current_league()->id ."'";
        }
    }

	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_display_columns() {
		return $columns= array(
			'id'=> __('id', 'kkl-ligatool'),
            'league_id' => __('league', 'kkl-ligatool'),
            'name'=> __('name', 'kkl-ligatool'),
			'start_date'=> __('start', 'kkl-ligatool'),
			'end_date'=>  __('end', 'kkl-ligatool'),
			'active'=> __('is_active', 'kkl-ligatool')
		);
	}

    function column_league_id($item) {
        $leagues = $this->get_leagues();
        return $leagues[$item['league_id']]->name;
    }

    function column_start_date($item) {
        return $this->column_date($item['start_date']);
    }

    function column_end_date($item) {
        return $this->column_date($item['end_date']);
    }

    function column_active($item) {
        return $this->column_boolean($item['active']);
    }

    function display() {
        print $this->display_league_filter();
        parent::display();
        print $this->display_create_link();
    }

}
