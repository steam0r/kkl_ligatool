<?php

namespace KKL\Ligatool\Pages;

use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Utils\LinkUtils;

class Teams {

  /**
   * @param $team_name
   * @return array
   */
  public function getSingleClub($team_name) {

    $clubService = ServiceBroker::getClubService();
    $teamService = ServiceBroker::getTeamService();
    $seasonService = ServiceBroker::getSeasonService();
    $leagueService = ServiceBroker::getLeagueService();
    $rankingService = ServiceBroker::getRankingService();

    $club = $clubService->byCode($team_name);

    $seasonTeams = $teamService->byClub($club);
    $teams = array();
    foreach ($seasonTeams as $seasonTeam) {

      $seasonTeam->season = $seasonService->byId($seasonTeam->getSeasonId());
      $seasonTeam->season->league = $leagueService->byId($seasonTeam->season->getLeagueId());

      $ranking = $rankingService->getRankingForLeagueAndSeasonAndGameDay(
        $seasonTeam->season->getLeagueId(),
        $seasonTeam->season->getId(),
        $seasonTeam->season->getCurrentGameDay()
      );
      $position = 1;
      foreach ($ranking as $rank) {
        if ($rank->team_id == $seasonTeam->getId()) {
          $seasonTeam->scores = $rank;
          $seasonTeam->scores->position = $position;
          break;
        }
        $position++;
      }

      $seasonTeam->link = LinkUtils::getLink('clubs', array('pathname' => $club->getShortName()));
      $seasonTeam->schedule_link = LinkUtils::getLink(
        'schedule', array(
          'league' => $seasonTeam->season->league->code,
          'season' => date(
            'Y', strtotime($seasonTeam->season->getStartDate())
          ),
          'team' => $seasonTeam->getShortName()
        )
      );

      $teams[$seasonTeam->getId()] = $seasonTeam;
    }

    $currentTeam = $teamService->getCurrentTeamForClub($club->id);

    $locationService = ServiceBroker::getLocationService();
    $currentLocation = $locationService->byId($currentTeam->properties['location']);

    return array(
      'club' => $club,
      'teams' => $teams,
      'current_location' => $currentLocation
    );
  }


  public function getAllActiveTeams() {
    $leagues = array();

    $seasonService = ServiceBroker::getSeasonService();
    $leagueService = ServiceBroker::getLeagueService();
    $teamService = ServiceBroker::getTeamService();
    $clubService = ServiceBroker::getClubService();

    foreach ($leagueService->getActive() as $league) {
      $league->season = $seasonService->byId($league->getCurrentSeason());
      $leagueService = ServiceBroker::getLeagueService();
      $league->teams = $leagueService->bySeason($league->season->getId());

      foreach ($league->teams as $team) {
        $club = $clubService->byId($team->club_id);
        if (!$team->logo) {
          $team->logo = $club->getLogo();
          if (!$club->getLogo) {
            $team->logo = "";
          }
        } else {
          $team->logo = "/images/team/" . $team->logo;
        }

        $team->link = LinkUtils::getLink('club', array('pathname' => $club->getShortName()));
      }
      $leagues[] = $league;
    }

    return $leagues;
  }
}