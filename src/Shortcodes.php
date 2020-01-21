<?php

namespace KKL\Ligatool;

use KKL\Ligatool\Pages\Pages;
use KKL\Ligatool\Pages\Ranking;

class Shortcodes {

  const TEMPLATE_PATH = 'shortcodes';


  /**
   * @param $atts
   * @param $content
   * @param $tag
   * @return mixed
   */
  public static function setMatchFixture($atts, $content, $tag) {
    $templateEngine = Template\Service::getTemplateEngine();

    $gameService = ServiceBroker::getGameService();
    $leagueService = ServiceBroker::getLeagueService();
    $teamPropertyService = ServiceBroker::getTeamPropertyService();

    $data = $gameService->getAllGamesForNextGameday();


    $all_matches = array();
    foreach ($data as $game) {
      $teamProperties = $teamPropertyService->byTeam($game->homeid);

      $leagueData = $leagueService->byId($game->league_id);
      $all_matches[$game->league_id]["id"] = $game->league_id;
      $all_matches[$game->league_id]["name"] = $leagueData->getName();
      $all_matches[$game->league_id]["code"] = $leagueData->getCode();
      $all_matches[$game->league_id]["matches"][] = array(
        "match" => $game,
        "location_id" => $teamProperties["location"]
      );
    }

    $locationService = ServiceBroker::getLocationService();
    $all_locations = $locationService->getAll();

    return $templateEngine->render(
      self::TEMPLATE_PATH . '/set_match_fixture.twig',
      array(
        'api_url' => get_site_url(),
        'all_matches' => $all_matches,
        'all_locations' => $all_locations
      )
    );
  }


  /**
   * @param $atts
   * @return mixed
   */
  public static function ranking($atts) {
    $templateEngine = Template\Service::getTemplateEngine();
    $ranking = new Ranking();
    $templateContext = array();

    $league = isset($atts['league']) ? $atts['league'] : null;
    $season = isset($atts['season']) ? $atts['season'] : null;
    $gameday = isset($atts['gameday']) ? $atts['gameday'] : null;

    if (isset($league)) {
      $pageContext = Pages::leagueContext($league, $season, $gameday);
      $templateContext = array(
        'rankings' => $ranking->getSingleLeague($pageContext)
      );
    }

    return $templateEngine->render(
      self::TEMPLATE_PATH . '/ranking.twig',
      $templateContext
    );
  }

}