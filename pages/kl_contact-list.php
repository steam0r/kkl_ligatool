<?php
global $post;
get_header();

$template = \KKL\Ligatool\Pages\Pages::contactList();

if(!post_password_required($post)) { ?>
    <main>
      <?php echo $template; ?>
        <aside class="kl-dynamic-sidebar">
            <ul><?php dynamic_sidebar('kkl_schedule_sidebar'); ?></ul>
        </aside>
    </main>
<?php } else { ?>
  <?php get_the_password_form(); ?>
<?php } ?>

<?php get_footer(); ?>