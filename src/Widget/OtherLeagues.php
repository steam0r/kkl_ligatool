<?php

namespace KKL\Ligatool\Widget;

use KKL\Ligatool\DB;
use KKL\Ligatool\Plugin;
use KKL\Ligatool\Template;
use WP_Widget;

class OtherLeagues extends WP_Widget
{

  private $tpl;

  public function __construct()
  {

    $kkl_twig = Template\Service::getTemplateEngine();

    parent::__construct('KKL_Widget_OtherLeagues', // Base ID
      'KKL Inactive Leagues', // Name
      array('description' => __('Shows the inactive leagues', 'kkl-ligatool'),) // Args
    );

    $this->tpl = $kkl_twig;
  }

  public function widget($args, $instance)
  {

    extract($args);


    $db = new DB\Wordpress();

    $leagues = $db->getInactiveLeagues();
    foreach ($leagues as $league) {
      $season = $db->getSeason($league->current_season);
      $league->link = Plugin::getLink('league', array('league' => $league->code, 'season' => date('Y', strtotime($season->start_date))));
    }
    if (!empty($leagues)) {
      $title = apply_filters('widget_title', $instance['title']);
      echo $before_widget;
      if (!empty($title))
        echo $before_title . $title . $after_title;
      echo $this->tpl->render('widgets/other_leagues.twig', array('leagues' => $leagues));
      echo $after_widget;
    }

  }

  public function update($new_instance, $old_instance)
  {
    $instance = array();
    $instance['title'] = strip_tags($new_instance['title']);

    return $instance;
  }

  public function form($instance)
  {
    $db = new DB\Wordpress();
    $leagues = $db->getLeagues();

    if (isset($instance['title'])) {
      $title = $instance['title'];
    } else {
      $title = __('New title', 'text_domain');
    }
    ?>
      <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                 name="<?php echo $this->get_field_name('title'); ?>" type="text"
                 value="<?php echo esc_attr($title); ?>"/>
      </p>
    <?php
  }

}
