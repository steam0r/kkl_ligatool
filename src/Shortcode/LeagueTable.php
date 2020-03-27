<?php


namespace KKL\Ligatool\Shortcode;

use KKL\Ligatool\Plugin;
use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Template;

class LeagueTable extends Shortcode {

  public static function render($atts, $content, $tag) {

    $kkl_twig = Template\Service::getTemplateEngine();

    $context = Plugin::getUrlContext();
    $rankings = array();

    $rankingService = ServiceBroker::getRankingService();
    $teamService = ServiceBroker::getTeamService();
    $clubService = ServiceBroker::getClubService();
    $seasonPropertyService = ServiceBroker::getSeasonPropertyService();

    if ($context->getLeague() && $context->getSeason()) {

      $league = $context->getLeague();
      $season = $context->getSeason();
      $ranking = $rankingService->getRankingForLeagueAndSeasonAndGameDay($league->getId(), $season->getId(), $context['gameDayNumber']);

      foreach ($ranking->getRanks() as $rank) {
        $team = $teamService->byId($rank->getTeamId());
        $club = $clubService->byId($team->getClubId());
        // TODO: use some kind of DTO
        $rank->team->link = Plugin::getLink('club', array('club' => $club->getShortName()));
      }

      $properties = $seasonPropertyService->bySeason($context['season']->id);
      if ($properties && array_key_exists('relegation_explanation', $properties)) {
        $ranking->relegation_explanation = $properties['relegation_explanation'];
      }
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