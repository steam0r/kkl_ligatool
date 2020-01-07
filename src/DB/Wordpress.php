<?php

namespace KKL\Ligatool\DB;

use KKL\Ligatool\DB;
use KKL\Ligatool\Model\ApiKey;
use KKL\Ligatool\Model\Award;
use KKL\Ligatool\Model\Club;
use KKL\Ligatool\Model\ClubAward;
use KKL\Ligatool\Model\ClubProperty;
use KKL\Ligatool\Model\Game;
use KKL\Ligatool\Model\GameDay;
use KKL\Ligatool\Model\League;
use KKL\Ligatool\Model\LeagueProperty;
use KKL\Ligatool\Model\Location;
use KKL\Ligatool\Model\Match;
use KKL\Ligatool\Model\MatchProperty;
use KKL\Ligatool\Model\Player;
use KKL\Ligatool\Model\PlayerProperty;
use KKL\Ligatool\Model\Season;
use KKL\Ligatool\Model\SeasonProperty;
use KKL\Ligatool\Model\Set;
use KKL\Ligatool\Model\SetAwayPlayer;
use KKL\Ligatool\Model\SetHomePlayer;
use KKL\Ligatool\Model\Team;
use KKL\Ligatool\Model\TeamPlayer;
use KKL\Ligatool\Model\TeamPlayerProperty;
use KKL\Ligatool\Model\TeamProperty;
use KKL\Ligatool\Model\TeamScore;
use KKL\Ligatool\ServiceBroker;
use stdClass;
use Symlink\ORM\Mapping;

class Wordpress extends DB {

  public static $VERSION_KEY = "kkl_wordpress_db_version";
  public static $VERSION = "4";

  public function getSeasonByLeagueAndYear($league, $year) {
    $sql = "SELECT * FROM " . static::$prefix . "seasons WHERE league_id = '" . esc_sql($league) . "' AND YEAR(start_date) = '" . esc_sql($year) . "'";
    return $this->getDb()->get_row($sql);
  }

  public function getGameDayBySeasonAndPosition($seasonId, $position) {
    $sql = "SELECT * FROM " . static::$prefix . "game_days WHERE season_id = '" . esc_sql($seasonId) . "' AND number = '" . esc_sql($position) . "'";

    return $this->getDb()->get_row($sql);
  }

  public function getAllGamesForNextGameday() {

    $sql = "SELECT  m.id,
                        m.score_away,
                        m.fixture,
                        m.score_home,
                        m.location,
                        l.id AS league_id,
                        ht.name AS homename,
                        at.name AS awayname,
                        at.id AS awayid,
                        ht.id AS homeid
                FROM    " . static::$prefix . "leagues AS l,
                        " . static::$prefix . "matches AS m,
                        " . static::$prefix . "teams AS at,
                        " . static::$prefix . "teams AS ht
                JOIN " . static::$prefix . "seasons AS s
                JOIN " . static::$prefix . "game_days AS cgd ON cgd.id = s.current_game_day
                JOIN " . static::$prefix . "game_days AS gd ON gd.season_id = s.id AND gd.number = (cgd.number + 1)
                WHERE   l.active = 1
                AND     s.id = l.current_season
                AND     m.game_day_id = gd.id
                AND     at.id = m.away_team
                AND     ht.id = m.home_team ORDER BY l.name ASC";

    return $this->getDb()->get_results($sql);

  }

  public function getTeamProperties($teamId) {

    $playerService = ServiceBroker::getPlayerService();
    $locationService = ServiceBroker::getLocationService();

    $sql = "SELECT * FROM " . static::$prefix . "team_properties WHERE objectId = '" . esc_sql($teamId) . "'";
    $results = $this->getDb()->get_results($sql);
    $properties = array();
    foreach ($results as $result) {
      $properties[$result->property_key] = $result->value;
      if ($result->property_key == 'captain') {
        $player = $playerService->byId($result->value);
        $properties['captain_email'] = $player->getEmail();
      } elseif ($result->property_key == 'vice_captain') {
        $player = $playerService->byId($result->value);
        $properties['vice_captain_email'] = $player->getEmail();
      } else {
        $location = $locationService->byId($result->value);
        $properties['location_name'] = $location->getTitle();
        $properties['lat'] = $location->getLatitude();
        $properties['lng'] = $location->getLongitude();
      }
    }

    return $properties;
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

  public function setPlayerProperties($player, $properties) {
    foreach ($properties as $key => $value) {
      $this->getDb()->delete(static::$prefix . 'player_properties', array('objectId' => $player->ID, 'property_key' => $key));
      if ($value !== false) {
        $this->getDb()->insert(static::$prefix . 'player_properties', array('objectId' => $player->ID, 'property_key' => $key, 'value' => $value,), array('%d', '%s', '%s'));
      }
    }
  }

  public function setSeasonProperties($season, $properties) {
    foreach ($properties as $key => $value) {
      $this->getDb()->delete(static::$prefix . 'season_properties', array('objectId' => $season->ID, 'property_key' => $key));
      if ($value !== false) {
        $this->getDb()->insert(static::$prefix . 'season_properties', array('objectId' => $season->ID, 'property_key' => $key, 'value' => $value,), array('%d', '%s', '%s'));
      }
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

  public function getCaptainsContactData() {
    $sql = "SELECT " . "p.first_name as first_name, p.last_name as last_name, p.email as email, p.phone as phone, " . "t.name as team, t.short_name as team_short, " . "l.name as league, l.code as league_short, loc.title as location " . "FROM " . static::$prefix . "team_properties AS tp " . "JOIN " . static::$prefix . "players AS p ON p.id = tp.value " . "JOIN " . static::$prefix . "teams AS t ON t.id = tp.objectId " . "JOIN " . static::$prefix . "seasons AS s ON s.id = t.season_id AND s.active = '1' " . "JOIN " . static::$prefix . "leagues AS l ON l.id = s.league_id " . "LEFT JOIN " . static::$prefix . "team_properties AS lp ON t.id = lp.objectId AND lp.property_key = 'location' " . "LEFT JOIN " . static::$prefix . "locations AS loc ON loc.id = lp.value " . "WHERE tp.property_key = 'captain' ";
    $data = $this->getDb()->get_results($sql);
    $captains = array();
    foreach ($data as $d) {
      $d->role = 'captain';
      $captains[] = $d;
    }

    return $captains;
  }

  public function getViceCaptainsContactData() {
    $sql = "SELECT " . "p.first_name as first_name, p.last_name as last_name, p.email as email, p.phone as phone, " . "t.name as team, t.short_name as team_short, " . "l.name as league, l.code as league_short, loc.title as location " . "FROM " . static::$prefix . "team_properties AS tp " . "JOIN " . static::$prefix . "players AS p ON p.id = tp.value " . "JOIN " . static::$prefix . "teams AS t ON t.id = tp.objectId " . "JOIN " . static::$prefix . "seasons AS s ON s.id = t.season_id AND s.active = '1' " . "JOIN " . static::$prefix . "leagues AS l ON l.id = s.league_id " . "LEFT JOIN " . static::$prefix . "team_properties AS lp ON t.id = lp.objectId AND lp.property_key = 'location' " . "LEFT JOIN " . static::$prefix . "locations AS loc ON loc.id = lp.value " . "WHERE tp.property_key = 'vice_captain' ";
    $data = $this->getDb()->get_results($sql);
    $captains = array();
    foreach ($data as $d) {
      $d->role = 'vice_captain';
      $captains[] = $d;
    }

    return $captains;

  }

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

  public function getScoresForTeamAndMatch($match, $team) {

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

  public function getTeamScoreForGameDay($team_id, $game_day_id) {

    $gameDayService = ServiceBroker::getGameDayService();

    $day = $gameDayService->byId($game_day_id);

    $sql = "SELECT " . "team_scores.team_id, " . "sum(team_scores.score) as score, " . "sum(team_scores.win) as wins, " . "sum(team_scores.loss) as losses, " . "sum(team_scores.draw) as draws, " . "sum(team_scores.goalsFor) as goalsFor, " . "sum(team_scores.goalsAgainst) as goalsAgainst " . "FROM " . static::$prefix . "game_days, " . "" . static::$prefix . "team_scores  " . "WHERE game_days.season_id='" . $day->getSeasonId() . "' " . "AND game_days.number <= '" . $day->getNumber() . "' " . "AND gameDay_id=game_days.id " . "AND team_id=" . $team_id;

    return $this->getDb()->get_row($sql);

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

  public function getScheduleForGameDay($day) {
    $sql = "SELECT * FROM " . static::$prefix . "matches WHERE game_day_id = '" . esc_sql($day->ID) . "'";
    $schedule = new stdClass;
    $schedule->matches = $this->getDb()->get_results($sql);
    $schedule->number = $day->number;
    $schedule->day = $day;
    $teamService = ServiceBroker::getTeamService();
    foreach ($schedule->matches as $match) {
      $match->home = $teamService->byId($match->home_team);
      $match->away = $teamService->byId($match->away_team);
    }

    return $schedule;
  }

  public function getScheduleForSeason($season) {

    $gameDayService = ServiceBroker::getGameDayService();
    $days = $gameDayService->allForSeason($season->ID);
    $schedules = array();
    foreach ($days as $day) {
      $schedules[] = $this->getScheduleForGameDay($day);
    }

    return $schedules;
  }

  public function getCurrentTeamForClub($clubId) {
    $sql = "SELECT * FROM " . static::$prefix . "teams WHERE club_id = '" . esc_sql($clubId) . "' ORDER BY ID DESC LIMIT 1";
    $team = $this->getDb()->get_row($sql);
    $properties = $this->getTeamProperties($team->ID);
    $team->properties = $properties;

    return $team;
  }


  public function getGamesForTeam($teamid) {

    $sql = "SELECT  m.id,
                        m.score_away,
                        m.fixture,
                        m.score_home,
                        m.location,
                        ht.name AS homename,
                        at.name AS awayname,
                        at.id AS awayid,
                        ht.id AS homeid
                FROM    " . static::$prefix . "game_days AS gd,
                        " . static::$prefix . "matches AS m,
                        " . static::$prefix . "teams AS at,
                        " . static::$prefix . "teams AS ht
                WHERE   (m.home_team = '" . esc_sql($teamid) . "' OR m.away_team = '" . esc_sql($teamid) . "')
                AND     m.game_day_id = gd.id
                AND     at.id = m.away_team
                AND     ht.id = m.home_team
                ORDER BY m.fixture ASC";

    $query = $this->getDb()->prepare($sql, array());
    return $this->getDb()->get_results($query);

  }

  public function getAllUpcomingGames() {

    $sql = "SELECT  m.id,
                        m.score_away,
                        m.fixture,
                        m.score_home,
                        m.location,
                        l.id AS league_id,
                        ht.name AS homename,
                        at.name AS awayname,
                        at.id AS awayid,
                        ht.id AS homeid
                FROM    " . static::$prefix . "leagues AS l,
                        " . static::$prefix . "seasons AS s,
                        " . static::$prefix . "game_days AS gd,
                        " . static::$prefix . "matches AS m,
                        " . static::$prefix . "teams AS at,
                        " . static::$prefix . "teams AS ht
                WHERE     s.id = l.current_season
                AND     l.active = 1
                AND     gd.id = s.current_game_day
                AND     m.game_day_id = gd.id
                AND     at.id = m.away_team
                AND     ht.id = m.home_team ORDER BY l.name ASC";

    return $this->getDb()->get_results($sql);

  }

  public function getUpcomingGames($league_id) {

    $sql = "SELECT 	m.id,
						m.score_away,
						m.fixture,
						m.score_home,
						m.location,
            m.status,
						ht.name AS homename,
						at.name AS awayname,
						at.id AS awayid,
						ht.id AS homeid
				FROM 	" . static::$prefix . "leagues AS l,
						" . static::$prefix . "seasons AS s,
						" . static::$prefix . "game_days AS gd,
						" . static::$prefix . "matches AS m,
						" . static::$prefix . "teams AS at,
						" . static::$prefix . "teams AS ht
				WHERE 	l.id = '%s'
				AND 	s.id = l.current_season
				AND 	gd.id = s.current_game_day
				AND 	m.game_day_id = gd.id
				AND 	at.id = m.away_team
				AND 	ht.id = m.home_team";

    $query = $this->getDb()->prepare($sql, $league_id);
    return $this->getDb()->get_results($query);

  }

  public function getTeamsByName($teamName) {
    $teamService = ServiceBroker::getTeamService();
    $sql = "SELECT ID FROM " . static::$prefix . "teams WHERE name = '" . esc_sql($teamName) . "'";
    $teamIds = $this->getDb()->get_results($sql);
    $teams = array();
    foreach ($teamIds as $teamId) {
      $teams[] = $teamService->byId($teamId->ID);
    }

    return $teams;
  }

  public function getReminderMatches($days_off) {
    $sql = "select l.name as league, s.id as season_id, g.number as day_number, l.code as leaguecode, home.name as home, away.name as away, home.id as home_id, away.id as away_id, loc.title as location, CONCAT(home.name,' gg. ', away.name) as title, slack.value as slack " . "from leagues as l " . "JOIN seasons as s ON s.id = l.current_season " . "JOIN game_days AS g ON g.id = s.current_game_day " . "JOIN game_days AS ug ON ug.season_id = s.id and ug.number = g.number + 1 AND date(ug.fixture) = date(NOW() + INTERVAL " . $days_off . " DAY) " . "JOIN `matches` AS m ON m.game_day_id = ug.id " . "JOIN teams as home ON m.home_team = home.id " . "JOIN teams AS away ON m.away_team = away.id " . "LEFT JOIN team_properties AS p ON p.objectId = home.id AND p.property_key = 'location' " . "LEFT JOIN locations AS loc ON loc.id = p.value " . "left join season_properties AS sp ON sp.objectId = s.id AND sp.property_key = 'season_admin' " . "left join players AS adm ON adm.id = sp.value " . "left join player_properties as slack ON adm.id = slack.objectId AND slack.property_key = 'slack_alias' " . "where l.active = 1 order by l.name ASC";
    $dbh = $this->getDb();
    $stmt = $dbh->prepare($sql, array());
    return $dbh->get_results($stmt);
  }

  public function getTopMatches($matches) {
    $seasons = array();
    $days = array();
    foreach ($matches as $match) {
      $days[$match->season_id] = $match->day_number;
      $seasons[$match->season_id] = array("name" => $match->league, "code" => $match->leaguecode);
    }

    $potentialMatches = array();
    foreach ($days as $season => $day) {
      $sql = "SELECT " . "team_scores.team_id, " . "sum(team_scores.score) as score, " . "sum(team_scores.gamesFor - team_scores.gamesAgainst) as gameDiff " . "FROM game_days, " . "team_scores  " . "WHERE game_days.season_id='" . $season . "' " . "AND game_days.number <= '" . $day . "' " . "AND gameDay_id=game_days.id " . "GROUP BY team_id " . "ORDER BY score DESC, gameDiff DESC ";
      $dbh = $this->getDb();
      $stmt = $dbh->prepare($sql, array());
      $ranking = $dbh->get_results($stmt);
      $first = $ranking[0];
      $second = $ranking[1];
      $lastButOne = $ranking[count($ranking) - 2];
      $last = $ranking[count($ranking) - 1];
      $potentialMatches[$seasons[$season]['name']] = array("top" => array($first->team_id, $second->team_id), "bottom" => array($lastButOne->team_id, $last->team_id));
    }

    $topMatches = array();
    foreach ($matches as $match) {
      $league = $match->league;
      $home = $match->home_id;
      $away = $match->away_id;
      if (in_array($home, $potentialMatches[$league]['top']) && in_array($away, $potentialMatches[$league]['top'])) {
        $topMatches[$league] = array("type" => "top", "home" => $match->home, "away" => $match->away, "leaguecode" => $match->leaguecode, "contact" => $match->slack);
      }
      if (in_array($home, $potentialMatches[$league]['bottom']) && in_array($away, $potentialMatches[$league]['bottom'])) {
        $topMatches[$league] = array("type" => "bottom", "home" => $match->home, "away" => $match->away, "leaguecode" => $match->leaguecode, "contact" => $match->slack);
      }
    }
    return $topMatches;
  }

  public function getScore($key) {
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


  public function installWordpressDatabase() {
    // $version = get_option(static::$VERSION_KEY);
    // if ( !$version || $version < static::$VERSION ) {
    $mapper = Mapping::getMapper();
    $mapper->updateSchema(ApiKey::class);
    $mapper->updateSchema(Award::class);
    $mapper->updateSchema(Club::class);
    $mapper->updateSchema(ClubAward::class);
    $mapper->updateSchema(ClubProperty::class);
    $mapper->updateSchema(Game::class);
    $mapper->updateSchema(GameDay::class);
    $mapper->updateSchema(League::class);
    $mapper->updateSchema(LeagueProperty::class);
    $mapper->updateSchema(Location::class);
    $mapper->updateSchema(Match::class);
    $mapper->updateSchema(MatchProperty::class);
    $mapper->updateSchema(Player::class);
    $mapper->updateSchema(PlayerProperty::class);
    $mapper->updateSchema(Season::class);
    $mapper->updateSchema(SeasonProperty::class);
    $mapper->updateSchema(Set::class);
    $mapper->updateSchema(SetAwayPlayer::class);
    $mapper->updateSchema(SetHomePlayer::class);
    $mapper->updateSchema(Team::class);
    $mapper->updateSchema(TeamPlayer::class);
    $mapper->updateSchema(TeamPlayerProperty::class);
    $mapper->updateSchema(TeamProperty::class);
    $mapper->updateSchema(TeamScore::class);
    update_option(static::$VERSION_KEY, static::$VERSION);
  }

  public function installWordpressData() {
    // TODO: fill database with initial data, this needs every table under orm control
  }

}
