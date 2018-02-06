<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 20.01.18
 * Time: 10:34
 */

class KKL_DB_Api extends KKL_DB {

  public function getLeagueAdmins() {
    $sql = "SELECT p.* FROM players AS p " .
      "JOIN player_properties AS pp ON pp.objectId = p.id " .
      "AND pp.property_key = 'member_ligaleitung' " .
      "AND pp.value = 'true' " .
      "ORDER BY p.first_name, p.last_name ASC";
    $players = $this->getDb()->get_results($sql);
    foreach ($players as $player) {
      $player->role = 'ligaleitung';
    }
    return $players;
  }

  public function getEmbeddable($table, $field, $id) {
    $sql = "SELECT * FROM " . esc_sql($table) . " WHERE " . esc_sql($field) . " = '" . esc_sql($id) . "'";
    return $this->getDb()->get_results($sql);
  }

    public function getRankingForLeagueAndSeasonAndGameDay($leagueId, $seasonId, $dayNumber, $live = false) {
        $sql = "SELECT " .
                "team_scores.team_id, " .
                "sum(team_scores.score) as score, " .
                "sum(team_scores.win) as wins, " .
                "sum(team_scores.loss) as losses, " .
                "sum(team_scores.draw) as draws, " .
                "sum(team_scores.goalsFor) as goalsFor, " .
                "sum(team_scores.goalsAgainst) as goalsAgainst, " .
                "sum(team_scores.goalsFor - team_scores.goalsAgainst) as goalDiff, " .
                "sum(team_scores.gamesFor) as gamesFor, " .
                "sum(team_scores.gamesAgainst) as gamesAgainst, " .
                "sum(team_scores.gamesFor - team_scores.gamesAgainst) as gameDiff " .
                "FROM game_days, " .
                "team_scores  " .
                "WHERE game_days.season_id='" . esc_sql($seasonId) . "' " .
                "AND game_days.number <= '" . esc_sql($dayNumber) . "' " .
                "AND gameDay_id=game_days.id " .
                "GROUP BY team_id " .
//      "ORDER BY score DESC, gameDiff DESC, goalDiff DESC, team_scores.goalsFor DESC";
                "ORDER BY score DESC, gameDiff DESC";

        $ranking = $this->getDb()->get_results($sql);

        if($live) {
            $ranking = $this->addLiveScores($ranking, $leagueId, $seasonId, $dayNumber);
        }

        $original_size = count($ranking);
        $teams = $this->getTeamsForSeason($seasonId);
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
                                if ($team->id == $iscore->team_id) $has_score = true;
                            }
                            if (!$has_score) {
                                $new_score = new stdClass;
                                $new_score->team_id = $team->id;
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
                    } else if ($original_size == ($i + 1)) {
                        // last element, add scores here
                        foreach ($teams as $team) {
                            $has_score = false;
                            foreach ($ranking as $iscore) {
                                if ($team->id == $iscore->team_id) $has_score = true;
                            }
                            if (!$has_score) {
                                $new_score = new stdClass;
                                $new_score->team_id = $team->id;
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
                        if ($team->id == $iscore->team_id) $has_score = true;
                    }
                    if (!$has_score) {
                        $new_score = new stdClass;
                        $new_score->team_id = $team->id;
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

            $rank->team = $this->getTeam($rank->team_id);
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

    public function getScoresForTeamAndMatch($match, $team) {

        $day = $this->getGameDay($match->game_day_id);

        $sql = "SELECT ".
                "* ".
                "FROM ".
                "team_scores  ".
                "WHERE gameDay_id ='".esc_sql($day->id)."' ".
                "AND team_id = '".esc_sql($team->id)."';";

        $score = $this->getDb()->get_row($sql);
        if ($score == null) {
            $score = new stdClass;
            $score->team_id = $team->id;
            $score->gameDay_id = $day->id;
            $score->final = false;
        }else{
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

        if ($match->home_team == $team->id) {
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

        if ($match->away_team == $team->id) {
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


    private function addLiveScores($ranking, $leagueId, $seasonId, $dayNumber) {
      $day = $this->getGameDay($dayNumber);
      $prevDay = $this->getPreviousGameDay($day);
      $matches = $this->getMatchesByGameDay($day->id);
      $scores = array();
      foreach ($matches as $match) {
          $home = $this->getTeam($match->home_team);
          $away = $this->getTeam($match->away_team);
          $scores[$match->home_team] = $this->getScoresForTeamAndMatch($match, $home);
          $scores[$match->away_team] = $this->getScoresForTeamAndMatch($match, $away);
      }
      foreach ($scores as $teamId => $score) {
          if(!$score->final && !($score->goalsFor == 0 && $score->goalsAgainst == 0)) {
              $scorePlus = 0;
              if($score->draw) {
                  $scorePlus = 1;
              }elseif ($score->win){
                  $scorePlus = 2;
              }
            $rank = new stdClass();
            $rank->team_id = $teamId;
            $rank->running = true;
              if($prevDay) {
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
              }else{
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
      uasort($ranking, function($first, $second) {
          if($first->score == $second->score) {
              if($first->gameDiff == $second->gameDiff) {
                  return 0;
              }
              return ($first->gameDiff > $second->gameDiff) ? -1 : 1;
          }
          return ($first->score > $second->score) ? -1 : 1;
      });

      return $ranking;
    }

}
