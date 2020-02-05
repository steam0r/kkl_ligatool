<?php


namespace KKL\Ligatool\Model;


class Ranking {

  /**
   * @var League
   */
  private $league;

  /**
   * @var Season
   */
  private $season;

  /**
   * @var GameDay
   */
  private $gameDay;

  /**
   * @var Rank[]
   */
  private $ranks = array();

  /**
   * @return League
   */
  public function getLeague() {
    return $this->league;
  }

  /**
   * @param League $league
   */
  public function setLeague($league) {
    $this->league = $league;
  }

  /**
   * @return Season
   */
  public function getSeason() {
    return $this->season;
  }

  /**
   * @param Season $season
   */
  public function setSeason($season) {
    $this->season = $season;
  }

  /**
   * @return GameDay
   */
  public function getGameDay() {
    return $this->gameDay;
  }

  /**
   * @param GameDay $gameDay
   */
  public function setGameDay($gameDay) {
    $this->gameDay = $gameDay;
  }

  /**
   * @return Rank[]
   */
  public function getRanks() {
    return $this->ranks;
  }

  /**
   * @param Rank[] $ranks
   */
  public function setRanks($ranks) {
    $this->ranks = $ranks;
  }

}