<?php


namespace KKL\Ligatool\Model;

use KKL\Ligatool\ServiceBroker;

class UrlContext {

  /**
   * @var Team
   */
  private $team;

  /**
   * @var Club
   */
  private $club;

  /**
   * @var League
   */
  private $league;

  /**
   * @var Season
   */
  private $season;

  /**
   * @var GameDay
   */
  private $gameDay;

  public function __construct() {
    $team_name = get_query_var('team_name');
    $leagueSlug = get_query_var('league');
    $seasonYear = get_query_var('season');
    $gameDayNumber = get_query_var('game_day');
    $clubSlug = get_query_var('team');

    $this->team = ServiceBroker::getTeamService()->byName($team_name);
    $this->club = ServiceBroker::getClubService()->byCode($clubSlug);
    $this->league = ServiceBroker::getLeagueService()->bySlug($leagueSlug);
    $this->season = null;
    $this->gameDay = null;
    if ($this->league) {
      $this->season = ServiceBroker::getSeasonService()->byLeagueAndYear($league, $seasonYear);
      if ($this->season) {
        $this->gameDay = ServiceBroker::getGameDayService()->bySeasonAndPosition($season, $gameDayNumber);
      }
    }
  }

  /**
   * @return Team|null
   */
  public function getTeam() {
    return $this->team;
  }

  /**
   * @return Club|null
   */
  public function getClub() {
    return $this->club;
  }

  /**
   * @return League|null
   */
  public function getLeague() {
    return $this->league;
  }

  /**
   * @return Season|null
   */
  public function getSeason() {
    return $this->season;
  }

  /**
   * @return GameDay|null
   */
  public function getGameDay() {
    return $this->gameDay;
  }
}