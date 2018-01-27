<?php

namespace KKL\Ligatool\Widget;

use KKL\Ligatool\DB;
use KKL\Ligatool\KKL;
use WP_Widget;

add_action('widgets_init', create_function('', 'register_widget( "KKL_Widget_UpcomingGames" );'));

class UpcomingGames extends WP_Widget {

  private $tpl;

  public function __construct() {

    global $kkl_twig;

    parent::__construct(
      'KKL_Widget_UpcomingGames', // Base ID
      'KKL Upcoming Games', // Name
      array('description' => __('Shows the next games for the defined League', 'text_domain'),) // Args
    );

    $this->tpl = $kkl_twig;
  }

  public function widget($args, $instance) {

    extract($args);
    $title = apply_filters('widget_title', $instance['title']);

    echo $before_widget;
    if (!empty($title)) echo $before_title . $title . $after_title;

    $db = new DB\Wordpress();

    $league_id = $instance['league'];
    if (!$league_id) {
      $context = KKL::getContext();
      $league_id = $context['league']->id;
      if (!$league_id) {
        $team = $context['team'];
        if ($team) {
          $current_team = $db->getCurrentTeamForClub($team->club_id);
          $data = $db->getGamesForTeam($current_team->id);
          echo $this->tpl->render('widgets/upcoming_games.twig', array('schedule' => $data, 'display_result' => true));
        } else {
          $data = $db->getAllUpcomingGames();
          $games = array();
          $leagues = array();
          foreach ($data as $game) {
            $leagues[$game->league_id] = true;
            $games[$game->league_id][] = $game;
          }
          foreach (array_keys($leagues) as $league_id) {
            $league = $db->getLeague($league_id);
            echo $this->tpl->render('widgets/upcoming_games.twig', array('schedule' => $games[$league_id], 'league' => $league));
          }
        }
      } else {
        $data = $db->getUpcomingGames($league_id);
        echo $this->tpl->render('widgets/upcoming_games.twig', array('schedule' => $data));
      }
    }

    echo $after_widget;

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
          <br/><br/>
          <label for="<?php echo $this->get_field_id('league'); ?>"><?php _e('League:'); ?></label>
          <select id="<?php echo $this->get_field_id('league'); ?>"
                  name="<?php echo $this->get_field_name('league'); ?>">
              <option value=""><?php echo __('get from context', 'kkl-ligatool'); ?></option>
            <?php
            foreach ($leagues as $l) {
              $selected = false;
              if ($l->id == $league) $selected = true;
              echo "<option value=\"$l->id\"";
              if ($selected) echo ' selected="selected"';
              echo ">";
              echo $l->name;
              echo "</option>";
            }
            ?>
          </select>
      </p>
    <?php
  }

}
