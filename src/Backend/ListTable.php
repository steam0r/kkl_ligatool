<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\DB;
use KKL\Ligatool\Template\Service;
use WP_List_Table;

abstract class ListTable extends WP_List_Table {
  
  public static $ITEMS_PER_PAGE = 20;
  public $_args = array();
  
  /**
   * @var DB\Wordpress
   */
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
  
  function __construct() {
    parent::__construct();
    $this->db = new DB\Wordpress();
    echo '<style type="text/css">';
    echo '.wp-list-table .column-id { width: 5%; }';
    echo '</style>';
    
  }
  
  /**
   * Prepare the table with different parameters, pagination, columns and table elements
   */
  function prepare_items() {
    
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();
    
    $this->_column_headers = array($columns, $hidden, $sortable);
    
    $this->set_pagination_args(array(
        'total_items' => $this->get_total_items(),
        'per_page' => $this->get_items_per_page(null)
    ));
    
    $query = $this->get_query();
    $this->items = $this->db->getDb()->get_results($query, ARRAY_A);
  }
  
  public function get_columns() {
    $default_cols = array('actions' => '');
    $custom_cols = $this->get_display_columns();
    
    return array_merge($default_cols, $custom_cols);
  }
  
  public function get_display_columns() {
    return array();
  }
  
  public function get_sortable_columns() {
    $sortables = array();
    foreach($this->get_columns() as $key => $value) {
      if($key == 'id') {
        $sortables[$key] = array($key, true);
      } else {
        $sortables[$key] = array($key, false);
      }
    }
    
    return $sortables;
  }
  
  protected function get_total_items() {
    if($this->total_items != null) {
      return $this->total_items;
    }
    $results = $this->db->getDb()->get_results($this->get_count_query(), ARRAY_A);
    if($results) {
      $this->total_items = $results[0]['count'];
    }
    
    return $this->total_items;
  }
  
  protected function get_count_query() {
    $query = "SELECT count(*) AS count FROM " . $this->get_table_name();
    if($this->get_filter_sql() || $_GET['s']) {
      $query .= " WHERE ";
    }
    if($this->get_filter_sql()) {
      $query .= $this->get_filter_sql();
    }
    
    if($_GET['s']) {
      if($this->get_filter_sql()) {
        $query .= " AND (";
      }
      $i = 0;
      foreach($this->get_search_fields() as $field) {
        if($i > 0) {
          $query .= " OR ";
        }
        $query .= $field . " LIKE '%" . esc_sql($_GET['s']) . "%'";
        $i++;
      }
      if($this->get_filter_sql()) {
        $query .= ")";
      }
    }
    
    return $query;
  }
  
  abstract public function get_table_name();
  
  public function get_filter_sql() {
    return "";
  }
  
  public function get_search_fields() {
    return array();
  }
  
  protected function get_query() {
    $query = "SELECT * FROM " . $this->get_table_name();
    if($this->get_filter_sql() || $_GET['s']) {
      $query .= " WHERE ";
    }
    if($this->get_filter_sql()) {
      $query .= $this->get_filter_sql();
    }
    
    if($_GET['s']) {
      if($this->get_filter_sql()) {
        $query .= " AND (";
      }
      $i = 0;
      foreach($this->get_search_fields() as $field) {
        if($i > 0) {
          $query .= " OR ";
        }
        $query .= $field . " LIKE '%" . esc_sql($_GET['s']) . "%' ";
        $i++;
      }
      if($this->get_filter_sql()) {
        $query .= ")";
      }
    }
    
    $orderby = !empty($_GET["orderby"]) ? esc_sql($_GET["orderby"]) : 'ASC';
    $order = !empty($_GET["order"]) ? esc_sql($_GET["order"]) : '';
    if(!empty($orderby) & !empty($order)) {
      $query .= ' ORDER BY ' . $orderby . ' ' . $order;
    }
    
    $perpage = $this->get_items_per_page($this->get_items_per_page(null), self::$ITEMS_PER_PAGE);
    $paged = !empty($_GET["paged"]) ? $_GET["paged"] : '';
    if(empty($paged) || !is_numeric($paged) || $paged <= 0) {
      $paged = 1;
    }
    
    if(!empty($paged) && !empty($perpage)) {
      $offset = ($paged - 1) * $perpage;
      $query .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
    }
    
    return $query;
  }
  
  public function display() {
    echo '<div class="wrap">';
    $this->display_search_field();
    parent::display();
    echo '</div>';
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
    
    return '<a href="' . add_query_arg(compact('page', 'id'), admin_url('admin.php')) . '">' . __('edit', 'kkl-ligatool') . '</a>';
  }
  
  public function get_edit_page() {
    $edit_page = "kkl_" . $this->get_table_name() . "_admin_page";
    
    return $edit_page;
  }
  
  public function column_default($item, $column_name) {
    return $item[$column_name];
  }
  
  public function column_boolean($value) {
    if($value == 1) {
      return __('yes', 'kkl-ligatool');
    }
    
    return __('no', 'kkl-ligatool');
  }
  
  public function column_date($value) {
    if($value == null) {
      return __('empty', 'kkl-ligatool');
    }
    $time = strtotime($value);
    
    return strftime("%d. %B %Y", $time);
  }
  
  public function column_datetime($value) {
    if($value == null) {
      return __('empty', 'kkl-ligatool');
    }
    $time = strtotime($value);
    
    return strftime("%d. %B %Y %H:%M:%S", $time);
  }
  
  function get_clubs() {
    if($this->clubs) {
      return $this->clubs;
    }
    $db = new DB\Wordpress();
    $clubs = $db->getClubs();
    $keyed = array();
    foreach($clubs as $club) {
      $keyed[$club->id] = $club;
    }
    $this->clubs = $keyed;
    
    return $keyed;
  }
  
  function get_teams() {
    if($this->teams) {
      return $this->teams;
    }
    $db = new DB\Wordpress();
    $teams = $db->getTeams();
    $keyed = array();
    foreach($teams as $team) {
      $keyed[$team->id] = $team;
    }
    $this->teams = $keyed;
    
    return $keyed;
  }

  /**
   *
   */
  function display_league_filter() {
    $filters = array();
    $cl = $this->get_current_league();

    foreach ($this->get_leagues() as $league) {
      $filterItem = array(
          "label" => $league->name,
          "id" => $league->id,
          "selected" => false
      );

      if ($cl->id == $league->id) {
        $filterItem["selected"] = true;
      }

      $filters[] = $filterItem;
    }

    $kkl_twig = Service::getTemplateEngine();
    echo $kkl_twig->render('admin/filter/select.twig', array(
        "type" => "league_filter",
        "label" => __('league', 'kkl-ligatool'),
        "all" => __('display_all', 'kkl-ligatool'),
        "filters" => $filters
    ));
  }
  
  function get_leagues() {
    if($this->leagues) {
      return $this->leagues;
    }
    $db = new DB\Wordpress();
    $leagues = $db->getLeagues();
    $keyed = array();
    foreach($leagues as $league) {
      $keyed[$league->id] = $league;
    }
    $this->leagues = $keyed;
    
    return $keyed;
  }
  
  function get_current_league() {
    if($this->currentLeague) {
      return $this->currentLeague;
    }
    $league = null;
    $db = new DB\Wordpress();
    if($_GET['game_day_filter']) {
      $league = $db->getLeagueForGameday($_GET['game_day_filter']);
    } elseif($_GET['season_filter']) {
      $league = $db->getLeagueForSeason($_GET['season_filter']);
    } elseif($_GET['league_filter']) {
      $league = $db->getLeague($_GET['league_filter']);
    } else {
      $default_league = get_user_option('kkl_ligatool_default_league');
      if($default_league) {
        $league = $db->getLeague($default_league);
      }
    }
    $this->currentLeague = $league;
    
    return $league;
  }

  /**
   *
   */
  function display_season_filter() {
    $filters = array();
    $cs = $this->get_current_season();
    $cl = $this->get_current_league();

    foreach ($this->get_seasons() as $season) {
      if ($cl && ($cl->id != $season->league_id)) {
        continue;
      }

      $filterItem = array(
          "label" => $season->name,
          "id" => $season->id,
          "selected" => false
      );

      if ($cs->id == $season->id) {
        $filterItem["selected"] = true;
      }

      $filters[] = $filterItem;
    }

    $kkl_twig = Service::getTemplateEngine();
    echo $kkl_twig->render('admin/filter/select.twig', array(
        "type" => "season_filter",
        "label" => __('season', 'kkl-ligatool'),
        "all" => __('display_all', 'kkl-ligatool'),
        "filters" => $filters
    ));
  }
  
  function get_seasons() {
    if($this->seasons) {
      return $this->seasons;
    }
    $db = new DB\Wordpress();
    $seasons = $db->getSeasons();
    $keyed = array();
    foreach($seasons as $season) {
      $keyed[$season->id] = $season;
    }
    $this->seasons = $keyed;
    
    return $keyed;
  }
  
  function get_current_season() {
    if($this->currentSeason) {
      return $this->currentSeason;
    }
    $season = null;
    $db = new DB\Wordpress();
    if($_GET['game_day_filter']) {
      $season = $db->getSeasonForGameday($_GET['game_day_filter']);
    } elseif($_GET['season_filter']) {
      $season = $db->getSeason($_GET['season_filter']);
    } elseif($this->get_current_league()) {
      $season = $db->getCurrentSeason($this->get_current_league()->id);
    }
    $this->currentSeason = $season;
    
    return $season;
  }

  /**
   *
   */
  function display_game_day_filter() {
    $filters = array();
    $cs = $this->get_current_season()->id;
    $cgd = $this->get_current_game_day();

    foreach ($this->get_game_days() as $day) {
      if ($cs && ($cs != $day->season_id)) {
        continue;
      }

      $filterItem = array(
          "label" => $day->number,
          "id" => $day->id,
          "selected" => false
      );

      if ($cgd->id == $day->id) {
        $filterItem["selected"] = true;
      }

      $filters[] = $filterItem;
    }

    $kkl_twig = Service::getTemplateEngine();
    echo $kkl_twig->render('admin/filter/select.twig', array(
        "type" => "game_day_filter",
        "label" => __('game_day', 'kkl-ligatool'),
        "all" => __('display_all', 'kkl-ligatool'),
        "filters" => $filters
    ));
  }
  
  function get_game_days() {
    if($this->gamedays) {
      return $this->gamedays;
    }
    $db = new DB\Wordpress();
    $days = $db->getGameDays();
    $keyed = array();
    if(is_array($days)) {
      foreach($days as $day) {
        $keyed[$day->id] = $day;
      }
    }
    $this->gamedays = $keyed;
    
    return $keyed;
  }
  
  function get_current_game_day() {
    if($this->currentGameDay) {
      return $this->currentGameDay;
    }
    $day = null;
    $db = new DB\Wordpress();
    if($_GET['game_day_filter']) {
      $day = $db->getGameday($_GET['game_day_filter']);
    } elseif($this->get_current_season()) {
      $day = $db->getCurrentGameDayForSeason($this->get_current_season()->id);
    } elseif($this->get_current_league()) {
      $day = $db->getCurrentGameDayForLeague($this->get_current_league()->id);
    }
    $this->currentGameDay = $day;
    
    return $day;
  }
  
  function display_create_link() {
    $page = $this->get_create_page();
    $link = add_query_arg(compact('page', 'id'), admin_url('admin.php'));
    $html = '<a class="button-primary" href="' . $link . '">' . __('create_new', 'kkl-ligatool') . '</a>';
    
    return $html;
  }
  
  public function get_create_page() {
    $edit_page = "kkl_" . $this->get_table_name() . "_admin_page";
    
    return $edit_page;
  }
  
}
