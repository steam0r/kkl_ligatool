<?php
namespace KKL\Ligatool;

$kkl = new Plugin();

$team = get_query_var('team');
$context = $kkl->getContextByClubCode($team);
Plugin::setContext($context);

/*
Template Name: Team Detail
*/
?><?php get_header(); ?>

<section class="contentWrapper">
    <div class="container">
        <div class="row">

            <div class="span5 teamDetail">
              <?php get_template_part('loop', 'team'); ?>
            </div><!-- .content -->

            <aside class="span2">
                <ul><?php dynamic_sidebar('kkl_teamdetail_sidebar'); ?></ul>
                <ul><?php dynamic_sidebar('kkl_global_sidebar'); ?></ul>
            </aside><!-- rechter content end. -->

        </div>
    </div><!-- .container -->
</section>


<?php get_footer(); ?>
