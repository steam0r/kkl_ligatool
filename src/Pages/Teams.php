<?php

namespace KKL\Ligatool\Pages;

use KKL\Ligatool\DB;
use KKL\Ligatool\Utils\LinkUtils;

class Teams {

  private $db;

  public function __construct() {
    $this->db = new DB\Wordpress();
  }


  /**
   * @param $team_name
   * @return array
   */
  public function getSingleClub($team_name) {
    $club = $this->db->getClubByCode($team_name);

    $seasonTeams = $this->db->getTeamsForClub($club->id);
    $teams = array();
    foreach($seasonTeams as $seasonTeam) {

      $seasonTeam->season = $this->db->getSeason($seasonTeam->season_id);
      $seasonTeam->season->league = $this->db->getLeague($seasonTeam->season->league_id);

      $ranking = $this->db->getRankingForLeagueAndSeasonAndGameDay(
          $seasonTeam->season->league_id, $seasonTeam->season->id, $seasonTeam->season->current_game_day
      );
      $position = 1;
      foreach($ranking as $rank) {
        if($rank->team_id == $seasonTeam->id) {
          $seasonTeam->scores = $rank;
          $seasonTeam->scores->position = $position;
          break;
        }
        $position++;
      }

      $seasonTeam->link = LinkUtils::getLink('clubs', array('pathname' => $club->short_name));
      $seasonTeam->schedule_link = LinkUtils::getLink(
          'schedule', array(
              'league' => $seasonTeam->season->league->code,
              'season' => date(
                  'Y', strtotime($seasonTeam->season->start_date)
              ),
              'team' => $seasonTeam->short_name
          )
      );

      $teams[$seasonTeam->id] = $seasonTeam;
    }

    $currentTeam = $this->db->getCurrentTeamForClub($club->id);
    $currentLocation = $this->db->getLocation($currentTeam->properties['location']);

    return array(
        'club' => $club,
        'teams' => $teams,
        'current_location' => $currentLocation
    );
  }


  public function getAllActiveTeams() {
    $leagues = array();

    foreach($this->db->getActiveLeagues() as $league) {
      $league->season = $this->db->getSeason($league->current_season);
      $league->teams = $this->db->getTeamsForSeason($league->season->id);

      foreach($league->teams as $team) {
        $club = $this->db->getClub($team->club_id);
        if(!$team->logo) {
          $team->logo = $club->logo;
          if(!$club->logo) {
            $team->logo = "";
          }
        } else {
          $team->logo = "/images/team/" . $team->logo;
        }

        $team->link = LinkUtils::getLink('club', array('pathname' => $club->short_name));
      }
      $leagues[] = $league;
    }

    return $leagues;
  }
}