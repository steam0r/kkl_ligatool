<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\DB\OrderBy;
use KKL\Ligatool\Model\Team;
use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Template\Service;

class TeamAdminPage extends AdminPage {

  function setup() {
    $this->args = array(
      'page_title' => __('team', 'kkl-ligatool'),
      'page_slug' => 'kkl_teams_admin_page',
      'parent' => null
    );
  }

  public static function display_tabs() {
    $tabs = array(
      'kkl_ligatool_leagues' => __('leagues', 'kkl-ligatool'),
      'kkl_ligatool_seasons' => __('seasons', 'kkl-ligatool'),
      'kkl_ligatool_gamedays' => __('game_days', 'kkl-ligatool'),
      'kkl_ligatool_matches' => __('matches', 'kkl-ligatool'),
      'kkl_ligatool_clubs' => __('clubs', 'kkl-ligatool'),
      'kkl_ligatool_teams' => __('teams', 'kkl-ligatool'),
      'kkl_ligatool_players' => __('players', 'kkl-ligatool'),
      'kkl_ligatool_locations' => __('locations', 'kkl-ligatool'),
      'kkl_ligatool_stats' => __('stats', 'kkl-ligatool'),
      'kkl_ligatool_settings' => __('settings', 'kkl-ligatool'),
    );

    $current = null;
    if (isset($_GET['page'])) {
      $current = $_GET['page'];
    }

    $kkl_twig = Service::getTemplateEngine();
    echo $kkl_twig->render('admin/navbar.twig', array(
      "navitems" => $tabs,
      "active" => $current
    ));
  }

  function display_content() {

    $team = $this->get_item();

    $seasonService = ServiceBroker::getSeasonService();
    $clubService = ServiceBroker::getClubService();

    $seasons = $seasonService->getAll(new OrderBy('start_date', 'ASC'));
    $season_options = array("" => __('please_select', 'kkl-ligatool'));
    foreach ($seasons as $season) {
      $season_options[$season->getId()] = $season->getName();
    }

    $clubs = $clubService->getAll();
    $club_options = array("" => __('please_select', 'kkl-ligatool'));
    foreach ($clubs as $club) {
      $club_options[$club->getId()] = $club->getName();
    }

    $service = ServiceBroker::getPlayerService();
    $players = $service->getAll(new OrderBy('first_name', 'ASC'));
    $captain_options = array("" => __('please_select', 'kkl-ligatool'));
    foreach ($players as $player) {
      $captain_options[$player->getId()] = $player->getFirstName() . " " . $player->getLastName() . " (" . $player->getEmail() . ")";
    }

    $locationService = ServiceBroker::getLocationService();
    $locations = $locationService->getAll();
    $location_options = array("" => __('please_select', 'kkl-ligatool'));
    foreach ($locations as $location) {
      $location_options[$location->getId()] = $location->getTitle();
    }

    $leaguewinner_checked = false;
    $leagueWinnerProperty = $team->getProperty('current_league_winner');
    if ($leagueWinnerProperty) {
      $leaguewinner_checked = $leagueWinnerProperty->getValue();
    }
    if ($this->errors && $_POST['current_league_winner']) {
      $leaguewinner_checked = true;
    }

    $cupwinner_checked = false;
    $cupWinnerProperty = $team->getProperty('current_cup_winner');
    if ($cupWinnerProperty) {
      $cupwinner_checked = $cupWinnerProperty->getValue();
    }
    if ($this->errors && $_POST['current_cup_winner']) {
      $cupwinner_checked = true;
    }

    $teamLocation = "";
    $locationProperty = $team->getProperty('location');
    if ($locationProperty) {
      $teamLocation = $locationProperty->getValue();
    }

    $teamCaptain = "";
    $captainProperty = $team->getProperty('captain');
    if ($captainProperty) {
      $teamCaptain = $captainProperty->getValue();
    }

    $teamViceCaptain = "";
    $viceCaptainProperty = $team->getProperty('vice_captain');
    if ($viceCaptainProperty) {
      $teamViceCaptain = $viceCaptainProperty->getValue();
    }

    echo $this->form_table(
      array(
        array(
          'type' => 'hidden',
          'name' => 'id',
          'value' => $team->getId()
        ),
        array(
          'title' => __('name', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'name',
          'value' => ($this->errors) ? $_POST['name'] : $team->getName(),
          'extra' => ($this->errors['name']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('url_code', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'short_name',
          'value' => ($this->errors) ? $_POST['short_name'] : $team->getShortName(),
          'extra' => ($this->errors['short_name']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('season', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'season',
          'choices' => $season_options,
          'selected' => ($this->errors) ? $_POST['season'] : $team->getSeasonId(),
          'extra' => ($this->errors['season']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('club', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'club',
          'choices' => $club_options,
          'selected' => ($this->errors) ? $_POST['club'] : $team->getClubId(),
          'extra' => ($this->errors['club']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('location', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'location',
          'choices' => $location_options,
          'selected' => ($this->errors) ? $_POST['location'] : $teamLocation
        ),
        array(
          'title' => __('captain', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'captain',
          'choices' => $captain_options,
          'selected' => ($this->errors) ? $_POST['captain'] : $teamCaptain,
          'extra' => ($this->errors['captain']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('vice-captain', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'vice_captain',
          'choices' => $captain_options,
          'selected' => ($this->errors) ? $_POST['vice_captain'] : $teamViceCaptain,
          'extra' => ($this->errors['vice_captain']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('current_league_winner', 'kkl-ligatool'),
          'type' => 'checkbox',
          'name' => 'current_league_winner',
          'checked' => $leaguewinner_checked
        ),
        array(
          'title' => __('current_cup_winner', 'kkl-ligatool'),
          'type' => 'checkbox',
          'name' => 'current_cup_winner',
          'checked' => $cupwinner_checked
        )
      )
    );
  }

  /**
   * @return Team|null
   */
  function get_item() {
    if ($this->item)
      return $this->item;
    if ($_GET['id']) {
      $teamService = ServiceBroker::getTeamService();
      $this->setItem($teamService->byId($_GET['id']));
    } else {
      $this->setItem(new Team());
    }
    return $this->item;

  }

  function validate($new_data, $old_data) {
    $errors = array();

    if (!$new_data['name'])
      $errors['name'] = true;
    if (!$new_data['short_name'])
      $errors['short_name'] = true;
    if (!$new_data['season'])
      $errors['season'] = true;
    if (!$new_data['club'])
      $errors['club'] = true;

    return $errors;
  }

  function save() {

    $team = new Team();
    $team->setId($_POST['id']);
    $team->setName($_POST['name']);
    $team->setShortName($_POST['short_name']);
    $team->setSeasonId($_POST['season']);
    $team->setClubId($_POST['club']);

    $service = ServiceBroker::getTeamService();
    $team = $service->createOrUpdate($team);

    $properties = array();
    $properties['location'] = false;
    $properties['current_league_winner'] = false;
    $properties['current_cup_winner'] = false;
    $properties['captain'] = false;
    $properties['vice_captain'] = false;
    if ($_POST['location'])
      $properties['location'] = $_POST['location'];
    if ($_POST['captain'])
      $properties['captain'] = $_POST['captain'];
    if ($_POST['vice_captain'])
      $properties['vice_captain'] = $_POST['vice_captain'];
    if ($_POST['current_league_winner'])
      $properties['current_league_winner'] = "true";
    if ($_POST['current_cup_winner'])
      $properties['current_cup_winner'] = "true";

    if (!empty($properties)) {
      $teamPropertyService = ServiceBroker::getTeamPropertyService();
      $teamPropertyService->setTeamProperties($team, $properties);
    }

    return $service->byId($team->getId());

  }

}
