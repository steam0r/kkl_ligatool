<?php
namespace KKL\Ligatool;

/*
Template Name: Liga Übersicht (Teams)

*/
if (isset($wp_query->query_vars['json'])) {

  header('Content-Type: application/json');

  $leagueService = ServiceBroker::getLeagueService();
  $seasonService = ServiceBroker::getSeasonService();
  $teamService = ServiceBroker::getTeamService();
  $gameDayService = ServiceBroker::getGameDayService();
  $clubService = ServiceBroker::getClubService();

  $all_leagues = $leagueService->getActive();
  $leagues = array();
  foreach ($all_leagues as $league) {
    $league->season = $seasonService->currentByLeague($league->getId());
    $league->teams = $teamService->forSeason($league->season->getId());
    foreach ($league->teams as $team) {
      $club = $clubService->byId($team->getClubId());
      if (!$team->getLogo()) {
        $team->setLogo($club->getLogo());
        if (!$club->getLogo()) {
          $team->setLogo("https://www.kickerligakoeln.de/wp-content/themes/kkl_2/img/kkl-logo_172x172.png");
        }
      } else {
        $team->setLogo("/images/team/" . $team->getLogo());
      }
      // HACK
      $team->link = Plugin::getLink('club', array('club' => $club->getShortName()));
    }

    $day = $gameDayService->byId($league->season->getCurrentGameDay());
    $league->link = Plugin::getLink(
      'league',
      array(
        'league' => $league->getCode(),
        'season' => date('Y', strtotime($league->season->getStartDate())),
        'game_day' => $day->getNumber()
      )
    );
    $leagues[] = $league;
  }

  echo json_encode($leagues);
  die();
}
?>
?>
<?php get_header(); ?>

<section class="kkl-content">
    <div class="container">
        <div class="row">

            <div class="col-md-9">
                <div class="row">
                    <div class="col-xs-12">
                        <nav class="isotope-nav">
                            <ul class="nav nav-pills">
                                <li><a href="#" rel="isotope" data-filter="*">Alle</a></li>
                                <li><a href="#" rel="isotope" data-filter=".koeln1">1. Liga</a></li>
                                <li><a href="#" rel="isotope" data-filter=".koeln2a">2. Liga A</a></li>
                                <li><a href="#" rel="isotope" data-filter=".koeln2b">2. Liga B</a></li>
                                <li><a href="#" rel="isotope" data-filter=".koeln3a">3. Liga A</a></li>
                                <li><a href="#" rel="isotope" data-filter=".koeln3b">3. Liga B</a></li>
                                <li><a href="#" rel="isotope" data-filter=".koeln3c">3. Liga C</a></li>
                                <li><a href="#" rel="isotope" data-filter=".koeln3d">3. Liga D</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="row">
                  <?php
                  /* Run the loop to output the page.
     * If you want to overload this in a child theme then include a file
     * called loop-page.php and that will be used instead.
     */
                  get_template_part('loop', 'page');
                  ?>
                </div>
            </div>

            <aside class="col-md-3 hidden-xs hidden-sm">
                <ul><?php dynamic_sidebar('kkl_leagues_sidebar'); ?></ul>
                <ul><?php dynamic_sidebar('kkl_global_sidebar'); ?></ul>
            </aside><!-- rechter content end. -->

        </div>
    </div>
</section>

<?php get_footer(); ?>
