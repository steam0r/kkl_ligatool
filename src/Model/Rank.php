<?php


namespace KKL\Ligatool\Model;


class Rank {

  private $team_id;
  private $running = true;
  private $score = 0;
  private $wins = 0;
  private $losses = 0;
  private $draws = 0;
  private $goalsFor = 0;
  private $goalsAgainst = 0;
  private $goalDiff = 0;
  private $gamesFor = 0;
  private $gamesAgainst = 0;
  private $gameDiff = 0;

  /**
   * @return mixed
   */
  public function getTeamId() {
    return $this->team_id;
  }

  /**
   * @param mixed $team_id
   */
  public function setTeamId($team_id) {
    $this->team_id = $team_id;
  }

  /**
   * @return bool
   */
  public function isRunning() {
    return $this->running;
  }

  /**
   * @param bool $running
   */
  public function setRunning($running) {
    $this->running = $running;
  }

  /**
   * @return int
   */
  public function getScore() {
    return $this->score;
  }

  /**
   * @param int $score
   */
  public function setScore($score) {
    $this->score = $score;
  }

  /**
   * @return int
   */
  public function getWins() {
    return $this->wins;
  }

  /**
   * @param int $wins
   */
  public function setWins($wins) {
    $this->wins = $wins;
  }

  /**
   * @return int
   */
  public function getLosses() {
    return $this->losses;
  }

  /**
   * @param int $losses
   */
  public function setLosses($losses) {
    $this->losses = $losses;
  }

  /**
   * @return int
   */
  public function getDraws() {
    return $this->draws;
  }

  /**
   * @param int $draws
   */
  public function setDraws($draws) {
    $this->draws = $draws;
  }

  /**
   * @return int
   */
  public function getGoalsFor() {
    return $this->goalsFor;
  }

  /**
   * @param int $goalsFor
   */
  public function setGoalsFor($goalsFor) {
    $this->goalsFor = $goalsFor;
  }

  /**
   * @return int
   */
  public function getGoalsAgainst() {
    return $this->goalsAgainst;
  }

  /**
   * @param int $goalsAgainst
   */
  public function setGoalsAgainst($goalsAgainst) {
    $this->goalsAgainst = $goalsAgainst;
  }

  /**
   * @return int
   */
  public function getGoalDiff() {
    return $this->goalDiff;
  }

  /**
   * @param int $goalDiff
   */
  public function setGoalDiff($goalDiff) {
    $this->goalDiff = $goalDiff;
  }

  /**
   * @return int
   */
  public function getGamesFor() {
    return $this->gamesFor;
  }

  /**
   * @param int $gamesFor
   */
  public function setGamesFor($gamesFor) {
    $this->gamesFor = $gamesFor;
  }

  /**
   * @return int
   */
  public function getGamesAgainst() {
    return $this->gamesAgainst;
  }

  /**
   * @param int $gamesAgainst
   */
  public function setGamesAgainst($gamesAgainst) {
    $this->gamesAgainst = $gamesAgainst;
  }

  /**
   * @return int
   */
  public function getGameDiff() {
    return $this->gameDiff;
  }

  /**
   * @param int $gameDiff
   */
  public function setGameDiff($gameDiff) {
    $this->gameDiff = $gameDiff;
  }

}