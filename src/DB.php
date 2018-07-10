<?php

namespace KKL\Ligatool;

use KKL\Ligatool\Model\ApiKey;
use stdClass;
use Symlink\ORM\Manager;
use Symlink\ORM\Mapping;
use wpdb;

abstract class DB {
  
  /**
   * @deprecated move to orm
   * @var wpdb wordpres db layer
   */
  private $db;
  
  /**
   * @var Manager simple wordpress orm layer
   */
  private $orm;
  
  public function __construct(wpdb $db = null) {
    if($db !== null) {
      $this->db = $db;
    } else {
      $options = get_option('kkl_ligatool');
      $this->db = new wpdb($options['db_user'], $options['db_pass'], $options['db_name'], $options['db_host']);
    }
    $this->orm = Manager::getManager();
  }
  
  
  public function getClubData($id) {
    
    $query = $this->getDb()->prepare("SELECT * FROM clubs WHERE short_name = '%s' LIMIT 1", array($id));
    $result = $this->getDb()->get_row($query);
    
    return $result;
    
  }
  
  /**
   * @deprecated user orm layer for new functions, implement databse-code in db-layer not in modules
   * @return wpdb
   */
  public function getDb() {
    return $this->db;
  }
  
  /**
   * @return Manager
   */
  protected function getOrm() {
    return $this->orm;
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
				FROM 	leagues AS l,
						seasons AS s,
						game_days AS gd,
						matches AS m,
						teams AS at,
						teams AS ht
				WHERE 	l.id = '%s'
				AND 	s.id = l.current_season
				AND 	gd.id = s.current_game_day
				AND 	m.game_day_id = gd.id
				AND 	at.id = m.away_team
				AND 	ht.id = m.home_team";
    
    $query = $this->getDb()->prepare($sql, $league_id);
    $result = $this->getDb()->get_results($query);
    
    return $result;
    
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
                FROM    game_days AS gd,
                        matches AS m,
                        teams AS at,
                        teams AS ht
                WHERE   (m.home_team = '" . esc_sql($teamid) . "' OR m.away_team = '" . esc_sql($teamid) . "')
                AND     m.game_day_id = gd.id
                AND     at.id = m.away_team
                AND     ht.id = m.home_team
                ORDER BY m.fixture ASC";
    
    $query = $this->getDb()->prepare($sql, array());
    $result = $this->getDb()->get_results($query);
    
    return $result;
    
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
                FROM    leagues AS l,
                        seasons AS s,
                        game_days AS gd,
                        matches AS m,
                        teams AS at,
                        teams AS ht
                WHERE     s.id = l.current_season
                AND     l.active = 1
                AND     gd.id = s.current_game_day
                AND     m.game_day_id = gd.id
                AND     at.id = m.away_team
                AND     ht.id = m.home_team ORDER BY l.name ASC";
    
    $result = $this->getDb()->get_results($sql);
    
    return $result;
    
  }
  
  public function getClubs() {
    $sql = "SELECT * FROM clubs ORDER BY name ASC";
    $results = $this->getDb()->get_results($sql);
    if(is_array($results)) {
      return $results;
    }else{
      return array();
    }
  }
  
  public function getTeams() {
    $sql = "SELECT * FROM teams ORDER BY name ASC";
    $teams = $this->getDb()->get_results($sql);
    if(is_array($teams)) {
      foreach($teams as $team) {
        $properties = $this->getTeamProperties($team->id);
        $team->properties = $properties;
      }
      return $teams;
    }else{
      return array();
    }
  }
  
  public function getTeamProperties($teamId) {
    $sql = "SELECT * FROM team_properties WHERE objectId = '" . esc_sql($teamId) . "'";
    $results = $this->getDb()->get_results($sql);
    $properties = array();
    foreach($results as $result) {
      $properties[$result->property_key] = $result->value;
      if($result->property_key == 'captain') {
        $player = $this->getPlayer($result->value);
        $properties['captain_email'] = $player->email;
      } elseif($result->property_key == 'vice_captain') {
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
    $sql = "SELECT * FROM players WHERE id = '" . esc_sql($playerId) . "'";
    $player = $this->getDb()->get_row($sql);
    $properties = $this->getPlayerProperties($player->id);
    $player->properties = $properties;
    
    return $player;
  }
  
  public function getPlayerProperties($playerId) {
    $sql = "SELECT * FROM player_properties WHERE objectId = '" . esc_sql($playerId) . "'";
    $results = $this->getDb()->get_results($sql);
    $properties = array();
    foreach($results as $result) {
      $properties[$result->property_key] = $result->value;
    }
    
    return $properties;
  }
  
  public function getLocation($location_id) {
    $sql = "SELECT * FROM locations WHERE id = '" . esc_sql($location_id) . "'";
    
    return $this->getDb()->get_row($sql);
  }
  
  public function getPlayers() {
    $sql = "SELECT * FROM players ORDER BY first_name, last_name ASC";
    $players = $this->getDb()->get_results($sql);
    foreach($players as $player) {
      $properties = $this->getPlayerProperties($player->id);
      $player->properties = $properties;
    }
    
    return $players;
  }
  
  public function getCurrentTeamForClub($clubId) {
    $sql = "SELECT * FROM teams WHERE club_id = '" . esc_sql($clubId) . "' ORDER BY id DESC LIMIT 1";
    $team = $this->getDb()->get_row($sql);
    $properties = $this->getTeamProperties($team->id);
    $team->properties = $properties;
    
    return $team;
  }
  
  public function getAwardsForClub($clubId) {
    $sql = "SELECT * FROM club_has_awards AS c JOIN awards AS a ON a.id = c.award_id WHERE c.club_id = '" . esc_sql($clubId) . "'";
    
    return $this->getDb()->get_results($sql);
  }
  
  public function getLeagues() {
    $sql = "SELECT * FROM leagues ORDER BY name ASC";
    $leagues = $this->getDb()->get_results($sql);
    foreach($leagues as $league) {
      $league->active = boolval($league->active);
    }
    
    return $leagues;
  }
  
  public function getActiveLeagues() {
    $sql = "SELECT * FROM leagues WHERE active = 1 ORDER BY name ASC";
    $leagues = $this->getDb()->get_results($sql);
    foreach($leagues as $league) {
      $league->active = boolval($league->active);
    }
    
    return $leagues;
  }
  
  public function getInactiveLeagues() {
    $sql = "SELECT * FROM leagues WHERE active = 0 ORDER BY name ASC";
    $leagues = $this->getDb()->get_results($sql);
    foreach($leagues as $league) {
      $league->active = boolval($league->active);
    }
    
    return $leagues;
  }
  
  public function getLeagueBySlug($slug) {
    $sql = "SELECT * FROM leagues WHERE code = '" . esc_sql($slug) . "'";
    $league = $this->getDb()->get_row($sql);
    $league->active = boolval($league->active);
    
    return $league;
  }
  
  public function getSeasons() {
    $sql = "SELECT * FROM seasons ORDER BY start_date ASC, name ASC";
    
    return $this->getDb()->get_results($sql);
  }
  
  public function getLeagueProperties($leagueId) {
    $sql = "SELECT * FROM league_properties WHERE objectId = '" . esc_sql($leagueId) . "'";
    $results = $this->getDb()->get_results($sql);
    $properties = array();
    foreach($results as $result) {
      $properties[$result->property_key] = $result->value;
    }
    
    return $properties;
  }
  
  public function setSeasonProperties($season, $properties) {
    foreach($properties as $key => $value) {
      $this->getDb()->delete('season_properties', array('objectId' => $season->id, 'property_key' => $key));
      if($value !== false) {
        $this->getDb()->insert('season_properties', array('objectId' => $season->id, 'property_key' => $key, 'value' => $value,), array('%d', '%s', '%s'));
      }
    }
  }
  
  public function getSeasonByLeagueAndYear($league, $year) {
    $sql = "SELECT * FROM seasons WHERE league_id = '" . esc_sql($league) . "' AND YEAR(start_date) = '" . esc_sql($year) . "'";
    
    return $this->getDb()->get_row($sql);
  }
  
  public function getSeasonsByLeague($league) {
    $sql = "SELECT * FROM seasons WHERE league_id = '" . esc_sql($league) . "'";
    
    return $this->getDb()->get_results($sql);
  }
  
  public function getTeamsForClub($club_id) {
    $sql = "SELECT t.* FROM teams AS t, seasons AS s WHERE t.club_id = '" . esc_sql($club_id) . "' AND t.season_id = s.id ORDER BY s.start_date ASC";
    $teams = $this->getDb()->get_results($sql);
    foreach($teams as $team) {
      $properties = $this->getTeamProperties($team->id);
      $team->properties = $properties;
    }
    
    return $teams;
  }
  
  public function getGameDays() {
    $sql = "SELECT * FROM game_days ORDER BY fixture ASC, number ASC";
    
    return $this->getDb()->get_results($sql);
  }
  
  public function getPreviousGameDay($day) {
    $number = $day->number - 1;
    if($number < 1) {
      return false;
    }
    $sql = "SELECT * FROM game_days WHERE season_id = '" . esc_sql($day->season_id) . "' AND number = '" . esc_sql($number) . "'";
    
    return $this->getDb()->get_row($sql);
  }
  
  public function getNextGameDay($day) {
    $number = $day->number + 1;
    $sql = "SELECT * FROM game_days WHERE season_id = '" . esc_sql($day->season_id) . "' AND number = '" . esc_sql($number) . "'";
    
    return $this->getDb()->get_row($sql);
  }
  
  public function getGameDayBySeasonAndPosition($seasonId, $position) {
    $sql = "SELECT * FROM game_days WHERE season_id = '" . esc_sql($seasonId) . "' AND number = '" . esc_sql($position) . "'";
    
    return $this->getDb()->get_row($sql);
  }
  
  public function getCurrentSeason($leagueId) {
    $result = null;
    $sql = "SELECT current_season FROM leagues WHERE id = '" . esc_sql($leagueId) . "'";
    $seasonId = $this->getDb()->get_var($sql);
    if($seasonId) {
      $sql = "SELECT * FROM seasons WHERE id = '" . esc_sql($seasonId) . "'";
      $result = $this->getDb()->get_row($sql);
    }
    
    return $result;
  }
  
  public function getCurrentGameDayForLeague($leagueId) {
    $sql = "SELECT * FROM game_days WHERE id = (SELECT current_game_day AS id FROM seasons WHERE id = (SELECT current_season AS id FROM leagues WHERE id = '" . esc_sql($leagueId) . "'))";
    
    return $this->getDb()->get_row($sql);
  }
  
  public function getCurrentGameDayForSeason($seasonId) {
    $sql = "SELECT * FROM game_days WHERE id = (SELECT current_game_day AS id FROM seasons WHERE id = '" . esc_sql($seasonId) . "')";
    
    return $this->getDb()->get_row($sql);
  }
  
  public function getLeagueForGameday($dayId) {
    $sql = "SELECT * FROM leagues WHERE id = (SELECT league_id AS id FROM seasons WHERE id = (SELECT season_id AS id FROM game_days WHERE id = '" . esc_sql($dayId) . "'))";
    $league = $this->getDb()->get_row($sql);
    $league->active = boolval($league->active);
    
    return $league;
  }
  
  public function getLeagueForSeason($seasonId) {
    $sql = "SELECT * FROM leagues WHERE id = (SELECT league_id AS id FROM seasons WHERE id = '" . esc_sql($seasonId) . "')";
    $league = $this->getDb()->get_row($sql);
    $league->active = boolval($league->active);
    
    return $league;
  }
  
  public function getSeasonForGameday($dayId) {
    $sql = "SELECT * FROM seasons WHERE id = (SELECT season_id AS id FROM game_days WHERE id = '" . esc_sql($dayId) . "')";
    
    return $this->getDb()->get_row($sql);
  }
  
  public function setCurrentGameDay($season, $day) {
    $columns = array();
    $columns['current_game_day'] = $day->id;
    $this->getDb()->update('seasons', $columns, array('id' => $season->id));
  }
  
  public function getMatches() {
    $sql = "SELECT * FROM matches ORDER BY fixture ASC";
    
    return $this->getDb()->get_results($sql);
  }
  
  public function getFullMatchInfo($matchId) {
    $sql = "SELECT m.*, away.name AS away_name, home.name AS home_name, l.name AS league, s.score_home, s.score_away, g.goals_home, g.goals_away, homecaptain.email AS home_email, awaycaptain.email AS away_email FROM matches AS m LEFT JOIN sets AS s ON m.id = s.match_id LEFT JOIN games AS g ON s.id = g.set_id LEFT JOIN teams AS away ON away.id = m.away_team LEFT JOIN team_properties AS ac ON ac.objectId = away.id AND ac.property_key = 'captain' LEFT JOIN players AS awaycaptain ON awaycaptain.id = ac.value LEFT JOIN teams AS home ON home.id = m.home_team LEFT JOIN team_properties AS hc ON hc.objectId = home.id AND hc.property_key = 'captain' LEFT JOIN players AS homecaptain ON homecaptain.id = hc.value LEFT JOIN game_days AS gd ON gd.id = m.game_day_id LEFT JOIN seasons AS season ON season.id = gd.season_id LEFT JOIN leagues AS l ON l.id = season.league_id WHERE m.id = '" . esc_sql($matchId) . "'";
    
    return $this->getDb()->get_row($sql);
    
  }
  
  public function getMatchesForSeason($seasonId) {
    $sql = "SELECT * FROM game_days WHERE season_id = '" . esc_sql($seasonId) . "' ORDER BY number ASC";
    
    return $this->getDb()->get_results($sql);
  }
  
  public function getMatchesForTeam($teamId) {
    $sql = "SELECT * FROM matches WHERE (home_team = '" . esc_sql($teamId) . "' OR away_team = '" . esc_sql($teamId) . "') ORDER BY fixture ASC";
    
    return $this->getDb()->get_results($sql);
  }
  
  public function getMatchesByGameDay($dayId) {
    $sql = "SELECT * FROM matches WHERE game_day_id = '" . esc_sql($dayId) . "' ORDER BY fixture ASC";
    
    return $this->getDb()->get_results($sql);
  }
  
  public function getPlayerByMailAddress($mailAddress) {
    $sql = "SELECT * FROM players WHERE email = '" . esc_sql($mailAddress) . "'";
    $player = $this->getDb()->get_row($sql);
    $properties = $this->getPlayerProperties($player->id);
    $player->properties = $properties;
    
    return $player;
  }
  
  public function getClubProperties($clubId) {
    $sql = "SELECT * FROM club_properties WHERE objectId = '" . esc_sql($clubId) . "'";
    $results = $this->getDb()->get_results($sql);
    $properties = array();
    foreach($results as $result) {
      $properties[$result->property_key] = $result->value;
    }
    
    return $properties;
  }
  
  public function getMatchProperties($matchId) {
    $sql = "SELECT * FROM match_properties WHERE objectId = '" . esc_sql($matchId) . "'";
    $results = $this->getDb()->get_results($sql);
    $properties = array();
    foreach($results as $result) {
      $properties[$result->property_key] = $result->value;
    }
    
    return $properties;
  }
  
  public function getCaptainsContactData() {
    $sql = "SELECT " . "p.first_name as first_name, p.last_name as last_name, p.email as email, p.phone as phone, " . "t.name as team, t.short_name as team_short, " . "l.name as league, l.code as league_short, loc.title as location " . "FROM team_properties AS tp " . "JOIN players AS p ON p.id = tp.value " . "JOIN teams AS t ON t.id = tp.objectId " . "JOIN seasons AS s ON s.id = t.season_id AND s.active = '1' " . "JOIN leagues AS l ON l.id = s.league_id " . "LEFT JOIN team_properties AS lp ON t.id = lp.objectId AND lp.property_key = 'location' " . "LEFT JOIN locations AS loc ON loc.id = lp.value " . "WHERE tp.property_key = 'captain' ";
    $data = $this->getDb()->get_results($sql);
    $captains = array();
    foreach($data as $d) {
      $d->role = 'captain';
      $captains[] = $d;
    }
    
    return $captains;
  }
  
  public function getViceCaptainsContactData() {
    $sql = "SELECT " . "p.first_name as first_name, p.last_name as last_name, p.email as email, p.phone as phone, " . "t.name as team, t.short_name as team_short, " . "l.name as league, l.code as league_short, loc.title as location " . "FROM team_properties AS tp " . "JOIN players AS p ON p.id = tp.value " . "JOIN teams AS t ON t.id = tp.objectId " . "JOIN seasons AS s ON s.id = t.season_id AND s.active = '1' " . "JOIN leagues AS l ON l.id = s.league_id " . "LEFT JOIN team_properties AS lp ON t.id = lp.objectId AND lp.property_key = 'location' " . "LEFT JOIN locations AS loc ON loc.id = lp.value " . "WHERE tp.property_key = 'vice_captain' ";
    $data = $this->getDb()->get_results($sql);
    $captains = array();
    foreach($data as $d) {
      $d->role = 'vice_captain';
      $captains[] = $d;
    }
    
    return $captains;
    
  }
  
  public function getTeamByCode($teamCode) {
    $sql = "SELECT * FROM teams WHERE short_name = '" . esc_sql($teamCode) . "'";
    $team = $this->getDb()->get_row($sql);
    $properties = $this->getTeamProperties($team->id);
    $team->properties = $properties;
    
    return $team;
  }
  
  public function getTeamByCodeAndSeason($teamCode, $seasonId) {
    $sql = "SELECT * FROM teams WHERE short_name = '" . esc_sql($teamCode) . "' AND season_id = '" . esc_sql($seasonId) . "'";
    $team = $this->getDb()->get_row($sql);
    $properties = $this->getTeamProperties($team->id);
    $team->properties = $properties;
    
    return $team;
  }
  
  public function getTeamsByName($teamName) {
    $sql = "SELECT id FROM teams WHERE name = '" . esc_sql($teamName) . "'";
    $teamIds = $this->getDb()->get_results($sql);
    $teams = array();
    foreach($teamIds as $teamId) {
      $teams[] = $this->getTeam($teamId->id);
    }
    
    return $teams;
  }
  
  public function getTeam($teamId) {
    $sql = "SELECT * FROM teams WHERE id = '" . esc_sql($teamId) . "'";
    $team = $this->getDb()->get_row($sql);
    $properties = $this->getTeamProperties($team->id);
    $team->properties = $properties;
    
    return $team;
  }
  
  public function getClubByCode($clubCode) {
    $sql = "SELECT * FROM clubs WHERE short_name = '" . esc_sql($clubCode) . "'";
    
    return $this->getDb()->get_row($sql);
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
           "GROUP BY team_id " . //      "ORDER BY score DESC, gameDiff DESC, goalDiff DESC, team_scores.goalsFor DESC";
           "ORDER BY score DESC, gameDiff DESC";
    
    $ranking = $this->getDb()->get_results($sql);
    
    $original_size = count($ranking);
    $teams = $this->getTeamsForSeason($seasonId);
    if($original_size < count($teams)) {
      // find place where to insert scores
      if($original_size > 0) {
        for($i = 0; $i < $original_size; $i++) {
          $score = $ranking[$i];
          // punkte = 0 oder weniger
          // UND differenz weniger als null ODER differenz gleich 0 und geschossene tore weniger als null
          if($score->score <= 0 && (($score->goalDiff < 0) || ($score->goalDiff == 0 && $score->goalsFor <= 0))) {
            foreach($teams as $team) {
              $has_score = false;
              foreach($ranking as $iscore) {
                if($team->id == $iscore->team_id) {
                  $has_score = true;
                }
              }
              if(!$has_score) {
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
          } elseif($original_size == ($i + 1)) {
            // last element, add scores here
            foreach($teams as $team) {
              $has_score = false;
              foreach($ranking as $iscore) {
                if($team->id == $iscore->team_id) {
                  $has_score = true;
                }
              }
              if(!$has_score) {
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
        foreach($teams as $team) {
          $has_score = false;
          foreach($ranking as $iscore) {
            if($team->id == $iscore->team_id) {
              $has_score = true;
            }
          }
          if(!$has_score) {
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
  
    $properties = $this->getSeasonProperties($seasonId);
    $relegationMarkers = array();
    if($properties && array_key_exists('relegation_markers', $properties)) {
      $relegationMarkers = explode(',', $properties['relegation_markers']);
    }
    
    $position = 0;
    $previousScore = 0;
    $previousGameDiff = 0;
    $markerCount = 0;
    foreach($ranking as $rank) {
      
      $position++;
      
      if(in_array($position, $relegationMarkers)) {
        $markerCount++;
        $rank->relegation_marker = $markerCount;
      }else{
        $rank->relegation_marker = false;
      }
      
      $rank->team = $this->getTeam($rank->team_id);
      $rank->games = $rank->wins + $rank->losses + $rank->draws;
      
      if(($previousScore == $rank->score) && ($previousGameDiff == $rank->gameDiff)) {
        $rank->shared_rank = true;
      }
      
      $previousScore = $rank->score;
      $previousGameDiff = $rank->gameDiff;
      $rank->position = $position;
      
    }
    
    return $ranking;
    
  }
  
  public function getTeamsForSeason($seasonId) {
    $sql = "SELECT * FROM teams WHERE season_id = '" . esc_sql($seasonId) . "'";
    $teams = $this->getDb()->get_results($sql);
    foreach($teams as $team) {
      $properties = $this->getTeamProperties($team->id);
      $team->properties = $properties;
    }
    
    return $teams;
  }
  
  public function getTeamScoreForGameDay($team_id, $game_day_id) {
    
    $day = $this->getGameDay($game_day_id);
    
    $sql = "SELECT " . "team_scores.team_id, " . "sum(team_scores.score) as score, " . "sum(team_scores.win) as wins, " . "sum(team_scores.loss) as losses, " . "sum(team_scores.draw) as draws, " . "sum(team_scores.goalsFor) as goalsFor, " . "sum(team_scores.goalsAgainst) as goalsAgainst " . "FROM game_days, " . "team_scores  " . "WHERE game_days.season_id='" . $day->season_id . "' " . "AND game_days.number <= '" . $day->number . "' " . "AND gameDay_id=game_days.id " . "AND team_id=" . $team_id;
    
    $scores = $this->getDb()->get_row($sql);
    
    return $scores;
    
  }
  
  public function getGameDay($dayId) {
    $sql = "SELECT * FROM game_days WHERE id = '" . esc_sql($dayId) . "'";
    
    return $this->getDb()->get_row($sql);
  }
  
  public function getScheduleForSeason($season) {
    
    $days = $this->getGameDaysForSeason($season->id);
    $schedules = array();
    foreach($days as $day) {
      $schedules[] = $this->getScheduleForGameDay($day);
    }
    
    return $schedules;
  }
  
  public function getGameDaysForSeason($seasonId) {
    $sql = "SELECT * FROM game_days WHERE season_id = '" . esc_sql($seasonId) . "' ORDER BY number ASC";
    
    return $this->getDb()->get_results($sql);
  }
  
  public function getScheduleForGameDay($day) {
    $sql = "SELECT * FROM matches WHERE game_day_id = '" . esc_sql($day->id) . "'";
    $schedule = new stdClass;
    $schedule->matches = $this->getDb()->get_results($sql);
    $schedule->number = $day->number;
    $schedule->day = $day;
    foreach($schedule->matches as $match) {
      $match->home = $this->getTeam($match->home_team);
      $match->away = $this->getTeam($match->away_team);
    }
    
    return $schedule;
  }
  
  public function getAllLocations() {
    $sql = "SELECT * FROM locations";
    $locations = $this->getDb()->get_results($sql);
    foreach($locations as $location) {
      $location->teams = $this->getTeamsForLocation($location);
    }
    
    return $locations;
  }
  
  public function getTeamsForLocation($location) {
    $sql = "SELECT objectId FROM team_properties WHERE property_key = 'location' AND value = '" . esc_sql($location->id) . "'";
    $teamIds = $this->getDb()->get_results($sql);
    $teams = array();
    foreach($teamIds as $teamId) {
      if($teamId->objectId && $teamId->objectId != 0) {
        $teams[] = $this->getTeam($teamId->objectId);
      }
    }
    
    return $teams;
  }
  
  public function getLocations() {
    $sql = "SELECT * FROM locations ORDER BY title ASC";
    
    return $this->getDb()->get_results($sql);
  }
  
  public function getAllGoals() {
    $sql = "SELECT SUM( goals_away ) FROM  `games` WHERE goals_away IS NOT NULL";
    
    return $this->getDb()->get_row($sql);
  }
  
  public function getAllGames() {
    $sql = "SELECT SUM( score_home ) FROM  `sets` WHERE score_home IS NOT NULL";
    
    return $this->getDb()->get_row($sql);
  }
  
  public function createOrUpdateTeam($team) {
    if($team->id) {
      $team = $this->updateTeam($team);
    } else {
      $team = $this->createTeam($team);
    }
    
    return $team;
  }
  
  public function updateTeam($team) {
    $columns = array();
    $columns['name'] = $team->name;
    $columns['short_name'] = $team->short_name;
    $columns['season_id'] = $team->season_id;
    $columns['club_id'] = $team->club_id;
    $this->getDb()->update('teams', $columns, array('id' => $team->id), array('%s', '%s', '%d', '%d',), array('%d'));
    
    return $this->getTeam($team->id);
  }
  
  public function createTeam($team) {
    $values = array('name' => $team->name, 'description' => $team->description, 'short_name' => $team->short_name, 'club_id' => $team->club_id, 'season_id' => $team->season_id,);
    $this->getDb()->insert('teams', $values, array('%s', '%s', '%s', '%d', '%d'));
    
    return $this->getTeam($this->getDb()->insert_id);
  }
  
  public function createOrUpdatePlayer($player) {
    if($player->id) {
      $player = $this->updatePlayer($player);
    } else {
      $player = $this->createPlayer($player);
    }
    
    return $player;
  }
  
  public function updatePlayer($player) {
    $columns = array();
    $columns['first_name'] = $player->first_name;
    $columns['last_name'] = $player->last_name;
    $columns['email'] = $player->email;
    $columns['phone'] = $player->phone;
    $this->getDb()->update('players', $columns, array('id' => $player->id), array('%s', '%s', '%s', '%s',), array('%d'));
    
    return $this->getPlayer($player->id);
  }
  
  public function createPlayer($player) {
    $values = array('first_name' => $player->first_name, 'last_name' => $player->last_name, 'email' => $player->email, 'phone' => $player->phone,);
    $this->getDb()->insert('players', $values, array('%s', '%s', '%s', '%s'));
    
    return $this->getPlayer($this->getDb()->insert_id);
  }
  
  public function createOrUpdateMatch($match) {
    if($match->id) {
      $match = $this->updateMatch($match);
    } else {
      $match = $this->createMatch($match);
    }
    if($match->status == 3) {
      $this->calculateScores($match);
    }
    
    return $match;
  }
  
  public function updateMatch($match) {
    
    $values = array('game_day_id' => $match->game_day_id, 'score_away' => $match->score_away, 'score_home' => $match->score_home, 'fixture' => $match->fixture, 'location' => $match->location, 'notes' => $match->notes, 'status' => $match->status, 'away_team' => $match->away_team, 'home_team' => $match->home_team,);
    $this->getDb()->update('matches', $values, array('id' => $match->id), array('%d', '%d', '%d', '%s', '%s', '%s', '%d', '%d', '%d',), array('%d'));
    
    $set = new stdClass;
    $set->score_away = $match->score_away;
    $set->score_home = $match->score_home;
    $set->number = 1;
    $set->match_id = $match->id;
    $set = $this->updateSet($set);
    
    $game = new stdClass;
    $game->goals_home = $match->goals_home;
    $game->goals_away = $match->goals_away;
    $game->number = 1;
    $game->set_id = $set->id;
    $this->updateGame($game);
    
    return $this->getMatch($match->id);
  }
  
  public function updateSet($set) {
    if(!$this->getSet($set->match_id)) {
      return $this->createSet($set);
    }
    $values = array('score_away' => $set->score_away, 'score_home' => $set->score_home, 'number' => $set->number, 'match_id' => $set->match_id,);
    $this->getDb()->update('sets', $values, array('match_id' => $set->match_id), array('%d', '%d', '%d', '%d',), array('%d'));
    
    return $this->getSet($set->match_id);
  }
  
  public function getSet($matchId) {
    $sql = "SELECT * FROM sets WHERE match_id = '" . esc_sql($matchId) . "'";
    
    return $this->getDb()->get_row($sql);
  }
  
  public function createSet($set) {
    $values = array('score_away' => $set->score_away, 'score_home' => $set->score_home, 'number' => $set->number, 'match_id' => $set->match_id,);
    $this->getDb()->insert('sets', $values, array('%d', '%d', '%d', '%d'));
    
    return $this->getSet($set->match_id);
  }
  
  public function updateGame($game) {
    if(!$this->getGame($game->set_id)) {
      return $this->createGame($game);
    }
    $values = array('goals_away' => $game->goals_away, 'goals_home' => $game->goals_home, 'number' => $game->number, 'set_id' => $game->set_id,);
    $this->getDb()->update('games', $values, array('set_id' => $game->set_id), array('%d', '%d', '%d', '%d',), array('%d'));
    
    return $this->getGame($game->match_id);
  }
  
  public function getGame($setId) {
    $sql = "SELECT * FROM games WHERE set_id = '" . esc_sql($setId) . "'";
    
    return $this->getDb()->get_row($sql);
  }
  
  public function createGame($game) {
    $values = array('goals_away' => $game->goals_away, 'goals_home' => $game->goals_home, 'number' => $game->number, 'set_id' => $game->set_id,);
    $this->getDb()->insert('games', $values, array('%d', '%d', '%d', '%d'));
    
    return $this->getGame($game->set_id);
  }
  
  public function getMatch($matchId) {
    $sql = "SELECT m.*, s.score_home, s.score_away, g.goals_home, g.goals_away FROM matches AS m LEFT JOIN sets AS s ON m.id = s.match_id LEFT JOIN games AS g ON s.id = g.set_id WHERE m.id = '" . esc_sql($matchId) . "'";
    
    return $this->getDb()->get_row($sql);
  }
  
  public function createMatch($match) {
    $values = array('game_day_id' => $match->game_day_id, 'score_away' => $match->score_away, 'score_home' => $match->score_home, 'fixture' => $match->fixture, 'location' => $match->location, 'notes' => $match->notes, 'status' => $match->status, 'away_team' => $match->away_team, 'home_team' => $match->home_team,);
    if($values['fixtures']) {
      $values['fixtures'] = null;
    }
    $this->getDb()->insert('matches', $values, array('%d', '%d', '%d', '%s', '%s', '%s', '%d', '%d', '%d'));
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
    $game->set_id = $set->id;
    $this->createGame($game);
    
    return $this->getMatch($match_id);
  }
  
  public function calculateScores($match) {
    
    $home = $this->getTeam($match->home_team);
    $away = $this->getTeam($match->away_team);
    
    $this->calculateScoresForTeamAndMatch($match, $home);
    $this->calculateScoresForTeamAndMatch($match, $away);
    
  }
  
  public function calculateScoresForTeamAndMatch($match, $team) {
    
    $day = $this->getGameDay($match->game_day_id);
    
    $sql = "SELECT " . "* " . "FROM " . "team_scores  " . "WHERE gameDay_id ='" . esc_sql($day->id) . "' " . "AND team_id = '" . esc_sql($team->id) . "';";
    
    $score = $this->getDb()->get_row($sql);
    if($score == null) {
      $score = new stdClass;
      $score->team_id = $team->id;
      $score->gameDay_id = $day->id;
    }
    
    $score->win = 0;
    $score->draw = 0;
    $score->loss = 0;
    $score->gamesAgainst = 0;
    $score->gamesFor = 0;
    $score->goalsAgainst = 0;
    $score->goalsFor = 0;
    $score->score = 0;
    
    if($match->home_team == $team->id) {
      $score->goalsFor = $this->getGoalsForTeam($match, $match->home_team);
      $score->goalsAgainst = $this->getGoalsForTeam($match, $match->away_team);
      $score->gamesFor = $match->score_home;
      $score->gamesAgainst = $match->score_away;
      if($match->score_home > $match->score_away) {
        $score->score = $this->getScore("win");
        $score->win = 1;
      } elseif($match->score_home < $match->score_away) {
        $score->score = $this->getScore("loss");
        $score->loss = 1;
      } else {
        $score->score = $this->getScore("draw");
        $score->draw = 1;
      }
    }
    
    if($match->away_team == $team->id) {
      $score->goalsFor = $this->getGoalsForTeam($match, $match->away_team);
      $score->goalsAgainst = $this->getGoalsForTeam($match, $match->home_team);
      $score->gamesFor = $match->score_away;
      $score->gamesAgainst = $match->score_home;
      if($match->score_home > $match->score_away) {
        $score->score = $this->getScore("loss");
        $score->loss = 1;
      } elseif($match->score_home < $match->score_away) {
        $score->score = $this->getScore("win");
        $score->win = 1;
      } else {
        $score->score = $this->getScore("draw");
        $score->draw = 1;
      }
    }
    
    if($score->id) {
      $this->getDb()->update('team_scores', get_object_vars($score), array('id' => $score->id));
    } else {
      $this->getDb()->insert('team_scores', get_object_vars($score));
    }
    
  }
  
  protected function getGoalsForTeam($match, $team_id) {
    
    $sql = "SELECT sum(`goals_away`) AS goals_away, sum(goals_home) AS goals_home FROM matches AS m " . "JOIN sets AS s ON s.match_id = m.id " . "JOIN games AS g ON g.set_id = s.id " . "WHERE m.id = " . esc_sql($match->id);
    
    $score = $this->getDb()->get_row($sql);
    if(!$score) {
      return 0;
    }
    
    
    if($match->home_team == $team_id) {
      return $score->goals_home;
    } elseif($match->away_team == $team_id) {
      return $score->goals_away;
    } else {
      return 0;
    }
  }
  
  public function getScore($key) {
    // TODO: maybe store this in database on a per season base, to make 3 point per win possible...
    $score = 0;
    switch($key) {
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
  
  public function setTeamProperties($team, $properties) {
    foreach($properties as $key => $value) {
      $this->getDb()->delete('team_properties', array('objectId' => $team->id, 'property_key' => $key));
      if($value !== false) {
        $this->getDb()->insert('team_properties', array('objectId' => $team->id, 'property_key' => $key, 'value' => $value,), array('%d', '%s', '%s'));
      }
    }
  }
  
  public function setPlayerProperties($player, $properties) {
    foreach($properties as $key => $value) {
      $this->getDb()->delete('player_properties', array('objectId' => $player->id, 'property_key' => $key));
      if($value !== false) {
        $this->getDb()->insert('player_properties', array('objectId' => $player->id, 'property_key' => $key, 'value' => $value,), array('%d', '%s', '%s'));
      }
    }
  }
  
  public function createOrUpdateLocation($location) {
    if($location->id) {
      $location = $this->updateLocation($location);
    } else {
      $location = $this->createLocation($location);
    }
    
    return $location;
  }
  
  public function updateLocation($location) {
    $columns = array();
    $columns['title'] = $location->title;
    $columns['description'] = $location->description;
    $columns['lat'] = $location->lat;
    $columns['lng'] = $location->lng;
    $this->getDb()->update('locations', $columns, array('id' => $location->id), array('%s', '%s', '%s', '%s',), array('%d'));
    
    return $this->getLocation($location->id);
  }
  
  public function createLocation($location) {
    $values = array('title' => $location->title, 'description' => $location->description, 'lat' => $location->lat, 'lng' => $location->lng,);
    $this->getDb()->insert('locations', $values, array('%s', '%s', '%s', '%s'));
    
    return $this->getLocation($this->getDb()->insert_id);
  }
  
  public function createOrUpdateLeague($league) {
    if($league->id) {
      $league = $this->updateLeague($league);
    } else {
      $league = $this->createLeague($league);
    }
    
    return $league;
  }
  
  public function updateLeague($league) {
    $columns = array();
    $columns['name'] = $league->name;
    $columns['code'] = $league->code;
    $columns['active'] = $league->active;
    $columns['current_season'] = $league->current_season;
    
    $this->getDb()->update('leagues', $columns, array('id' => $league->id), array('%s', '%s', '%d', '%d',), array('%d'));
    
    return $this->getLeague($league->id);
  }
  
  public function getLeague($leagueId) {
    $sql = "SELECT * FROM leagues WHERE id = '" . esc_sql($leagueId) . "'";
    $league = $this->getDb()->get_row($sql);
    $league->active = boolval($league->active);
    
    return $league;
  }
  
  public function createLeague($league) {
    $values = array('name' => $league->name, 'code' => $league->code, 'active' => $league->active, 'current_season' => $league->current_season,);
    $this->getDb()->insert('leagues', $values, array('%s', '%s', '%s'));
    
    return $this->getLeague($this->getDb()->insert_id);
  }
  
  public function createOrUpdateSeason($season) {
    if($season->id) {
      $season = $this->updateSeason($season);
    } else {
      $season = $this->createSeason($season);
    }
    
    return $season;
  }
  
  public function updateSeason($season) {
    $columns = array();
    $columns['name'] = $season->name;
    $columns['start_date'] = $season->start_date;
    $columns['end_date'] = $season->end_date;
    $columns['active'] = $season->active;
    $columns['current_game_day'] = $season->current_game_day;
    $columns['league_id'] = $season->league_id;
    
    $this->getDb()->update('seasons', $columns, array('id' => $season->id), array('%s', '%s', '%s', '%d', '%d', '%d',), array('%d'));
    
    return $this->getSeason($season->id);
  }
  
  public function getSeason($seasonId) {
    $sql = "SELECT * FROM seasons WHERE id = '" . esc_sql($seasonId) . "'";
    $season = $this->getDb()->get_row($sql);
    $properties = $this->getSeasonProperties($season->id);
    $season->properties = $properties;
    
    return $season;
  }
  
  public function getSeasonProperties($seasonId) {
    $sql = "SELECT * FROM season_properties WHERE objectId = '" . esc_sql($seasonId) . "'";
    $results = $this->getDb()->get_results($sql);
    $properties = array();
    foreach($results as $result) {
      $properties[$result->property_key] = $result->value;
    }
    
    return $properties;
  }
  
  public function createSeason($season) {
    $values = array('name' => $season->name, 'start_date' => $season->start_date, 'end_date' => $season->end_date, 'active' => $season->active, 'current_game_day' => $season->current_game_day, 'league_id' => $season->league_id,);
    $this->getDb()->insert('seasons', $values, array('%s', '%s', '%s', '%d', '%d', '%d'));
    
    return $this->getSeason($this->getDb()->insert_id);
  }
  
  public function createOrUpdateClub($club) {
    if($club->id) {
      $club = $this->updateClub($club);
    } else {
      $club = $this->createClub($club);
    }
    
    return $club;
  }
  
  public function updateClub($club) {
    $columns = array();
    $columns['name'] = $club->name;
    $columns['short_name'] = $club->short_name;
    $columns['description'] = $club->description;
    if($club->logo) {
      $columns['logo'] = $club->logo;
      $this->getDb()->update('clubs', $columns, array('id' => $club->id), array('%s', '%s', '%s', '%s',), array('%d'));
    } else {
      $this->getDb()->update('clubs', $columns, array('id' => $club->id), array('%s', '%s', '%s',), array('%d'));
    }
    
    return $this->getClub($club->id);
  }
  
  public function getClub($clubId) {
    $sql = "SELECT * FROM clubs WHERE id = '" . esc_sql($clubId) . "'";
    
    return $this->getDb()->get_row($sql);
  }
  
  public function createClub($club) {
    $values = array('name' => $club->name, 'short_name' => $club->short_name, 'description' => $club->description,);
    if($club->logo) {
      $values['logo'] = $club->logo;
      $this->getDb()->insert('clubs', $values, array('%s', '%s', '%s', '%s'));
    } else {
      $this->getDb()->insert('clubs', $values, array('%s', '%s', '%s'));
    }
    
    return $this->getClub($this->getDb()->insert_id);
  }
  
  public function createOrUpdateGameDay($day) {
    if($day->id) {
      $day = $this->updateGameDay($day);
    } else {
      $day = $this->createGameDay($day);
    }
    
    return $day;
  }
  
  public function updateGameDay($day) {
    $columns = array();
    $columns['number'] = $day->number;
    $columns['fixture'] = $day->start_date;
    $columns['end'] = $day->end_date;
    $columns['season_id'] = $day->season_id;
    
    $this->getDb()->update('game_days', $columns, array('id' => $day->id), array('%d', '%s', '%s', '%d',), array('%d'));
    
    return $this->getGameDay($day->id);
  }
  
  public function createGameDay($day) {
    $values = array('number' => $day->number, 'fixture' => $day->start_date, 'end' => $day->end_date, 'season_id' => $day->season_id,);
    $this->getDb()->insert('game_days', $values, array('%d', '%s', '%s', '%d'));
    
    return $this->getGameDay($this->getDb()->insert_id);
  }
  
  /**
   * @return ApiKey[]
   */
  public function getApiKeys() {
    $orm = $this->getOrm();
    $repo = $orm->getRepository(ApiKey::class);
    $results = $repo->createQueryBuilder()->orderBy('ID', 'ASC')->buildQuery()->getResults(true);
    if(!is_array($results)) {
      $results = array();
    }
    return $results;
  }
  
  /**
   * @param string $key
   * @return ApiKey|null
   */
  public function getApiKey($key) {
    $orm = $this->getOrm();
    $repo = $orm->getRepository(ApiKey::class);
    $results = $repo->createQueryBuilder()->where('api_key', $key, '=')->buildQuery()->getResults(true);
    if($results) {
      return $results[0];
    } else {
      return null;
    }
  }
  
}
