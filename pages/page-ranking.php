<?php
namespace KKL\Ligatool;

$overview = false;
$league = get_query_var('league');
$season = get_query_var('season');
$game_day = get_query_var('game_day');

$KKL = new Plugin();
if ($league && $season && $game_day) {
  $context = $KKL->getContextByLeagueAndSeasonAndGameDay($league, $season, $game_day);
} elseif ($league && $season) {
  $context = $KKL->getContextByLeagueAndSeason($league, $season);
} elseif ($league) {
  $context = $KKL->getContextByLeague($league);
} else {
  $context = null;
  $overview = true;
}
$KKL->setContext($context);

if (isset($wp_query->query_vars['json'])) {

  header('Content-Type: application/json');

  $db = new DB\Wordpress();

  $context = Plugin::getContext();
  $rankings = array();

  $output = array();
  $clubService = ServiceBroker::getClubService();
  $teamService = ServiceBroker::getTeamService();
  $gameDayService = ServiceBroker::getGameDayService();
  $leagueService = ServiceBroker::getLeagueService();
  $seasonService = ServiceBroker::getSeasonService();

  if (!$overview) {
    $ranking = new \stdClass;
    $ranking->league = $context['league'];
    $ranking->ranks = $db->getRankingForLeagueAndSeasonAndGameDay($context['league']->id, $context['season']->id, $context['game_day']->number);
    foreach ($ranking->ranks as $rank) {
      $team = $teamService->byId($rank->team_id);
      $club = $clubService->byId($team->getClubId());
      $rank->team->link = get_site_url() . '/team/' . Plugin::getLink('club', array('club' => $club->getShortName()));
    }

    $rankings[] = $ranking;
    $output['rankings'] = $rankings;

    $schedules = array();
    $schedule = $db->getScheduleForGameDay($context['game_day']);
    foreach ($schedule->matches as $match) {
      $home_club = $clubService->byId($match->home->club_id);
      $away_club = $clubService->byId($match->away->club_id);
      $match->home->link = get_site_url() . '/team/' . Plugin::getLink('club', array('club' => $home_club->getShortName()));
      $match->away->link = get_site_url() . '/team/' . Plugin::getLink('club', array('club' => $away_club->getShortName()));
    }
    $schedule->link = get_site_url() . '/spielplan/' . Plugin::getLink('schedule', array('league' => $context['league']->code, 'season' => date('Y', strtotime($context['season']->start_date))));

    $schedules[] = $schedule;

    $output['schedules'] = $schedules;

  } else {
    foreach ($leagueService->getAll() as $league) {
      if ($league->isActive() != 1) continue;
      $season = $seasonService->byId($league->getCurrentSeason());
      $day = $gameDayService->byId($season->getCurrentGameDay());
      $ranking = new \stdClass;
      $ranking->league = $league;
      $ranking->ranks = $db->getRankingForLeagueAndSeasonAndGameDay($league->getId(), $season->getId(), $day->getNumber());
      foreach ($ranking->ranks as $rank) {
        $team = $teamService->byId($rank->team_id);
        $club = $clubService->byId($team->getClubId());
        $rank->team->link = get_site_url() . '/team/' . Plugin::getLink('club', array('club' => $club->getShortName()));
      }
      $ranking->league->link = get_site_url() . '/spielplan/' . Plugin::getLink(
          'league',
          array(
            'league' => $league->getCode(),
            'season' => date('Y', strtotime($season->getStartDate())),
            'game_day' => $day->getNumber())
        );
      $rankings[] = $ranking;
    }
    $output['rankings'] = $rankings;
  }

  print json_encode($output);
  die();

};

/*
Template Name: Tabelle

*/
?><?php get_header(); ?>
<section class="kkl-content">
    <div class="container">
        <div class="row">

            <div class="col-md-9">
              <?php
              if (!$overview) {
                get_template_part('loop', 'page');
              } else {
                echo do_shortcode('[table_overview]');
              }
              ?>
            </div>

            <aside class="col-md-3 hidden-xs hidden-sm">
                <ul><?php dynamic_sidebar('kkl_leagues_sidebar'); ?></ul>
                <ul><?php dynamic_sidebar('kkl_global_sidebar'); ?></ul>
            </aside><!-- rechter content end. -->

        </div>
    </div>
</section>


<?php get_footer(); ?>
