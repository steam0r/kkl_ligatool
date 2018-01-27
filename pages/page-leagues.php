<?php
namespace KKL\Ligatool;

$KKL = new KKL();
/*
Template Name: Liga Übersicht (Teams)

*/
if (isset($wp_query->query_vars['json'])) {

  header('Content-Type: application/json');

  global $kkl_twig;
  $db = new DB\Wordpress();
  $context = KKL::getContext();
  $all_leagues = $db->getActiveLeagues();
  $leagues = array();
  foreach ($all_leagues as $league) {
    $league->season = $db->getSeason($league->current_season);
    $league->teams = $db->getTeamsForSeason($league->season->id);
    foreach ($league->teams as $team) {
      $club = $db->getClub($team->club_id);
      if (!$team->logo) {
        $team->logo = $club->logo;
        if (!$club->logo) {
          $team->logo = "https://www.kickerligakoeln.de/wp-content/themes/kkl_2/img/kkl-logo_172x172.png";
        }
      } else {
        $team->logo = "/images/team/" . $team->logo;
      }
      // HACK
      $team->link = KKL::getLink('club', array('club' => $club->short_name));
    }

    $day = $db->getGameDay($league->season->current_game_day);
    $league->link = KKL::getLink('league', array('league' => $league->code, 'season' => date('Y', strtotime($league->season->start_date)), 'game_day' => $day->number));
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
