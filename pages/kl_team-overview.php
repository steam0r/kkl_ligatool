<?php
get_header();
$template = \KKL\Ligatool\Pages\Pages::teamOverview();
?>

<main>
  <?php echo $template; ?>
</main>

<?php get_footer(); ?>