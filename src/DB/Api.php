<?php

namespace KKL\Ligatool\DB;

use KKL\Ligatool\DB;
use KKL\Ligatool\Model\ApiKey;
use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Services\ScoringService;
use stdClass;
use Symlink\ORM\Exceptions\InvalidOperatorException;
use Symlink\ORM\Exceptions\NoQueryException;
use Symlink\ORM\Exceptions\PropertyDoesNotExistException;

class Api extends DB {

  public function getLeagueAdmins() {
    $sql = "SELECT p.* FROM " . static::$prefix . "players AS p " . "JOIN " . static::$prefix . "player_properties AS pp ON pp.objectId = p.id " . "AND pp.property_key = 'member_ligaleitung' " . "AND pp.value = 'true' " . "ORDER BY p.first_name, p.last_name ASC";
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
    $sql = "SELECT " . "team_scores.team_id, " . "sum(team_scores.score) as score, " . "sum(team_scores.win) as wins, " . "sum(team_scores.loss) as losses, " . "sum(team_scores.draw) as draws, " . "sum(team_scores.goalsFor) as goalsFor, " . "sum(team_scores.goalsAgainst) as goalsAgainst, " . "sum(team_scores.goalsFor - team_scores.goalsAgainst) as goalDiff, " . "sum(team_scores.gamesFor) as gamesFor, " . "sum(team_scores.gamesAgainst) as gamesAgainst, " . "sum(team_scores.gamesFor - team_scores.gamesAgainst) as gameDiff " . "FROM " . static::$prefix . "game_days, " . "" . static::$prefix . "team_scores  " . "WHERE game_days.season_id='" . esc_sql($seasonId) . "' " . "AND game_days.number <= '" . esc_sql($dayNumber) . "' " . "AND gameDay_id=game_days.id " . "GROUP BY team_id " . //      "ORDER BY score DESC, gameDiff DESC, goalDiff DESC, team_scores.goalsFor DESC";
      "ORDER BY score DESC, gameDiff DESC";

    $ranking = $this->getDb()->get_results($sql);

    if ($live) {
      $ranking = $this->addLiveScores($ranking, $dayNumber);
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
                if ($team->ID == $iscore->team_id)
                  $has_score = true;
              }
              if (!$has_score) {
                $new_score = new stdClass;
                $new_score->team_id = $team->ID;
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
                if ($team->ID == $iscore->team_id)
                  $has_score = true;
              }
              if (!$has_score) {
                $new_score = new stdClass;
                $new_score->team_id = $team->ID;
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
            if ($team->ID == $iscore->team_id)
              $has_score = true;
          }
          if (!$has_score) {
            $new_score = new stdClass;
            $new_score->team_id = $team->ID;
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

  private function addLiveScores($ranking, $dayNumber) {
    $day = $this->getGameDay($dayNumber);
    $prevDay = $this->getPreviousGameDay($day);
    $matches = $this->getMatchesByGameDay($day->ID);
    $scores = array();
    foreach ($matches as $match) {
      $home = $this->getTeam($match->home_team);
      $away = $this->getTeam($match->away_team);
      $scores[$match->home_team] = $this->getScoresForTeamAndMatch($match, $home);
      $scores[$match->away_team] = $this->getScoresForTeamAndMatch($match, $away);
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

  public function getScoresForTeamAndMatch($match, $team) {

    $scoringService = ServiceBroker::getScoringService();
    
    $day = $this->getGameDay($match->game_day_id);

    $sql = "SELECT " . "* " . "FROM " . "" . static::$prefix . "team_scores  " . "WHERE gameDay_id ='" . esc_sql($day->ID) . "' " . "AND team_id = '" . esc_sql($team->ID) . "';";

    $score = $this->getDb()->get_row($sql);
    if ($score == null) {
      $score = new stdClass;
      $score->team_id = $team->ID;
      $score->gameDay_id = $day->ID;
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
        $score->score = $scoringService->getPointsForMatchResult(ScoringService::$WIN);
        $score->win = 1;
      } elseif ($match->score_home < $match->score_away) {
        $score->score = $scoringService->getPointsForMatchResult(ScoringService::$LOSS);
        $score->loss = 1;
      } else {
        $score->score = $scoringService->getPointsForMatchResult(ScoringService::$DRAW);
        $score->draw = 1;
      }
    }

    if ($match->away_team == $team->ID) {
      $score->goalsFor = $this->getGoalsForTeam($match, $match->away_team);
      $score->goalsAgainst = $this->getGoalsForTeam($match, $match->home_team);
      $score->gamesFor = $match->score_away;
      $score->gamesAgainst = $match->score_home;
      if ($match->score_home > $match->score_away) {
        $score->score = $scoringService->getPointsForMatchResult(ScoringService::$LOSS);
        $score->loss = 1;
      } elseif ($match->score_home < $match->score_away) {
        $score->score = $scoringService->getPointsForMatchResult(ScoringService::$WIN);
        $score->win = 1;
      } else {
        $score->score = $scoringService->getPointsForMatchResult(ScoringService::$DRAW);
        $score->draw = 1;
      }
    }

    return $score;
  }

  /**
   * @param $mail
   * @return array|null|object|void
   * @deprecated
   */
  public function getInfoByEmailAddress($mail) {
    $sql = "
      select
      tp.property_key as role,
      t.name as team,
      l.name as league,
      s.name as season,
      m.id as suggested_matchId,
      slack.value as slack
      from players as p
      join team_properties as tp ON p.id = tp.value AND (tp.property_key = 'captain' or tp.property_key = 'vice_captain')
      join teams AS t ON t.id = tp.objectId
      join seasons AS s ON s.id = t.season_id AND s.active = 1
      join leagues as l ON l.id = s.league_id
      left join matches AS m ON m.game_day_id = s.current_game_day AND (t.id = m.home_team OR t.id = m.away_team)
      left join season_properties AS sp ON sp.objectId = s.id AND sp.property_key = 'season_admin'
      left join players AS adm ON adm.id = sp.value
      left join player_properties as slack ON adm.id = slack.objectId AND slack.property_key = 'slack_alias'
      where p.email = '$mail' order by s.id DESC limit 1;";
    return $this->getDb()->get_row($sql);
  }

  public function getClubs() {
    $sql = "SELECT * FROM " . static::$prefix . "clubs ORDER BY name ASC";
    $results = $this->getDb()->get_results($sql);
    if (is_array($results)) {
      return $results;
    } else {
      return array();
    }
  }

  public function getTeams() {
    $sql = "SELECT * FROM " . static::$prefix . "teams ORDER BY name ASC";
    $teams = $this->getDb()->get_results($sql);
    if (is_array($teams)) {
      foreach ($teams as $team) {
        $properties = $this->getTeamProperties($team->ID);
        $team->properties = $properties;
      }
      return $teams;
    } else {
      return array();
    }
  }

  public function getTeamProperties($teamId) {
    $sql = "SELECT * FROM " . static::$prefix . "team_properties WHERE objectId = '" . esc_sql($teamId) . "'";
    $results = $this->getDb()->get_results($sql);
    $properties = array();
    foreach ($results as $result) {
      $properties[$result->property_key] = $result->value;
      if ($result->property_key == 'captain') {
        $player = $this->getPlayer($result->value);
        $properties['captain_email'] = $player->email;
      } elseif ($result->property_key == 'vice_captain') {
        $player = $this->getPlayer($result->value);
        $properties['vice_captain_email'] = $player->email;
      } else {
        $location = $this->getLocation($result->value);
        $properties['location_name'] = $location->title;
        $properties['lat'] = $location->lat;
        $properties['lng'] = $location->lng;
      }
    }

    return $properties;
  }

  public function getPlayer($playerId) {
    $sql = "SELECT * FROM " . static::$prefix . "players WHERE ID = '" . esc_sql($playerId) . "'";
    $player = $this->getDb()->get_row($sql);
    $properties = $this->getPlayerProperties($player->ID);
    if ($properties) {
      $player->properties = $properties;
    }

    return $player;
  }

  public function getPlayerProperties($playerId) {
    $sql = "SELECT * FROM " . static::$prefix . "player_properties WHERE objectId = '" . esc_sql($playerId) . "'";
    $results = $this->getDb()->get_results($sql);
    $properties = array();
    foreach ($results as $result) {
      $properties[$result->property_key] = $result->value;
    }

    return $properties;
  }

  public function getLocation($locationId) {
    $sql = "SELECT * FROM " . static::$prefix . "locations WHERE ID = '" . esc_sql($locationId) . "'";

    return $this->getDb()->get_row($sql);
  }

  public function getPlayers() {
    $sql = "SELECT * FROM " . static::$prefix . "players ORDER BY first_name, last_name ASC";
    $players = $this->getDb()->get_results($sql);
    foreach ($players as $player) {
      $properties = $this->getPlayerProperties($player->ID);
      $player->properties = $properties;
    }

    return $players;
  }

  public function getCurrentTeamForClub($clubId) {
    $sql = "SELECT * FROM " . static::$prefix . "teams WHERE club_id = '" . esc_sql($clubId) . "' ORDER BY ID DESC LIMIT 1";
    $team = $this->getDb()->get_row($sql);
    $properties = $this->getTeamProperties($team->ID);
    $team->properties = $properties;

    return $team;
  }

  public function getAwardsForClub($clubId) {
    $sql = "SELECT * FROM " . static::$prefix . "club_has_awards AS c JOIN " . static::$prefix . "awards AS a ON a.id = c.award_id WHERE c.club_id = '" . esc_sql($clubId) . "'";

    return $this->getDb()->get_results($sql);
  }

  public function getLeagues() {
    $sql = "SELECT * FROM " . static::$prefix . "leagues ORDER BY name ASC";
    $leagues = $this->getDb()->get_results($sql);
    foreach ($leagues as $league) {
      $league->active = boolval($league->active);
    }

    return $leagues;
  }

  public function getLeagueBySlug($slug) {
    $sql = "SELECT * FROM " . static::$prefix . "leagues WHERE code = '" . esc_sql($slug) . "'";
    $league = $this->getDb()->get_row($sql);
    $league->active = boolval($league->active);

    return $league;
  }

  public function getSeasons() {
    $sql = "SELECT * FROM " . static::$prefix . "seasons ORDER BY start_date ASC, name ASC";

    return $this->getDb()->get_results($sql);
  }

  public function getLeagueProperties($leagueId) {
    $sql = "SELECT * FROM " . static::$prefix . "league_properties WHERE objectId = '" . esc_sql($leagueId) . "'";
    $results = $this->getDb()->get_results($sql);
    $properties = array();
    foreach ($results as $result) {
      $properties[$result->property_key] = $result->value;
    }

    return $properties;
  }

  public function getSeasonsByLeague($league) {
    $sql = "SELECT * FROM " . static::$prefix . "seasons WHERE league_id = '" . esc_sql($league) . "'";

    return $this->getDb()->get_results($sql);
  }

  public function getTeamsForClub($club_id) {
    $sql = "SELECT t.* FROM " . static::$prefix . "teams AS t, " . static::$prefix . "seasons AS s WHERE t.club_id = '" . esc_sql($club_id) . "' AND t.season_id = s.id ORDER BY s.start_date ASC";
    $teams = $this->getDb()->get_results($sql);
    foreach ($teams as $team) {
      $properties = $this->getTeamProperties($team->ID);
      $team->properties = $properties;
    }

    return $teams;
  }

  public function getGameDays() {
    $sql = "SELECT * FROM " . static::$prefix . "game_days ORDER BY fixture ASC, number ASC";

    return $this->getDb()->get_results($sql);
  }

  public function getPreviousGameDay($day) {
    $number = $day->number - 1;
    if ($number < 1) {
      return false;
    }
    $sql = "SELECT * FROM " . static::$prefix . "game_days WHERE season_id = '" . esc_sql($day->season_id) . "' AND number = '" . esc_sql($number) . "'";

    return $this->getDb()->get_row($sql);
  }

  public function getNextGameDay($day) {
    $number = $day->number + 1;
    $sql = "SELECT * FROM " . static::$prefix . "game_days WHERE season_id = '" . esc_sql($day->season_id) . "' AND number = '" . esc_sql($number) . "'";

    return $this->getDb()->get_row($sql);
  }

  public function getGameDayBySeasonAndPosition($seasonId, $position) {
    $sql = "SELECT * FROM " . static::$prefix . "game_days WHERE season_id = '" . esc_sql($seasonId) . "' AND number = '" . esc_sql($position) . "'";

    return $this->getDb()->get_row($sql);
  }

  public function getCurrentSeason($leagueId) {
    $result = null;
    $sql = "SELECT current_season FROM " . static::$prefix . "leagues WHERE ID = '" . esc_sql($leagueId) . "'";
    $seasonId = $this->getDb()->get_var($sql);
    if ($seasonId) {
      $sql = "SELECT * FROM " . static::$prefix . "seasons WHERE ID = '" . esc_sql($seasonId) . "'";
      $result = $this->getDb()->get_row($sql);
    }

    return $result;
  }

  public function getCurrentGameDayForSeason($seasonId) {
    $sql = "SELECT * FROM " . static::$prefix . "game_days WHERE ID = (SELECT current_game_day AS ID FROM " . static::$prefix . "seasons WHERE ID = '" . esc_sql($seasonId) . "')";

    return $this->getDb()->get_row($sql);
  }

  public function getLeagueForGameday($dayId) {
    $sql = "SELECT * FROM " . static::$prefix . "leagues WHERE ID = (SELECT league_id AS ID FROM " . static::$prefix . "seasons WHERE ID = (SELECT season_id AS ID FROM " . static::$prefix . "game_days WHERE ID = '" . esc_sql($dayId) . "'))";
    $league = $this->getDb()->get_row($sql);
    $league->active = boolval($league->active);

    return $league;
  }

  public function getMatches() {
    $sql = "SELECT * FROM " . static::$prefix . "matches ORDER BY fixture ASC";

    return $this->getDb()->get_results($sql);
  }

  public function getFullMatchInfo($matchId) {
    $sql = "SELECT m.*, away.name AS away_name, home.name AS home_name, l.name AS league, s.score_home, s.score_away, g.goals_home, g.goals_away, homecaptain.email AS home_email, awaycaptain.email AS away_email FROM " . static::$prefix . "matches AS m LEFT JOIN " . static::$prefix . "sets AS s ON m.id = s.match_id LEFT JOIN " . static::$prefix . "games AS g ON s.id = g.set_id LEFT JOIN " . static::$prefix . "teams AS away ON away.id = m.away_team LEFT JOIN " . static::$prefix . "team_properties AS ac ON ac.objectId = away.id AND ac.property_key = 'captain' LEFT JOIN " . static::$prefix . "players AS awaycaptain ON awaycaptain.id = ac.value LEFT JOIN " . static::$prefix . "teams AS home ON home.id = m.home_team LEFT JOIN " . static::$prefix . "team_properties AS hc ON hc.objectId = home.id AND hc.property_key = 'captain' LEFT JOIN" . static::$prefix . " players AS homecaptain ON homecaptain.id = hc.value LEFT JOIN " . static::$prefix . "game_days AS gd ON gd.id = m.game_day_id LEFT JOIN " . static::$prefix . "seasons AS season ON season.id = gd.season_id LEFT JOIN " . static::$prefix . "leagues AS l ON l.id = season.league_id WHERE m.id = '" . esc_sql($matchId) . "'";

    return $this->getDb()->get_row($sql);

  }

  public function getMatchesForTeam($teamId) {
    $sql = "SELECT * FROM " . static::$prefix . "matches WHERE (home_team = '" . esc_sql($teamId) . "' OR away_team = '" . esc_sql($teamId) . "') ORDER BY fixture ASC";

    return $this->getDb()->get_results($sql);
  }

  public function getMatchesByGameDay($dayId) {
    $sql = "SELECT * FROM " . static::$prefix . "matches WHERE game_day_id = '" . esc_sql($dayId) . "' ORDER BY fixture ASC";

    return $this->getDb()->get_results($sql);
  }

  public function getClubProperties($clubId) {
    $sql = "SELECT * FROM " . static::$prefix . "club_properties WHERE objectId = '" . esc_sql($clubId) . "'";
    $results = $this->getDb()->get_results($sql);
    $properties = array();
    foreach ($results as $result) {
      $properties[$result->property_key] = $result->value;
    }

    return $properties;
  }

  public function getMatchProperties($matchId) {
    $sql = "SELECT * FROM " . static::$prefix . "match_properties WHERE objectId = '" . esc_sql($matchId) . "'";
    $results = $this->getDb()->get_results($sql);
    $properties = array();
    foreach ($results as $result) {
      $properties[$result->property_key] = $result->value;
    }

    return $properties;
  }

  public function getTeamByCodeAndSeason($teamCode, $seasonId) {
    $sql = "SELECT * FROM " . static::$prefix . "teams WHERE short_name = '" . esc_sql($teamCode) . "' AND season_id = '" . esc_sql($seasonId) . "'";
    $team = $this->getDb()->get_row($sql);
    $properties = $this->getTeamProperties($team->ID);
    $team->properties = $properties;

    return $team;
  }

  public function getTeam($teamId) {
    $sql = "SELECT * FROM " . static::$prefix . "teams WHERE ID = '" . esc_sql($teamId) . "'";
    $team = $this->getDb()->get_row($sql);
    $properties = $this->getTeamProperties($team->ID);
    $team->properties = $properties;

    return $team;
  }

  public function getClubByCode($clubCode) {
    $sql = "SELECT * FROM " . static::$prefix . "clubs WHERE short_name = '" . esc_sql($clubCode) . "'";

    return $this->getDb()->get_row($sql);
  }

  public function getTeamsForSeason($seasonId) {
    $sql = "SELECT * FROM " . static::$prefix . "teams WHERE season_id = '" . esc_sql($seasonId) . "'";
    $teams = $this->getDb()->get_results($sql);
    foreach ($teams as $team) {
      $properties = $this->getTeamProperties($team->ID);
      $team->properties = $properties;
    }

    return $teams;
  }

  public function getTeamScoreForGameDay($team_id, $game_day_id) {

    $day = $this->getGameDay($game_day_id);

    $sql = "SELECT " . "team_scores.team_id, " . "sum(team_scores.score) as score, " . "sum(team_scores.win) as wins, " . "sum(team_scores.loss) as losses, " . "sum(team_scores.draw) as draws, " . "sum(team_scores.goalsFor) as goalsFor, " . "sum(team_scores.goalsAgainst) as goalsAgainst " . "FROM " . static::$prefix . "game_days, " . "" . static::$prefix . "team_scores  " . "WHERE game_days.season_id='" . $day->season_id . "' " . "AND game_days.number <= '" . $day->number . "' " . "AND gameDay_id=game_days.id " . "AND team_id=" . $team_id;

    return $this->getDb()->get_row($sql);

  }

  public function getGameDay($dayId) {
    $sql = "SELECT * FROM " . static::$prefix . "game_days WHERE ID = '" . esc_sql($dayId) . "'";

    return $this->getDb()->get_row($sql);
  }

  public function getScheduleForSeason($season) {

    $days = $this->getGameDaysForSeason($season->ID);
    $schedules = array();
    foreach ($days as $day) {
      $schedules[] = $this->getScheduleForGameDay($day);
    }

    return $schedules;
  }

  public function getGameDaysForSeason($seasonId) {
    $sql = "SELECT * FROM " . static::$prefix . "game_days WHERE season_id = '" . esc_sql($seasonId) . "' ORDER BY number ASC";

    return $this->getDb()->get_results($sql);
  }

  public function getScheduleForGameDay($day) {
    $sql = "SELECT * FROM " . static::$prefix . "matches WHERE game_day_id = '" . esc_sql($day->ID) . "'";
    $schedule = new stdClass;
    $schedule->matches = $this->getDb()->get_results($sql);
    $schedule->number = $day->number;
    $schedule->day = $day;
    foreach ($schedule->matches as $match) {
      $match->home = $this->getTeam($match->home_team);
      $match->away = $this->getTeam($match->away_team);
    }

    return $schedule;
  }

  public function getTeamsForLocation($location) {
    $sql = "SELECT objectId FROM " . static::$prefix . "team_properties WHERE property_key = 'location' AND value = '" . esc_sql($location->ID) . "'";
    $teamIds = $this->getDb()->get_results($sql);
    $teams = array();
    foreach ($teamIds as $teamId) {
      if ($teamId->objectId && $teamId->objectId != 0) {
        $teams[] = $this->getTeam($teamId->objectId);
      }
    }

    return $teams;
  }

  public function getLocations() {
    $sql = "SELECT * FROM " . static::$prefix . "locations ORDER BY title ASC";

    return $this->getDb()->get_results($sql);
  }

  public function createTeam($team) {
    $values = array('name' => $team->name, 'description' => $team->description, 'short_name' => $team->short_name, 'club_id' => $team->club_id, 'season_id' => $team->season_id,);
    $this->getDb()->insert(static::$prefix . 'teams', $values, array('%s', '%s', '%s', '%d', '%d'));

    return $this->getTeam($this->getDb()->insert_id);
  }

  public function updateMatch($match) {

    $values = array('game_day_id' => $match->game_day_id, 'score_away' => $match->score_away, 'score_home' => $match->score_home, 'fixture' => $match->fixture, 'location' => $match->location, 'notes' => $match->notes, 'status' => $match->status, 'away_team' => $match->away_team, 'home_team' => $match->home_team,);
    $this->getDb()->update(static::$prefix . 'matches', $values, array('ID' => $match->ID), array('%d', '%d', '%d', '%s', '%s', '%s', '%d', '%d', '%d',), array('%d'));

    $set = new stdClass;
    $set->score_away = $match->score_away;
    $set->score_home = $match->score_home;
    $set->number = 1;
    $set->match_id = $match->ID;
    $set = $this->updateSet($set);

    $game = new stdClass;
    $game->goals_home = $match->goals_home;
    $game->goals_away = $match->goals_away;
    $game->number = 1;
    $game->set_id = $set->ID;
    $this->updateGame($game);

    return $this->getMatch($match->ID);
  }

  public function updateSet($set) {
    if (!$this->getSet($set->match_id)) {
      return $this->createSet($set);
    }
    $values = array('score_away' => $set->score_away, 'score_home' => $set->score_home, 'number' => $set->number, 'match_id' => $set->match_id,);
    $this->getDb()->update(static::$prefix . 'sets', $values, array('match_id' => $set->match_id), array('%d', '%d', '%d', '%d',), array('%d'));

    return $this->getSet($set->match_id);
  }

  public function getSet($matchId) {
    $sql = "SELECT * FROM " . static::$prefix . "sets WHERE match_id = '" . esc_sql($matchId) . "'";

    return $this->getDb()->get_row($sql);
  }

  public function createSet($set) {
    $values = array('score_away' => $set->score_away, 'score_home' => $set->score_home, 'number' => $set->number, 'match_id' => $set->match_id,);
    $this->getDb()->insert(static::$prefix . 'sets', $values, array('%d', '%d', '%d', '%d'));

    return $this->getSet($set->match_id);
  }

  public function updateGame($game) {
    if (!$this->getGame($game->set_id)) {
      return $this->createGame($game);
    }
    $values = array('goals_away' => $game->goals_away, 'goals_home' => $game->goals_home, 'number' => $game->number, 'set_id' => $game->set_id,);
    $this->getDb()->update(static::$prefix . 'games', $values, array('set_id' => $game->set_id), array('%d', '%d', '%d', '%d',), array('%d'));

    return $this->getGame($game->match_id);
  }

  public function getGame($setId) {
    $sql = "SELECT * FROM " . static::$prefix . "games WHERE set_id = '" . esc_sql($setId) . "'";

    return $this->getDb()->get_row($sql);
  }

  public function createGame($game) {
    $values = array('goals_away' => $game->goals_away, 'goals_home' => $game->goals_home, 'number' => $game->number, 'set_id' => $game->set_id,);
    $this->getDb()->insert(static::$prefix . 'games', $values, array('%d', '%d', '%d', '%d'));

    return $this->getGame($game->set_id);
  }

  public function getMatch($matchId) {
    $sql = "SELECT m.*, s.score_home, s.score_away, g.goals_home, g.goals_away FROM " . static::$prefix . "matches AS m LEFT JOIN " . static::$prefix . "sets AS s ON m.ID = s.match_id LEFT JOIN " . static::$prefix . "games AS g ON s.id = g.set_id WHERE m.ID = '" . esc_sql($matchId) . "'";
    return $this->getDb()->get_row($sql);
  }

  public function createMatch($match) {
    $values = array('game_day_id' => $match->game_day_id, 'score_away' => $match->score_away, 'score_home' => $match->score_home, 'fixture' => $match->fixture, 'location' => $match->location, 'notes' => $match->notes, 'status' => $match->status, 'away_team' => $match->away_team, 'home_team' => $match->home_team,);
    if ($values['fixtures']) {
      $values['fixtures'] = null;
    }
    $this->getDb()->insert(static::$prefix . 'matches', $values, array('%d', '%d', '%d', '%s', '%s', '%s', '%d', '%d', '%d'));
    $match_id = $this->getDb()->insert_id;

    $set = new stdClass;
    $set->score_away = 0;
    $set->score_home = 0;
    $set->number = 1;
    $set->match_id = $match_id;
    $set = $this->createSet($set);

    $game = new stdClass;
    $game->goals_away = 0;
    $game->goals_home = 0;
    $game->number = 1;
    $game->set_id = $set->ID;
    $this->createGame($game);

    return $this->getMatch($match_id);
  }

  protected function getGoalsForTeam($match, $team_id) {

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

  public function setTeamProperties($team, $properties) {
    foreach ($properties as $key => $value) {
      $this->getDb()->delete(static::$prefix . 'team_properties', array('objectId' => $team->ID, 'property_key' => $key));
      if ($value !== false) {
        $this->getDb()->insert(static::$prefix . 'team_properties', array('objectId' => $team->ID, 'property_key' => $key, 'value' => $value,), array('%d', '%s', '%s'));
      }
    }
  }

  public function getLeague($leagueId) {
    $sql = "SELECT * FROM " . static::$prefix . "leagues WHERE ID = '" . esc_sql($leagueId) . "'";
    $league = $this->getDb()->get_row($sql);
    $league->active = boolval($league->active);

    return $league;
  }

  public function updateSeason($season) {
    $columns = array();
    $columns['name'] = $season->name;
    $columns['start_date'] = $season->start_date;
    $columns['end_date'] = $season->end_date;
    $columns['active'] = $season->active;
    $columns['current_game_day'] = $season->current_game_day;
    $columns['league_id'] = $season->league_id;

    $this->getDb()->update(static::$prefix . 'seasons', $columns, array('ID' => $season->ID), array('%s', '%s', '%s', '%d', '%d', '%d',), array('%d'));

    return $this->getSeason($season->ID);
  }

  public function getSeason($seasonId) {
    $sql = "SELECT * FROM " . static::$prefix . "seasons WHERE ID = '" . esc_sql($seasonId) . "'";
    $season = $this->getDb()->get_row($sql);
    $properties = $this->getSeasonProperties($season->ID);
    $season->properties = $properties;

    return $season;
  }

  public function getSeasonProperties($seasonId) {
    $sql = "SELECT * FROM " . static::$prefix . "season_properties WHERE objectId = '" . esc_sql($seasonId) . "'";
    $results = $this->getDb()->get_results($sql);
    $properties = array();
    foreach ($results as $result) {
      $properties[$result->property_key] = $result->value;
    }

    return $properties;
  }

  public function createSeason($season) {
    $values = array('name' => $season->name, 'start_date' => $season->start_date, 'end_date' => $season->end_date, 'active' => $season->active, 'current_game_day' => $season->current_game_day, 'league_id' => $season->league_id,);
    $this->getDb()->insert(static::$prefix . 'seasons', $values, array('%s', '%s', '%s', '%d', '%d', '%d'));

    return $this->getSeason($this->getDb()->insert_id);
  }

  public function getClub($clubId) {
    $sql = "SELECT * FROM " . static::$prefix . "clubs WHERE ID = '" . esc_sql($clubId) . "'";

    return $this->getDb()->get_row($sql);
  }

  public function createClub($club) {
    $values = array('name' => $club->name, 'short_name' => $club->short_name, 'description' => $club->description,);
    if ($club->logo) {
      $values['logo'] = $club->logo;
      $this->getDb()->insert(static::$prefix . 'clubs', $values, array('%s', '%s', '%s', '%s'));
    } else {
      $this->getDb()->insert(static::$prefix . 'clubs', $values, array('%s', '%s', '%s'));
    }

    return $this->getClub($this->getDb()->insert_id);
  }

  public function createGameDay($day) {
    $values = array('number' => $day->number, 'fixture' => $day->start_date, 'end' => $day->end_date, 'season_id' => $day->season_id,);
    $this->getDb()->insert(static::$prefix . 'game_days', $values, array('%d', '%s', '%s', '%d'));

    return $this->getGameDay($this->getDb()->insert_id);
  }

  /**
   * @param string $key
   * @return ApiKey|null
   * @throws InvalidOperatorException
   * @throws NoQueryException
   * @throws PropertyDoesNotExistException
   */
  public function getApiKey($key) {
    $orm = $this->getOrm();
    $repo = $orm->getRepository(ApiKey::class);
    $results = $repo->createQueryBuilder()->where('api_key', $key, '=')->buildQuery()->getResults(true);
    if ($results) {
      return $results[0];
    } else {
      return null;
    }
  }
}
