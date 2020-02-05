<?php


namespace KKL\Ligatool\Shortcode;

use KKL\Ligatool\Plugin;
use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Template;

class TableOverview extends Shortcode {

  public static function render($atts, $content, $tag) {
    $kkl_twig = Template\Service::getTemplateEngine();
    $leagueService = ServiceBroker::getLeagueService();
    $seasonService = ServiceBroker::getSeasonService();
    $gameDayService = ServiceBroker::getGameService();
    $rankingService = ServiceBroker::getRankingService();
    $teamService = ServiceBroker::getTeamService();
    $clubService = ServiceBroker::getClubService();

    $context = Plugin::getContext();
    $rankings = array();
    foreach ($leagueService->getActive() as $league) {
      $season = $seasonService->byId($league->getCurrentSeason());
      $day = $gameDayService->byId($season->getCurrentGameDay());
      $ranking = $rankingService->getRankingForLeagueAndSeasonAndGameDay($league->getId(), $season->getId(), $day->getNumber());
      foreach ($ranking->getRanks() as $rank) {
        $team = $teamService->byId($rank->getTeamId());
        $club = $clubService->byId($team->getClubId());
        // TODO: use some kind of DTO
        $team->link = Plugin::getLink('club', array('club' => $club->getShortName()));
      }

      // TODO: use some kind of DTO
      $league->link = Plugin::getLink(
        'league',
        array(
          'league' => $league->getCode(),
          'season' => date('Y', strtotime($season->getStartDate())),
          'game_day' => $day->getNumber())
      );
      $rankings[] = $ranking;
    }

    return $kkl_twig->render(
      self::$TEMPLATE_PATH . '/ranking.twig',
      array(
        'context' => $context,
        'rankings' => $rankings
      ));
  }
}