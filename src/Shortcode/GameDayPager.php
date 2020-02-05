<?php


namespace KKL\Ligatool\Shortcode;

use KKL\Ligatool\Model\GameDay;
use KKL\Ligatool\Model\League;
use KKL\Ligatool\Model\Season;
use KKL\Ligatool\Plugin;
use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Template;

class GameDayPager extends Shortcode {

  public static function render($atts, $content, $tag) {
    $kkl_twig = Template\Service::getTemplateEngine();

    $gameDayService = ServiceBroker::getGameDayService();

    $context = Plugin::getContext();

    /* @var GameDay */
    $day = $context['game_day'];
    /* @var League */
    $league = $context['league'];
    /* @var Season */
    $season = $context['season'];

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