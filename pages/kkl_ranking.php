<?php
global $post;
use KKL\Ligatool\Pages\Pages;
get_header();
?>

<main>
    <?php echo Pages::ranking(); ?>
    <aside class="kl-dynamic-sidebar">
        <ul><?php dynamic_sidebar('kkl_leagues_sidebar'); ?></ul>
        <ul><?php dynamic_sidebar('kkl_global_sidebar'); ?></ul>
    </aside>
</main>