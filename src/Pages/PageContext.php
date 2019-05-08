<?php

namespace KKL\Ligatool\Pages;

use KKL\Ligatool\Plugin;
use KKL\Ligatool\DB;
use stdClass;

class PageContext {

  private $db;

  public function __construct() {
    $this->db = new DB\Wordpress();
  }


  /**
   * @return array
   */
  public function getAllActiveLeagues() {
    $rankings = array();

    foreach($this->db->getActiveLeagues() as $league) {
      $season = $this->db->getSeason($league->current_season);
      $day = $this->db->getGameDay($season->current_game_day);

      $ranking = new stdClass;
      $ranking->league = $league;
      $ranking->ranks = $this->db->getRankingForLeagueAndSeasonAndGameDay($league->id, $season->id, $day->number);

      foreach($ranking->ranks as $rank) {
        $team = $this->db->getTeam($rank->team_id);
        $club = $this->db->getClub($team->club_id);
        $rank->team->link = Plugin::getLink('club', array('club' => $club->short_name));
      }

      $ranking->league->link = Plugin::getLink(
          'league', array(
              'league' => $league->code,
              'season' => date('Y', strtotime($season->start_date)),
              'game_day' => $day->number
          )
      );
      $rankings[] = $ranking;
    }

    return $rankings;
  }


  /**
   * @param $league
   * @param $season
   * @param $game_day
   * @return array
   */
  public function getLeagueAndSeasonAndGameDay($league, $season, $game_day) {
    $data = array();

    $league = $this->db->getLeagueBySlug($league);
    $season = $this->db->getSeasonByLeagueAndYear($league->id, $season);
    $game_day = $this->db->getGameDayBySeasonAndPosition($season->id, $game_day);

    $data['league'] = $league;
    $data['season'] = $season;
    $data['game_day'] = $game_day;

    $rankings = array();

    $ranking = new stdClass;
    $ranking->league = $data['league'];
    $ranking->ranks = $this->db->getRankingForLeagueAndSeasonAndGameDay(
        $data['league']->id,
        $data['season']->id,
        $data['game_day']->number
    );

    foreach($ranking->ranks as $rank) {
      $team = $this->db->getTeam($rank->team_id);
      $club = $this->db->getClub($team->club_id);
      $rank->team->link = Plugin::getLink('club', array('club' => $club->short_name));
    }

    $properties = $this->db->getSeasonProperties($data['season']->id);

    if($properties && array_key_exists('relegation_explanation', $properties)) {
      $ranking->relegation_explanation = $properties['relegation_explanation'];
    }

    $rankings[] = $ranking;

    return $rankings;
  }


  /**
   * @param $league
   * @param $season
   * @return array
   */
  public function getLeagueAndSeason($league, $season) {
    $data = array();

    $league = $this->db->getLeagueBySlug($league);
    $season = $this->db->getSeasonByLeagueAndYear($league->id, $season);
    $game_day = $this->db->getGameDay($season->current_game_day);

    $data['league'] = $league;
    $data['season'] = $season;
    $data['game_day'] = $game_day;

    return $this->generateRanking($data);
  }


  /**
   * @param $league
   * @return array
   */
  public function getLeague($league) {
    $data = array();

    $league = $this->db->getLeagueBySlug($league);
    $season = $this->db->getSeason($league->current_season);
    $game_day = $this->db->getGameDay($season->current_game_day);

    $data['league'] = $league;
    $data['season'] = $season;
    $data['game_day'] = $game_day;

    return $this->generateRanking($data);
  }


  /**
   * @param $data
   * @return array
   */
  private function generateRanking($data) {
    $rankings = array();

    $ranking = new stdClass;
    $ranking->league = $data['league'];
    $ranking->ranks = $this->db->getRankingForLeagueAndSeasonAndGameDay(
        $data['league']->id,
        $data['season']->id,
        $data['game_day']->number
    );

    foreach($ranking->ranks as $rank) {
      $team = $this->db->getTeam($rank->team_id);
      $club = $this->db->getClub($team->club_id);
      $rank->team->link = Plugin::getLink('club', array('club' => $club->short_name));
    }

    $properties = $this->db->getSeasonProperties($data['season']->id);

    if($properties && array_key_exists('relegation_explanation', $properties)) {
      $ranking->relegation_explanation = $properties['relegation_explanation'];
    }

    $rankings[] = $ranking;

    return $rankings;
  }
}