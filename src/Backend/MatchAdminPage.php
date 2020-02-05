<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\Model\Match;
use KKL\Ligatool\ServiceBroker;

class MatchAdminPage extends AdminPage {

  private $match;
  private $game_day;

  function setup() {
    $this->args = array('page_title' => __('match', 'kkl-ligatool'), 'page_slug' => 'kkl_matches_admin_page', 'parent' => null);
  }

  function display_content() {

    $match = $this->get_item();

    $locationService = ServiceBroker::getLocationService();
    $teamService = ServiceBroker::getTeamService();

    $db_locations = $locationService->getAll();
    $locations = array();
    $locations[0] = __('unknown location', 'kkl-ligatool');
    foreach ($db_locations as $location) {
      $locations[$location->getId()] = $location->getTitle();
    }
    if (!$match->getLocation() && !$_POST['location']) {
      $home = $teamService->byId($match->getHomeTeam());
      if ($home) {
        $match->setLocation($home->getProperty('location'));
      }
    }

    $current_game_day = $this->get_game_day();
    $db_game_days = $this->get_game_days();

    $game_days = array();
    foreach ($db_game_days as $game_day) {
      $game_days[$game_day->getId()] = $game_day->getNumber();
    }

    $teamService = ServiceBroker::getTeamService();
    $db_teams = $teamService->forSeason($current_game_day->getSeasonId());
    $teams = array();
    foreach ($db_teams as $team) {
      $teams[$team->getId()] = $team->getName();
    }

    $matchService = ServiceBroker::getMatchService();
    $db_games = $matchService->byGameDay($current_game_day->getId());
    $games = array();
    $games[0] = __('back to overview', 'kkl-ligatool');
    $games[1] = __('create new game', 'kkl-ligatool');
    foreach ($db_games as $game) {
      $home = $teamService->byId($game->getHomeTeam());
      $away = $teamService->byId($game->getAwayTeam());
      $games[$game->getId()] = $home->getName() . " - " . $away->getName();
    }

    $final_checked = ($match->getStatus() == 3);
    if ($this->errors && $_POST['final_score']) {
      $final_checked = true;
    }

    $fixture = $this->cleanDate($match->getFixture());
    echo $this->form_table(
      array(
        array(
          'type' => 'hidden',
          'name' => 'id',
          'value' => $match->getId()
        ),
        array(
          'title' => __('game_day', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'game_day',
          'value' => $game_days,
          'selected' => ($this->errors) ? $_POST['game_day'] : $current_game_day->getId()
        ),
        array(
          'title' => __('fixture', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'fixture',
          'value' => $fixture,
          'extra' => array('class' => "pickfixture")
        ),
        array(
          'title' => __('team_home', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'team_home',
          'value' => $teams,
          'selected' => ($this->errors) ? $_POST['team_home'] : $match->getHomeTeam()
        ),
        array(
          'title' => __('team_away', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'team_away',
          'value' => $teams, 'selected' => ($this->errors) ? $_POST['team_away'] : $match->getAwayTeam()
        ),
        array(
          'title' => __('location', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'location',
          'value' => $locations,
          'selected' => ($this->errors) ? $_POST['location'] : $match->getLocation()
        ),
        array(
          'title' => __('goals_home', 'kkl-ligatool'),
          'type' => 'select', 'name' => 'goals_home',
          'value' => $this->get_goals_array(),
          'selected' => ($this->errors) ? $_POST['goals_home'] : $match->goals_home
        ),
        array(
          'title' => __('goals_away', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'goals_away',
          'value' => $this->get_goals_array(),
          'selected' => ($this->errors) ? $_POST['goals_away'] : $match->goals_away
        ),
        array(
          'title' => __('score_home', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'score_home',
          'value' => $this->get_score_array(),
          'selected' => ($this->errors) ? $_POST['score_home'] : $match->getScoreHome()
        ),
        array(
          'title' => __('score_away', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'score_away',
          'value' => $this->get_score_array(),
          'selected' => ($this->errors) ? $_POST['score_away'] : $match->getScoreAway()
        ),
        array(
          'title' => __('final_score', 'kkl-ligatool'),
          'type' => 'checkbox',
          'name' => 'final_score',
          'checked' => $final_checked
        ),
        array(
          'title' => __('description', 'kkl-ligatool'),
          'type' => 'textarea',
          'name' => 'description',
          'value' => ($this->errors) ? $_POST['description'] : $match->getNotes(),
          'extra' => array('rows' => 7, 'cols' => 100)
        ),
        array(
          'title' => __('next_game', 'kkl-ligatool'),
          'type' => 'select', 'name' => 'next_game',
          'value' => $games,
          'selected' => ($this->errors) ? $_POST['next_game'] : null
        )
      )
    );
  }

  function get_item() {
    if ($this->match)
      return $this->match;
    if ($_GET['id']) {
      $matchService = ServiceBroker::getMatchService();
      $this->match = $matchService->byId($_GET['id']);
    } else {
      $this->setItem(new Match());
    }
    return $this->match;
  }

  function get_game_day() {
    if ($this->game_day)
      return $this->game_day;

    $gameDayService = ServiceBroker::getGameDayService();

    $item = $this->get_item();
    if ($item) {
      $this->game_day = $gameDayService->byId($item->getGameDayId());
    } elseif ($_GET['gameDayId']) {
      $this->game_day = $gameDayService->byId($_GET['gameDayId']);
    }
    return $this->game_day;
  }

  function get_game_days() {
    $gameDayService = ServiceBroker::getGameDayService();
    return $gameDayService->allForSeason($this->get_game_day()->getSeasonId());
  }

  function get_goals_array() {
    $goals = array();
    for ($i = 0; $i <= 100; $i++) {
      $goals[$i] = $i;
    }
    return $goals;
  }

  function get_score_array() {
    $score = array();
    for ($i = 0; $i <= 20; $i++) {
      $score[$i] = $i;
    }
    return $score;
  }

  function validate($new_data, $old_data) {
    $errors = array();

    if (!$new_data['game_day'])
      $errors['game_day'] = true;
    if (!$new_data['team_home'])
      $errors['team_home'] = true;
    if (!$new_data['team_away'])
      $errors['team_away'] = true;

    return $errors;
  }

  function save() {

    $service = ServiceBroker::getMatchService();
    $match = $service->getOrCreate($_POST['id']);
    $match->setGameDayId($_POST['game_day']);
    $match->setFixture($this->cleanDate($_POST['fixture']));
    $match->setHomeTeam($_POST['team_home']);
    $match->setAwayTeam($_POST['team_away']);
    $match->setLocation($_POST['location']);
    $match->setGoalsHome($_POST['goals_home']);
    $match->setGoalsAway($_POST['goals_away']);
    $match->setScoreHome($_POST['score_home']);
    $match->setScoreAway($_POST['score_away']);
    $match->setNotes($_POST['description']);
    if ($_POST['final_score']) {
      $match->setStatus(3);
    } else {
      $match->setStatus(-1);
    }

    $this->match = $service->createOrUpdate($match);
    return $this->match;

  }

  function redirect_after_save() {
    $next_game = $_POST['next_game'];
    if ($next_game && $next_game > 1) {
      $page = menu_page_url("kkl_matches_admin_page", false);
      $page = $page . "&id=" . $next_game;
    } elseif ($next_game && $next_game == 1) {
      $page = menu_page_url("kkl_matches_admin_page", false);
      $page = $page . "&gameDayId=" . $this->match->game_day_id;
    } else {
      $page = menu_page_url("kkl_ligatool_matches", false);
      $page = $page . "&game_day_filter=" . $this->match->game_day_id;
    }

    wp_redirect($page);
    exit();
  }

}
