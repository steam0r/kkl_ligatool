<?php
$options = get_option('kkl_ligatool');
$kkl_db = new wpdb($options['db_user'], $options['db_pass'], $options['db_name'], $options['db_host']);

class KKL_DB {

	private $db;

	public function __construct($db = false) {
		global $kkl_db;
		if($db) {
			$this->db = $db;
		}else{
			$this->db = $kkl_db;
		}
	}


	public function getClubData($id) {

		$query = $this->db->prepare("SELECT * FROM clubs WHERE short_name = '%s' LIMIT 1", array($id));
		$result = $this->db->get_row($query);

		return $result;

	}

	public function getUpcomingGames($league_id) {

		$sql = "SELECT 	m.id,
						m.score_away,
						m.fixture,
						m.score_home,
						m.location,
                        m.status,
						ht.name as homename,
						at.name as awayname,
						at.id as awayid,
						ht.id as homeid
				FROM 	leagues as l,
						seasons as s,
						game_days as gd,
						matches as m,
						teams as at,
						teams as ht
				WHERE 	l.id = '%s'
				AND 	s.id = l.current_season
				AND 	gd.id = s.current_game_day
				AND 	m.game_day_id = gd.id
				AND 	at.id = m.away_team
				AND 	ht.id = m.home_team";

		$query = $this->db->prepare($sql, $league_id);
		$result = $this->db->get_results($query);

		return $result;

	}

    public function getGamesForTeam($teamid) {

        $sql = "SELECT  m.id,
                        m.score_away,
                        m.fixture,
                        m.score_home,
                        m.location,
                        ht.name as homename,
                        at.name as awayname,
                        at.id as awayid,
                        ht.id as homeid
                FROM    game_days as gd,
                        matches as m,
                        teams as at,
                        teams as ht
                WHERE   (m.home_team = '" . esc_sql($teamid) . "' OR m.away_team = '" . esc_sql($teamid) . "') 
                AND     m.game_day_id = gd.id
                AND     at.id = m.away_team
                AND     ht.id = m.home_team
                ORDER BY m.fixture ASC";

        $query = $this->db->prepare($sql, $league_id);
        $result = $this->db->get_results($query);

        return $result;

    }

    public function getAllUpcomingGames() {

        $sql = "SELECT  m.id,
                        m.score_away,
                        m.fixture,
                        m.score_home,
                        m.location,
                        l.id as league_id,
                        ht.name as homename,
                        at.name as awayname,
                        at.id as awayid,
                        ht.id as homeid
                FROM    leagues as l,
                        seasons as s,
                        game_days as gd,
                        matches as m,
                        teams as at,
                        teams as ht
                WHERE     s.id = l.current_season
                AND     l.active = 1
                AND     gd.id = s.current_game_day
                AND     m.game_day_id = gd.id
                AND     at.id = m.away_team
                AND     ht.id = m.home_team ORDER BY l.name ASC";

        $result = $this->db->get_results($sql);

        return $result;

    }

    public function getClubs() {
        $sql = "SELECT * FROM clubs ORDER by name ASC";
        return $this->db->get_results($sql);
    }

    public function getTeams() {
        $sql = "SELECT * FROM teams ORDER by name ASC";
        $teams = $this->db->get_results($sql);
        foreach ($teams as $team) {
            $properties = $this->getTeamProperties($team->id);
            $team->properties = $properties;
        }
        return $teams;
    }

		public function getPlayers() {
        $sql = "SELECT * FROM players ORDER by first_name, last_name ASC";
        $players = $this->db->get_results($sql);
        foreach ($players as $player) {
            $properties = $this->getPlayerProperties($team->id);
            $player->properties = $properties;
        }
        return $players;
    }

		public function getLeagueAdmins() {
			$sql = "SELECT p.* FROM players AS p ".
				"JOIN player_properties AS pp ON pp.objectId = p.id " .
				"AND pp.property_key = 'member_ligaleitung' ".
				"AND pp.value = 'true' ".
				"ORDER by p.first_name, p.last_name ASC";
        $players = $this->db->get_results($sql);
        foreach ($players as $player) {
            $properties = $this->getPlayerProperties($team->id);
						$player->properties = $properties;
						$player->role = 'ligaleitung';
        }
        return $players;
    }

    public function getTeamsForSeason($seasonId) {
        $sql = "SELECT * FROM teams WHERE season_id = '" . esc_sql($seasonId) . "'";
        $teams = $this->db->get_results($sql);
        foreach ($teams as $team) {
            $properties = $this->getTeamProperties($team->id);
            $team->properties = $properties;
        }
        return $teams;
    }

    public function getCurrentTeamForClub($clubId) {
        $sql = "SELECT * FROM teams WHERE club_id = '" . esc_sql($clubId) . "' ORDER BY id DESC LIMIT 1";
        $team = $this->db->get_row($sql);
        $properties = $this->getTeamProperties($team->id);
        $team->properties = $properties;
        return $team;
    }

    public function getLeagues() {
        $sql = "SELECT * FROM leagues ORDER BY name ASC";
	$leagues = $this->db->get_results($sql);
	foreach($leagues as $league) {
		$league->active = ord($league->active);
	}
        return $leagues;
    }

    public function getActiveLeagues() {
        $sql = "SELECT * FROM leagues WHERE active = 1 ORDER BY name ASC";
       	$leagues = $this->db->get_results($sql);
	foreach($leagues as $league) {
		$league->active = ord($league->active);
	}
 	return $leagues;
    }

    public function getInactiveLeagues() {
        $sql = "SELECT * FROM leagues WHERE active = 0 ORDER BY name ASC";
    	$leagues = $this->db->get_results($sql);
	foreach($leagues as $league) {
		$league->active = ord($league->active);
	}
 	return $leagues;
    }

    public function getLeagueBySlug($slug) {
        $sql = "SELECT * FROM leagues WHERE code = '" . esc_sql($slug) ."'";
	$league = $this->db->get_row($sql);
	$league->active = ord($league->active);
	return $league;
    }

    public function getSeasons() {
        $sql = "SELECT * FROM seasons ORDER BY start_date ASC, name ASC";
        return $this->db->get_results($sql);
    }

    public function getSeason($seasonId) {
        $sql = "SELECT * FROM seasons WHERE id = '" . esc_sql($seasonId) . "'";
        $season = $this->db->get_row($sql);
        $properties = $this->getSeasonProperties($season->id);
        $season->properties = $properties;
        return $season;
		}

   public function getSeasonProperties($seasonId) {
        $sql = "SELECT * FROM season_properties WHERE objectId = '" . esc_sql($seasonId) . "'";
        $results = $this->db->get_results($sql);
        $properties = array();
        foreach ($results as $result) {
            $properties[$result->property_key] = $result->value;
        }
        return $properties;
    }

		public function setSeasonProperties($season, $properties) {
        foreach($properties as $key => $value) {
            $this->db->delete( 'season_properties', array( 'objectId' => $season->id, 'property_key' => $key ));
            if($value !== false) {
                $this->db->insert('season_properties', array('objectId' => $season->id, 'property_key' => $key, 'value' => $value), array('%d','%s','%s'));
            }
        }
    }

    public function getSeasonByLeagueAndYear($league, $year) {
        $sql = "SELECT * FROM seasons WHERE league_id = '" . esc_sql($league) . "' AND YEAR(start_date) = '" . esc_sql($year) . "'";
        return $this->db->get_row($sql);
    }

    public function getSeasonsByLeague($league) {
        $sql = "SELECT * FROM seasons WHERE league_id = '" . esc_sql($league) . "'";
        return $this->db->get_results($sql);
    }

    public function getTeamsForClub($club_id) {
        $sql = "SELECT t.* FROM teams as t, seasons as s WHERE t.club_id = '" . esc_sql($club_id) . "' AND t.season_id = s.id ORDER BY s.start_date ASC";
        $teams = $this->db->get_results($sql);
        foreach ($teams as $team) {
            $properties = $this->getTeamProperties($team->id);
            $team->properties = $properties;
        }
        return $teams;
    }

    public function getGameDays() {
        $sql = "SELECT * FROM game_days ORDER BY fixture ASC, number ASC";
        return $this->db->get_results($sql);
    }

    public function getPreviousGameDay($day) {
        $number = $day->number - 1;
        if($number < 1) return false;
        $sql = "SELECT * FROM game_days WHERE season_id = '" . esc_sql($day->season_id) . "' AND number = '" . esc_sql($number) . "'";
        return $this->db->get_row($sql);
    }

    public function getNextGameDay($day) {
        $number = $day->number + 1;
        $sql = "SELECT * FROM game_days WHERE season_id = '" . esc_sql($day->season_id) . "' AND number = '" . esc_sql($number) . "'";
        return $this->db->get_row($sql);
    }

    public function getGameDaysForSeason($seasonId) {
        $sql = "SELECT * FROM game_days WHERE season_id = '" . esc_sql($seasonId) . "' ORDER BY number ASC";
        return $this->db->get_results($sql);
    }

    public function getGameDayBySeasonAndPosition($seasonId, $position) {
         $sql = "SELECT * FROM game_days WHERE season_id = '" . esc_sql($seasonId) . "' AND number = '" . esc_sql($position) . "'";
         return $this->db->get_row($sql);
    }

    public  function getCurrentSeason($leagueId) {
        $result = null;
        $sql = "SELECT current_season FROM leagues WHERE id = '" . esc_sql($leagueId) . "'";
        $seasonId = $this->db->get_var($sql);
        if($seasonId) {
            $sql = "SELECT * FROM seasons WHERE id = '" . esc_sql($seasonId). "'";
            $result = $this->db->get_row($sql);
        }
        return $result;
    }

    public  function getCurrentGameDayForLeague($leagueId) {
        $sql = "SELECT * FROM game_days WHERE id = (SELECT current_game_day AS id FROM seasons WHERE id = (SELECT current_season AS id FROM leagues WHERE id = '". esc_sql($leagueId)."'))";
        return $this->db->get_row($sql);
    }

    public function getCurrentGameDayForSeason($seasonId) {
        $sql = "SELECT * FROM game_days WHERE id = (SELECT current_game_day AS id FROM seasons WHERE id = '". esc_sql($seasonId)."')";
        return $this->db->get_row($sql);
    }

    public function getLeagueForGameday($dayId) {
        $sql = "SELECT * FROM leagues WHERE id = (SELECT league_id AS id FROM seasons WHERE id = (SELECT season_id AS id FROM game_days WHERE id = '" . esc_sql($dayId) . "'))";
	$league = $this->db->get_row($sql);
	$league->active = ord($league->active);
	return $league;
    }

    public function getLeagueForSeason($seasonId) {
        $sql = "SELECT * FROM leagues WHERE id = (SELECT league_id AS id FROM seasons WHERE id = '" . esc_sql($seasonId) . "')";
        $league = $this->db->get_row($sql);
	    $league->active = ord($league->active);
	    return $league;
    }

    public function getLeague($leagueId) {
       $sql = "SELECT * FROM leagues WHERE id = '" . esc_sql($leagueId) . "'";
       $league = $this->db->get_row($sql);
	   $league->active = ord($league->active);
	   return $league;
    }

    public function getSeasonForGameday($dayId) {
        $sql = "SELECT * FROM seasons WHERE id = (SELECT season_id AS id FROM game_days WHERE id = '" . esc_sql($dayId) . "')";
        return $this->db->get_row($sql);
    }

    public function getGameDay($dayId) {
        $sql = "SELECT * FROM game_days WHERE id = '" . esc_sql($dayId) . "'";
        return $this->db->get_row($sql);
    }

    public function setCurrentGameDay($season, $day) {
        $columns = array();
        $columns['current_game_day'] = $day->id;
        $this->db->update('seasons', $columns, array('id' => $season->id));
    }

    public function getMatch($matchId) {
        $sql = "SELECT m.*, s.score_home, s.score_away, g.goals_home, g.goals_away FROM matches as m LEFT JOIN sets as s ON m.id = s.match_id LEFT JOIN games as g ON s.id = g.set_id WHERE m.id = '" . esc_sql($matchId) . "'";
        return $this->db->get_row($sql);
		}

		public function getFullMatchInfo($matchId) {
				$sql = "SELECT m.*, away.name as away_name, home.name as home_name, l.name as league, s.score_home, s.score_away, g.goals_home, g.goals_away, homecaptain.email as home_email, awaycaptain.email as away_email FROM matches as m LEFT JOIN sets as s ON m.id = s.match_id LEFT JOIN games as g ON s.id = g.set_id LEFT JOIN teams as away ON away.id = m.away_team LEFT JOIN team_properties AS ac ON ac.objectId = away.id and ac.property_key = 'captain' LEFT JOIN players AS awaycaptain ON awaycaptain.id = ac.value LEFT JOIN teams as home on home.id = m.home_team LEFT JOIN team_properties as hc ON hc.objectId = home.id AND hc.property_key = 'captain' LEFT JOIN players AS homecaptain ON homecaptain.id = hc.value LEFT JOIN game_days as gd ON gd.id = m.game_day_id LEFT JOIN seasons as season on season.id = gd.season_id LEFT JOIN leagues as l ON l.id = season.league_id WHERE m.id = '" . esc_sql($matchId) . "'";
        return $this->db->get_row($sql);
		
		}

    public function getMatchesForGameDay($dayId) {
        $sql = "SELECT * FROM game_days WHERE season_id = '" . esc_sql($seasonId) . "' ORDER BY number ASC";
        return $this->db->get_results($sql);
    }

    public function getMatchesByGameDay($dayId) {
        $sql = "SELECT * FROM matches WHERE game_day_id = '" . esc_sql($dayId) . "' ORDER BY fixture ASC";
        return $this->db->get_results($sql);
    }

    public function getTeam($teamId) {
        $sql = "SELECT * FROM teams WHERE id = '" . esc_sql($teamId) . "'";
        $team = $this->db->get_row($sql);
        $properties = $this->getTeamProperties($team->id);
        $team->properties = $properties;
        return $team;
    }

    public function getTeamProperties($teamId) {
        $sql = "SELECT * FROM team_properties WHERE objectId = '" . esc_sql($teamId) . "'";
        $results = $this->db->get_results($sql);
        $properties = array();
        foreach ($results as $result) {
					$properties[$result->property_key] = $result->value;
					if($result->property_key == 'captain') {
						$player = $this->getPlayer($result->value);
						$properties['captain_email'] = $player->email;
					}else if($result->property_key == 'vice_captain') {
						$player = $this->getPlayer($result->value);
						$properties['vice_captain_email'] = $player->email;
					}else{
						$location = $this->getLocation($result->value);
						$properties['location_name'] = $location->title;
					}
        }
        return $properties;
    }

		public function getPlayer($playerId) {
        $sql = "SELECT * FROM players WHERE id = '" . esc_sql($playerId) . "'";
        $player = $this->db->get_row($sql);
        $properties = $this->getPlayerProperties($player->id);
				$player->properties = $properties;
        return $player;
    }

    public function getPlayerProperties($playerId) {
        $sql = "SELECT * FROM player_properties WHERE objectId = '" . esc_sql($playerId) . "'";
        $results = $this->db->get_results($sql);
        $properties = array();
        foreach ($results as $result) {
            $properties[$result->property_key] = $result->value;
        }
        return $properties;
		}

		public function getCaptainsContactData() {
			$sql = "SELECT " . 
				"p.first_name as first_name, p.last_name as last_name, p.email as email, p.phone as phone, " .
				"t.name as team, t.short_name as team_short, ".
				"l.name as league, l.code as league_short, loc.title as location ".
				"FROM team_properties AS tp ".
				"JOIN players AS p ON p.id = tp.value ".
				"JOIN teams AS t ON t.id = tp.objectId ".
				"JOIN seasons AS s ON s.id = t.season_id AND s.active = '1' ".
				"JOIN leagues AS l ON l.id = s.league_id ".
				"LEFT JOIN team_properties AS lp ON t.id = lp.objectId AND lp.property_key = 'location' ".
				"LEFT JOIN locations AS loc ON loc.id = lp.value ".
				"WHERE tp.property_key = 'captain' ";
			$data = $this->db->get_results($sql);
			$captains = array();
			foreach($data as $d) {
				$d->role = 'captain';
				$captains[] = $d;
			}
			return $captains;
		}

		public function getViceCaptainsContactData() {
			$sql = "SELECT " . 
				"p.first_name as first_name, p.last_name as last_name, p.email as email, p.phone as phone, " .
				"t.name as team, t.short_name as team_short, ".
				"l.name as league, l.code as league_short, loc.title as location ".
				"FROM team_properties AS tp ".
				"JOIN players AS p ON p.id = tp.value ".
				"JOIN teams AS t ON t.id = tp.objectId ".
				"JOIN seasons AS s ON s.id = t.season_id AND s.active = '1' ".
				"JOIN leagues AS l ON l.id = s.league_id ".
				"LEFT JOIN team_properties AS lp ON t.id = lp.objectId AND lp.property_key = 'location' ".
				"LEFT JOIN locations AS loc ON loc.id = lp.value ".
				"WHERE tp.property_key = 'vice_captain' ";
			$data = $this->db->get_results($sql);
			$captains = array();
			foreach($data as $d) {
				$d->role = 'vice_captain';
				$captains[] = $d;
			}
			return $captains;
		
		}

    public function getTeamByCode($teamCode) {
        $sql = "SELECT * FROM teams WHERE short_name = '" . esc_sql($teamCode) . "'";
        $team = $this->db->get_row($sql);
        $properties = $this->getTeamProperties($team->id);
        $team->properties = $properties;
        return $team;
    }

		public function getTeamsByName($teamName) {
			$sql = "SELECT id FROM teams WHERE name = '" . esc_sql($teamName) . "'";
			$teamIds = $this->db->get_results($sql);
			$teams = array();
			foreach($teamIds as $teamId) {
				$teams[] = $this->getTeam($teamId->id);
			}
      return $teams;
    }

    public function getClub($clubId) {
        $sql = "SELECT * FROM clubs WHERE id = '" . esc_sql($clubId) . "'";
        return $this->db->get_row($sql);
    }

    public function getClubByCode($clubCode) {
        $sql = "SELECT * FROM clubs WHERE short_name = '" . esc_sql($clubCode) . "'";
        return $this->db->get_row($sql);
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

        $ranking = $this->db->get_results($sql);

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
                            if($team->id == $iscore->team_id) $has_score = true;
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
                }else if($original_size == (i + 1)) {
                    // last element, add scores here
                    foreach($teams as $team) {
                        $has_score = false;
                        foreach($ranking as $iscore) {
                            if($team->id == $iscore->team_id) $has_score = true;
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
            }else {
                // no scores at all, fake everything
                foreach($teams as $team) {
                    $has_score = false;
                    foreach($ranking as $iscore) {
                        if($team->id == $iscore->team_id) $has_score = true;
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

        $position = 0;
        $previousScore = 0;
        $previousGameDiff = 0;
        foreach($ranking as $rank) {

            $position++;

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

    public function getTeamScoreForGameDay($team_id, $game_day_id) {

        $day = $this->getGameDay($game_day_id);

        $sql = "SELECT " .
                "team_scores.team_id, " .
                "sum(team_scores.score) as score, " .
                "sum(team_scores.win) as wins, " . 
                "sum(team_scores.loss) as losses, " . 
                "sum(team_scores.draw) as draws, " .
                "sum(team_scores.goalsFor) as goalsFor, " . 
                "sum(team_scores.goalsAgainst) as goalsAgainst " . 
                "FROM game_days, " .
                "team_scores  " .
                "WHERE game_days.season_id='" . $day->season_id . "' " . 
                "AND game_days.number <= '" . $day->number . "' " .
                "AND gameDay_id=game_days.id " .
                "AND team_id=". $team_id; 

        $scores = $this->db->get_row($sql);
        return $scores;

    }


    public function getScheduleForGameDay($day) {
        $sql = "SELECT * FROM matches WHERE game_day_id = '" . esc_sql($day->id) . "'";
        $schedule = new stdClass;
        $schedule->matches = $this->db->get_results($sql);
        $schedule->number = $day->number;
        $schedule->day = $day;
        foreach($schedule->matches as $match) {
            $match->home = $this->getTeam($match->home_team);
            $match->away = $this->getTeam($match->away_team);
        }
        return $schedule;
    }

    public function getScheduleForSeason($season) {

        $days = $this->getGameDaysForSeason($season->id);
        $schedules = array();
        foreach($days as $day) {
            $schedules[] = $this->getScheduleForGameDay($day);
        }

        return $schedules;
    }

    public function getAllLocations() {
        $sql = "SELECT * FROM locations";
        $locations = $this->db->get_results($sql);
        foreach($locations as $location) {
            $location->teams = $this->getTeamsForLocation($location);
        }
        return $locations;
    }

    public function getTeamsForLocation($location) {
        $sql = "SELECT objectId FROM team_properties WHERE property_key = 'location' AND value = '" . esc_sql($location->id) . "'";
        $teamIds = $this->db->get_results($sql);
        $teams = array();
        foreach($teamIds as $teamId) {
            if($teamId->objectId && $teamId->objectId != 0) {
                $teams[] = $this->getTeam($teamId->objectId);
            }
        }
        return $teams;
    }

    public function getLocations() {
        $sql = "SELECT * FROM locations ORDER by title ASC";
        return $this->db->get_results($sql);
    }

    public function getLocation($location_id) {
        $sql = "SELECT * FROM locations WHERE id = '" . esc_sql($location_id) . "'";
        return $this->db->get_row($sql);
    }

    public function getAllGoals(){
        $sql = "SELECT SUM( goals_away ) FROM  `games` WHERE goals_away IS NOT NULL";
        return $this->db->get_row($sql);   
    }

    public function getAllGames(){
        $sql = "SELECT SUM( score_home ) FROM  `sets` WHERE score_home IS NOT NULL";
        return $this->db->get_row($sql);      
    }

    public function createTeam($team) {
        $values = array(  'name' => $team->name,
                'description' => $team->description,
                'short_name' => $team->short_name,
                'club_id' => $team->club_id,
                'season_id' => $team->season_id
              );
        $result = $this->db->insert('teams', $values, array('%s','%s','%s','%d', '%d'));
        return $this->getTeam($this->db->insert_id);
    }

    public function updateTeam($team) {
        $columns = array();
        $columns['name'] = $team->name;
        $columns['short_name'] = $team->short_name;
        $columns['season_id'] = $team->season_id;
        $columns['club_id'] = $team->club_id;
        $this->db->update('teams', $columns, array('id' => $team->id), array('%s', '%s', '%d', '%d'), array('%d'));
        return $this->getTeam($team->id);
    }

    public function createOrUpdateTeam($team) {
        if($team->id) {
            $team = $this->updateTeam($team);
        }else{
            $team = $this->createTeam($team);
        }
        return $team;
    }

    public function createPlayer($player) {
        $values = array(  'first_name' => $player->first_name,
                'last_name' => $player->last_name,
                'email' => $player->email,
                'phone' => $player->phone
              );
        $result = $this->db->insert('players', $values, array('%s','%s','%s', '%s'));
        return $this->getPlayer($this->db->insert_id);
		}

		public function updatePlayer($player) {
        $columns = array();
        $columns['first_name'] = $player->first_name;
        $columns['last_name'] = $player->last_name;
        $columns['email'] = $player->email;
        $columns['phone'] = $player->phone;
        $this->db->update('players', $columns, array('id' => $player->id), array('%s', '%s', '%s', '%s'), array('%d'));
        return $this->getPlayer($player->id);
    }

		public function createOrUpdatePlayer($player) {
        if($player->id) {
            $player = $this->updatePlayer($player);
        }else{
            $player = $this->createPlayer($player);
        }
        return $player;
    }

    public function createMatch($match) {
        $values = array(  
                'game_day_id' => $match->game_day_id,
                'score_away' => $match->score_away,
                'score_home' => $match->score_home,
                'fixture' => $match->fixture,
                'location' => $match->location,
                'notes' => $match->notes,
                'status' => $match->status,
                'away_team' => $match->away_team,
                'home_team' => $match->home_team
              );
	if($values['fixtures']) {
		$values['fixtures'] = NULL;
	}
        $result = $this->db->insert('matches', $values, array('%d','%d','%d','%s','%s','%s', '%d','%d','%d'));
        $match_id = $this->db->insert_id;

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

    public function updateMatch($match) {

        $values = array(  
                'game_day_id' => $match->game_day_id,
                'score_away' => $match->score_away,
                'score_home' => $match->score_home,
                'fixture' => $match->fixture,
                'location' => $match->location,
                'notes' => $match->notes,
                'status' => $match->status,
                'away_team' => $match->away_team,
                'home_team' => $match->home_team
              );
        $this->db->update('matches', $values, array('id' => $match->id), array('%d','%d','%d','%s','%s','%s','%d','%d','%d'), array('%d'));

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
        $game = $this->updateGame($game);

        return $this->getMatch($match->id);
    }

    public function createOrUpdateMatch($match) {
        if($match->id) {
            $match = $this->updateMatch($match);
        }else{
            $match = $this->createMatch($match);
        }
        if($match->status == 3) {
            $this->calculateScores($match);
        }
        return $match;
    }

    public function createSet($set) {
        $values = array(  
                'score_away' => $set->score_away,
                'score_home' => $set->score_home,
                'number' => $set->number,
                'match_id' => $set->match_id
              );
        $result = $this->db->insert('sets', $values, array('%d','%d','%d','%d'));
        return $this->getSet($set->match_id);
    }

    public function updateSet($set) {
        if(!$this->getSet($set->match_id)) {
            return $this->createSet($set);
        }
        $values = array(  
                'score_away' => $set->score_away,
                'score_home' => $set->score_home,
                'number' => $set->number,
                'match_id' => $set->match_id
              );
        $this->db->update('sets', $values, array('match_id' => $set->match_id), array('%d','%d','%d','%d'), array('%d'));
        return $this->getSet($set->match_id);
    }

    public function getSet($matchId) {
        $sql = "SELECT * FROM sets WHERE match_id = '" . esc_sql($matchId) . "'";
        return $this->db->get_row($sql);
    }

    public function createGame($game) {
        $values = array(  
                'goals_away' => $game->goals_away,
                'goals_home' => $game->goals_home,
                'number' => $game->number,
                'set_id' => $game->set_id
              );
        $result = $this->db->insert('games', $values, array('%d','%d','%d','%d'));
        return $this->getGame($game->set_id);
    }

    public function updateGame($game) {
        if(!$this->getGame($game->set_id)) {
            return $this->createGame($game);
        }
        $values = array(  
                'goals_away' => $game->goals_away,
                'goals_home' => $game->goals_home,
                'number' => $game->number,
                'set_id' => $game->set_id
              );
        $this->db->update('games', $values, array('set_id' => $game->set_id), array('%d','%d','%d','%d'), array('%d'));
        return $this->getGame($game->match_id);
    }

    public function getGame($setId) {
        $sql = "SELECT * FROM games WHERE set_id = '" . esc_sql($setId) . "'";
        return $this->db->get_row($sql);
    }
    
    public function setTeamProperties($team, $properties) {
        foreach($properties as $key => $value) {
            $this->db->delete( 'team_properties', array( 'objectId' => $team->id, 'property_key' => $key ));
            if($value !== false) {
                $this->db->insert('team_properties', array('objectId' => $team->id, 'property_key' => $key, 'value' => $value), array('%d','%s','%s'));
            }
        }
    }

		public function setPlayerProperties($player, $properties) {
        foreach($properties as $key => $value) {
            $this->db->delete( 'player_properties', array( 'objectId' => $player->id, 'property_key' => $key ));
            if($value !== false) {
                $this->db->insert('player_properties', array('objectId' => $player->id, 'property_key' => $key, 'value' => $value), array('%d','%s','%s'));
            }
        }
    }

    public function createLocation($location) {
        $values = array(  'title' => $location->title,
                'description' => $location->description,
                'lat' => $location->lat,
                'lng' => $location->lng
              );
        $result = $this->db->insert('locations', $values, array('%s','%s','%s','%s'));
        return $this->getLocation($this->db->insert_id);
    }

    public function updateLocation($location) {
        $columns = array();
        $columns['title'] = $location->title;
        $columns['description'] = $location->description;
        $columns['lat'] = $location->lat;
        $columns['lng'] = $location->lng;
        $this->db->update('locations', $columns, array('id' => $location->id), array('%s', '%s', '%s', '%s'), array('%d'));
        return $this->getLocation($location->id);
    }

    public function createOrUpdateLocation($location) {
        if($location->id) {
            $location = $this->updateLocation($location);
        }else{
            $location = $this->createLocation($location);
        }
        return $location;
    }

    public function createLeague($league) {
        $values = array(  'name' => $league->name,
                'code' => $league->code,
                'active' => $league->active,
                'current_season' => $league->current_season
              );
        $result = $this->db->insert('leagues', $values, array('%s','%s','%s'));
        return $this->getLeague($this->db->insert_id);
    }

    public function updateLeague($league) {
        $columns = array();
        $columns['name'] = $league->name;
        $columns['code'] = $league->code;
        $columns['active'] = $league->active;
        $columns['current_season'] = $league->current_season;
        
        $this->db->update('leagues', $columns, array('id' => $league->id), array('%s', '%s', '%d', '%d'), array('%d'));
        return $this->getLeague($league->id);
    }

    public function createOrUpdateLeague($league) {
        if($league->id) {
            $league = $this->updateLeague($league);
        }else{
            $league = $this->createLeague($league);
        }
        return $league;
    }

    public function createSeason($season) {
        $values = array(  'name' => $season->name,
                'start_date' => $season->start_date,
                'end_date' => $season->end_date,
                'active' => $season->active,
                'current_game_day' => $season->current_game_day,
                'league_id' => $season->league_id
              );
        $result = $this->db->insert('seasons', $values, array('%s','%s','%s', '%d', '%d', '%d'));
        return $this->getSeason($this->db->insert_id);
    }

    public function updateSeason($season) {
        $columns = array();
        $columns['name'] = $season->name;
        $columns['start_date'] = $season->start_date;
        $columns['end_date'] = $season->end_date;
        $columns['active'] = $season->active;
        $columns['current_game_day'] = $season->current_game_day;
        $columns['league_id'] = $season->league_id;
        
        $this->db->update('seasons', $columns, array('id' => $season->id), array('%s', '%s', '%s', '%d', '%d', '%d'), array('%d'));
        return $this->getSeason($season->id);
    }

    public function createOrUpdateSeason($season) {
        if($season->id) {
            $season = $this->updateSeason($season);
        }else{
            $season = $this->createSeason($season);
        }
        return $season;
    }

    public function createClub($club) {
        $values = array(  'name' => $club->name,
                'short_name' => $club->short_name,
                'description' => $club->description
              );
        if($club->logo) {
        	$values['logo'] = $club->logo;
		$result = $this->db->insert('clubs', $values, array('%s','%s','%s', '%s'));
	}else{
		$result = $this->db->insert('clubs', $values, array('%s','%s','%s'));
	}
        return $this->getClub($this->db->insert_id);
    }

    public function updateClub($club) {
        $columns = array();
        $columns['name'] = $club->name;
        $columns['short_name'] = $club->short_name;
        $columns['description'] = $club->description;
	if($club->logo) {
        	$columns['logo'] = $club->logo;
        	$this->db->update('clubs', $columns, array('id' => $club->id), array('%s', '%s', '%s', '%s'), array('%d'));
	}else{
        	$this->db->update('clubs', $columns, array('id' => $club->id), array('%s', '%s', '%s'), array('%d'));
	}
     
        return $this->getClub($club->id);
    }

    public function createOrUpdateClub($club) {
        if($club->id) {
            $club = $this->updateClub($club);
        }else{
            $club = $this->createClub($club);
        }
        return $club;
    }

    public function createGameDay($day) {
        $values = array(  'number' => $day->number,
                'fixture' => $day->start_date,
                'end' => $day->end_date,
                'season_id' => $day->season_id,
              );
        $result = $this->db->insert('game_days', $values, array('%d','%s','%s', '%d'));
        return $this->getGameDay($this->db->insert_id);
    }

    public function updateGameDay($day) {
        $columns = array();
        $columns['number'] = $day->number;
        $columns['fixture'] = $day->start_date;
        $columns['end'] = $day->end_date;
        $columns['season_id'] = $day->season_id;
     
        $this->db->update('game_days', $columns, array('id' => $day->id), array('%d', '%s', '%s', '%d'), array('%d'));
        return $this->getGameDay($day->id);
    }

    public function createOrUpdateGameDay($day) {
        if($day->id) {
            $day = $this->updateGameDay($day);
        }else{
            $day = $this->createGameDay($day);
        }
        return $day;
    }

    public function calculateScores($match) {

        $home = $this->getTeam($match->home_team);
        $away = $this->getTeam($match->away_team);

        $this->calculateScoresForTeamAndMatch($match, $home);
        $this->calculateScoresForTeamAndMatch($match, $away);

    }

    public function calculateScoresForTeamAndMatch($match, $team) {

        $day = $this->getGameDay($match->game_day_id);

        $sql = "SELECT " .
               "* " .
               "FROM ".
               "team_scores  " .
               "WHERE gameDay_id ='" . esc_sql($day->id) . "' " . 
               "AND team_id = '" . esc_sql($team->id) . "';";

        $score = $this->db->get_row($sql);
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
            }else if($match->score_home < $match->score_away) {
                $score->score = $this->getScore("loss");
                $score->loss = 1;
            }else{
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
            }else if($match->score_home < $match->score_away) {
                $score->score = $this->getScore("win");
                $score->win = 1;
            }else{
                $score->score = $this->getScore("draw");
                $score->draw = 1;
            }
        }

        if($score->id) {
            $result = $this->db->update('team_scores', get_object_vars($score), array('id' => $score->id));
        }else{
            $result = $this->db->insert('team_scores', get_object_vars($score));
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

    private function getGoalsForTeam($match, $team_id) {

        $sql = "SELECT sum(`goals_away`) as goals_away, sum(goals_home) as goals_home FROM matches as m ".
               "JOIN sets as s ON s.match_id = m.id ".
               "JOIN games as g ON g.set_id = s.id ".
               "WHERE m.id = " . esc_sql($match->id);

        $score = $this->db->get_row($sql);
        if(!$score) {
            return 0;
        }


        if($match->home_team == $team_id) {
            return $score->goals_home;
        }else if($match->away_team == $team_id) {
            return $score->goals_away;
        }else{
            return 0;
        }
    }

}
