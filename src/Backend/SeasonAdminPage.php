<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\DB;
use stdClass;

class SeasonAdminPage extends AdminPage {
  
  function setup() {
    $this->args = array('page_title' => __('season', 'kkl-ligatool'), 'page_slug' => 'kkl_seasons_admin_page',
                        'parent'     => null);
  }
  
  function display_content() {
    
    $season = $this->get_item();
    
    $db = new DB\Wordpress();
    $leagues = $db->getLeagues();
    $league_options = array();
    foreach($leagues as $league) {
      $league_options[$league->id] = $league->name;
    }
    
    $days = $db->getGameDaysForSeason($season->id);
    $day_options = array("" => __('please_select', 'kkl-ligatool'));
    foreach($days as $day) {
      $day_options[$day->id] = 'Spieltag ' . $day->number;
    }
    
    $admins = $db->getLeagueAdmins();
    $contact_options = array("" => __('please_select', 'kkl-ligatool'));
    foreach($admins as $admin) {
      $contact_options[$admin->id] = $admin->first_name . " " . $admin->last_name;
    }
    
    $active_checked = ($season->active == 1);
    if($this->errors && $_POST['active']) {
      $active_checked = true;
    }
    
    echo $this->form_table(
      array(array('type' => 'hidden', 'name' => 'id', 'value' => $season->id),
            array('title' => __('name', 'kkl-ligatool'), 'type' => 'text', 'name' => 'name',
                  'value' => ($this->errors) ? $_POST['name'] : $season->name,
                  'extra' => ($this->errors['name']) ? array('style' => "border-color: red;") : array()),
            array('title'   => __('league', 'kkl-ligatool'), 'type' => 'select', 'name' => 'league',
                  'choices' => $league_options, 'selected' => ($this->errors) ? $_POST['league'] : $season->league_id,
                  'extra'   => ($this->errors['league']) ? array('style' => "border-color: red;") : array()),
            array('title' => __('start_date', 'kkl-ligatool'), 'type' => 'text', 'name' => 'start_date',
                  'value' => $season->start_date, 'extra' => array('class' => 'datetimepicker')),
            array('title' => __('end_date', 'kkl-ligatool'), 'type' => 'text', 'name' => 'end_date',
                  'value' => $season->end_date, 'extra' => array('class' => 'datetimepicker')),
            array('title'   => __('current_game_day', 'kkl-ligatool'), 'type' => 'select', 'name' => 'current_game_day',
                  'choices' => $day_options, 'selected' => $season->current_game_day),
            array('title'   => __('active', 'kkl-ligatool'), 'type' => 'checkbox', 'name' => 'active',
                  'checked' => $active_checked),
            array('title'    => __('season_admin', 'kkl-ligatool'), 'type' => 'select', 'name' => 'season_admin',
                  'choices'  => $contact_options,
                  'selected' => ($this->errors) ? $_POST['season_admin'] : $season->properties['season_admin'],
                  'extra'    => ($this->errors['season_admin']) ? array('style' => "border-color: red;") : array()),
            array('title' => "Auf- und Abstiegslinien<br/>(unterhalb von)", 'type' => 'text', 'name' => 'relegation_markers',
                  'value' => ($this->errors) ? $_POST['relegation_markers'] : $season->properties['relegation_markers'],
                  'extra' => ($this->errors['relegation_markers']) ? array('style' => "border-color: red;") : array()),
            array('title' => "Auf- und Abstiegsregeln", 'type' => 'textarea', 'name' => 'relegation_explanation',
                  'value' => ($this->errors) ? $_POST['relegation_explanation'] : $season->properties['relegation_explanation'],
                  'extra' => ($this->errors['relegation_explanation']) ? array('rows' => 7, 'cols' => 50, 'style' => "border-color: red;") : array('rows' => 7, 'cols' => 50)))
    );
  }
  
  function get_item() {
    if($this->item)
      return $this->item;
    if($_GET['id']) {
      $db = new DB\Wordpress();
      $this->setItem($db->getSeason($_GET['id']));
    }
    return $this->item;
    
  }
  
  function validate($new_data, $old_data) {
    $errors = array();
    
    if(!$new_data['name'])
      $errors['name'] = true;
    if(!$new_data['league'])
      $errors['league'] = true;
    
    return $errors;
  }
  
  function save() {
    
    $start_date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $_POST['start_date'])));
    $end_date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $_POST['end_date'])));
    
    $season = new stdClass;
    $season->id = $_POST['id'];
    $season->name = $_POST['name'];
    $season->start_date = $start_date;
    $season->end_date = $end_date;
    $season->active = ($_POST['active']) ? 1 : 0;
    $season->current_game_day = $_POST['current_game_day'];
    $season->league_id = $_POST['league'];
    
    $db = new DB\Wordpress();
    $season = $db->createOrUpdateSeason($season);
    
    $properties['season_admin'] = false;
    $properties['relegation_markers'] = false;
    if($_POST['season_admin']) {
      $properties['season_admin'] = $_POST['season_admin'];
    }
    if($_POST['relegation_markers']) {
      $properties['relegation_markers'] = $_POST['relegation_markers'];
    }
    if($_POST['relegation_explanation']) {
      $properties['relegation_explanation'] = $_POST['relegation_explanation'];
    }
    if(!empty($properties)) {
      $db->setSeasonProperties($season, $properties);
    }
    
    return $season;
    
  }
  
}
