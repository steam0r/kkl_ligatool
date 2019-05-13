<?php
get_header();
$template = \KKL\Ligatool\Pages\Pages::teams();
?>

    <main>
      <?php echo $template; ?>
    </main>

<?php get_footer(); ?>