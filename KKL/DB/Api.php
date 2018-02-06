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

    public function getRankingForLeagueAndSeasonAndGameDay($leagueId, $seasonId, $dayNumber) {
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

}
