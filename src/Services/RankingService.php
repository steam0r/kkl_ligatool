<?php


namespace KKL\Ligatool\Services;


use KKL\Ligatool\DB\Wordpress;
use KKL\Ligatool\ServiceBroker;

class RankingService {

  /**
   * @param $leagueId
   * @param $seasonId
   * @param $dayNumber
   * @param bool $live
   * @return array
   * @deprecated use orm
   */
  public function getRankingForLeagueAndSeasonAndGameDay($leagueId, $seasonId, $dayNumber, $live = false) {

    $teamService = ServiceBroker::getTeamService();
    $sql = "SELECT " . "team_scores.team_id, " . "sum(team_scores.score) as score, " . "sum(team_scores.win) as wins, " . "sum(team_scores.loss) as losses, " . "sum(team_scores.draw) as draws, " . "sum(team_scores.goalsFor) as goalsFor, " . "sum(team_scores.goalsAgainst) as goalsAgainst, " . "sum(team_scores.goalsFor - team_scores.goalsAgainst) as goalDiff, " . "sum(team_scores.gamesFor) as gamesFor, " . "sum(team_scores.gamesAgainst) as gamesAgainst, " . "sum(team_scores.gamesFor - team_scores.gamesAgainst) as gameDiff " . "FROM " . static::$prefix . "game_days, " . "" . static::$prefix . "team_scores  " . "WHERE game_days.season_id='" . esc_sql($seasonId) . "' " . "AND game_days.number <= '" . esc_sql($dayNumber) . "' " . "AND gameDay_id=game_days.id " . "GROUP BY team_id " . //      "ORDER BY score DESC, gameDiff DESC, goalDiff DESC, team_scores.goalsFor DESC";
      "ORDER BY score DESC, gameDiff DESC";

    $ranking = $this->getDb()->get_results($sql);

    if ($live) {
      $ranking = $this->addLiveScores($ranking, $dayNumber);
    }

    $original_size = count($ranking);
    $teams = $teamService->forSeason($seasonId);
    if ($original_size < count($teams)) {
      // find place where to insert scores
      if ($original_size > 0) {
        for ($i = 0; $i < $original_size; $i++) {
          $score = $ranking[$i];
          // punkte = 0 oder weniger
          // UND differenz weniger als null ODER differenz gleich 0 und geschossene tore weniger als null
          if ($score->score <= 0 && (($score->goalDiff < 0) || ($score->goalDiff == 0 && $score->goalsFor <= 0))) {
            foreach ($teams as $team) {
              $has_score = false;
              foreach ($ranking as $iscore) {
                if ($team->getId() == $iscore->team_id)
                  $has_score = true;
              }
              if (!$has_score) {
                $new_score = new stdClass;
                $new_score->team_id = $team->getId();
                $new_score->wins = 0;
                $new_score->draws = 0;
                $new_score->losses = 0;
                $new_score->goalsFor = 0;
                $new_score->goalsAgainst = 0;
                $new_score->goalDiff = 0;
                $new_score->gamesFor = 0;
                $new_score->gamesAgainst = 0;
                $new_score->gameDiff = 0;
                $new_score->score = 0;
                $ranking[] = $new_score;
              }
            }
          } elseif ($original_size == ($i + 1)) {
            // last element, add scores here
            foreach ($teams as $team) {
              $has_score = false;
              foreach ($ranking as $iscore) {
                if ($team->getId() == $iscore->team_id)
                  $has_score = true;
              }
              if (!$has_score) {
                $new_score = new stdClass;
                $new_score->team_id = $team->getId();
                $new_score->wins = 0;
                $new_score->draws = 0;
                $new_score->losses = 0;
                $new_score->goalsFor = 0;
                $new_score->goalsAgainst = 0;
                $new_score->goalDiff = 0;
                $new_score->gamesFor = 0;
                $new_score->gamesAgainst = 0;
                $new_score->gameDiff = 0;
                $new_score->score = 0;
                $ranking[] = $new_score;
              }
            }
          }
        }
      } else {
        // no scores at all, fake everything
        foreach ($teams as $team) {
          $has_score = false;
          foreach ($ranking as $iscore) {
            if ($team->getId() == $iscore->team_id)
              $has_score = true;
          }
          if (!$has_score) {
            $new_score = new stdClass;
            $new_score->team_id = $team->getId();
            $new_score->wins = 0;
            $new_score->draws = 0;
            $new_score->losses = 0;
            $new_score->goalsFor = 0;
            $new_score->goalsAgainst = 0;
            $new_score->goalDiff = 0;
            $new_score->gamesFor = 0;
            $new_score->gamesAgainst = 0;
            $new_score->gameDiff = 0;
            $new_score->score = 0;
            $ranking[] = $new_score;
          }
        }
      }
    }

    $position = 0;
    $previousScore = 0;
    $previousGameDiff = 0;
    foreach ($ranking as $rank) {

      $position++;

      $rank->team = $teamService->byId($rank->team_id);
      $rank->games = $rank->wins + $rank->losses + $rank->draws;

      if (($previousScore == $rank->score) && ($previousGameDiff == $rank->gameDiff)) {
        $rank->shared_rank = true;
      }

      $previousScore = $rank->score;
      $previousGameDiff = $rank->gameDiff;
      $rank->position = $position;

    }

    return $ranking;

  }

  private function addLiveScores($ranking, $dayNumber) {

    $gameDayService = ServiceBroker::getGameDayService();
    $matchService = ServiceBroker::getMatchService();
    $teamService = ServiceBroker::getTeamService();

    $day = $gameDayService->byId($dayNumber);
    $prevDay = $gameDayService->getPrevious($day);
    $matches = $matchService->byGameDay($day->getId());
    $scores = array();
    foreach ($matches as $match) {
      $home = $teamService->byId($match->getHomeTeam());
      $away = $teamService->byId($match->getAwayTeam());
      $scores[$match->getHomeTeam()] = $this->getScoresForTeamAndMatch($match, $home);
      $scores[$match->getAwayTeam()] = $this->getScoresForTeamAndMatch($match, $away);
    }
    foreach ($scores as $teamId => $score) {
      if (!$score->final && !($score->goalsFor == 0 && $score->goalsAgainst == 0)) {
        $scorePlus = 0;
        if ($score->draw) {
          $scorePlus = 1;
        } elseif ($score->win) {
          $scorePlus = 2;
        }
        $rank = new stdClass();
        $rank->team_id = $teamId;
        $rank->running = true;
        if ($prevDay) {
          $prevScore = $this->getTeamScoreForGameDay($teamId, $prevDay);
          $rank->score = $prevScore->score + $scorePlus;
          $rank->wins = $prevScore->score + $score->win;
          $rank->losses = $prevScore->score + $score->loss;
          $rank->draws = $prevScore->score + $score->draw;
          $rank->goalsFor = $prevScore->score + $score->goalsFor;
          $rank->goalsAgainst = $prevScore->score + $score->goalsAgainst;
          $rank->goalDiff = $prevScore->score + ($score->goalsFor - $score->goalsAgainst);
          $rank->gamesFor = $prevScore->score + $score->gamesFor;
          $rank->gamesAgainst = $prevScore->score + $score->gamesAgainst;
          $rank->gameDiff = $prevScore->score + ($score->gamesFor - $score->gamesAgainst);
        } else {
          $rank->score = $scorePlus;
          $rank->wins = $score->win;
          $rank->losses = $score->loss;
          $rank->draws = $score->draw;
          $rank->goalsFor = $score->goalsFor;
          $rank->goalsAgainst = $score->goalsAgainst;
          $rank->goalDiff = ($score->goalsFor - $score->goalsAgainst);
          $rank->gamesFor = $score->gamesFor;
          $rank->gamesAgainst = $score->gamesAgainst;
          $rank->gameDiff = ($score->gamesFor - $score->gamesAgainst);
        }
        $ranking[] = $rank;
      }
    }
    uasort($ranking, function ($first, $second) {
      if ($first->score == $second->score) {
        if ($first->gameDiff == $second->gameDiff) {
          return 0;
        }
        return ($first->gameDiff > $second->gameDiff) ? -1 : 1;
      }
      return ($first->score > $second->score) ? -1 : 1;
    });

    return $ranking;
  }

  private function getScoresForTeamAndMatch($match, $team) {

    $gameDayService = ServiceBroker::getGameDayService();
    $day = $gameDayService->byId($match->game_day_id);

    $sql = "SELECT " . "* " . "FROM " . "" . static::$prefix . "team_scores  " . "WHERE gameDay_id ='" . esc_sql($day->getId()) . "' " . "AND team_id = '" . esc_sql($team->ID) . "';";

    $score = $this->getDb()->get_row($sql);
    if ($score == null) {
      $score = new stdClass;
      $score->team_id = $team->ID;
      $score->gameDay_id = $day->getId();
      $score->final = false;
    } else {
      $score->final = true;
    }

    $score->win = 0;
    $score->draw = 0;
    $score->loss = 0;
    $score->gamesAgainst = 0;
    $score->gamesFor = 0;
    $score->goalsAgainst = 0;
    $score->goalsFor = 0;
    $score->score = 0;

    if ($match->home_team == $team->ID) {
      $score->goalsFor = $this->getGoalsForTeam($match, $match->home_team);
      $score->goalsAgainst = $this->getGoalsForTeam($match, $match->away_team);
      $score->gamesFor = $match->score_home;
      $score->gamesAgainst = $match->score_away;
      if ($match->score_home > $match->score_away) {
        $score->score = $this->getScore("win");
        $score->win = 1;
      } elseif ($match->score_home < $match->score_away) {
        $score->score = $this->getScore("loss");
        $score->loss = 1;
      } else {
        $score->score = $this->getScore("draw");
        $score->draw = 1;
      }
    }

    if ($match->away_team == $team->ID) {
      $score->goalsFor = $this->getGoalsForTeam($match, $match->away_team);
      $score->goalsAgainst = $this->getGoalsForTeam($match, $match->home_team);
      $score->gamesFor = $match->score_away;
      $score->gamesAgainst = $match->score_home;
      if ($match->score_home > $match->score_away) {
        $score->score = $this->getScore("loss");
        $score->loss = 1;
      } elseif ($match->score_home < $match->score_away) {
        $score->score = $this->getScore("win");
        $score->win = 1;
      } else {
        $score->score = $this->getScore("draw");
        $score->draw = 1;
      }
    }

    return $score;
  }

  private function getGoalsForTeam($match, $team_id) {

    $sql = "SELECT sum(`goals_away`) AS goals_away, sum(goals_home) AS goals_home FROM " . static::$prefix . "matches AS m " . "JOIN " . static::$prefix . "sets AS s ON s.match_id = m.id " . "JOIN " . static::$prefix . "games AS g ON g.set_id = s.id " . "WHERE m.id = " . esc_sql($match->ID);

    $score = $this->getDb()->get_row($sql);
    if (!$score) {
      return 0;
    }

    if ($match->home_team == $team_id) {
      return $score->goals_home;
    } elseif ($match->away_team == $team_id) {
      return $score->goals_away;
    } else {
      return 0;
    }
  }

  private function getScore($key) {
    // TODO: maybe store this in database on a per season base, to make 3 point per win possible...
    $score = 0;
    switch ($key) {
      case "win":
        $score = 2;
        break;

      case "draw":
        $score = 1;
        break;

      case "loss":
      default:
        $score = 0;
        break;
    }

    return $score;
  }

  private function getTeamScoreForGameDay($team_id, $game_day_id) {

    $gameDayService = ServiceBroker::getGameDayService();

    $day = $gameDayService->byId($game_day_id);

    $sql = "SELECT " . "team_scores.team_id, " . "sum(team_scores.score) as score, " . "sum(team_scores.win) as wins, " . "sum(team_scores.loss) as losses, " . "sum(team_scores.draw) as draws, " . "sum(team_scores.goalsFor) as goalsFor, " . "sum(team_scores.goalsAgainst) as goalsAgainst " . "FROM " . static::$prefix . "game_days, " . "" . static::$prefix . "team_scores  " . "WHERE game_days.season_id='" . $day->getSeasonId() . "' " . "AND game_days.number <= '" . $day->getNumber() . "' " . "AND gameDay_id=game_days.id " . "AND team_id=" . $team_id;

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