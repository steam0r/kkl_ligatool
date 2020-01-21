<?php

namespace KKL\Ligatool\Pages;

use KKL\Ligatool\DB;
use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Utils\LinkUtils;
use stdClass;

class Ranking {

  /**
   * @param $pageContext
   * @return array
   */
  public function getSingleLeague($pageContext) {
    $rankings = array();

    $teamService = ServiceBroker::getTeamService();
    $clubService = ServiceBroker::getClubService();
    $seasonService = ServiceBroker::getSeasonService();
    $rankingService = ServiceBroker::getRankingService();

    $ranking = new stdClass;
    $ranking->league = $pageContext['league'];
    $ranking->ranks = $rankingService->getRankingForLeagueAndSeasonAndGameDay(
      $pageContext['league']->id,
      $pageContext['season']->id,
      $pageContext['game_day']->number
    );

    foreach ($ranking->ranks as $rank) {
      $team = $teamService->byId($rank->team_id);
      $club = $clubService->byId($team->getClubId());
      $rank->team->link = LinkUtils::getLink('club', array('pathname' => $club->getShortName()));
    }

    $properties = $seasonService->bySeason($pageContext['season']->id);

    if ($properties && array_key_exists('relegation_explanation', $properties)) {
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

    $leagueService = ServiceBroker::getLeagueService();
    $gameDayService = ServiceBroker::getGameDayService();
    $seasonService = ServiceBroker::getSeasonService();
    $rankingService = ServiceBroker::getRankingService();

    foreach ($leagueService->getActive() as $league) {
      $season = $seasonService->byId($league->getCurrentSeason());
      $day = $gameDayService->byId($season->getCurrentGameDay());

      $ranking = new stdClass;
      $ranking->league = $league;
      $ranking->ranks = $rankingService->getRankingForLeagueAndSeasonAndGameDay(
        $league->getId(),
        $season->getId(),
        $day->getNumber()
      );


      $clubService = ServiceBroker::getClubService();
      $teamService = ServiceBroker::getTeamService();

      foreach ($ranking->ranks as $rank) {
        $team = $teamService->byId($rank->team_id);
        $club = $clubService->byId($team->getId());
        $rank->team->link = LinkUtils::getLink('club', array('pathname' => $club->getShortName()));
      }

      $ranking->league->link = LinkUtils::getLink(
        'league', array(
          'league' => $league->getCode(),
          'season' => date('Y', strtotime($season->getStartDate())),
          'game_day' => $day->getNumber()
        )
      );
      $rankings[] = $ranking;
    }

    return $rankings;
  }
}