<?php


abstract class KKL_List_Table extends WP_List_Table {

    public $_args = array();
    public static $ITEMS_PER_PAGE = 10;

    private $db;
    private $total_items = null;

    private $leagues;
    private $seasons;
    private $gamedays;
    private $teams;
    private $clubs;

    private $currentLeague;
    private $currentSeason;
    private $currentGameDay;

    abstract public function get_table_name();

    function __construct() {
	parent::__construct();

        global $kkl_db;
        $this->db = $kkl_db;

        echo '<style type="text/css">';
        echo '.wp-list-table .column-id { width: 5%; }';
        echo '</style>';

    }

    public function get_create_page() {
        $edit_page = "kkl_" . $this->get_table_name() . "_admin_page";
        return $edit_page;
    }

    public function get_edit_page() {
        $edit_page = "kkl_" . $this->get_table_name() . "_admin_page";
        return $edit_page;
    }

    public function get_search_fields() {
        return array();
    }

    public function get_filter_sql() {
        return "";
    }

    protected function get_count_query() {
        $query = "SELECT count(*) as count FROM " . $this->get_table_name();
        if($this->get_filter_sql() || $_GET['s']) $query .= " WHERE ";
        if($this->get_filter_sql()) $query .= $this->get_filter_sql();

        if($_GET['s']) {
            if($this->get_filter_sql()) $query .= " AND (";
            $i = 0;
            foreach($this->get_search_fields() as $field) {
                if($i > 0) $query .= " OR ";
                $query .= $field . " LIKE '%" . esc_sql($_GET['s']) . "%'";
                $i++;
            }
            if($this->get_filter_sql()) $query .= ")";
        }
        return $query;
    }

    protected function get_query() {
        $query = "SELECT * FROM " . $this->get_table_name();
        if($this->get_filter_sql() || $_GET['s']) $query .= " WHERE ";
        if($this->get_filter_sql()) $query .= $this->get_filter_sql();

        if($_GET['s']) {
            if($this->get_filter_sql()) $query .= " AND (";
            $i = 0;
            foreach($this->get_search_fields() as $field) {
                if($i > 0) $query .= " OR ";
                $query .= $field . " LIKE '%" . esc_sql($_GET['s']) . "%' ";
                $i++;
            }
            if($this->get_filter_sql()) $query .= ")";
        }

        $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
        $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
        if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

        $perpage = $this->get_items_per_page($this->get_items_per_page(null), self::$ITEMS_PER_PAGE);
        $paged = !empty($_GET["paged"]) ? $_GET["paged"] : '';
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }

        if(!empty($paged) && !empty($perpage)){
            $offset=($paged-1) * $perpage;
            $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
        }
        return $query;
    }

    protected function get_total_items() {
        if($this->total_items != null) return $this->total_items;
        $results = $this->db->get_results($this->get_count_query(), ARRAY_A);
        if($results) {
            $this->total_items = $results[0]['count'];
        }
        return $this->total_items;
    }

    public function get_sortable_columns() {
        $sortables = array();
        foreach($this->get_columns() as $key => $value) {
            if($key == 'id') {
                $sortables[$key] = array($key, true);
            }else{
                $sortables[$key] = array($key, false);
            }
        }
        return $sortables;
    }

    public function get_display_columns() {
        return array();
    }

    public function get_columns() {
        $default_cols = array('actions' => '');
        $custom_cols = $this->get_display_columns();
        return array_merge($default_cols, $custom_cols);
    }

    /**
     * Prepare the table with different parameters, pagination, columns and table elements
     */
    function prepare_items() {

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->set_pagination_args( array(
            'total_items' => $this->get_total_items(),
            'per_page'    => $this->get_items_per_page(null)
        ));

        $query = $this->get_query();
        $this->items = $this->db->get_results($query, ARRAY_A);
    }

    public function display() {
        $this->display_search_field();
        parent::display();
    }

    public function display_search_field() {
        $field = $this->get_search_fields();
        if(!empty($field)) {
            echo '<form method="GET">';
            echo '<input type="hidden" name="page" value="' . $_GET['page'] . '"/>';
            $this->search_box(__('search', 'kkl-ligatool'), "search_value");
            echo '</form>';
        }
    }

    public function column_actions($item) {
        $page = $this->get_edit_page();
        $id = $item['id'];
        return '<a href="' . add_query_arg(compact('page', 'id'), admin_url('admin.php')) . '">' . __('edit', 'kkl-ligatool') .'</a>';
    }

    public function column_default($item, $column_name) {
        return $item[ $column_name ];
    }

    public function column_boolean($value) {
        if($value == 1) return __('yes', 'kkl-ligatool');
        return __('no', 'kkl-ligatool');
    }

    public function column_date($value) {
        if($value == null) return __('empty', 'kkl-ligatool');
        $time = strtotime($value);
        return strftime("%d. %B %Y", $time);
    }

    public function column_datetime($value) {
        if($value == null) return  __('empty', 'kkl-ligatool');
        $time = strtotime($value);
        return strftime("%d. %B %Y %H:%M:%S", $time);
    }

    function get_leagues() {
        if($this->leagues) return $this->leagues;
        $db = new KKL_DB_Wordpress();
        $leagues = $db->getLeagues();
        $keyed = array();
        foreach($leagues as $league) {
            $keyed[$league->id] = $league;
        }
        $this->leagues = $keyed;
        return $keyed;
    }

    function get_seasons() {
        if($this->seasons) return $this->seasons;
        $db = new KKL_DB_Wordpress();
        $seasons = $db->getSeasons();
        $keyed = array();
        foreach($seasons as $season) {
            $keyed[$season->id] = $season;
        }
        $this->seasons = $keyed;
        return $keyed;
    }

    function get_game_days() {
        if($this->gamedays) return $this->gamedays;
        $db = new KKL_DB_Wordpress();
        $days = $db->getGameDays();
        $keyed = array();
        foreach($days as $day) {
            $keyed[$day->id] = $day;
        }
        $this->gamedays = $keyed;
        return $keyed;
    }

    function get_clubs() {
        if($this->clubs) return $this->clubs;
        $db = new KKL_DB_Wordpress();
        $clubs = $db->getClubs();
        $keyed = array();
        foreach($clubs as $club) {
            $keyed[$club->id] = $club;
        }
        $this->clubs = $keyed;
        return $keyed;
    }

    function get_teams() {
        if($this->teams) return $this->teams;
        $db = new KKL_DB_Wordpress();
        $teams = $db->getTeams();
        $keyed = array();
        foreach($teams as $team) {
            $keyed[$team->id] = $team;
        }
        $this->teams = $keyed;
        return $keyed;
    }

    function get_current_league() {
        if($this->currentLeague) return $this->currentLeague;
        $league = null;
        $db = new KKL_DB_Wordpress();
        if($_GET['game_day_filter']) {
            $league = $db->getLeagueForGameday($_GET['game_day_filter']);
        }elseif($_GET['season_filter']) {
            $league = $db->getLeagueForSeason($_GET['season_filter']);
        }elseif($_GET['league_filter']) {
            $league = $db->getLeague($_GET['league_filter']);
        }else{
            $default_league = get_user_option('kkl_ligatool_default_league');
            if($default_league) {
                $league = $db->getLeague($default_league);
            }
        }
        $this->currentLeague = $league;
        return $league;
    }

    function get_current_season() {
        if($this->currentSeason) return $this->currentSeason;
        $season = null;
        $db = new KKL_DB_Wordpress();
        if($_GET['game_day_filter']) {
            $season = $db->getSeasonForGameday($_GET['game_day_filter']);
        }elseif($_GET['season_filter']) {
            $season = $db->getSeason($_GET['season_filter']);
        }elseif($this->get_current_league()) {
            $season = $db->getCurrentSeason($this->get_current_league()->id);
        }
        $this->currentSeason = $season;
        return $season;
    }

    function get_current_game_day() {
        if($this->currentGameDay) return $this->currentGameDay;
        $day = null;
        $db = new KKL_DB_Wordpress();
        if($_GET['game_day_filter']) {
            $day = $db->getGameday($_GET['game_day_filter']);
        }elseif($this->get_current_season()) {
            $day = $db->getCurrentGameDayForSeason($this->get_current_season()->id);
        }elseif($this->get_current_league()) {
            $day = $db->getCurrentGameDayForLeague($this->get_current_league()->id);
        }
        $this->currentGameDay = $day;
        return $day;
    }

    function display_league_filter() {

        $filter = '<div class="filter_container"><label for="league_filter">'.__('league', 'kkl-ligatool').':</label><br/><select id="league_filter" name="league_filter" onchange="window.location.href = kkl_backend_addFilter(window.location.href, \'league_filter\', this.value);">';
        $filter .= '<option value="">' . __('display_all', 'kkl-ligatool') . '</option>';
        foreach($this->get_leagues() as $league) {
            $cl = $this->get_current_league();
            if($cl->id == $league->id) {
                $filter .= '<option value="'.$league->id.'" selected="selected">'.$league->name.'</option>';
            }else{
                $filter .= '<option value="'.$league->id.'">'.$league->name.'</option>';
            }
        }
        $filter .= "</select></div>";
        return $filter;
    }

    function display_season_filter() {
        $league = $this->get_current_league();
        $filter = '<div class="filter_container"><label for="season_filter">'.__('season', 'kkl-ligatool').':</label><br/><select id="season_filter" name="season_filter" onchange="window.location.href = kkl_backend_addFilter(window.location.href, \'season_filter\', this.value);">';
        $filter .= '<option value="">' . __('display_all', 'kkl-ligatool') . '</option>';
        foreach($this->get_seasons() as $season) {
            if($league && ($league->id != $season->league_id)) continue;
            if($this->get_current_season()->id == $season->id) {
                $filter .= '<option value="'.$season->id.'" selected="selected">'. $season->name.'</option>';
            }else{
                $filter .= '<option value="'.$season->id.'">'. $season->name.'</option>';
            }
        }
        $filter .= "</select></div>";
        return $filter;
    }

    function display_game_day_filter() {
        $filter = '<div class="filter_container"><label for="gameday_filter">'.__('game_day', 'kkl-ligatool').':</label><br/><select id="gameday_filter" name="gameday_filter" onchange="window.location.href = kkl_backend_addFilter(window.location.href, \'game_day_filter\', this.value);">';
        $filter .= '<option value="">' . __('display_all', 'kkl-ligatool') . '</option>';
        foreach($this->get_game_days() as $day) {
            $seasonId = $this->get_current_season()->id;
            if($seasonId && ($seasonId != $day->season_id)) continue;
            if($this->get_current_game_day()->id == $day->id) {
                $filter .= '<option value="'.$day->id.'" selected="selected">'.$day->number.'</option>';
            }else{
                $filter .= '<option value="'.$day->id.'">'.$day->number.'</option>';
            }
        }
        $filter .= "</select></div>";
        return $filter;
    }

    function display_create_link() {
        $page = $this->get_create_page();
        $link = add_query_arg(compact('page', 'id'), admin_url('admin.php'));
        $html = '<a href="' . $link . '">' . __('create_new', 'kkl-ligatool') . '</a>';
        return $html;
    }

}
