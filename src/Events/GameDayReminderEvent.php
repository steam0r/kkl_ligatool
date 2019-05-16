<?php

namespace KKL\Ligatool\Events;

class GameDayReminderEvent implements Event {

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
