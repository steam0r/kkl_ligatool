<?php


namespace KKL\Ligatool\Shortcode;

use KKL\Ligatool\Pages\Pages;
use KKL\Ligatool\Pages\RankingPage;
use KKL\Ligatool\Template;

class RankingCode extends Shortcode {

  public static function render($atts, $content, $tag) {
    $templateEngine = Template\Service::getTemplateEngine();
    $ranking = new RankingPage();
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
      self::$TEMPLATE_PATH . '/ranking.twig',
      $templateContext
    );
  }
}