<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 17:54
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"code", "name"}, type="object")
 * @ORM_Type              Entity
 * @ORM_Table "kkl_team_scores"
 * @ORM_AllowSchemaUpdate True
 */
class TeamScore extends KKLModel {

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $draw;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $gamesAgainst;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $gamesFor;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $goalsAgainst;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $goalsFor;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $loss;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $position;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $score;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $win;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $gameDay_id;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $team_id;

  /**
   * @var bool
   */
  protected $final = false;

  /**
   * @return int
   */
  public function getDraw() {
    return $this->draw;
  }

  /**
   * @param int $draw
   */
  public function setDraw($draw) {
    $this->draw = $draw;
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
  public function getLoss() {
    return $this->loss;
  }

  /**
   * @param int $loss
   */
  public function setLoss($loss) {
    $this->loss = $loss;
  }

  /**
   * @return int
   */
  public function getPosition() {
    return $this->position;
  }

  /**
   * @param int $position
   */
  public function setPosition($position) {
    $this->position = $position;
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
  public function getWin() {
    return $this->win;
  }

  /**
   * @param int $win
   */
  public function setWin($win) {
    $this->win = $win;
  }

  /**
   * @return int
   */
  public function getGameDayId() {
    return $this->gameDay_id;
  }

  /**
   * @param int $gameDay_id
   */
  public function setGameDayId($gameDay_id) {
    $this->gameDay_id = $gameDay_id;
  }

  /**
   * @return int
   */
  public function getTeamId() {
    return $this->team_id;
  }

  /**
   * @param int $team_id
   */
  public function setTeamId($team_id) {
    $this->team_id = $team_id;
  }

  /**
   * TODO: what does this do?
   *
   * @return bool
   */
  public function isFinal() {
    return $this->final;
  }

  /**
   * TODO: what does this do?
   *
   * @param bool $final
   */
  public function setFinal($final) {
    $this->final = $final;
  }

}
