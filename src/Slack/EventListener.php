<?php

namespace KKL\Ligatool\Slack;

use KKL\Ligatool\DB;
use KKL\Ligatool\Events;
use tigokr\Slack\Slack;

class EventListener {
  
  private static $TEST_CHANNEL = '#test';
  private static $LEAGUE_CHANNEL = '#spielbetrieb';
  
  public function init() {
    Events\Service::registerCallback(Events\Service::$MATCH_FIXTURE_SET, array($this, 'post_new_fixture'));
    Events\Service::registerCallback(Events\Service::$NEW_GAMEDAY_UPCOMING, array($this, 'post_new_upcoming_gameday'));
    
  }
  
  public function post_new_fixture(Events\MatchFixtureUpdatedEvent $event) {
    $slack = $this->getSlack();
    $db = new DB\Wordpress();
    $match = $event->getMatch();
    $home = $db->getTeam($match->home_team);
    $away = $db->getTeam($match->away_team);
    $league = $db->getLeagueForGameday($match->game_day_id);
    $attachment = array();
    $attachment['title'] = $league->name . ': ' . $home->name . ' gegen ' . $away->name;
    $time = strtotime($match->fixture);
    setlocale(LC_TIME, 'de_DE');
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
    
    $slack->call('chat.postMessage', array("icon_emoji" => ":robot_face:", "username" => "Mr. Robot", "channel" => static::$TEST_CHANNEL, "text" => $event->getActorEmail() . " hat gerade einen Spieltermin eingetragen", "attachments" => json_encode(array($attachment))));
  }
  
  private function getSlack() {
    $options = get_option('kkl_ligatool');
    $slack = new Slack($options['slackid']);
    return $slack;
  }
  
  public function post_new_gameday(Events\GameDayReminderEvent $event) {
    
    $topMatches = $event->getTopMatches();
    $attachments = array();
    $attachment['title'] = "@benedikt: Zeit die Erinnerungsmail zu verschicken!";
    $attachment['text'] = "Mit Klick auf den Link, kommst du zu der vorformatierten Mail.";
    $attachment['title_link'] = "https://www.kickerligakoeln.de/wp-kkl/reminder.php?offset=" . $event->getOffset() . "&echo=true";
    $attachment['color'] = "#FF0000";
    $attachments[] = $attachment;
    foreach($topMatches as $leagueName => $topMatch) {
      $a = array();
      $title = "@" . $topMatch['contact'] . ": ";
      if($topMatch['type'] == "top") {
        $title .= "Topspiel";
        $a['color'] = "#88AF7C";
      } else {
        $title .= "Abstiegskampf";
        $a['color'] = "#AB4D47";
      }
      $title .= " in " . $leagueName;
      $a['title'] = $title;
      $a['title_link'] = "https://www.kickerligakoeln.de/tabelle/" . $topMatch['leaguecode'] . "/";
      $a['text'] = $topMatch['home'] . " gegen " . $topMatch['away'];
      $attachments[] = $a;
    }
    $slack = $this->getSlack();
    $slack->call('chat.postMessage', array("icon_emoji" => ":robot_face:", "username" => "Mr. Robot", "channel" => static::$TEST_CHANNEL, "text" => "Ein neuer Spieltag steht an:", "attachments" => json_encode($attachments)));
    
  }
  
}
