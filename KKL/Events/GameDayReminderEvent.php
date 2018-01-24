<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 20.01.18
 * Time: 16:46
 */

class KKL_Events_GameDayReminderEvent implements KKL_Event {

  /**
   * @var array
   */
  private $matches = array();
  /**
   * @var array
   */
  private $topMatches;
  private $offset = 6;

  /**
   * KKL_Events_GameDayReminderEvent constructor.
   * @param array $matches
   * @param array $topMatches
   * @param int $offset
   */
  public function __construct(array $matches, array $topMatches, $offset) {
    $this->matches = $matches;
    $this->offset = $offset;
    $this->topMatches = $topMatches;
  }

  /**
   * @return array
   */
  public function getMatches() {
    return $this->matches;
  }

  /**
   * @return array
   */
  public function getTopMatches() {
    return $this->topMatches;
  }

  /**
   * @return int
   */
  public function getOffset() {
    return $this->offset;
  }


}
