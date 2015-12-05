<?php

class KKL_Backend {

    public function validate_setting($plugin_options) {  return $plugin_options;}

    public function section_cb() {}

    public function host_setting() {
        $options = get_option('kkl_ligatool');
        echo "<input name='kkl_ligatool[db_host]' type='text' value='{$options['db_host']}' />";
    }

    public function name_setting() {
        $options = get_option('kkl_ligatool');
        echo "<input name='kkl_ligatool[db_name]' type='text' value='{$options['db_name']}' />";
    }

    public function user_setting() {
        $options = get_option('kkl_ligatool');
        echo "<input name='kkl_ligatool[db_user]' type='text' value='{$options['db_user']}' />";
    }

    public function pass_setting() {
        $options = get_option('kkl_ligatool');
        echo "<input name='kkl_ligatool[db_pass]' type='password' value='{$options['db_pass']}' />";
    }

    public function admin_menu() {
        $hook = add_menu_page('KKL Ligatool', 'KKL Ligatool', 'manage_options', 'kkl_ligatool', array(__CLASS__, 'plugin_page'));
        add_action('manage_'. $hook .'_columns', array(__CLASS__, 'add_screen_options'));
        add_action('load-'. $hook, array(__CLASS__, 'add_help_options'));

        self::add_kkl_ligatool_page( NULL, __('leagues', 'kkl-ligatool'), __('leagues', 'kkl-ligatool'), 'manage_options', 'kkl_ligatool_leagues', array(__CLASS__, 'leagues_page'));
        self::add_kkl_ligatool_page( NULL, __('seasons', 'kkl-ligatool'), __('seasons', 'kkl-ligatool'), 'manage_options', 'kkl_ligatool_seasons', array(__CLASS__, 'seasons_page'));
        self::add_kkl_ligatool_page( NULL, __('game_days', 'kkl-ligatool'), __('game_days', 'kkl-ligatool'), 'manage_options', 'kkl_ligatool_gamedays', array(__CLASS__, 'gamedays_page'));
        self::add_kkl_ligatool_page( NULL, __('matches', 'kkl-ligatool'), __('matches', 'kkl-ligatool'), 'manage_options', 'kkl_ligatool_matches', array(__CLASS__, 'matches_page'));
        self::add_kkl_ligatool_page( NULL, __('clubs', 'kkl-ligatool'), __('clubs', 'kkl-ligatool'), 'manage_options', 'kkl_ligatool_clubs', array(__CLASS__, 'clubs_page'));
        self::add_kkl_ligatool_page( NULL, __('teams', 'kkl-ligatool'), __('teams', 'kkl-ligatool'), 'manage_options', 'kkl_ligatool_teams', array(__CLASS__, 'teams_page'));
        self::add_kkl_ligatool_page( NULL, __('locations', 'kkl-ligatool'), __('locations', 'kkl-ligatool'), 'manage_options', 'kkl_ligatool_locations', array(__CLASS__, 'locations_page'));
        self::add_kkl_ligatool_page( NULL, __('stats', 'kkl-ligatool'), __('stats', 'kkl-ligatool'), 'manage_options', 'kkl_ligatool_stats', array(__CLASS__, 'stats_page'));
        self::add_kkl_ligatool_page( NULL, __('settings', 'kkl-ligatool'), __('settings', 'kkl-ligatool'), 'administrator', 'kkl_ligatool_settings', array(__CLASS__, 'settings_page'));

    }

    public function add_kkl_ligatool_page($parent_slug, $page_title, $page_title, $capability, $menu_slug, $function) {
        $hook = add_submenu_page( $parent_slug, $page_title, $page_title, $capability, $menu_slug, $function);
        add_action('manage_'. $hook .'_columns', array(__CLASS__, 'add_screen_options'));
    }

    public static function add_help_options() {
	$screen = get_current_screen();
	$screen->add_help_tab( array(
		'id'       => 'kkl_help_common',
		'title'    => __( 'help_common_title', 'kkl-ligatool' ),
		'content'  => __( 'help_common_content', 'kkl-ligatool' ) 
	));
    	$screen->add_help_tab( array(
		'id'       => 'kkl_help_leagues',
		'title'    => __( 'leagues', 'kkl-ligatool' ),
		'content'  => __( 'help_leagues_content', 'kkl-ligatool' ) 
	));
	$screen->add_help_tab( array(
		'id'       => 'kkl_help_seasons',
		'title'    => __( 'seasons', 'kkl-ligatool' ),
		'content'  => __( 'help_seasons_content', 'kkl-ligatool' ) 
	));
	$screen->add_help_tab( array(
		'id'       => 'kkl_help_game_days',
		'title'    => __( 'game_days', 'kkl-ligatool' ),
		'content'  => __( 'help_game_days_content', 'kkl-ligatool' ) 
	));
	$screen->add_help_tab( array(
		'id'       => 'kkl_help_matches',
		'title'    => __( 'matches', 'kkl-ligatool' ),
		'content'  => __( 'help_matches_content', 'kkl-ligatool' ) 
	));
	$screen->add_help_tab( array(
		'id'       => 'kkl_help_clubs',
		'title'    => __( 'clubs', 'kkl-ligatool' ),
		'content'  => __( 'help_clubs_content', 'kkl-ligatool' ) 
	));
	$screen->add_help_tab( array(
		'id'       => 'kkl_help_teams',
		'title'    => __( 'teams', 'kkl-ligatool' ),
		'content'  => __( 'help_teams_content', 'kkl-ligatool' ) 
	));
	$screen->add_help_tab( array(
		'id'       => 'kkl_help_locations',
		'title'    => __( 'locations', 'kkl-ligatool' ),
		'content'  => __( 'help_locations_content', 'kkl-ligatool' ) 
	));
    }

    public static function add_screen_options() {

        $default_league = get_user_option('kkl_ligatool_default_league'); // load values from db

        if ($default_league === false) { // if values exist
            $default_league = 0; // using default values
        }

        $column_id = 'screen_options_hack'; // this id will be used to identify and hide checkbox which will be automatically created by WP

        $html = '</label><script type="text/javascript">jQuery("label[for=\''. $column_id .'-hide\']").hide()</script>'; // using jQuery to hide unnecessary checkbox

        $html .= '<div class="screen-options"><label for="kkl_ligatool_default_league">' . __('default league', 'kkl-ligatool') . ':</label>';

        $html .= '<select id="kkl_ligatool_default_league" name="wp_screen_options[value][kkl_ligatool_default_league]" value="'. $default_league .'">';
        $db = new KKL_DB();
        $leagues = $db->getLeagues();
        $html .= '<option value="0">' . __('please select', 'kkl-ligatool') . '</option>';
        foreach($leagues as $league) {
            if($league->id == $default_league) {
                $html .= '<option value="'.$league->id.'" selected="selected">'. $league->name .'</option>';
            }else{
                $html .= '<option value="'.$league->id.'">'. $league->name .'</option>';
            }
        }
        $html .= '</select><br/><br/>';

        $html .= '<input type="hidden" name="wp_screen_options[option]" value="kkl_ligatool_default_league" />'; // screen options db variable name (make sure it does not contain digits). Do not change field name attribute value!
        $html .= '<input type="submit" class="button" value="' . __('Apply', 'kkl-ligatool') . '" />'; // submit button
        $html .= '</div>';

        return array($column_id=>$html);
    }

    public static function set_screen_option($status, $option, $value) {
	if($_POST['wp_screen_options'] && $_POST['wp_screen_options']['value'] && $_POST['wp_screen_options']['value']['kkl_ligatool_default_league']) {
		return $_POST['wp_screen_options']['value']['kkl_ligatool_default_league'];
	}else{
		return $value;
	} 
    }

    public static function add_help_screen($help_content) {
        $help_content['toplevel_page_kkl_ligatool'] = array('title' => 'test', content => 'Help for plugin settings page'); // using self::$plugin_page_id as array key adds your text only to plugin settings page.

        return $help_content;
    }

    public static function display_tabs() {
        $tabs = array(
            'kkl_ligatool_leagues' => __('leagues', 'kkl-ligatool'),
            'kkl_ligatool_seasons' => __('seasons', 'kkl-ligatool'),
            'kkl_ligatool_gamedays' => __('game_days', 'kkl-ligatool'),
            'kkl_ligatool_matches' => __('matches', 'kkl-ligatool'),
            'kkl_ligatool_clubs' => __('clubs', 'kkl-ligatool'),
            'kkl_ligatool_teams' => __('teams', 'kkl-ligatool'),
            'kkl_ligatool_locations' => __('locations', 'kkl-ligatool'),
            'kkl_ligatool_stats' => __('stats', 'kkl-ligatool'),
            'kkl_ligatool_settings' => __('settings', 'kkl-ligatool')
        );

        if(isset($_GET['page'])){
            $current = $_GET['page'];
        }

        $content = '';
        $content .= '<h2 class="nav-tab-wrapper">';
        foreach($tabs as $location => $tabname){
            if($current == $location){
                $class = ' nav-tab-active';
            } else{
                $class = '';
            }
            $content .= '<a class="nav-tab'.$class.'" href="?page='.$location.'">'.$tabname.'</a>';
        }
        $content .= '</h2>';
        echo $content;
    }

    public static function plugin_page() {

        global $kkl_twig;

        self::display_tabs();

        echo $kkl_twig->render('admin/home.tpl');

    }

    public static function leagues_page() {

        global $kkl_twig;

        self::display_tabs();

        $wp_list_table = new KKL_League_List_Table();
        $wp_list_table->prepare_items();
        $wp_list_table->display();

    }

    public static function seasons_page() {

        global $kkl_twig;

        self::display_tabs();

        $wp_list_table = new KKL_Season_List_Table();
        $wp_list_table->prepare_items();
        $wp_list_table->display();

    }

    public static function gamedays_page() {

        global $kkl_twig;

        self::display_tabs();

        $wp_list_table = new KKL_GameDay_List_Table();
        $wp_list_table->prepare_items();
        $wp_list_table->display();

    }


    public static function matches_page() {

        global $kkl_twig;

        self::display_tabs();

        $wp_list_table = new KKL_Match_List_Table();
        $wp_list_table->prepare_items();
        $wp_list_table->display();

    }

    public static function clubs_page() {

        global $kkl_twig;

        self::display_tabs();

        $wp_list_table = new KKL_Club_List_Table();
        $wp_list_table->prepare_items();
        $wp_list_table->display();

    }

    public static function teams_page() {

        global $kkl_twig;

        self::display_tabs();

        $wp_list_table = new KKL_Team_List_Table();
        $wp_list_table->prepare_items();
        $wp_list_table->display();

    }

    public static function locations_page() {

        global $kkl_twig;

        self::display_tabs();

        $wp_list_table = new KKL_Location_List_Table();
        $wp_list_table->prepare_items();
        $wp_list_table->display();

    }

    public static function stats_page() {

        global $kkl_twig;

        self::display_tabs();

        echo $kkl_twig->render('admin/stats.tpl');

    }

    public static function settings_page() {

        global $kkl_twig;

        self::display_tabs();

        $vars = array();

        ob_start();
        settings_fields('kkl_ligatool');
        $vars['fields'] = ob_get_contents();
        ob_end_clean();

        ob_start();
        do_settings_sections(__FILE__);
        $vars['sections'] = ob_get_contents();
        ob_end_clean();

        $vars['save'] = esc_attr('Save Changes');

        echo $kkl_twig->render('admin/settings.tpl', $vars);

    }

    public static function register_settings() {

        register_setting( 'kkl_ligatool', 'kkl_ligatool', array(__CLASS__, 'validate_setting'));

        add_settings_section('kkl_ligatool_db', 'Database', array(__CLASS__, 'section_cb'), __FILE__);

        add_settings_field('db_host', 'Host:', array(__CLASS__, 'host_setting'), __FILE__, 'kkl_ligatool_db');
        add_settings_field('db_name', 'Name:', array(__CLASS__, 'name_setting'), __FILE__, 'kkl_ligatool_db');
        add_settings_field('db_user', 'User:', array(__CLASS__, 'user_setting'), __FILE__, 'kkl_ligatool_db');
        add_settings_field('db_pass', 'Pass:', array(__CLASS__, 'pass_setting'), __FILE__, 'kkl_ligatool_db');
    }

    public static function display() {

        wp_enqueue_script( 'kkl_datepicker', plugins_url() . '/kkl_ligatool/js/jquery.datetimepicker.js');
        wp_enqueue_script( 'kkl_backend', plugins_url() . '/kkl_ligatool/js/kkl_backend.js');

        wp_enqueue_style( 'kkl_datepicker', plugins_url() . '/kkl_ligatool/css/jquery.datetimepicker.css');
        wp_enqueue_style( 'kkl_backend', plugins_url() . '/kkl_ligatool/css/kkl_backend.css');

        add_action('admin_menu', array(__CLASS__, 'admin_menu'));
        add_action('set-screen-option', array(__CLASS__, 'set_screen_option'));
        add_action('admin_init', array(__CLASS__, 'register_settings' ));

    }


}
