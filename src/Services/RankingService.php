<?php


namespace KKL\Ligatool\Services;


use Closure;
use KKL\Ligatool\DB\Where;
use KKL\Ligatool\DB\Wordpress;
use KKL\Ligatool\Model\Match;
use KKL\Ligatool\Model\Rank;
use KKL\Ligatool\Model\Ranking;
use KKL\Ligatool\Model\Team;
use KKL\Ligatool\Model\TeamScore;
use KKL\Ligatool\ServiceBroker;

class RankingService {

  public static $RANKING_MODEL_SCORE_GAME_DIFF = 0;

  public function getRankingModelForSeason($seasonId) {
    // TODO: maybe store this in database on a per season base, to make other sorting possible
    return self::$RANKING_MODEL_SCORE_GAME_DIFF;
  }

  /**
   * @param $model int
   * @return Closure
   */
  public function getRankingModelSortingFunction($model) {
    switch ($model) {
      case self::$RANKING_MODEL_SCORE_GAME_DIFF:
      default:
        $sortingFunction = function (array $unsorted) {
          uasort($unsorted, function (Rank $first, Rank $second) {
            if ($first->getScore() == $second->getScore()) {
              if ($first->getGameDiff() == $second->getGameDiff()) {
                return 0;
              }
              return ($first->getGameDiff() > $second->getGameDiff()) ? -1 : 1;
            }
            return ($first->getScore() > $second->getScore()) ? -1 : 1;
          });
        };
        break;
    }
    return $sortingFunction;

  }

  /**
   * @param $leagueId
   * @param $seasonId
   * @param $dayNumber
   * @param bool $live
   * @return Ranking
   */
  public function getRankingForLeagueAndSeasonAndGameDay($leagueId, $seasonId, $dayNumber, $live = false) {

    $ranking = new Ranking();

    $seasonService = ServiceBroker::getSeasonService();
    $leagueService = ServiceBroker::getLeagueService();
    $teamService = ServiceBroker::getTeamService();
    $gameDayService = ServiceBroker::getGameDayService();
    $teamScoreService = ServiceBroker::getTeamScoreService();

    $season = $seasonService->byId($seasonId);
    $league = $leagueService->byId($leagueId);
    $day = $gameDayService->bySeasonAndPosition($seasonId, $dayNumber);

    $ranking->setLeague($league);
    $ranking->setSeason($season);
    $ranking->setGameDay($day);

    $teams = $teamService->forSeason($seasonId);

    $ranks = [];
    foreach ($teams as $team) {
      $scores = $teamScoreService->forTeamUntilGameDay($team, $day);
      $scoreSum = 0;
      $wins = 0;
      $losses = 0;
      $draws = 0;
      $goalsFor = 0;
      $goalsAgainst = 0;
      $gamesFor = 0;
      $gamesAgainst = 0;
      foreach ($scores as $score) {
        $scoreSum += $score->getScore();
        $wins += $score->getWin() ? 1 : 0;
        $losses += $score->getLoss() ? 1 : 0;
        $draws += $score->getDraw() ? 1 : 0;
        $goalsFor += $score->getGoalsFor();
        $goalsAgainst += $score->getGoalsAgainst();
        $gamesFor += $score->getGamesFor();
        $gamesAgainst += $score->getGamesAgainst();
        $gamesFor += $score->getGamesFor();
      }
      $rank = new Rank();
      $rank->setTeamId($team->getId());
      $rank->setScore($scoreSum);
      $rank->setWins($wins);
      $rank->setLosses($losses);
      $rank->setDraws($draws);
      $rank->setGoalsFor($goalsFor);
      $rank->setGoalsAgainst($goalsAgainst);
      $rank->setGamesFor($gamesFor);
      $rank->setGamesAgainst($gamesAgainst);
      $rank->setGameDiff($gamesFor - $gamesAgainst);
      $rank->setRunning(false);
      $ranks[] = $rank;
    }

    if ($live) {
      $ranks = $this->addLiveScores($ranks, $dayNumber);
    }
    $sortingFunction = $this->getRankingModelSortingFunction($this->getRankingModelForSeason($day->getSeasonId()));
    $sortingFunction($ranks);

    $original_size = count($ranks);
    $teams = $teamService->forSeason($seasonId);
    if ($original_size < count($teams)) {
      // find place where to insert scores
      if ($original_size > 0) {
        for ($i = 0; $i < $original_size; $i++) {
          $score = $ranks[$i];
          // punkte = 0 oder weniger
          // UND differenz weniger als null ODER differenz gleich 0 und geschossene tore weniger als null
          if ($score->getScore() <= 0 && (($score->getGoalDiff() < 0) || ($score->getGoalDiff() == 0 && $score->getGoalsFor() <= 0))) {
            foreach ($teams as $team) {
              $has_score = false;
              foreach ($ranks as $iscore) {
                if ($team->getId() == $iscore->getTeamId())
                  $has_score = true;
              }
              if (!$has_score) {
                $new_score = new Rank();
                $new_score->setTeamId($team->getId());
                $new_score->setWins(0);
                $new_score->setDraws(0);
                $new_score->setLosses(0);
                $new_score->setGoalsFor(0);
                $new_score->setGoalsAgainst(0);
                $new_score->setGoalDiff(0);
                $new_score->setGamesFor(0);
                $new_score->setGamesAgainst(0);
                $new_score->setGameDiff(0);
                $new_score->setScore(0);
                $ranks[] = $new_score;
              }
            }
          } elseif ($original_size == ($i + 1)) {
            // last element, add scores here
            foreach ($teams as $team) {
              $has_score = false;
              foreach ($ranks as $iscore) {
                if ($team->getId() == $iscore->getTeamId())
                  $has_score = true;
              }
              if (!$has_score) {
                $new_score = new Rank();
                $new_score->setTeamId($team->getId());
                $new_score->setWins(0);
                $new_score->setDraws(0);
                $new_score->setLosses(0);
                $new_score->setGoalsFor(0);
                $new_score->setGoalsAgainst(0);
                $new_score->setGoalDiff(0);
                $new_score->setGamesFor(0);
                $new_score->setGamesAgainst(0);
                $new_score->setGameDiff(0);
                $new_score->setScore(0);
                $ranks[] = $new_score;
              }
            }
          }
        }
      } else {
        // no scores at all, fake everything
        foreach ($teams as $team) {
          $has_score = false;
          foreach ($ranks as $iscore) {
            if ($team->getId() == $iscore->getTeamId())
              $has_score = true;
          }
          if (!$has_score) {
            $new_score = new Rank();
            $new_score->setTeamId($team->getId());
            $new_score->setWins(0);
            $new_score->setDraws(0);
            $new_score->setLosses(0);
            $new_score->setGoalsFor(0);
            $new_score->setGoalsAgainst(0);
            $new_score->setGoalDiff(0);
            $new_score->setGamesFor(0);
            $new_score->setGamesAgainst(0);
            $new_score->setGameDiff(0);
            $new_score->setScore(0);
            $ranks[] = $new_score;
          }
        }
      }
    }

    $position = 0;
    $previousScore = 0;
    $previousGameDiff = 0;
    foreach ($ranks as $rank) {

      $position++;

      $rank->team = $teamService->byId($rank->getTeamId());
      $rank->games = $rank->getWins() + $rank->getLosses() + $rank->getDraws();

      if (($previousScore == $rank->getScore()) && ($previousGameDiff == $rank->getGameDiff())) {
        $rank->shared_rank = true;
      }

      $previousScore = $rank->getScore();
      $previousGameDiff = $rank->getGameDiff();
      $rank->position = $position;

    }

    $ranking->setRanks($ranks);
    return $ranking;

  }

  /**
   * @param $ranking
   * @param $dayNumber
   * @return Rank[]
   */
  private function addLiveScores($ranking, $dayNumber) {

    $gameDayService = ServiceBroker::getGameDayService();
    $matchService = ServiceBroker::getMatchService();
    $teamService = ServiceBroker::getTeamService();

    $day = $gameDayService->byId($dayNumber);
    $prevDay = $gameDayService->getPrevious($day);
    $matches = $matchService->byGameDay($day->getId());

    /**
     * @var TeamScore[]
     */
    $scores = array();
    foreach ($matches as $match) {
      $home = $teamService->byId($match->getHomeTeam());
      $away = $teamService->byId($match->getAwayTeam());
      $scores[$match->getHomeTeam()] = $this->getScoresForTeamAndMatch($match, $home);
      $scores[$match->getAwayTeam()] = $this->getScoresForTeamAndMatch($match, $away);
    }
    foreach ($scores as $teamId => $score) {
      if (!$score->isFinal() && !($score->getGoalsFor() == 0 && $score->getGoalsAgainst() == 0)) {
        $scorePlus = 0;
        if ($score->draw) {
          $scorePlus = 1;
        } elseif ($score->win) {
          $scorePlus = 2;
        }
        $rank = new Rank();
        $rank->setTeamId($teamId);
        $rank->setRunning(true);
        if ($prevDay) {
          $prevScore = $this->getTeamScoreForGameDay($teamId, $prevDay);
          $rank->setScore($prevScore->score + $scorePlus);
          $rank->setWins($prevScore->score + $score->win);
          $rank->setLosses($prevScore->score + $score->loss);
          $rank->setDraws($prevScore->score + $score->draw);
          $rank->setGoalsFor($prevScore->score + $score->goalsFor);
          $rank->setGoalsAgainst($prevScore->score + $score->goalsAgainst);
          $rank->setGoalDiff($prevScore->score + ($score->goalsFor - $score->goalsAgainst));
          $rank->setGamesFor($prevScore->score + $score->gamesFor);
          $rank->setGamesAgainst($prevScore->score + $score->gamesAgainst);
          $rank->setGameDiff($prevScore->score + ($score->gamesFor - $score->gamesAgainst));
        } else {
          $rank->setScore($scorePlus);
          $rank->setWins($score->win);
          $rank->setLosses($score->loss);
          $rank->setDraws($score->draw);
          $rank->setGoalsFor($score->goalsFor);
          $rank->setGoalsAgainst($score->goalsAgainst);
          $rank->setGoalDiff($score->goalsFor - $score->goalsAgainst);
          $rank->setGamesFor($score->gamesFor);
          $rank->setGamesAgainst($score->gamesAgainst);
          $rank->setGameDiff($score->gamesFor - $score->gamesAgainst);
        }
        $ranking[] = $rank;
      }
    }

    return $ranking;
  }


  /**
   * @param $match Match
   * @param $team Team
   * @return TeamScore|null
   */
  private function getScoresForTeamAndMatch($match, $team) {

    $gameDayService = ServiceBroker::getGameDayService();
    $teamScoreService = ServiceBroker::getTeamScoreService();
    $scoringService = ServiceBroker::getScoringService();

    $day = $gameDayService->byId($match->getGameDayId());

    $score = $teamScoreService->findOne([
      new Where('gameDay_id', $day->getId()),
      new Where('team_id', $team->getId())
    ]);

    if ($score == null) {
      $score = new TeamScore();
      $score->setTeamId($team->getId());
      $score->setGameDayId($day->getId());
      $score->setFinal(false);
    } else {
      $score->setFinal(true);
    }

    $score->setWin(0);
    $score->setDraw(0);
    $score->setLoss(0);
    $score->setGamesAgainst(0);
    $score->setGamesFor(0);
    $score->setGoalsAgainst(0);
    $score->setGoalsFor(0);
    $score->setScore(0);

    if ($match->getHomeTeam() == $team->getId()) {
      $score->setGoalsFor($this->getGoalsForTeam($match, $match->getHomeTeam()));
      $score->setGoalsAgainst($this->getGoalsForTeam($match, $match->getAwayTeam()));
      $score->setGamesFor($match->getScoreHome());
      $score->setGamesAgainst($match->getScoreAway());
      if ($match->getScoreHome() > $match->getScoreAway()) {
        $score->setScore($scoringService->getPointsForMatchResult(ScoringService::$WIN));
        $score->setWin(1);
      } elseif ($match->getScoreHome() < $match->getScoreAway()) {
        $score->setScore($scoringService->getPointsForMatchResult(ScoringService::$LOSS));
        $score->setLoss(1);
      } else {
        $score->setScore($scoringService->getPointsForMatchResult(ScoringService::$DRAW));
        $score->setDraw(1);
      }
    }

    if ($match->getAwayTeam() == $team->getId()) {
      $score->setGoalsFor($this->getGoalsForTeam($match, $match->getAwayTeam()));
      $score->setGoalsAgainst($this->getGoalsForTeam($match, $match->getHomeTeam()));
      $score->setGamesFor($match->getScoreAway());
      $score->setGamesAgainst($match->getScoreHome());
      if ($match->getScoreHome() > $match->getScoreAway()) {
        $score->setScore($scoringService->getPointsForMatchResult(ScoringService::$LOSS));
        $score->setLoss(1);
      } elseif ($match->getScoreHome() < $match->getScoreAway()) {
        $score->setScore($scoringService->getPointsForMatchResult(ScoringService::$WIN));
        $score->setWin(1);
      } else {
        $score->setScore($scoringService->getPointsForMatchResult(ScoringService::$DRAW));
        $score->setDraw(1);
      }
    }

    return $score;
  }

  /**
   * @param $match Match
   * @param $team_id int
   * @return int
   */
  private function getGoalsForTeam($match, $team_id) {

    $db = $this->getDb();
    $sql = "SELECT sum(`goals_away`) AS goals_away, sum(goals_home) AS goals_home FROM " .
      $db->getPrefix() . "matches AS m " .
      "JOIN " . $db->getPrefix() . "sets AS s ON s.match_id = m.id " .
      "JOIN " . $db->getPrefix() . "games AS g ON g.set_id = s.id " .
      "WHERE m.id = " . esc_sql($match->getId());

    $score = $db->get_row($sql);
    if (!$score) {
      return 0;
    }

    if ($match->getHomeTeam() == $team_id) {
      return $score->goals_home;
    } elseif ($match->getAwayTeam() == $team_id) {
      return $score->goals_away;
    } else {
      return 0;
    }
  }

  /**
   * TODO: use orm
   *
   * @param $team_id
   * @param $game_day_id
   * @return mixed
   */
  private function getTeamScoreForGameDay($team_id, $game_day_id) {

    $db = $this->getDb();
    $gameDayService = ServiceBroker::getGameDayService();
    $day = $gameDayService->byId($game_day_id);

    $sql = "SELECT " .
      "team_scores.team_id, " .
      "sum(team_scores.score) as score, " .
      "sum(team_scores.win) as wins, " .
      "sum(team_scores.loss) as losses, " .
      "sum(team_scores.draw) as draws, " .
      "sum(team_scores.goalsFor) as goalsFor, " .
      "sum(team_scores.goalsAgainst) as goalsAgainst " .
      "FROM " . $db->getPrefix() . "game_days, " . "" . $db->getPrefix() . "team_scores  " .
      "WHERE game_days.season_id='" . $day->getSeasonId() . "' " .
      "AND game_days.number <= '" . $day->getNumber() . "' " .
      "AND gameDay_id=game_days.id " .
      "AND team_id=" . $team_id;

    return $this->getDb()->get_row($sql);

  }

  /**
   * @return Wordpress
   * @deprecated use orm layer
   */
  private function getDb() {
    return new Wordpress();
  }


}