<?php


namespace KKL\Ligatool\Shortcode;

use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Template;

class SetMatchFixture extends Shortcode {

  public static function render($atts, $content, $tag) {
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
      self::$TEMPLATE_PATH . '/set_match_fixture.twig',
      array(
        'api_url' => get_site_url(),
        'all_matches' => $all_matches,
        'all_locations' => $all_locations
      )
    );
  }

}