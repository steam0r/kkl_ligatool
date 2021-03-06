<?php
namespace KKL\Ligatool;

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
}
$KKL->setContext($context);

/*
Template Name: Spielplan

*/
?><?php get_header(); ?>

<section class="kkl-content">
    <div class="container">
        <div class="row">

            <div class="col-md-9">
              <?php
              get_template_part('loop', 'page');
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
