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
 * @ORM_Table "kkl_games"
 * @ORM_AllowSchemaUpdate True
 */
class Game extends KKLModel {

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null    NULL
   */
  protected $goals_away;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $goals_home;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $number;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $set_id;

  /**
   * @return int
   */
  public function getGoalsAway() {
    return $this->goals_away;
  }

  /**
   * @param int $goals_away
   */
  public function setGoalsAway($goals_away) {
    $this->goals_away = $goals_away;
  }

  /**
   * @return int
   */
  public function getGoalsHome() {
    return $this->goals_home;
  }

  /**
   * @param int $goals_home
   */
  public function setGoalsHome($goals_home) {
    $this->goals_home = $goals_home;
  }

  /**
   * @return int
   */
  public function getNumber() {
    return $this->number;
  }

  /**
   * @param int $number
   */
  public function setNumber($number) {
    $this->number = $number;
  }

  /**
   * @return int
   */
  public function getSetId() {
    return $this->set_id;
  }

  /**
   * @param int $set_id
   */
  public function setSetId($set_id) {
    $this->set_id = $set_id;
  }


}
