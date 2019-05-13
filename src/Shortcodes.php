<?php

namespace KKL\Ligatool;

use stdClass;

class Shortcodes {
  
  public static function getClub($atts, $content, $tag) {
    
    $kkl_twig = Template\Service::getTemplateEngine();
    
    $db = new DB\Wordpress();
    
    $context = Plugin::getContext();
    $club = $db->getClubData($atts['id']);
    
    return $kkl_twig->render('shortcodes/club.twig', array('context' => $context, 'club' => $club));
    
  }

  
  public static function leagueTable($atts, $content, $tag) {
    
    $kkl_twig = Template\Service::getTemplateEngine();
    $db = new DB\Wordpress();
    
    $context = Plugin::getContext();
    $rankings = array();
    
    $ranking = new stdClass;
    $ranking->league = $context['league'];
    $ranking->ranks = $db->getRankingForLeagueAndSeasonAndGameDay(
      $context['league']->id, $context['season']->id, $context['game_day']->number
    );
    foreach($ranking->ranks as $rank) {
      $team = $db->getTeam($rank->team_id);
      $club = $db->getClub($team->club_id);
      $rank->team->link = Plugin::getLink('club', array('club' => $club->short_name));
    }
    $properties = $db->getSeasonProperties($context['season']->id);
    if($properties && array_key_exists('relegation_explanation', $properties)) {
      $ranking->relegation_explanation = $properties['relegation_explanation'];
    }
    $rankings[] = $ranking;
    
    return $kkl_twig->render('shortcodes/table.twig', array('context' => $context, 'rankings' => $rankings));
  }
  
  public static function tableOverview($atts, $content, $tag) {
    
    $kkl_twig = Template\Service::getTemplateEngine();
    $db = new DB\Wordpress();
    
    $context = Plugin::getContext();
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
        $rank->team->link = Plugin::getLink('club', array('club' => $club->short_name));
      }

      $ranking->league->link = Plugin::getLink(
        'league', array('league'   => $league->code, 'season' => date('Y', strtotime($season->start_date)),
                        'game_day' => $day->number)
      );
      $rankings[] = $ranking;
    }
    
    return $kkl_twig->render('shortcodes/table.twig', array('context' => $context, 'rankings' => $rankings));
  }
  
  
  public static function gameDayTable($atts, $content, $tag) {
    
    $kkl_twig = Template\Service::getTemplateEngine();
    $db = new DB\Wordpress();
    
    $context = Plugin::getContext();
    $schedules = array();
    $schedule = $db->getScheduleForGameDay($context['game_day']);
    foreach($schedule->matches as $match) {
      $home_club = $db->getClub($match->home->club_id);
      $away_club = $db->getClub($match->away->club_id);
      $match->home->link = Plugin::getLink('club', array('club' => $home_club->short_name));
      $match->away->link = Plugin::getLink('club', array('club' => $away_club->short_name));
    }
    $schedule->link = Plugin::getLink(
      'schedule',
      array('league' => $context['league']->code, 'season' => date('Y', strtotime($context['season']->start_date)))
    );
    
    $schedules[] = $schedule;
    
    return $kkl_twig->render(
      'shortcodes/game_day.twig', array('context' => $context, 'schedules' => $schedules, 'view' => 'current')
    );
  }
  
  public static function gameDayOverview($atts, $content, $tag) {
    
    $kkl_twig = Template\Service::getTemplateEngine();
    $db = new DB\Wordpress();
    
    $data = $db->getAllUpcomingGames();
    $games = array();
    $leagues = array();
    foreach($data as $game) {
      $leagues[$game->league_id] = true;
      $games[$game->league_id][] = $game;
    }
    foreach(array_keys($leagues) as $league_id) {
      $league = $db->getLeague($league_id);
      echo $kkl_twig->render(
        'shortcodes/gameday_overview.twig', array('schedule' => $games[$league_id], 'league' => $league)
      );
    }
  }
  
  public static function seasonSchedule($atts, $content, $tag) {
    
    $kkl_twig = Template\Service::getTemplateEngine();
    $db = new DB\Wordpress();
    
    $activeTeam = $_GET['team'];
    
    $context = Plugin::getContext();
    $schedules = $db->getScheduleForSeason($context['season']);
    foreach($schedules as $schedule) {
      foreach($schedule->matches as $match) {
        $home_club = $db->getClub($match->home->club_id);
        $away_club = $db->getClub($match->away->club_id);
        $match->home->link = Plugin::getLink('club', array('club' => $home_club->short_name));
        $match->away->link = Plugin::getLink('club', array('club' => $away_club->short_name));
      }
    }
    
    return $kkl_twig->render(
      'shortcodes/game_day.twig',
      array('context' => $context, 'schedules' => $schedules, 'view' => 'all', 'activeTeam' => $activeTeam)
    );
  }
  
  public static function clubDetail($atts, $content, $tag) {
    
    $kkl_twig = Template\Service::getTemplateEngine();
    
    $db = new DB\Wordpress();
    $context = Plugin::getContext();
    $contextClub = $context['club'];
    
    $seasonTeams = $db->getTeamsForClub($contextClub->id);
    $teams = array();
    foreach($seasonTeams as $seasonTeam) {
      
      $seasonTeam->season = $db->getSeason($seasonTeam->season_id);
      $seasonTeam->season->league = $db->getLeague($seasonTeam->season->league_id);
      
      $ranking = $db->getRankingForLeagueAndSeasonAndGameDay(
        $seasonTeam->season->league_id, $seasonTeam->season->id, $seasonTeam->season->current_game_day
      );
      $position = 1;
      foreach($ranking as $rank) {
        if($rank->team_id == $seasonTeam->id) {
          $seasonTeam->scores = $rank;
          $seasonTeam->scores->position = $position;
          break;
        }
        $position++;
      }

      $seasonTeam->link = Plugin::getLink('club', array('club' => $contextClub->short_name));
      $seasonTeam->schedule_link = Plugin::getLink(
        'schedule', array('league' => $seasonTeam->season->league->code, 'season' => date(
                    'Y', strtotime($seasonTeam->season->start_date)
                  ), 'team'        => $seasonTeam->short_name)
      );

      $teams[$seasonTeam->id] = $seasonTeam;
    }
    
    $currentTeam = $db->getCurrentTeamForClub($contextClub->id);
    $currentLocation = $db->getLocation($currentTeam->properties['location']);
    
    return $kkl_twig->render(
      'shortcodes/club_detail.twig',
      array(
          'context' => $context,
          'club' => $contextClub,
          'teams' => $teams,
          'current_location' => $currentLocation)
    );
    
  }
  
  
  public static function gameDayPager($atts, $content, $tag) {
    $kkl_twig = Template\Service::getTemplateEngine();
    
    $db = new DB\Wordpress();
    $context = Plugin::getContext();
    
    $day = $context['game_day'];
    $league = $context['league'];
    $season = $context['season'];
    
    $prev = $db->getPreviousGameDay($day);
    if(!$prev) {
      $day->isFirst = true;
    } else {
      $prev->link = Plugin::getLink(
        'league', array('league'   => $league->code, 'season' => date('Y', strtotime($season->start_date)),
                        'game_day' => $prev->number)
      );

    }
    $next = $db->getNextGameDay($day);
    if(!$next) {
      $day->isLast = true;
    } else {
      $next->link = Plugin::getLink(
        'league', array('league'   => $league->code, 'season' => date('Y', strtotime($season->start_date)),
                        'game_day' => $next->number)
      );
      $next->link = Plugin::getLink('league', array('league' => $league->code, 'season' => date('Y', strtotime($season->start_date)), 'game_day' => $next->number));
    }
    
    return $kkl_twig->render(
      'shortcodes/gameday_pager.twig', array('context' => $context, 'prev' => $prev, 'day' => $day, 'next' => $next)
    );
    
  }

  public static function setMatchFixture($atts, $content, $tag) {

    $templateEngine = Template\Service::getTemplateEngine();
    $db = new DB\Wordpress();
    $data = $db->getAllGamesForNextGameday();

    $all_matches = array();
    foreach($data as $game) {
      $teamProperties = $db->getTeamProperties($game->homeid);
      
      $leagueData = $db->getLeague($game->league_id);
      $all_matches[$game->league_id]["id"] = $game->league_id;
      $all_matches[$game->league_id]["name"] = $leagueData->name;
      $all_matches[$game->league_id]["code"] = $leagueData->code;
      $all_matches[$game->league_id]["matches"][] = array("match"       => $game,
                                                          "location_id" => $teamProperties["location"]);
    }
    
    $all_locations = $db->getLocations();
    return $templateEngine->render(
      'shortcodes/set_match_fixture.twig',
      array(
        'api_url' => get_site_url(),
        'all_matches' => $all_matches,
        'all_locations' => $all_locations
      )
    );
    
  }
  
  private static function cmp($a, $b) {
    return strcmp($a->team, $b->team);
  }
  
}
