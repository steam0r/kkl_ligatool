<?php

namespace KKL\Ligatool;

use KKL\Ligatool\Backend\PageTemplater;
use KKL\Ligatool\Shortcode\ClubDetail;
use KKL\Ligatool\Shortcode\ContactList;
use KKL\Ligatool\Shortcode\GameDayOverview;
use KKL\Ligatool\Shortcode\GameDayPager;
use KKL\Ligatool\Shortcode\GameDayTable;
use KKL\Ligatool\Shortcode\LeagueOverview;
use KKL\Ligatool\Shortcode\LeagueTable;
use KKL\Ligatool\Shortcode\RankingCode;
use KKL\Ligatool\Shortcode\SeasonSchedule;
use KKL\Ligatool\Shortcode\SetMatchFixture;
use KKL\Ligatool\Shortcode\TableOverview;
use KKL\Ligatool\Widget\OtherLeagues;
use KKL\Ligatool\Widget\OtherSeasons;
use KKL\Ligatool\Widget\UpcomingGames;
use scbLoad4;

class Plugin {

  private static $baseUrl;
  private static $basePath;
  private static $context = array();
  private static $pluginFile;

  private static $pluginPath;

  private $db;

  private $pageTemplates = array(
    'contacts' => array(
      'name' => 'KL Contactlist',
      'filename' => 'kl_contact-list.php',
      'page' => array(
        'matches' => array('')
      )
    ),
    'clubs' => array(
      'name' => 'KL Teams',
      'filename' => 'kl_teams.php',
      'page' => array(
        'matches' => array('team_name')
      )
    ),
    'ranking' => array(
      'name' => 'KL Ranking',
      'filename' => 'kl_ranking.php',
      'page' => array(
        'matches' => array(
          'league',
          'season',
          'game_day'
        )
      )
    ),
    'fixtures' => array(
      'name' => 'KL Fixtures',
      'filename' => 'kl_fixtures.php',
      'page' => array(
        'matches' => array(
          'league',
          'season'
        )
      )
    )
  );

  public function __construct($pluginFile, $baseUrl, $basePath) {
    static::$pluginFile = $pluginFile;
    static::$baseUrl = $baseUrl;
    static::$basePath = $basePath;
    static::$pluginPath = plugins_url('/kkl_ligatool');

  }

  public static function getContext() {
    return self::$context;
  }

  public static function setContext($context) {
    self::$context = $context;
  }

  /**
   * @return mixed
   */
  public static function getBaseUrl() {
    return self::$baseUrl;
  }

  /**
   * @return mixed
   */
  public static function getBasePath() {
    return self::$basePath;
  }

  public static function hasNoLeague() {
    $service = ServiceBroker::getLeagueService();
    $leagues = $service->getAll();
    return empty($leagues);
  }

  /**
   * @return mixed
   */
  public static function getPluginFile() {
    return self::$pluginFile;
  }


  /**
   * TODO: refactor!!
   *
   * @param $type
   * @param $values
   * @return mixed|string
   */
  public static function getLink($type, $values) {
    $link = "";

    if ($type == "club") {
      $link = $values['club'];
      $link .= "/";
    }

    if ($type == "league") {
      $link = $values['league'];
      if ($values['season'])
        $link .= "/" . $values['season'];
      if ($values['game_day'])
        $link .= "/" . $values['game_day'];
      $link .= "/";
    }

    if ($type == "schedule") {
      $link = $values['league'] . "/" . $values['season'] . "/";
      if ($values['team'])
        $link .= "?team=" . $values['team'];
    }

    return $link;

  }

  public function init() {

    ServiceBroker::init('LIVE');

    $db = new DB\Wordpress();

    add_option(DB\Wordpress::$VERSION_KEY, DB\Wordpress::$VERSION);

    register_activation_hook(static::getPluginFile(), array(
      $db,
      'installWordpressDatabase'
    ));
    register_activation_hook(static::getPluginFile(), array(
      $db,
      'installWordpressData'
    ));

    add_filter('query_vars', array(
      $this,
      'add_query_vars_filter'
    ));

    add_action('page_templates', array(
      $this,
      'init_page_templates'
    ));
    do_action('page_templates');

    add_action('init', array(
      $this,
      'add_rewrite_rules'
    ));

    // Add Shortcodes
    $this->init_shortcodes();

    add_action('widgets_init', array(
      $this,
      'register_widgets'
    ));

    register_sidebar(array(
      'name' => __(__('kkl_global_sidebar', 'kkl-ligatool')),
      'id' => 'kkl_global_sidebar',
      'description' => __('Widgets in this area will be shown on pages using any kkl page template below the page sidebar.'),
      'class' => 'kkl_global_sidebar',
      'before_title' => '<h4>',
      'after_title' => '</h4>',
    ));
    register_sidebar(array(
      'name' => __(__('kkl_table_sidebar', 'kkl-ligatool')),
      'id' => 'kkl_table_sidebar',
      'description' => __('Widgets in this area will be shown on pages using the table page template.'),
      'class' => 'kkl_table_sidebar',
      'before_title' => '<h4>',
      'after_title' => '</h4>',
    ));
    register_sidebar(array(
      'name' => __(__('kkl_leagues_sidebar', 'kkl-ligatool')),
      'id' => 'kkl_leagues_sidebar',
      'description' => __('Widgets in this area will be shown on pages using the league page template.'),
      'class' => 'kkl_league_sidebar',
      'before_title' => '<h4>',
      'after_title' => '</h4>',
    ));
    register_sidebar(array(
      'name' => __(__('kkl_schedule_sidebar', 'kkl-ligatool')),
      'id' => 'kkl_schedule_sidebar',
      'description' => __('Widgets in this area will be shown on pages using the schedule page template.'),
      'class' => 'kkl_schedule_sidebar',
      'before_title' => '<h4>',
      'after_title' => '</h4>',
    ));
    register_sidebar(array(
      'name' => __(__('kkl_teamdetail_sidebar', 'kkl-ligatool')),
      'id' => 'kkl_teamdetail_sidebar',
      'description' => __('Widgets in this area will be shown on pages using the team page template.'),
      'class' => 'kkl_teamdetail_sidebar',
      'before_title' => '<h4>',
      'after_title' => '</h4>',
    ));

    $slackIntegration = new Slack\EventListener();
    $slackIntegration->init();

    $mailIntegration = new Mail\EventListener();
    $mailIntegration->init();

    Api\Service::init();
    Tasks\Service::init();

    if (is_admin()) {
      new Updater(__FILE__, 'steam0r', 'kkl_ligatool');
      scbLoad4::init(function () {
        $options = array();
        new Backend\LeagueAdminPage(__FILE__, $options);
        new Backend\ClubAdminPage(__FILE__, $options);
        new Backend\GameDayAdminPage(__FILE__, $options);
        new Backend\MatchAdminPage(__FILE__, $options);
        new Backend\SeasonAdminPage(__FILE__, $options);
        new Backend\TeamAdminPage(__FILE__, $options);
        new Backend\LocationAdminPage(__FILE__, $options);
        new Backend\PlayerAdminPage(__FILE__, $options);
      });
      add_filter('set-screen-option', array(
        'KKLBackend',
        'set_screen_options'
      ), 10, 3);
      Backend::display();
    } else {
      static::enqueue_scripts(
        array(
          array(
            'handle' => 'kkl_frontend',
            'src' => '/frontend/sortTable.js',
            'type' => 'js'
          ),
          array(
            'handle' => 'kkl_frontend_jquery',
            'src' => '/frontend/jquery-1.7.2.js',
            'type' => 'js'
          ),
          array(
            'handle' => 'kkl_frontend_datetimepicker',
            'src' => '/jquery.datetimepicker.js',
            'type' => 'js'
          ),
          array(
            'handle' => 'kkl_frontend_datepicker',
            'src' => '/datepicker.min.js',
            'type' => 'js'
          ),
          array(
            'handle' => 'kkl_frontend_fixture',
            'src' => '/frontend/set_fixture.js',
            'type' => 'js'
          ),
          array(
            'handle' => 'kkl_frontend',
            'src' => '/ligatool_frontend.css',
            'type' => 'css'
          )
        ));
    }

    add_action('plugins_loaded', function () {
      load_plugin_textdomain('kkl-ligatool', false, dirname(plugin_basename(__FILE__)) . '/../lang/');
    });
  }


  /**
   * register scripts (css, js)
   *
   * @param $arr
   */
  public static function enqueue_scripts($arr) {
    foreach ($arr as $script) {
      if ($script['type'] === 'js') {
        $path = static::$pluginPath . '/js' . $script['src'];

        wp_enqueue_script($script['handle'], $path);
      } elseif ($script['type'] === 'css') {
        $path = static::$pluginPath . '/css' . $script['src'];

        wp_enqueue_style($script['handle'], $path);
      }
    }
  }


  /**
   * offer "page-templates" on wp pages
   */
  public function init_page_templates() {
    $output = array();

    foreach ($this->pageTemplates as $template) {
      $output[$template['filename']] = $template['name'];
    }

    PageTemplater::get_instance($output);
  }


  /**
   *
   */
  public function init_shortcodes() {
    add_shortcode('kl-set-fixture', array(SetMatchFixture::class, 'render'));
    add_shortcode('kl-ranking', array(RankingCode::class, 'render'));
    add_shortcode('league_table', array(LeagueTable::class, 'render'));
    add_shortcode('table_overview', array(TableOverview::class, 'render'));
    add_shortcode('gameday_table', array(GameDayTable::class, 'render'));
    add_shortcode('gameday_overview', array(GameDayOverview::class, 'render'));
    add_shortcode('league_overview', array(LeagueOverview::class, 'render'));
    add_shortcode('club_detail', array(ClubDetail::class, 'render'));
    add_shortcode('gameday_pager', array(GameDayPager::class, 'render'));
    add_shortcode('season_schedule', array(SeasonSchedule::class, 'render'));
    add_shortcode('contact_list', array(ContactList::class, 'render'));
    add_shortcode('set_match_fixture', array(SetMatchFixture::class, 'render'));
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

  public function register_widgets() {
    register_widget(new OtherLeagues());
    register_widget(new OtherSeasons());
    register_widget(new UpcomingGames());
  }


  /**
   * @param $templateName
   * @return mixed
   */
  public function getPageTemplateContext($templateName) {
    $templateData = $this->pageTemplates[$templateName];

    return get_pages(array(
      'meta_key' => '_wp_page_template',
      'meta_value' => $templateData['filename']
    ))[0];
  }


  /**
   *
   */
  public function add_rewrite_rules() {
    $pageTemplates = $this->pageTemplates;

    foreach ($pageTemplates as $template) {
      // get all pages are using page-templates
      $pages = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => $template['filename']
      ));

      // define url matches & regex for page-template
      $matches = '';
      $regex = '';
      foreach ($template['page']['matches'] as $key => $match) {
        $matches = $matches . '&' . $match . '=$matches[' . ($key + 1) . ']';
        $regex = $regex . '([^/]*)?/?';
      }

      // add rewrite rule for every page
      foreach ($pages as $page) {
        $url_endpoint = get_permalink($page->ID);
        $url_endpoint = parse_url($url_endpoint);
        $url_endpoint = ltrim($url_endpoint['path'], '/'); // issue ?!

        add_rewrite_rule($url_endpoint . $regex, 'index.php?page_id=' . $page->ID . $matches, 'top');
      }

      // save matches in var
      foreach ($template['page']['matches'] as $match) {
        add_rewrite_tag('%' . $match . '%', '([^/]+)');
      }
    }


    // TODO: kann dann gelöscht werden ?
    $pages = get_pages(array(
      'meta_key' => '_wp_page_template',
      'meta_value' => 'page-ranking.php',
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
      'meta_value' => 'page-club.php',
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
      'meta_value' => 'page-schedule.php',
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


  /*
   * TODO:
   * try to remove!!
   */
  public function getContextByLeagueAndSeasonAndGameDay($league, $year, $game_day) {

    $data = array();

    $leagueService = ServiceBroker::getLeagueService();
    $seasonService = ServiceBroker::getSeasonService();
    $gameDayService = ServiceBroker::getGameDayService();

    $league = $leagueService->bySlug($league);
    $season = $seasonService->byLeagueAndYear($league->getId(), $year);
    $game_day = $gameDayService->bySeasonAndPosition($season->getId(), $game_day);

    $data['league'] = $league;
    $data['season'] = $season;
    $data['game_day'] = $game_day;

    return $data;
  }

  public function getContextByLeagueAndSeason($league, $year) {
    $data = array();

    $leagueService = ServiceBroker::getLeagueService();
    $seasonService = ServiceBroker::getSeasonService();
    $gameDayService = ServiceBroker::getGameDayService();

    $league = $leagueService->bySlug($league);
    $season = $seasonService->byLeagueAndYear($league->getId(), $year);
    $game_day = $gameDayService->byId($season->getCurrentGameDay());

    $data['league'] = $league;
    $data['season'] = $season;
    $data['game_day'] = $game_day;

    return $data;
  }

  public function getContextByLeague($league) {

    $data = array();

    $gameDayService = ServiceBroker::getGameDayService();
    $leagueService = ServiceBroker::getLeagueService();
    $seasonService = ServiceBroker::getSeasonService();

    $league = $leagueService->bySlug($league);
    $season = $seasonService->byId($league->getCurrentSeason());
    $game_day = $gameDayService->byId($season->getCurrentGameDay());

    $data['league'] = $league;
    $data['season'] = $season;
    $data['game_day'] = $game_day;

    return $data;
  }

  public function getContextByClubCode($clubCode) {

    $data = array();
    $clubService = ServiceBroker::getClubService();
    $data['club'] = $clubService->byCode($clubCode);

    return $data;

  }

}
