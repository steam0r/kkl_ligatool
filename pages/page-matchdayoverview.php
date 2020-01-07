<?php
namespace KKL\Ligatool;

$gameDayService = ServiceBroker::getGameDayService();
$currentGameDay = $gameDayService->currentForLeague(1);

/*
Template Name: Spieltag Übersicht

*/
?><?php get_header(); ?>

<section class="kkl-content">
    <div class="container">
        <div class="row">

            <div class="col-sm-10 col-md-8 col-sm-offset-1 col-md-offset-2">
              <?php if (have_posts()) while (have_posts()) : the_post(); ?>

                  <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                      <header class="text-center">
                          <small class="entry-metadata">
                            <?php echo date('d.m.Y', strtotime($currentGameDay->getFixture())); ?>
                              - <?php echo date('d.m.Y', strtotime($currentGameDay->getEnd())); ?>
                          </small>
                          <h2><?php echo $currentGameDay->getNumber(); ?>. Spieltag</h2>
                      </header>

                      <div class="entry-content">
                        <?php the_content(); ?>
                        <?php edit_post_link(__('Edit', 'twentyten'), '<span class="edit-link">', '</span>'); ?>
                      </div><!-- .entry-content -->
                  </article><!-- #post-## -->

              <?php endwhile; // end of the loop.
              ?>
            </div>

        </div>
    </div>
</section>


<?php get_footer(); ?>
