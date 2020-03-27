<?php


namespace KKL\Ligatool\Shortcode;

use KKL\Ligatool\Plugin;
use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Template;

class SeasonSchedule extends Shortcode {

  public static function render($atts, $content, $tag) {
    $kkl_twig = Template\Service::getTemplateEngine();

    $scheduleService = ServiceBroker::getScheduleService();
    $clubService = ServiceBroker::getClubService();
    $teamService = ServiceBroker::getTeamService();

    $activeTeam = $_GET['team'];

    $context = Plugin::getUrlContext();
    $schedules = [];
    if($context->getSeason()) {
      $schedules = $scheduleService->getScheduleForSeason();
      foreach ($schedules as $schedule) {
        foreach ($schedule->getMatches() as $match) {
          $home_team = $teamService->byId($match->getHomeTeam());
          $away_team = $teamService->byId($match->getAwayTeam());
          $home_club = $clubService->byId($home_team->getClubId());
          $away_club = $clubService->byId($away_team->getClubId());
          // TODO: use some kind of DTO
          $match->home->link = Plugin::getLink('club', array('club' => $home_club->getShortName()));
          $match->away->link = Plugin::getLink('club', array('club' => $away_club->getShortName()));
        }
      }
    }

    return $kkl_twig->render(
      'shortcodes/game_day.twig',
      array('context' => $context, 'schedules' => $schedules, 'view' => 'all', 'activeTeam' => $activeTeam)
    );
  }
}