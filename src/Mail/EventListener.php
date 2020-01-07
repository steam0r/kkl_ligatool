<?php

namespace KKL\Ligatool\Mail;

use KKL\Ligatool\Events;
use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Template;

class EventListener {

  public function init() {
    Events\Service::registerCallback(Events\Service::$NEW_GAMEDAY_UPCOMING, array($this, 'send_reminder_mail'));
    Events\Service::registerCallback(Events\Service::$MATCH_FIXTURE_SET, array($this, 'post_new_fixture'));
  }

  public function post_new_fixture(Events\MatchFixtureUpdatedEvent $event) {
    $match = $event->getMatch();
    $teamService = ServiceBroker::getTeamService();
    $leagueService = ServiceBroker::getLeagueService();

    $home = $teamService->byId($match->home_team);
    $away = $teamService->byId($match->away_team);
    $league = $leagueService->byGameDay($match->game_day_id);

    $time = strtotime($match->fixture);
    setlocale(LC_TIME, 'de_DE');
    $text = strftime("%d. %B %Y %H:%M:%S", $time);
    if ($match->location) {
      $locationService = ServiceBroker::getLocationService();
      $location = $locationService->byId($match->getLocation());
      if ($location) {
        $text .= ", Spielort: " . $location->getTitle();
      }
    }

    $playerService = ServiceBroker::getPlayerService();
    $actor = $playerService->byMailAddress($event->getActorEmail());
    if (!$actor) {
      $actor = $event->getActorEmail();
    } else {
      $actor = $actor->getFirstName() . " " . $actor->getLastName();
    }

    $data = array(
      "mail" => array(
        "to" => array("name" => ""),
        "actor" => array("name" => $actor)
      ),
      "match" => array("text" => $league->getName() . ': ' . $home->getName() . ' gegen ' . $away->getName(), "fixture" => $text)
    );

    $homeCaptainMail = null;
    $homeViceCaptainMail = null;
    $awayCaptainMail = null;
    $awayViceCaptainMail = null;

    $playerService = ServiceBroker::getPlayerService();

    if ($home->getProperty('captain')) {
      $captain = $playerService->byId($home->getProperty('captain')->getValue());
      if ($captain && $captain->getEmail()) {
        $homeCaptainMail = $captain->getEmail();
      }
    }
    $vice = $playerService->byId($home->getProperty('vice_captain')->getValue());
    if ($vice && $vice->getEmail()) {
      if (!$homeCaptainMail) {
        $homeCaptainMail = $vice->getEmail();
      } else {
        $homeViceCaptainMail = $vice->getEmail();
      }
    }

    if ($away->getProperty('captain')) {
      $captain = $playerService->byId($away->getProperty('captain')->getValue());
      if ($captain && $captain->getEmail()) {
        $awayCaptainMail = $captain->getEmail();
      }
      $vice = $playerService->byId($away->getProperty('vice_captain')->getValue());
      if ($vice && $vice->getEmail()) {
        if (!$awayCaptainMail) {
          $awayCaptainMail = $vice->getEmail();
        } else {
          $awayViceCaptainMail = $vice->getEmail();
        }
      }
    }

    $data['mail']['to']['name'] = 'Ligaleitung';
    $this->sendMail('ligaleitung@kickerligakoeln.de', null, $data);
    $data['mail']['to']['name'] = $home->getName();
    $this->sendMail($homeCaptainMail, $homeViceCaptainMail, $data);
    $data['mail']['to']['name'] = $away->getName();
    $this->sendMail($awayCaptainMail, $awayViceCaptainMail, $data);

  }

  public
  function send_reminder_mail(Events\GameDayReminderEvent $event) {

    $to = "Kölner Kickerliga <ligaleitung@kickerligakoeln.de>";
    $subject = "Anstehende Spiele in der Kölner Kickerliga";
    $headers = array(
      'From: Ligaleitung <ligaleitung@kickerligakoeln.de>',
      'Reply-To: ligaleitung@kickerligakoeln.de',
      'MIME-Version: 1.0',
      'Content-type: text/html; charset=utf-8'
    );

    $templateEngine = Template\Service::getTemplateEngine();
    $template = $templateEngine->loadTemplate('mails/reminder_mail.twig');
    $message = $template->render(array('matches' => $event->getMatches()));
    wp_mail($to, $subject, $message, $headers);

  }

  private
  function sendMail($to, $cc, $data) {

    $to = "stephan@5711.org";
    $cc = "scherer.benedikt@googlemail.com";

    $kkl_twig = Template\Service::getTemplateEngine();

    $to = $data['mail']['to']['name'] . "<" . $to . ">";
    $subject = '[kkl] Spieltermin ' . $data['match']['text'];
    $headers = array(
      'From: Ligaleitung <ligaleitung@kickerligakoeln.de>',
      'Reply-To: ligaleitung@kickerligakoeln.de',
      'MIME-Version: 1.0',
      'Content-type: text/html; charset=utf-8'
    );
    if ($cc != null) {
      $headers[] = 'Cc: ' . $cc;
    }

    $message = $kkl_twig->render('mails/new_fixture_mail.twig', $data);
    wp_mail($to, $subject, $message, $headers);
  }

}
