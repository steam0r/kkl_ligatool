<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:43
 */

namespace KKL\Ligatool\Services;


use Exception;
use KKL\Ligatool\DB\Where;
use KKL\Ligatool\Model\Match;
use KKL\Ligatool\ServiceBroker;

class MatchService extends KKLModelService {

  /**
   * @return Match
   */
  public function getModel() {
    return new Match();
  }

  /**
   * @param int $id
   * @return Match|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return Match[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return Match[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return Match|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }

  public function byGameDay($gameDayId) {
    return $this->find(new Where("game_day_id", $gameDayId, '='));
  }


  /**
   * @return Match[]
   */
  public function getAllUpcomingMatches() {

    $leagueService = ServiceBroker::getLeagueService();
    $seasonService = ServiceBroker::getSeasonService();
    $gameDayService = ServiceBroker::getGameDayService();
    $matchService = ServiceBroker::getMatchService();
    $teamService = ServiceBroker::getTeamService();

    $activeLeagues = $leagueService->getActive();
    $allMatches = [];
    foreach ($activeLeagues as $activeLeague) {
      $currentSeason = $seasonService->currentByLeague($activeLeague->getId());
      $currentGameDay = $gameDayService->currentForSeason($currentSeason->getId());
      $matches = $matchService->byGameDay($currentGameDay->getId());
      foreach ($matches as $match) {
        $home = $teamService->byId($match->getHomeTeam());
        $away = $teamService->byId($match->getAwayTeam());
        // TODO: use some kind of DTO
        $match->homename = $home->getName();
        $match->awayname = $away->getName();
        $allMatches[] = $match;
      }
    }
    return $allMatches;

  }

  /**
   * @param $league_id
   * @return Match[]
   */
  public function getUpcomingMatches($league_id) {

    $leagueService = ServiceBroker::getLeagueService();
    $seasonService = ServiceBroker::getSeasonService();
    $gameDayService = ServiceBroker::getGameDayService();
    $matchService = ServiceBroker::getMatchService();
    $teamService = ServiceBroker::getTeamService();

    $activeLeague = $leagueService->byId($league_id);
    $allMatches = [];
    $currentSeason = $seasonService->currentByLeague($activeLeague->getId());
    $currentGameDay = $gameDayService->currentForSeason($currentSeason->getId());
    $matches = $matchService->byGameDay($currentGameDay->getId());
    foreach ($matches as $match) {
      $home = $teamService->byId($match->getHomeTeam());
      $away = $teamService->byId($match->getAwayTeam());
      // TODO: use some kind of DTO
      $match->homename = $home->getName();
      $match->awayname = $away->getName();
      $allMatches[] = $match;
    }
    return $allMatches;

  }

  /**
   * @param $teamid
   * @return Match[]
   */
  public function getMatchesForTeam($teamid) {

    $homeMatches = $this->find(
      new Where('home_team', $teamid)
    );

    $awayMatches = $this->find(
      new Where('away_team', $teamid)
    );

    $matches = array_merge($homeMatches, $awayMatches);
    $teamService = ServiceBroker::getTeamService();
    $allMatches = [];
    foreach ($matches as $match) {
      $home = $teamService->byId($match->getHomeTeam());
      $away = $teamService->byId($match->getAwayTeam());
      // TODO: use some kind of DTO
      $match->homename = $home->getName();
      $match->awayname = $away->getName();
      $allMatches[] = $match;
    }
    return $allMatches;
  }


  /**
   * @return Match[]
   */
  public function getAllMatchesForNextGameday() {

    $leagueService = ServiceBroker::getLeagueService();
    $seasonService = ServiceBroker::getSeasonService();
    $gameDayService = ServiceBroker::getGameDayService();
    $matchService = ServiceBroker::getMatchService();
    $teamService = ServiceBroker::getTeamService();

    $allMatches = [];
    foreach ($leagueService->getActive() as $league) {
      $season = $seasonService->currentByLeague($league->getId());
      $currentDay = $gameDayService->byId($season->getCurrentGameDay());
      $nextDay = $gameDayService->getNext($currentDay);
      foreach ($matchService->byGameDay($nextDay) as $match) {
        $home = $teamService->byId($match->getHomeTeam());
        $away = $teamService->byId($match->getAwayTeam());
        // TODO: use some kind of DTO
        $match->homename = $home->getName();
        $match->awayname = $away->getName();
        $match->awayid = $away->getId();
        $match->homeid = $away->getId();
        $match->league_id = $league->getId();
        $allMatches[] = $match;
      }
    }
    return $allMatches;
  }

  /**
   * @param Match[] $matches
   * @return array
   */
  public function topMatches($matches) {

    $seasonService = ServiceBroker::getSeasonService();
    $leagueService = ServiceBroker::getLeagueService();
    $gameDayService = ServiceBroker::getGameDayService();
    $rankingService = ServiceBroker::getRankingService();
    $teamService = ServiceBroker::getTeamService();

    $seasons = array();
    $days = array();
    foreach ($matches as $match) {
      $gameDay = $gameDayService->byId($match->getGameDayId());
      $season = $seasonService->byId($gameDay->getSeasonId());
      $league = $leagueService->byId($season->getLeagueId());
      $days[$gameDay->getSeasonId()] = $gameDay->getNumber();
      $seasons[$gameDay->getSeasonId()] = $league;
    }

    $potentialMatches = array();
    foreach ($days as $season => $day) {
      $matches = $this->byGameDay($day->getId());
      $league = $seasons[$season];
      $ranking = $rankingService->getRankingForLeagueAndSeasonAndGameDay($league->getId(), $season, $day);
      $ranks = $ranking->getRanks();
      $first = $ranks[0];
      $second = $ranks[1];
      $lastButOne = $ranks[count($ranks) - 2];
      $last = $ranks[count($ranks) - 1];
      $potentialMatches[$league->getName()] = array(
        "top" => array(
          $first->getTeamId(),
          $second->getTeamId()
        ),
        "bottom" => array(
          $lastButOne->getTeamId(),
          $last->getTeamId()
        )
      );
    }

    $topMatches = array();
    foreach ($matches as $match) {
      $gameDay = $gameDayService->byId($match->getGameDayId());
      $season = $seasonService->byId($gameDay->getSeasonId());
      $league = $leagueService->byId($season->getLeagueId());
      $home = $teamService->byId($match->getHomeTeam());
      $away = $teamService->byId($match->getAwayTeam());
      if (in_array($home, $potentialMatches[$league]['top']) && in_array($away, $potentialMatches[$league]['top'])) {
        $topMatches[$league] = array("type" => "top", "home" => $home->getName(), "away" => $away->getName(), "leaguecode" => $league->getCode(), "contact" => $match->slack);
      }
      if (in_array($home, $potentialMatches[$league]['bottom']) && in_array($away, $potentialMatches[$league]['bottom'])) {
        $topMatches[$league] = array("type" => "bottom", "home" => $home->getName(), "away" => $away->getName(), "leaguecode" => $league->getCode(), "contact" => $match->slack);
      }
    }
    return $topMatches;
  }

  /**
   * @param int $days_off
   * @return Match[]
   * @throws Exception
   */
  public function reminderMatches($days_off) {

    $leagueService = ServiceBroker::getLeagueService();
    $matchService = ServiceBroker::getMatchService();
    $seasonService = ServiceBroker::getSeasonService();
    $gameDayService = ServiceBroker::getGameDayService();

    $allMatches = [];
    foreach ($leagueService->getActive() as $league) {
      $season = $seasonService->currentByLeague($leagueService);
      $currentDay = $gameDayService->byId($season->getCurrentGameDay());
      $nextDay = $gameDayService->getNext($currentDay);
      foreach ($matchService->byGameDay($nextDay) as $match) {
        $offDate = new \DateTime($match->getFixture());
        $offDate->modify('+' . $days_off . ' day');

        $matchDate = new \DateTime($match->getFixture());
        if ($matchDate->format('Y-m-d') === $offDate->format('Y-m-d')) {
          $allMatches = $match;
        }
      }
    }
    return $allMatches;
  }

}