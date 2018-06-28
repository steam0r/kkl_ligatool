<?php

namespace KKL\Ligatool\Api;

use Eluceo\iCal\Property\Event\Geo;
use KKL\Ligatool\DB;

class ICal extends CalendarController {
  
  public function registerRoutes() {
    // no normal routes here, just a calendar at /calendar
  }
  
  public function getBaseName() {
      return 'calendar';
  }
  
  protected function getEvents() {
    
    $events = array();
    $db = new DB\Api();
    foreach($db->getActiveLeagues() as $league) {
      $season = $db->getCurrentSeason($league->id);
      foreach($db->getGameDaysForSeason($season->id) as $gameday) {
        $event = new \Eluceo\iCal\Component\Event();
      
        $dtStart = new \DateTime($gameday->fixture);
        $dtStart->setTimezone(new \DateTimeZone('Europe/Berlin'));
      
        $dtEnd = new \DateTime($gameday->end);
        $dtEnd->setTimezone(new \DateTimeZone('Europe/Berlin'));
      
        $event->setDtStart($dtStart)
          ->setDtEnd($dtEnd)
          ->setSummary($league->name . ' - Spieltag ' . $gameday->number)
          ->setUseTimezone(true)
          ->setNoTime(true);
        $events[] = $event;
        
        foreach($db->getMatchesByGameDay($gameday->id) as $match) {
          if($match->fixture != null) {
            $teamHome = $db->getTeam($match->home_team);
            $teamAway = $db->getTeam($match->away_team);
  
            $dtStart = new \DateTime($match->fixture);
            $dtStart->setTimezone(new \DateTimeZone('Europe/Berlin'));
  
            $interval = date_interval_create_from_date_string('3 hour');
            $dtEnd = new \DateTime($match->fixture);
            $dtEnd = $dtEnd->add($interval);
            $dtEnd->setTimezone(new \DateTimeZone('Europe/Berlin'));
            
            $event = $event->setDtStart($dtStart)
              ->setDtEnd($dtEnd)
              ->setSummary($teamHome->name . ' vs. ' . $teamAway->name)
              ->setUseTimezone(true)
              ->setNoTime(false);
            
            if(is_numeric($match->location)) {
              $location = $db->getLocation($match->location);
              $geo = new Geo($location->lat, $location->lng);
              $event->setLocation($location->title, $location->description, $geo);
            }
            $events[] = $event;
          }
        }
        
      }
    }
    return $events;
  }
  
}
