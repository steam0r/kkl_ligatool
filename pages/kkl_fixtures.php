<?php
global $post;
get_header();

$template = \KKL\Ligatool\Pages\Pages::fixtures();
?>

<main>
  <?php echo $template; ?>
    <aside class="kl-dynamic-sidebar">
        <ul><?php dynamic_sidebar('kkl_leagues_sidebar'); ?></ul>
        <ul><?php dynamic_sidebar('kkl_global_sidebar'); ?></ul>
    </aside>
</main>