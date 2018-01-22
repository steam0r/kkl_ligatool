<?php
class KKL_Shortcodes {

	public static function getClub($atts, $content, $tag) {

		global $kkl_twig;

		$db = new KKL_DB_Wordpress();

		$context = KKL::getContext();
		$club = $db->getClubData($atts['id']);

		return $kkl_twig->render('shortcodes/club.tpl', array('context' => $context, 'club' => $club));

	}

	public static function leagueOverview($attrs, $content, $tag) {

		global $kkl_twig;
		$db = new KKL_DB_Wordpress();

		$context = KKL::getContext();
		$all_leagues = $db->getActiveLeagues();
		$leagues = array();
		foreach($all_leagues as $league) {
			$league->season = $db->getSeason($league->current_season);
			$league->teams =  $db->getTeamsForSeason($league->season->id);
			foreach($league->teams as $team) {
				$club = $db->getClub($team->club_id);
				if(!$team->logo) {
					$team->logo = $club->logo;
					if(!$club->logo) {
						$team->logo = "https://www.kickerligakoeln.de/wp-content/themes/kkl_2/img/kkl-logo_172x172.png";
					}
				}else{
					$team->logo = "/images/team/" . $team->logo;
				}
				// HACK
				$team->link = KKL::getLink('club', array('club' => $club->short_name));
			}

			$day = $db->getGameDay($league->season->current_game_day);
			$league->link = KKL::getLink('league', array('league' => $league->code, 'season' => date('Y', strtotime($league->season->start_date)), 'game_day' => $day->number));
			$leagues[] = $league;
		}

		return $kkl_twig->render('shortcodes/league_overview.tpl', array('context' => $context, 'leagues' => $leagues));

	}

	public static function leagueTable( $atts, $content, $tag ) {

		global $kkl_twig;
		$db = new KKL_DB_Wordpress();

		$context = KKL::getContext();
		$rankings = array();

		$ranking = new stdClass;
		$ranking->league = $context['league'];
		$ranking->ranks = $db->getRankingForLeagueAndSeasonAndGameDay($context['league']->id, $context['season']->id, $context['game_day']->number);
		foreach($ranking->ranks as $rank) {
			$team = $db->getTeam($rank->team_id);
			$club = $db->getClub($team->club_id);
			$rank->team->link = KKL::getLink('club', array('club' => $club->short_name));
		}

		$rankings[] = $ranking;

        return $kkl_twig->render('shortcodes/table.tpl', array('context' => $context, 'rankings' => $rankings));
	}

	public static function tableOverview( $atts, $content, $tag ) {

		global $kkl_twig;
		$db = new KKL_DB_Wordpress();

		$context = KKL::getContext();
		$rankings = array();
		foreach($db->getActiveLeagues() as $league) {
			$season = $db->getSeason($league->current_season);
			$day = $db->getGameDay($season->current_game_day);
			$ranking = new stdClass;
			$ranking->league = $league;
			$ranking->ranks = $db->getRankingForLeagueAndSeasonAndGameDay($league->id, $season->id, $day->number);
			foreach($ranking->ranks as $rank) {
				$team = $db->getTeam($rank->team_id);
				$club = $db->getClub($team->club_id);
				$rank->team->link =  KKL::getLink('club', array('club' => $club->short_name));
			}
			$ranking->league->link = KKL::getLink('league', array('league' => $league->code, 'season' => date('Y', strtotime($season->start_date)), 'game_day' => $day->number));
			$rankings[] = $ranking;
		}

        return $kkl_twig->render('shortcodes/table.tpl', array('context' => $context, 'rankings' => $rankings));
	}


	public static function gameDayTable( $atts, $content, $tag ) {

		global $kkl_twig;
		$db = new KKL_DB_Wordpress();

		$context = KKL::getContext();
		$schedules = array();
		$schedule = $db->getScheduleForGameDay($context['game_day']);
		foreach($schedule->matches as $match) {
			$home_club = $db->getClub($match->home->club_id);
			$away_club = $db->getClub($match->away->club_id);
			$match->home->link = KKL::getLink('club', array('club' => $home_club->short_name));
			$match->away->link = KKL::getLink('club', array('club' => $away_club->short_name));
		}
		$schedule->link = KKL::getLink('schedule', array('league' => $context['league']->code, 'season' => date('Y', strtotime($context['season']->start_date))));

		$schedules[] = $schedule;

        return $kkl_twig->render('shortcodes/game_day.tpl', array('context' => $context, 'schedules' => $schedules, 'view' => 'current'));
	}

	public static function gameDayOverview( $atts, $content, $tag ) {

		global $kkl_twig;
		$db = new KKL_DB_Wordpress();

		$data = $db->getAllUpcomingGames();
		$games = array();
		$leagues = array();
		foreach($data as $game) {
			$leagues[$game->league_id] = true;
			$games[$game->league_id][] = $game;
		}
		foreach(array_keys($leagues) as $league_id) {
			$league = $db->getLeague($league_id);
        	echo $kkl_twig->render('shortcodes/gameday_overview.tpl', array('schedule' => $games[$league_id], 'league' => $league));
		}
	}

	public static function seasonSchedule( $atts, $content, $tag ) {

		global $kkl_twig;
		$db = new KKL_DB_Wordpress();

		$activeTeam = $_GET['team'];

		$context = KKL::getContext();
		$schedules = $db->getScheduleForSeason($context['season']);
		foreach($schedules as $schedule) {
			foreach($schedule->matches as $match) {
				$home_club = $db->getClub($match->home->club_id);
				$away_club = $db->getClub($match->away->club_id);
				$match->home->link = KKL::getLink('club', array('club' => $home_club->short_name));
				$match->away->link = KKL::getLink('club', array('club' => $away_club->short_name));
			}
		}

        return $kkl_twig->render('shortcodes/game_day.tpl', array('context' => $context, 'schedules' => $schedules, 'view' => 'all', 'activeTeam' => $activeTeam));
	}

	public static function clubDetail( $atts, $content, $tag) {

		global $kkl_twig;

		$db = new KKL_DB_Wordpress();
		$context = KKL::getContext();
		$contextClub = $context['club'];
		$contextClub->logo = $contextClub->logo;

		$seasonTeams = $db->getTeamsForClub($contextClub->id);
		$teams = array();
		foreach($seasonTeams as $seasonTeam) {

			$seasonTeam->season = $db->getSeason($seasonTeam->season_id);
			$seasonTeam->season->league = $db->getLeague($seasonTeam->season->league_id);

			$ranking = $db->getRankingForLeagueAndSeasonAndGameDay($seasonTeam->season->league_id, $seasonTeam->season->id,  $seasonTeam->season->current_game_day);
			$position = 1;
			foreach($ranking as $rank) {
				if($rank->team_id == $seasonTeam->id) {
					$seasonTeam->scores = $rank;
					$seasonTeam->scores->position = $position;
					break;
				}
				$position++;
			}

			$seasonTeam->link = KKL::getLink('club', array('club' => $contextClub->short_name));
			$seasonTeam->schedule_link = KKL::getLink('schedule', array('league' => $seasonTeam->season->league->code, 'season' => date('Y', strtotime($seasonTeam->season->start_date)), 'team' => $seasonTeam->short_name));


			$teams[$seasonTeam->id] = $seasonTeam;
		}

		$currentTeam = $db->getCurrentTeamForClub($contextClub->id);
		$currentLocation = $db->getLocation($currentTeam->properties['location']);
        return $kkl_twig->render('shortcodes/club_detail.tpl', array('context' => $context, 'club' => $contextClub, 'teams' => $teams, 'current_location' => $currentLocation));

	}


	public static function gameDayPager( $atts, $content, $tag) {
		global $kkl_twig;

		$db = new KKL_DB_Wordpress();
		$context = KKL::getContext();

		$day = $context['game_day'];
		$league = $context['league'];
		$season = $context['season'];

		$prev = $db->getPreviousGameDay($day);
		if(!$prev) {
			$day->isFirst = true;
		}else{
			$prev->link = KKL::getLink('league', array('league' => $league->code, 'season' => date('Y', strtotime($season->start_date)), 'game_day' => $prev->number));
		}
		$next = $db->getNextGameDay($day);
		if(!$next) {
			$day->isLast = true;
		}else {
			$next->link = KKL::getLink('league', array('league' => $league->code, 'season' => date('Y', strtotime($season->start_date)), 'game_day' => $next->number));
		}

		return $kkl_twig->render('shortcodes/gameday_pager.tpl', array('context' => $context, 'prev' => $prev, 'day' => $day, 'next' => $next));

	}

    public static function contactList($atts, $content, $tag) {
        global $kkl_twig;

        $db = new KKL_DB_Wordpress();
        $context = KKL::getContext();

        $season = $context['season'];

        $players = array();
        $leagues = $db->getActiveLeagues();
        $leagueadmins = $db->getLeagueAdmins();
        $captains = $db->getCaptainsContactData();
        $vicecaptains = $db->getViceCaptainsContactData();
        $players = array_merge($leagueadmins, $captains, $vicecaptains);

        usort($players, array(
                __CLASS__,
                "cmp"
        ));

        return $kkl_twig->render('shortcodes/contact_list.tpl', array(
                'context' => $context,
                'leagues' => $leagues,
                'players' => $players
        ));

    }

	private static function cmp($a, $b)
	{
		    return strcmp($a->team, $b->team);
	}


}
