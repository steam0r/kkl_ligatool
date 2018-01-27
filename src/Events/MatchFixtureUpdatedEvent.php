<?php
namespace KKL\Ligatool\Events;

class KKL_Events_MatchFixtureUpdatedEvent implements KKL_Event {

  private $match;
  private $actorEmail;

  /**
   * KKL_Events_MatchFixtureUpdatedEvent constructor.
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
