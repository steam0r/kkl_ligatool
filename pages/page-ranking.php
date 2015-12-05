<?php

$overview = false;
$league = get_query_var('league');
$season =  get_query_var('season');
$game_day =  get_query_var('game_day');

$KKL = new KKL();
if($league && $season && $game_day) {
  $context = $KKL->getContextByLeagueAndSeasonAndGameDay($league, $season, $game_day);
}else if($league && $season) {
  $context = $KKL->getContextByLeagueAndSeason($league, $season, $game_day);
}else if($league) {
  $context = $KKL->getContextByLeague($league, $season, $game_day);
}else{
  $context = null;
  $overview = true;
}
$KKL->setContext($context);

if(isset($wp_query->query_vars['json'])) {

  header('Content-Type: application/json');

  global $kkl_twig;
  $db = new KKL_DB();

  $context = KKL::getContext();
  $rankings = array();

  $output = array();

  if(!$overview)
  {
  $ranking = new stdClass;
  $ranking->league = $context['league'];
  $ranking->ranks = $db->getRankingForLeagueAndSeasonAndGameDay($context['league']->id, $context['season']->id, $context['game_day']->number);
  foreach($ranking->ranks as $rank) {
    $team = $db->getTeam($rank->team_id);
    $club = $db->getClub($team->club_id);
    $rank->team->link = get_site_url() . '/team/' . KKL::getLink('club', array('club' => $club->short_name));
  }

  $rankings[] = $ranking;
  $output['rankings'] = $rankings;

  $schedules = array();
  $schedule = $db->getScheduleForGameDay($context['game_day']);
  foreach($schedule->matches as $match) {
    $home_club = $db->getClub($match->home->club_id);
    $away_club = $db->getClub($match->away->club_id);
    $match->home->link = get_site_url() . '/team/' . KKL::getLink('club', array('club' => $home_club->short_name));
    $match->away->link = get_site_url() . '/team/' . KKL::getLink('club', array('club' => $away_club->short_name));
  }
  $schedule->link = get_site_url() . '/spielplan/' . KKL::getLink('schedule', array('league' => $context['league']->code, 'season' => date('Y', strtotime($context['season']->start_date))));

  $schedules[] = $schedule;

  $output['schedules'] = $schedules;

  }else{
    $context = KKL::getContext();
    foreach($db->getLeagues() as $league) {
      if($league->active != 1) continue;
      $season = $db->getSeason($league->current_season);
      $day = $db->getGameDay($season->current_game_day);
      $ranking = new stdClass;
      $ranking->league = $league;
      $ranking->ranks = $db->getRankingForLeagueAndSeasonAndGameDay($league->id, $season->id, $day->number);
      foreach($ranking->ranks as $rank) {
        $team = $db->getTeam($rank->team_id);
        $club = $db->getClub($team->club_id);
        $rank->team->link =  get_site_url() . '/team/' . KKL::getLink('club', array('club' => $club->short_name));
      }
      $ranking->league->link = get_site_url() . '/spielplan/' . KKL::getLink('league', array('league' => $league->code, 'season' => date('Y', strtotime($season->start_date)), 'game_day' => $day->number));
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
          if(!$overview) {
            get_template_part( 'loop', 'page' );
          } else {
            echo do_shortcode('[table_overview]');
          }
        ?>
      </div>
      
      <aside class="col-md-3 hidden-xs hidden-sm">
        <ul><?php dynamic_sidebar( 'kkl_leagues_sidebar' ); ?></ul>
        <ul><?php dynamic_sidebar( 'kkl_global_sidebar' ); ?></ul>
      </aside><!-- rechter content end. -->

    </div>
  </div>
</section>



<?php get_footer(); ?>
