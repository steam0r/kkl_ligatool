<?php


namespace KKL\Ligatool\Shortcode;

use KKL\Ligatool\Plugin;
use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Template;

class GameDayPager extends Shortcode {

  public static function render($atts, $content, $tag) {

    $kkl_twig = Template\Service::getTemplateEngine();

    $gameDayService = ServiceBroker::getGameDayService();

    $context = Plugin::getUrlContext();
    if ($context->getLeague() && $context->getSeason() && $context->getGameDay()) {

      $league = $context->getLeague();
      $season = $context->getSeason();
      $day = $context->getGameDay();

      $prev = $gameDayService->getPrevious($day);
      if (!$prev) {
        // TODO: use some kind of DTO
        $day->isFirst = true;
      } else {
        // TODO: use some kind of DTO
        $prev->link = Plugin::getLink(
          'league',
          array(
            'league' => $league->getCode(),
            'season' => date('Y', strtotime($season->getStartDate())),
            'game_day' => $prev->getNumber()
          )
        );

      }
      $next = $gameDayService->getNext($day);
      if (!$next) {
        // TODO: use some kind of DTO
        $day->isLast = true;
      } else {
        // TODO: use some kind of DTO
        $next->link = Plugin::getLink(
          'league', array('league' => $league->getCode(), 'season' => date('Y', strtotime($season->getStartDate())),
            'game_day' => $next->getNumber())
        );
        $next->link = Plugin::getLink('league', array('league' => $league->getCode(), 'season' => date('Y', strtotime($season->getStartDate())), 'game_day' => $next->getNumber()));
      }
    }

    return $kkl_twig->render(
      'widgets/upcoming_games.twig',
      array(
        'context' => $context,
        'prev' => $prev,
        'day' => $day,
        'next' => $next
      )
    );
  }
}