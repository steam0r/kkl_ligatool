<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 28.06.18
 * Time: 15:43
 */

namespace KKL\Ligatool\Api;

use WP_REST_Request;
use WP_REST_Server;

abstract class CalendarController extends Controller {
  
  /**
   * @return \Eluceo\iCal\Component\Event[]
   */
  protected abstract function getEvents();
  
  public function init() {
    $this->registerRoutes();
    register_rest_route($this->getNamespace(), '/' . $this->getBaseName(), array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => array(
        $this,
        'calendar_init'
      ),
      'args' => array(),
    ));
    $this->registerAdditionalCalendarRoutes();
  }
  
  /**
   * @param WP_REST_Request $request
   * @return void
   */
  public function calendar_init(WP_REST_Request $request) {
    $calendar = $this->getCalendarWithEvents($this->getEvents());
    $this->outputCalendar($calendar);
  }
  
  /**
   * @param $events \Eluceo\iCal\Component\Event[]
   */
  protected function getCalendarWithEvents($events) {
  
    $calendar = new \Eluceo\iCal\Component\Calendar(get_site_url());
    foreach($events as $event) {
      $calendar->addComponent($event);
    }
    $calendar->setTimezone($this->getTimeZoneDefintionsEuropeBerlin());
    return $calendar;
    
  }
  
  /**
   * @param $calendar \Eluceo\iCal\Component\Calendar
   */
  protected function outputCalendar($calendar) {
    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="cal.ics"');
    print $calendar->render();
    die();
  }
  
  protected function getTimeZoneDefintionsEuropeBerlin() {
    
    $tz  = 'Europe/Berlin';
    $dtz = new \DateTimeZone($tz);
    date_default_timezone_set($tz);
    
    // 2. Create timezone rule object for Daylight Saving Time
    $vTimezoneRuleDst = new \Eluceo\iCal\Component\TimezoneRule(\Eluceo\iCal\Component\TimezoneRule::TYPE_DAYLIGHT);
    $vTimezoneRuleDst->setTzName('CEST');
    $vTimezoneRuleDst->setDtStart(new \DateTime('1981-03-29 02:00:00', $dtz));
    $vTimezoneRuleDst->setTzOffsetFrom('+0100');
    $vTimezoneRuleDst->setTzOffsetTo('+0200');
    $dstRecurrenceRule = new \Eluceo\iCal\Property\Event\RecurrenceRule();
    $dstRecurrenceRule->setFreq(\Eluceo\iCal\Property\Event\RecurrenceRule::FREQ_YEARLY);
    $dstRecurrenceRule->setByMonth(3);
    $dstRecurrenceRule->setByDay('-1SU');
    $vTimezoneRuleDst->setRecurrenceRule($dstRecurrenceRule);
    // 3. Create timezone rule object for Standard Time
    $vTimezoneRuleStd = new \Eluceo\iCal\Component\TimezoneRule(\Eluceo\iCal\Component\TimezoneRule::TYPE_STANDARD);
    $vTimezoneRuleStd->setTzName('CET');
    $vTimezoneRuleStd->setDtStart(new \DateTime('1996-10-27 03:00:00', $dtz));
    $vTimezoneRuleStd->setTzOffsetFrom('+0200');
    $vTimezoneRuleStd->setTzOffsetTo('+0100');
    $stdRecurrenceRule = new \Eluceo\iCal\Property\Event\RecurrenceRule();
    $stdRecurrenceRule->setFreq(\Eluceo\iCal\Property\Event\RecurrenceRule::FREQ_YEARLY);
    $stdRecurrenceRule->setByMonth(10);
    $stdRecurrenceRule->setByDay('-1SU');
    $vTimezoneRuleStd->setRecurrenceRule($stdRecurrenceRule);
    // 4. Create timezone definition and add rules
    $vTimezone = new \Eluceo\iCal\Component\Timezone($tz);
    $vTimezone->addComponent($vTimezoneRuleDst);
    $vTimezone->addComponent($vTimezoneRuleStd);
    
    return $vTimezone;
    
  }
  
  /**
   * can be overridden in subclass to add more routes for calendars
   *
   * @return void
   */
  protected function registerAdditionalCalendarRoutes() {
  }
  
}
