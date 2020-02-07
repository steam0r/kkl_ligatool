<?php


namespace KKL\Ligatool\Shortcode;

use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Template;

class GameDayOverview extends Shortcode {

  public static function render($atts, $content, $tag) {
    $kkl_twig = Template\Service::getTemplateEngine();

    $matchService = ServiceBroker::getMatchService();
    $leagueService = ServiceBroker::getLeagueService();

    $data = $matchService->getAllUpcomingMatches();
    $games = array();
    $leagues = array();
    foreach ($data as $game) {
      $leagues[$game->league_id] = true;
      $games[$game->league_id][] = $game;
    }
    foreach (array_keys($leagues) as $league_id) {
      $league = $leagueService->byId($league_id);
      echo $kkl_twig->render(
        self::$TEMPLATE_PATH . '/gameday_overview.twig',
        array(
          'schedule' => $games[$league_id],
          'league' => $league
        )
      );
    }
  }
}