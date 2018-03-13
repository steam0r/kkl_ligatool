<?php

namespace KKL\Ligatool\Events;

class MatchFixtureUpdatedEvent implements Event {
  
  private $match;
  private $actorEmail;
  
  /**
   * KKL_Events_MatchFixtureUpdatedEvent constructor.
   * @param $match
   * @param $actorEmail
   */
  public function __construct($match, $actorEmail) {
    $this->match = $match;
    $this->actorEmail = $actorEmail;
  }
  
  /**
   * @return mixed
   */
  public function getMatch() {
    return $this->match;
  }
  
  /**
   * @return mixed
   */
  public function getActorEmail() {
    return $this->actorEmail;
  }
  
}
