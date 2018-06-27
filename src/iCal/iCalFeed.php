<?php

namespace KKL\Ligatool\iCal;

use KKL\Ligatool\DB;

class iCalFeed {

    public function init() {

        if(strpos($_SERVER['REQUEST_URI'], 'ical') !== false) {
            $this->createCalendar();
            die();
        }

    }


    private function createCalendar() {
        $db = new DB\Wordpress();

        // $allLeagues = $db->getLeagues();
        $currentSeasonForLeague = $db->getCurrentSeason(1); // koeln1
        $gamedaysForCurrentLeagueAndSeason = $db->getGameDaysForSeason($currentSeasonForLeague->id);

        define('DATE_ICAL', 'Ymd\THis\Z');
        $output = "BEGIN:VCALENDAR<br>
            METHOD:PUBLISH<br>
            VERSION:2.0<br>
            PRODID:-//Kölner Kickerliga//Spieltermine//DE<br>";

        foreach($gamedaysForCurrentLeagueAndSeason as $gameday):
            $output .= "BEGIN:VEVENT<br>
                SUMMARY:" . $gameday->number . ". Spieltag<br>
                UID:" . $gameday->id . "<br>
                STATUS:CONFIRMED<br>
                DTSTAMP:" . date(DATE_ICAL, strtotime($gameday->fixture)) . "<br>
                DTSTART:" . date(DATE_ICAL, strtotime($gameday->fixture)) . "<br>
                DTEND:" . date(DATE_ICAL, strtotime($gameday->end)) . "<br>
                LAST-MODIFIED:" . date(DATE_ICAL, strtotime($gameday->updated_at)) . "<br>
                END:VEVENT<br>";
        endforeach;

        // close calendar
        $output .= "END:VCALENDAR";

        echo $output;
    }
}