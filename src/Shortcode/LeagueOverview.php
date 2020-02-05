<?php


namespace KKL\Ligatool\Shortcode;

use KKL\Ligatool\Plugin;
use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Template;

class LeagueOverview extends Shortcode {

  public static function render($atts, $content, $tag) {
    $kkl_twig = Template\Service::getTemplateEngine();

    $leagueService = ServiceBroker::getLeagueService();
    $seasonService = ServiceBroker::getSeasonService();
    $teamService = ServiceBroker::getTeamService();
    $clubService = ServiceBroker::getClubService();
    $gameDayService = ServiceBroker::getGameDayService();

    $context = Plugin::getContext();
    $all_leagues = $leagueService->getActive();
    $leagues = array();
    foreach ($all_leagues as $league) {
      $league->season = $seasonService->byId($league->getCurrentSeason());
      $league->teams = $teamService->forSeason($league->season->getId());
      foreach ($league->teams as $team) {
        $club = $clubService->byId($team->getClubId());
        if (!$team->getLogo()) {
          $team->setLogo($club->getLogo());
          if (!$club->getLogo()) {
            $team->setLogo("");
          }
        } else {
          $team->setLogo("/images/team/" . $team->getLogo());
        }
        // HACK
        $team->link = Plugin::getLink('club', array('club' => $club->getShortName()));
      }

      $day = $gameDayService->byId($league->season->getCurrentGameDay());

      $league->link = Plugin::getLink(
        'league', array('league' => $league->getCode(), 'season' => date('Y', strtotime($league->season->getStartDate())),
          'game_day' => $day->getNumber(),)
      );
      $leagues[] = $league;
    }

    return $kkl_twig->render(
      self::$TEMPLATE_PATH . '/league_overview.twig',
      array('context' => $context, 'leagues' => $leagues, 'all_leagues' => $all_leagues,)
    );
  }
}