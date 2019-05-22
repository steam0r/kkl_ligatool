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
    $db = new DB\Wordpress();
    $data = $db->getAllGamesForNextGameday();

    $all_matches = array();
    foreach($data as $game) {
      $teamProperties = $db->getTeamProperties($game->homeid);

      $leagueData = $db->getLeague($game->league_id);
      $all_matches[$game->league_id]["id"] = $game->league_id;
      $all_matches[$game->league_id]["name"] = $leagueData->name;
      $all_matches[$game->league_id]["code"] = $leagueData->code;
      $all_matches[$game->league_id]["matches"][] = array(
          "match" => $game,
          "location_id" => $teamProperties["location"]
      );
    }

    $all_locations = $db->getLocations();

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
   *
   */
  public static function ranking($atts) {
    $templateEngine = Template\Service::getTemplateEngine();
    $ranking = new Ranking();
    $templateContext = array();

    $league = isset($atts['league']) ? $atts['league'] : null;
    $season = isset($atts['season']) ? $atts['season'] : null;
    $gameday = isset($atts['gameday']) ? $atts['gameday'] : null;

    if(isset($league)) {
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