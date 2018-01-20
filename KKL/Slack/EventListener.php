<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 20.01.18
 * Time: 17:04
 */

class KKL_Slack_EventListener {

  public function init() {
    KKL_Events_Service::registerCallback(KKL_Events_Service::$MATCH_FIXTURE_SET, array($this, 'post_new_fixture'));
  }

  private function getSlack() {
    $options = get_option('kkl_ligatool');
    $slack = new Slack($options['slackid']);
    return $slack;
  }

  public function post_new_fixture(KKL_Events_MatchFixtureUpdatedEvent $event) {
    $slack = $this->getSlack();
    $db = new KKL_DB_Wordpress();
    $match = $event->getMatch();
    $home = $db->getTeam($match->home_team);
    $away = $db->getTeam($match->away_team);
    $league = $db->getLeagueForGameday($match->game_day_id);
    $attachment = array();
    $attachment['title'] = $league->name . ': ' . $home->name . ' gegen ' . $away->name;
    $time = strtotime($match->fixture);
    $text = strftime("%d. %B %Y %H:%M:%S", $time);
    if($match->location) {
      $location = $db->getLocation($match->location);
      if($location) {
        $text .= ", Spielort: " . $location->title;
      }
    }
    $attachment['text'] = $text;
    $attachment['title_link'] = get_site_url() . '/wp-admin/admin.php?page=kkl_matches_admin_page&id=' . $match->id;
    $attachment['color'] = "#FF0000";

    $slack->call('chat.postMessage', array(
      "icon_emoji" => ":robot_face:",
      "username" => "Mr. Robot",
      "channel" => "#test",
      "text" => $event->getActorEmail() . " hat gerade einen Spieltermin eingetragen",
      "attachments" => json_encode(array($attachment))
    ));
  }

}
