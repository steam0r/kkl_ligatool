<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\Model\Season;
use KKL\Ligatool\ServiceBroker;

class SeasonAdminPage extends AdminPage {

  function setup() {
    $this->args = array(
      'page_title' => __('season', 'kkl-ligatool'),
      'page_slug' => 'kkl_seasons_admin_page',
      'parent' => null
    );
  }

  function display_content() {

    $season = $this->get_item();

    $leagueService = ServiceBroker::getLeagueService();
    $leagues = $leagueService->getAll();
    $league_options = array();
    foreach ($leagues as $league) {
      $league_options[$league->getId()] = $league->getName();
    }

    $gameDayService = ServiceBroker::getGameDayService();
    $days = $gameDayService->allForSeason($season->getId());
    $day_options = array("" => __('please_select', 'kkl-ligatool'));
    foreach ($days as $day) {
      $day_options[$day->getId()] = 'Spieltag ' . $day->getNumber();
    }

    $playerService = ServiceBroker::getPlayerService();
    $admins = $playerService->getLeagueAdmins();
    $contact_options = array("" => __('please_select', 'kkl-ligatool'));
    foreach ($admins as $admin) {
      $contact_options[$admin->getId()] = $admin->getFirstName() . " " . $admin->getLastName();
    }

    $active_checked = $season->isActive();
    if ($this->errors && $_POST['active']) {
      $active_checked = true;
    }

    $seasonAdmin = null;
    $relegationMarkers = null;
    $relegationExplanation = null;

    $adminProperty = $season->getProperty('season_admin');
    if ($adminProperty) $seasonAdmin = $adminProperty->getValue();

    $relegationProperty = $season->getProperty('relegation_markers');
    if ($relegationProperty) $relegationMarkers = $relegationProperty->getValue();

    $explanationProperty = $season->getProperty('relegation_explanation');
    if ($explanationProperty) $relegationExplanation = $explanationProperty->getValue();

    echo $this->form_table(
      array(
        array(
          'type' => 'hidden',
          'name' => 'id',
          'value' => $season->getId()
        ),
        array(
          'title' => __('name', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'name',
          'value' => ($this->errors) ? $_POST['name'] : $season->getName(),
          'extra' => ($this->errors['name']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('league', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'league',
          'choices' => $league_options,
          'selected' => ($this->errors) ? $_POST['league'] : $season->getLeagueId(),
          'extra' => ($this->errors['league']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('start_date', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'start_date',
          'value' => $season->getStartDate(),
          'extra' => array('class' => 'datetimepicker')
        ),
        array(
          'title' => __('end_date', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'end_date',
          'value' => $season->getEndDate(),
          'extra' => array('class' => 'datetimepicker')
        ),
        array(
          'title' => __('current_game_day', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'current_game_day',
          'choices' => $day_options,
          'selected' => $season->getCurrentGameDay()
        ),
        array(
          'title' => __('active', 'kkl-ligatool'),
          'type' => 'checkbox',
          'name' => 'active',
          'checked' => $active_checked
        ),
        array(
          'title' => __('season_admin', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'season_admin',
          'choices' => $contact_options,
          'selected' => ($this->errors) ? $_POST['season_admin'] : $seasonAdmin,
          'extra' => ($this->errors['season_admin']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => "Auf- und Abstiegslinien<br/>(unterhalb von)",
          'type' => 'text',
          'name' => 'relegation_markers',
          'value' => ($this->errors) ? $_POST['relegation_markers'] : $relegationMarkers,
          'extra' => ($this->errors['relegation_markers']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => "Auf- und Abstiegsregeln",
          'type' => 'textarea',
          'name' => 'relegation_explanation',
          'value' => ($this->errors) ? $_POST['relegation_explanation'] : $relegationExplanation,
          'extra' => ($this->errors['relegation_explanation']) ? array('rows' => 7, 'cols' => 50, 'style' => "border-color: red;") : array('rows' => 7, 'cols' => 50)
        )
      )
    );
  }

  /**
   * @return Season
   */
  function get_item() {
    if ($this->item)
      return $this->item;
    if ($_GET['id']) {
      $seasonService = ServiceBroker::getSeasonService();
      $this->setItem($seasonService->byId($_GET['id']));
    } else {
      $this->setItem(new Season());
    }
    return $this->item;

  }

  function validate($new_data, $old_data) {
    $errors = array();

    if (!$new_data['name'])
      $errors['name'] = true;
    if (!$new_data['league'])
      $errors['league'] = true;

    return $errors;
  }

  function save() {

    $start_date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $_POST['start_date'])));
    $end_date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $_POST['end_date'])));

    $service = ServiceBroker::getPlayerService();
    $season = $service->getOrCreate($_POST['id']);
    $season->setName($_POST['name']);
    $season->setStartDate($start_date);
    $season->setEndDate($end_date);
    $season->setActive(($_POST['active']) ? 1 : 0);
    $season->setCurrentGameDay($_POST['current_game_day']);
    $season->setLeagueId($_POST['league']);

    $season = $service->createOrUpdate($season);

    $properties['season_admin'] = false;
    $properties['relegation_markers'] = false;
    if ($_POST['season_admin']) {
      $properties['season_admin'] = $_POST['season_admin'];
    }
    if ($_POST['relegation_markers']) {
      $properties['relegation_markers'] = $_POST['relegation_markers'];
    }
    if ($_POST['relegation_explanation']) {
      $properties['relegation_explanation'] = $_POST['relegation_explanation'];
    }
    if (!empty($properties)) {
      $seasonPropertyService = ServiceBroker::getSeasonPropertyService();
      $seasonPropertyService->setSeasonProperties($season, $properties);
    }

    return $season;

  }

}
