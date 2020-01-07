<?php

namespace KKL\Ligatool\Pages;


use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Template;
use KKL\Ligatool\DB;

class Pages {

  const TEMPLATE_PATH = 'pages';


  /**
   * @param $league
   * @param null $season
   * @param null $game_day
   * @return array
   */
  public static function leagueContext($league, $season = null, $game_day = null) {
    $db = new DB\Wordpress();

    $gameDayService = ServiceBroker::getGameDayService();
    $leagueService = ServiceBroker::getLeagueService();
    $seasonService = ServiceBroker::getSeasonService();

    $output = array(
        'league' => $leagueService->bySlug($league)
    );

    if($season) {
      $output['season'] = $db->getSeasonByLeagueAndYear($output['league']->getId(), $season);
    } else {
      $output['season'] = $seasonService->byId($output['league']->current_season);
    }

    if($game_day) {
      $output['game_day'] = $db->getGameDayBySeasonAndPosition($output['season']->getId(), $game_day);
    } else {
      $output['game_day'] = $gameDayService->byId($output['season']->getCurrentGameDay());
    }

    return $output;
  }


  /**
   *
   * @return mixed
   */
  public static function contactList() {
    $kkl_twig = Template\Service::getTemplateEngine();

    $leagueService = ServiceBroker::getLeagueService();

    $db = new DB\Wordpress();

    $leagues = $leagueService->getActive();

    $playerService = ServiceBroker::getPlayerService();
    $leagueadmins = $playerService->getLeagueAdmins();
    $captains = $db->getCaptainsContactData();
    $vicecaptains = $db->getViceCaptainsContactData();
    $players = array_merge($leagueadmins, $captains, $vicecaptains);

    $leagueMap = array();
    foreach($leagues as $league) {
      $leagueMap[$league->getCode()] = $league;
    }

    $contactMap = array();
    foreach($players as $player) {

      if($player->league_short) {
        if(!isset($contactMap[$player->league_short])) {
          $contactMap[$player->league_short]['league'] = $leagueMap[$player->league_short];
        }
        $contactMap[$player->league_short]['players'][] = $player;
      } else if($player->role === 'ligaleitung') {
        if(!isset($contactMap['ligaleitung'])) {
          $contactMap['ligaleitung']['league'] = array(
              'id' => 'ligaleitung',
              'code' => 'ligaleitung',
              'name' => 'Ligaleitung'
          );
        }

        $contactMap['ligaleitung']['players'][] = $player;
      }
    }

    return $kkl_twig->render(
        self::TEMPLATE_PATH . '/contact_list.twig', array(
            'leagues' => $leagues,
            'contactMap' => $contactMap
        )
    );
  }


  /**
   *
   * @return mixed
   */
  public static function teams() {
    $kkl_twig = Template\Service::getTemplateEngine();
    $teams = new Teams();

    $team_name = get_query_var('team_name');

    if($team_name) {
      $templateName = '/team-single.twig';
      $templatContext = $teams->getSingleClub($team_name);
    } else {
      $templateName = '/team-all.twig';
      $templatContext = array(
          'leagues' => $teams->getAllActiveTeams()
      );
    }

    return $kkl_twig->render(self::TEMPLATE_PATH . $templateName, $templatContext);
  }


  /**
   * @return mixed
   */
  public static function ranking() {
    $kkl_twig = Template\Service::getTemplateEngine();
    $ranking = new Ranking();
    $schedule = new Schedule();

    $league = get_query_var('league');
    $season = get_query_var('season');
    $game_day = get_query_var('game_day');

    if($league) {
      $templateName = '/ranking-single.twig';
      $pageContext = Pages::leagueContext($league, $season, $game_day);
      $templateContext = array(
          'rankings' => $ranking->getSingleLeague($pageContext),
          'schedules' => $schedule->getSingleLeague($pageContext)
      );
    } else {
      $templateName = '/ranking-all.twig';
      $templateContext = array(
          'rankings' => $ranking->getAllActiveLeagues()
      );
    }

    return $kkl_twig->render(self::TEMPLATE_PATH . $templateName, $templateContext);
  }


  /**
   * @return mixed
   */
  public static function fixtures() {
    $kkl_twig = Template\Service::getTemplateEngine();
    $schedule = new Schedule();

    $league = get_query_var('league');
    $season = get_query_var('season');

    if($league) {
      $templateName = '/fixtures.twig';
      $pageContext = Pages::leagueContext($league, $season);
      $templateContext = array(
          'schedules' => $schedule->getSeason($pageContext)
      );

      if(isset($_GET['team'])) {
        $templateContext['activeTeam'] = $_GET['team'];
      }
    } else {
      $templateName = '/fixtures-all.twig';
      $templateContext = array(
          'leagues' => $schedule->getCurrentGameday()
      );
    }

    return $kkl_twig->render(self::TEMPLATE_PATH . $templateName, $templateContext);
  }
}