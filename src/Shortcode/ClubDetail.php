<?php


namespace KKL\Ligatool\Shortcode;

use KKL\Ligatool\Plugin;
use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Template;

class ClubDetail extends Shortcode {

  public static function render($atts, $content, $tag) {
    $kkl_twig = Template\Service::getTemplateEngine();

    $context = Plugin::getContext();
    $contextClub = $context['club'];

    $teamService = ServiceBroker::getTeamService();
    $seasonService = ServiceBroker::getSeasonService();
    $rankingService = ServiceBroker::getRankingService();
    $leagueService = ServiceBroker::getLeagueService();
    $locationService = ServiceBroker::getLocationService();

    $seasonTeams = $teamService->byClub($contextClub->getId());
    $teams = array();
    foreach ($seasonTeams as $seasonTeam) {

      // TODO: use some kind of DTO
      $seasonTeam->season = $seasonService->byId($seasonTeam->getSeasonId());
      $seasonTeam->season->league = $leagueService->byId($seasonTeam->season->getLeagueId());

      $ranking = $rankingService->getRankingForLeagueAndSeasonAndGameDay(
        $seasonTeam->season->getLeagueId(), $seasonTeam->season->getId(), $seasonTeam->season->getCurrentGameDay()
      );
      $position = 1;
      foreach ($ranking as $rank) {
        if ($rank->team_id == $seasonTeam->getId()) {
          $seasonTeam->scores = $rank;
          $seasonTeam->scores->position = $position;
          break;
        }
        $position++;
      }

      // TODO: use some kind of DTO
      $seasonTeam->link = Plugin::getLink('club', array('club' => $contextClub->short_name));
      $seasonTeam->schedule_link = Plugin::getLink(
        'schedule', array('league' => $seasonTeam->season->league->code, 'season' => date(
          'Y', strtotime($seasonTeam->season->getStartDate())
        ), 'team' => $seasonTeam->getShortName())
      );

      $teams[$seasonTeam->getId()] = $seasonTeam;
    }

    $currentTeam = $teamService->getCurrentTeamForClub($contextClub->id);
    $currentLocation = $locationService->byId($currentTeam->properties['location']);

    return $kkl_twig->render(
      self::$TEMPLATE_PATH . '/club_detail.twig',
      array(
        'context' => $context,
        'club' => $contextClub,
        'teams' => $teams,
        'current_location' => $currentLocation
      )
    );
  }
}