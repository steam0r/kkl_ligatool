<?php

namespace KKL\Ligatool\Widget;

use KKL\Ligatool\DB;
use KKL\Ligatool\Plugin;
use KKL\Ligatool\Template;
use WP_Widget;

add_action('widgets_init', create_function('', 'register_widget( "KKL_Widget_OtherSeasons" );'));

class OtherSeasons extends WP_Widget {

  private $tpl;

  public function __construct() {

    $kkl_twig = Template\Service::getTemplateEngine();

    parent::__construct('KKL_Widget_OtherSeasons', // Base ID
      'KKL Other Seasons', // Name
      array('description' => __('Shows the other seasons games for the defined League', 'kkl-ligatool'),) // Args
    );

    $this->tpl = $kkl_twig;
  }

  public function widget($args, $instance) {

    extract($args);


    $db = new DB\Wordpress();

    $league_id = $instance['league'];
    if (!$league_id) {
      $context = Plugin::getContext();
      $league = $context['league'];
    } else {
      $league = $db->getLeague($league_id);
    }

    $seasons = $db->getSeasonsByLeague($league->getId());
    foreach ($seasons as $season) {
      $season->link = Plugin::getLink('league', array('league' => $league->getCode(), 'season' => date('Y', strtotime($season->getStartDate()))));
    }
    if (!empty($seasons)) {
      $title = apply_filters('widget_title', $instance['title']);

      echo $before_widget;
      if (!empty($title))
        echo $before_title . $title . $after_title;
      echo $this->tpl->render('widgets/other_seasons.twig', array('seasons' => $seasons));
      echo $after_widget;
    }

  }

  public function update($new_instance, $old_instance) {
    $instance = array();
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['league'] = strip_tags($new_instance['league']);

    return $instance;
  }

  public function form($instance) {
    $db = new DB\Wordpress();
    $leagues = $db->getLeagues();

    if (isset($instance['title'])) {
      $title = $instance['title'];
    } else {
      $title = __('New title', 'text_domain');
    }
    if (isset($instance['league'])) {
      $league = $instance['league'];
    } else {
      $league = __('Set League', 'text_domain');
    }
    ?>
      <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                 name="<?php echo $this->get_field_name('title'); ?>" type="text"
                 value="<?php echo esc_attr($title); ?>"/>
          <br/><br/> <label for="<?php echo $this->get_field_id('league'); ?>"><?php _e('League:'); ?></label>
          <select id="<?php echo $this->get_field_id('league'); ?>"
                  name="<?php echo $this->get_field_name('league'); ?>">
              <option value=""><?php echo __('get from context', 'kkl-ligatool'); ?></option>
            <?php
            foreach ($leagues as $l) {
              $selected = false;
              if ($l->getId() == $league)
                $selected = true;
              echo '<option value="' . $l->getId() . '"';
              if ($selected)
                echo ' selected="selected"';
              echo ">";
              echo $l->getName();
              echo "</option>";
            }
            ?>
          </select>
      </p>
    <?php
  }

}
