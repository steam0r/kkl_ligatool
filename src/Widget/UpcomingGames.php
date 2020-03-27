<?php

namespace KKL\Ligatool\Widget;

use KKL\Ligatool\Plugin;
use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Template;
use WP_Widget;


class UpcomingGames extends WP_Widget {

  private $tpl;

  public function __construct() {

    $kkl_twig = Template\Service::getTemplateEngine();

    parent::__construct('KKL_Widget_UpcomingGames', // Base ID
      'KKL Upcoming Games', // Name
      array('description' => __('Shows the next games for the defined League', 'text_domain'),) // Args
    );

    $this->tpl = $kkl_twig;
  }

  public function widget($args, $instance) {

    $title = apply_filters('widget_title', $instance['title']);

    echo $args['before_widget'];
    if (!empty($title)) {
      echo $args['before_title'] . $title . $args['after_title'];
    }

    $leagueService = ServiceBroker::getLeagueService();
    $matchService = ServiceBroker::getMatchService();
    $teamService = ServiceBroker::getTeamService();
    $clubService = ServiceBroker::getClubService();

    $context = Plugin::getUrlContext();

    $league_id = $instance['league'];
    if (!$league_id) {
      $league = $leagueService->bySlug($context->getLeague()->getCode());
      if (!$league) {
        if ($context->getClub()->getShortName()) {
          $club = $clubService->byCode($context->getClub()->getShortName());
          $current_team = $teamService->getCurrentTeamForClub($club->getId());
          $data = $matchService->getMatchesForTeam($current_team->getId());
          echo $this->tpl->render('widgets/upcoming_games.twig', array('schedule' => $data, 'display_result' => true));
        } else {
          $data = $matchService->getAllUpcomingMatches();
          $matches = array();
          $leagues = array();
          foreach ($data as $match) {
            $league = $leagueService->byGameDay($match->getGameDayId());
            $leagues[$league->getId()] = $league;
            $matches[$league->getId()][] = $match;
          }
          foreach ($leagues as $league) {
            echo $this->tpl->render('widgets/upcoming_games.twig', array('schedule' => $matches[$league->getId()], 'league' => $league));
          }
        }
      } else {
        $data = $matchService->getUpcomingMatches($league->getId());
        echo $this->tpl->render('widgets/upcoming_games.twig', array('schedule' => $data));
      }
    } else {
      $data = $matchService->getUpcomingMatches($league_id);
      echo $this->tpl->render('widgets/upcoming_games.twig', array('schedule' => $data));
    }
    echo $args['after_widget'];
  }

  public
  function update($new_instance, $old_instance) {
    $instance = array();
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['league'] = strip_tags($new_instance['league']);

    return $instance;
  }

  public
  function form($instance) {

    $leagueService = ServiceBroker::getLeagueService();
    $leagues = $leagueService->getAll();

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
