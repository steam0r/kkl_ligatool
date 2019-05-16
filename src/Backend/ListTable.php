<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\DB;
use KKL\Ligatool\Model\GameDay;
use KKL\Ligatool\Model\KKLModel;
use KKL\Ligatool\Model\KKLModelService;
use KKL\Ligatool\Model\League;
use KKL\Ligatool\Model\Season;
use KKL\Ligatool\Model\Team;
use WP_List_Table;

abstract class ListTable extends WP_List_Table {

  public static $ITEMS_PER_PAGE = 10;
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
        'per_page' => $this->get_items_per_page($this->get_items_per_page(null), self::$ITEMS_PER_PAGE))
    );

    $orderBy = null;
    if (!empty($_GET["orderby"]) && !empty($_GET["order"])) {
      $orderBy = new DB\OrderBy($_GET["orderby"], $_GET["order"]);
    }

    $perpage = $this->get_items_per_page($this->get_items_per_page(null), self::$ITEMS_PER_PAGE);
    $paged = !empty($_GET["paged"]) ? $_GET["paged"] : '';
    if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
      $paged = 1;
    }
    $offset = ($paged - 1) * $perpage;
    $limit = new DB\Limit($perpage, $offset);

    $this->items = $this->getModelService()->find($this->get_filter_sql(), $orderBy, $limit);
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
    foreach ($this->get_columns() as $key => $value) {
      if ($key == 'ID') {
        $sortables[$key] = array($key, true);
      } else {
        $sortables[$key] = array($key, false);
      }
    }

    return $sortables;
  }

  protected function get_total_items() {
    if ($this->total_items != null) {
      return $this->total_items;
    }
    $this->total_items = sizeof($this->getModelService()->getAll());
    return $this->total_items;
  }

  /**
   * @return KKLModelService
   */
  abstract public function getModelService();

  public function get_filter_sql() {
    return null;
  }

  public function get_search_fields() {
    return array();
  }

  protected function get_query() {
    $query = "SELECT * FROM " . $this->getModelService()->getFullTableName();
    if ($_GET['s']) {
      if ($this->get_filter_sql()) {
        $query .= " AND (";
      }
      $i = 0;
      foreach ($this->get_search_fields() as $field) {
        if ($i > 0) {
          $query .= " OR ";
        }
        $query .= $field . " LIKE '%" . esc_sql($_GET['s']) . "%' ";
        $i++;
      }
      if ($this->get_filter_sql()) {
        $query .= ")";
      }
    }

    return $query;
  }

  public function display() {
    $this->display_search_field();
    parent::display();
  }

  public function display_search_field() {
    $field = $this->get_search_fields();
    if (!empty($field)) {
      echo '<form method="GET">';
      echo '<input type="hidden" name="page" value="' . $_GET['page'] . '"/>';
      $this->search_box(__('search', 'kkl-ligatool'), "search_value");
      echo '</form>';
    }
  }

  /**
   * @param KKLModel $item
   * @return string
   */
  public function column_actions($item) {
    $page = $this->get_edit_page();
    $id = $item->getId();
    return '<a href="' . add_query_arg(compact('page', 'id'), admin_url('admin.php')) . '">' . __('edit', 'kkl-ligatool') . '</a>';
  }

  public function get_edit_page() {
    $edit_page = $this->getModelService()->getTableName() . "_admin_page";
    return $edit_page;
  }

  /**
   * @param KKLModel $item
   * @param string $column_name
   */
  public function column_default($item, $column_name) {
    return $item->get($column_name);
  }

  public function column_boolean($value) {
    if ($value == 1) {
      return __('yes', 'kkl-ligatool');
    }
    return __('no', 'kkl-ligatool');
  }

  public function column_date($value) {
    if ($value == null) {
      return __('empty', 'kkl-ligatool');
    }
    $time = strtotime($value);
    return strftime("%d. %B %Y", $time);
  }

  public function column_datetime($value) {
    if ($value == null) {
      return __('empty', 'kkl-ligatool');
    }
    $time = strtotime($value);
    return strftime("%d. %B %Y %H:%M:%S", $time);
  }

  /**
   * @return Team[]
   */
  function get_teams() {
    if ($this->teams) {
      return $this->teams;
    }
    $db = new DB\Wordpress();
    $this->teams = $db->getTeams();
    return $this->teams;
  }

  function display_league_filter() {

    $filter = '<div class="filter_container"><label for="league_filter">' . __('league', 'kkl-ligatool') . ':</label><br/><select id="league_filter" name="league_filter" onchange="window.location.href = kkl_backend_addFilter(window.location.href, \'league_filter\', this.value);">';
    $filter .= '<option value="">' . __('display_all', 'kkl-ligatool') . '</option>';
    foreach ($this->get_leagues() as $league) {
      $cl = $this->get_current_league();
      if ($cl && ($cl->getId() == $league->getId())) {
        $filter .= '<option value="' . $league->getId() . '" selected="selected">' . $league->getName() . '</option>';
      } else {
        $filter .= '<option value="' . $league->getId() . '">' . $league->getName() . '</option>';
      }
    }
    $filter .= "</select></div>";

    return $filter;
  }

  function get_leagues() {
    if ($this->leagues) {
      return $this->leagues;
    }
    $db = new DB\Wordpress();
    $this->leagues = $db->getLeagues();
    return $this->leagues;
  }

  /**
   * @return League|null
   */
  function get_current_league() {
    if ($this->currentLeague) {
      return $this->currentLeague;
    }
    $league = null;
    $db = new DB\Wordpress();
    if ($_GET['game_day_filter']) {
      $league = $db->getLeagueForGameday($_GET['game_day_filter']);
    } elseif ($_GET['season_filter']) {
      $league = $db->getLeagueForSeason($_GET['season_filter']);
    } elseif ($_GET['league_filter']) {
      $league = $db->getLeague($_GET['league_filter']);
    } else {
      $default_league = get_user_option('kkl_ligatool_default_league');
      if ($default_league) {
        $league = $db->getLeague($default_league);
      }
    }
    $this->currentLeague = $league;

    return $this->currentLeague;
  }

  function display_season_filter() {
    $league = $this->get_current_league();
    $filter = '<div class="filter_container"><label for="season_filter">' . __('season', 'kkl-ligatool') . ':</label><br/><select id="season_filter" name="season_filter" onchange="window.location.href = kkl_backend_addFilter(window.location.href, \'season_filter\', this.value);">';
    $filter .= '<option value="">' . __('display_all', 'kkl-ligatool') . '</option>';
    foreach ($this->get_seasons() as $season) {
      if ($league && ($league->getId() != $season->getLeagueId())) {
        continue;
      }
      $cs = $this->get_current_season();
      if ($cs && ($cs->getId() == $season->getId())) {
        $filter .= '<option value="' . $season->getId() . '" selected="selected">' . $season->getName() . '</option>';
      } else {
        $filter .= '<option value="' . $season->getId() . '">' . $season->getName() . '</option>';
      }
    }
    $filter .= "</select></div>";

    return $filter;
  }

  /**
   * @return Season[]
   */
  function get_seasons() {
    if ($this->seasons) {
      return $this->seasons;
    }
    $db = new DB\Wordpress();
    $this->seasons = $db->getSeasons();
    return $this->seasons;
  }

  /**
   * @return Season|null
   */
  function get_current_season() {
    if ($this->currentSeason) {
      return $this->currentSeason;
    }
    $season = null;
    $db = new DB\Wordpress();
    if ($_GET['game_day_filter']) {
      $season = $db->getSeasonForGameday($_GET['game_day_filter']);
    } elseif ($_GET['season_filter']) {
      $season = $db->getSeason($_GET['season_filter']);
    } elseif ($this->get_current_league()) {
      $season = $db->getCurrentSeason($this->get_current_league()->getId());
    }
    $this->currentSeason = $season;

    return $this->currentSeason;
  }

  function display_game_day_filter() {
    $filter = '<div class="filter_container"><label for="gameday_filter">' . __('game_day', 'kkl-ligatool') . ':</label><br/><select id="gameday_filter" name="gameday_filter" onchange="window.location.href = kkl_backend_addFilter(window.location.href, \'game_day_filter\', this.value);">';
    $filter .= '<option value="">' . __('display_all', 'kkl-ligatool') . '</option>';
    foreach ($this->get_game_days() as $day) {
      $cs = $this->get_current_season();;
      if ($cs && ($cs->getId() != $day->getSeasonId())) {
        continue;
      }
      $cd = $this->get_current_game_day();
      if ($cd && ($cd->getId() == $day->getId())) {
        $filter .= '<option value="' . $day->getId() . '" selected="selected">' . $day->getNumber() . '</option>';
      } else {
        $filter .= '<option value="' . $day->getId() . '">' . $day->getNumber() . '</option>';
      }
    }
    $filter .= "</select></div>";

    return $filter;
  }

  /**
   * @return GameDay[]
   */
  function get_game_days() {
    if ($this->gamedays) {
      return $this->gamedays;
    }
    $db = new DB\Wordpress();
    $this->gamedays = $db->getGameDays();
    return $this->gamedays;
  }

  /**
   * @return GameDay|null
   */
  function get_current_game_day() {
    if ($this->currentGameDay) {
      return $this->currentGameDay;
    }
    $day = null;
    $db = new DB\Wordpress();
    if ($_GET['game_day_filter']) {
      $day = $db->getGameday($_GET['game_day_filter']);
    } elseif ($this->get_current_season()) {
      $day = $db->getCurrentGameDayForSeason($this->get_current_season()->getId());
    } elseif ($this->get_current_league()) {
      $day = $db->getCurrentGameDayForLeague($this->get_current_league()->getId());
    }
    $this->currentGameDay = $day;

    return $this->currentGameDay;
  }

  function display_create_link() {
    $page = $this->get_create_page();
    $link = add_query_arg(compact('page', 'id'), admin_url('admin.php'));
    $html = '<a href="' . $link . '">' . __('create_new', 'kkl-ligatool') . '</a>';

    return $html;
  }

  public function get_create_page() {
    $edit_page = $this->getModelService()->getTableName() . "_admin_page";
    return $edit_page;
  }

}
