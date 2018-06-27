<?php

namespace KKL\Ligatool\Api;

use KKL\Ligatool\DB;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class iCal extends Controller {

    public function register_routes() {
        register_rest_route($this->getNamespace(), '/' . $this->getBaseName(), array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array(
                        $this,
                        'calendar_init'
                ),
                'args' => array(),
        ));
    }

    public function getBaseName() {
        return 'calendar';
    }


    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function calendar_init(WP_REST_Request $request) {
        $calendar = $this->create_calendar();

        $response = new WP_REST_Response($calendar, 200);
        $response->header('Content-Type', 'text/calendar; charset=utf-8');

        return $response;
    }


    /**
     * @return string
     */
    private function create_calendar() {
        $db = new DB\Wordpress();

        // $allLeagues = $db->getLeagues();
        $currentSeasonForLeague = $db->getCurrentSeason(2); // koeln1
        $gamedaysForCurrentLeagueAndSeason = $db->getGameDaysForSeason($currentSeasonForLeague->id);

        define('DATE_ICAL', 'Ymd\THis\Z');
        $output =
"BEGIN:VCALENDAR
METHOD:PUBLISH
VERSION:2.0
PRODID:-//Kölner Kickerliga//Spieltermine//DE\n";

        foreach($gamedaysForCurrentLeagueAndSeason as $gameday):
            $output .=
"BEGIN:VEVENT
SUMMARY:" . $gameday->number . ". Spieltag
UID:" . $gameday->id . "
STATUS:CONFIRMED
DTSTAMP:" . date(DATE_ICAL, strtotime($gameday->fixture)) . "
DTSTART:" . date(DATE_ICAL, strtotime($gameday->fixture)) . "
DTEND:" . date(DATE_ICAL, strtotime($gameday->end)) . "
LAST-MODIFIED:" . date(DATE_ICAL, strtotime($gameday->updated_at)) . "
END:VEVENT\n";
        endforeach;

        // close calendar
        $output .= "END:VCALENDAR";

        return $output;
    }
}