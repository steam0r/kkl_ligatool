<?php

namespace KKL\Ligatool;

use KKL\Ligatool\DB;

class KKL {

  public static $context = array();

  private $db;

  public function __construct() {
    $this->db = new DB\KKL_DB_Wordpress();
  }

  public function getDB() {
    return $this->db;
  }

  public function init() {


    $this->addPageTemplates();

    add_filter('query_vars', array($this, 'add_query_vars_filter'));

    add_action('init', array($this, 'add_rewrite_rules'));

    add_shortcode('league_table', array('KKL_Shortcodes', 'leagueTable'));
    add_shortcode('table_overview', array('KKL_Shortcodes', 'tableOverview'));
    add_shortcode('gameday_table', array('KKL_Shortcodes', 'gameDayTable'));
    add_shortcode('gameday_overview', array('KKL_Shortcodes', 'gameDayOverview'));
    add_shortcode('league_overview', array('KKL_Shortcodes', 'leagueOverview'));
    add_shortcode('club_detail', array('KKL_Shortcodes', 'clubDetail'));
    add_shortcode('gameday_pager', array('KKL_Shortcodes', 'gameDayPager'));
    add_shortcode('season_schedule', array('KKL_Shortcodes', 'seasonSchedule'));
    add_shortcode('contact_list', array('KKL_Shortcodes', 'contactList'));

    register_sidebar(array(
      'name' => __(__('kkl_global_sidebar', 'kkl-ligatool')),
      'id' => 'kkl_global_sidebar',
      'description' => __('Widgets in this area will be shown on pages using any kkl page template below the page sidebar.'),
      'class' => 'kkl_global_sidebar',
      'before_title' => '<h4>',
      'after_title' => '</h4>'
    ));

    register_sidebar(array(
      'name' => __(__('kkl_table_sidebar', 'kkl-ligatool')),
      'id' => 'kkl_table_sidebar',
      'description' => __('Widgets in this area will be shown on pages using the table page template.'),
      'class' => 'kkl_table_sidebar',
      'before_title' => '<h4>',
      'after_title' => '</h4>'
    ));

    register_sidebar(array(
      'name' => __(__('kkl_leagues_sidebar', 'kkl-ligatool')),
      'id' => 'kkl_leagues_sidebar',
      'description' => __('Widgets in this area will be shown on pages using the league page template.'),
      'class' => 'kkl_league_sidebar',
      'before_title' => '<h4>',
      'after_title' => '</h4>'
    ));

    register_sidebar(array(
      'name' => __(__('kkl_schedule_sidebar', 'kkl-ligatool')),
      'id' => 'kkl_schedule_sidebar',
      'description' => __('Widgets in this area will be shown on pages using the schedule page template.'),
      'class' => 'kkl_schedule_sidebar',
      'before_title' => '<h4>',
      'after_title' => '</h4>'
    ));

    register_sidebar(array(
      'name' => __(__('kkl_teamdetail_sidebar', 'kkl-ligatool')),
      'id' => 'kkl_teamdetail_sidebar',
      'description' => __('Widgets in this area will be shown on pages using the team page template.'),
      'class' => 'kkl_teamdetail_sidebar',
      'before_title' => '<h4>',
      'after_title' => '</h4>'
    ));
  }

  public function add_query_vars_filter($vars) {
    $vars[] = "league";
    $vars[] = "season";
    $vars[] = "game_day";
    $vars[] = "match";
    $vars[] = "club";
    $vars[] = "team";
    $vars[] = "json";
    return $vars;
  }

  public function add_rewrite_rules() {

    $pages = get_pages(array(
      'meta_key' => '_wp_page_template',
      'meta_value' => 'page-ranking.php'
    ));

    foreach ($pages as $page) {
      $url_endpoint = get_permalink($page->ID);
      $url_endpoint = parse_url($url_endpoint);
      $url_endpoint = ltrim($url_endpoint['path'], '/');

      $rule = '^' . $url_endpoint . '([^/]*)?/?([^/]*)?/?([^/]*)?/?';
      $page = 'index.php?page_id=' . $page->ID . '&league=$matches[1]&season=$matches[2]&game_day=$matches[3]';
      add_rewrite_rule($rule, $page, 'top');
    }

    $pages = get_pages(array(
      'meta_key' => '_wp_page_template',
      'meta_value' => 'page-club.php'
    ));

    foreach ($pages as $page) {
      $url_endpoint = get_permalink($page->ID);
      $url_endpoint = parse_url($url_endpoint);
      $url_endpoint = ltrim($url_endpoint['path'], '/');

      $rule = '^' . $url_endpoint . '([^/]*)?/?';
      $page = 'index.php?page_id=' . $page->ID . '&club=$matches[1]';
      add_rewrite_rule($rule, $page, 'top');
    }

    $pages = get_pages(array(
      'meta_key' => '_wp_page_template',
      'meta_value' => 'page-schedule.php'
    ));

    foreach ($pages as $page) {
      $url_endpoint = get_permalink($page->ID);
      $url_endpoint = parse_url($url_endpoint);
      $url_endpoint = ltrim($url_endpoint['path'], '/');

      $rule = '^' . $url_endpoint . '([^/]*)?/?([^/]*)?/?';
      $page = 'index.php?page_id=' . $page->ID . '&league=$matches[1]&season=$matches[2]';
      add_rewrite_rule($rule, $page, 'top');
    }

    flush_rewrite_rules(true);

  }

  public function getContextByLeagueAndSeasonAndGameDay($league, $season, $game_day) {

    $data = array();

    $league = $this->db->getLeagueBySlug($league);
    $season = $this->db->getSeasonByLeagueAndYear($league->id, $season);
    $game_day = $this->db->getGameDayBySeasonAndPosition($season->id, $game_day);

    $data['league'] = $league;
    $data['season'] = $season;
    $data['game_day'] = $game_day;

    return $data;
  }

  public function getContextByLeagueAndSeason($league, $season) {
    $data = array();

    $league = $this->db->getLeagueBySlug($league);
    $season = $this->db->getSeasonByLeagueAndYear($league->id, $season);
    $game_day = $this->db->getGameDay($season->current_game_day);

    $data['league'] = $league;
    $data['season'] = $season;
    $data['game_day'] = $game_day;

    return $data;
  }

  public function getContextByLeague($league) {

    $data = array();

    $league = $this->db->getLeagueBySlug($league);
    $season = $this->db->getSeason($league->current_season);
    $game_day = $this->db->getGameDay($season->current_game_day);

    $data['league'] = $league;
    $data['season'] = $season;
    $data['game_day'] = $game_day;

    return $data;
  }

  public function getContextByClubCode($clubCode) {

    $data = array();
    $data['club'] = $this->db->getClubByCode($clubCode);
    return $data;

  }

  public static function getContext() {
    return self::$context;
  }

  public static function setContext($context) {
    self::$context = $context;
  }

  public static function getLink($type, $values) {

    $link = "";

    if ($type == "club") {
      $link = $values['club'];
      $link .= "/";
    }

    if ($type == "league") {
      $link = $values['league'];
      if ($values['season']) $link .= "/" . $values['season'];
      if ($values['game_day']) $link .= "/" . $values['game_day'];
      $link .= "/";
    }

    if ($type == "schedule") {
      $link = $values['league'] . "/" . $values['season'] . "/";
      if ($values['team']) $link .= "?team=" . $values['team'];
    }

    return $link;

  }

  private function addPageTemplates() {
    /*
    add_action( 'template_include', array($this, 'addPageMatchDayOveriew' ));
    add_action( 'template_include', array($this, 'addPageLeagues' ));
    add_action( 'template_include', array($this, 'addSchedule' ));
    add_action( 'template_include', array($this, 'addRanking' ));
    add_action( 'template_include', array($this, 'addPageTeam' ));
    */
  }

  /*
  public static function addPageMatchDayOveriew($template) {
    print $template;
    $name = "matchdayoverview";
      $plugindir = dirname( __FILE__ ) . '/..';
      if ( !is_page_template( 'page-' . $name . '.php' )) 	$template = $plugindir . '/pages/page-' . $name . '.php';
      print $template;
        return $template;
    }

  public static function addPageLeagues($template) {
    $name = "leagues";
      $plugindir = dirname( __FILE__ ) . '/..';
      if ( !is_page_template( 'page-' . $name . '.php' )) $template = $plugindir . '/pages/page-' . $name . '.php';
        return $template;
  }

  public static function addSchedule($template) {
    $name = "schedule";
      $plugindir = dirname( __FILE__ ) . '/..';
      if ( !is_page_template( 'page-' . $name . '.php' )) $template = $plugindir . '/pages/page-' . $name . '.php';
        return $template;
  }

  public static function addRanking($template) {
    $name = "ranking";
      $plugindir = dirname( __FILE__ ) . '/..';
      if ( !is_page_template( 'page-' . $name . '.php' )) $template = $plugindir . '/pages/page-' . $name . '.php';
        return $template;
  }

  public static function addPageTeam($template) {
    $name = "team";
      $plugindir = dirname( __FILE__ ) . '/..';
      if ( !is_page_template( 'page-' . $name . '.php' )) $template = $plugindir . '/pages/page-' . $name . '.php';
        return $template;
  }
  */

}
