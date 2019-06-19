<?php

namespace KKL\Ligatool\Pages;

use KKL\Ligatool\DB;
use KKL\Ligatool\Utils\LinkUtils;
use stdClass;

class Ranking {

  private $db;

  public function __construct() {
    $this->db = new DB\Wordpress();
  }


  /**
   * @param $pageContext
   * @return array
   */
  public function getSingleLeague($pageContext) {
    $rankings = array();

    $ranking = new stdClass;
    $ranking->league = $pageContext['league'];
    $ranking->ranks = $this->db->getRankingForLeagueAndSeasonAndGameDay(
        $pageContext['league']->id,
        $pageContext['season']->id,
        $pageContext['game_day']->number
    );

    foreach($ranking->ranks as $rank) {
      $team = $this->db->getTeam($rank->team_id);
      $club = $this->db->getClub($team->club_id);
      $rank->team->link = LinkUtils::getLink('club', array('pathname' => $club->short_name));
    }

    $properties = $this->db->getSeasonProperties($pageContext['season']->id);

    if($properties && array_key_exists('relegation_explanation', $properties)) {
      $ranking->relegation_explanation = $properties['relegation_explanation'];
    }

    $rankings[] = $ranking;

    return $rankings;
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
        $rank->team->link = LinkUtils::getLink('club', array('pathname' => $club->short_name));
      }

      $ranking->league->link = LinkUtils::getLink(
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
}