<?php
global $post;
get_header();

if(!post_password_required($post)) {
  echo \KKL\Ligatool\Pages::contactList();
} else {
  echo get_the_password_form();
}
?>

<?php get_footer(); ?>