<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 18:47
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"fixture", "seasonId"}, type="object")
 * @ORM_Type              Entity
 * @ORM_Table "kkl_game_days"
 * @ORM_AllowSchemaUpdate True
 */
class GameDay extends KKLModel {

  /**
   * @var int
   * @SWG\Property()
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $season_id;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $number;

  /**
   * @var string
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @ORM_Column_Type   DATETIME
   * @ORM_Column_Null   NULL
   */
  protected $fixture;

  /**
   * @var string
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @ORM_Column_Type   DATETIME
   * @ORM_Column_Null   NULL
   */
  protected $end;

  /**
   * @return int
   */
  public function getSeasonId() {
    return $this->season_id;
  }

  /**
   * @param int $season_id
   */
  public function setSeasonId($season_id) {
    $this->season_id = $season_id;
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
   * @return string
   */
  public function getEnd() {
    return $this->end;
  }

  /**
   * @param string $end
   */
  public function setEnd($end) {
    $this->end = $end;
  }

  /**
   * @return string
   */
  public function getFixture() {
    return $this->fixture;
  }

  /**
   * @param string $fixture
   */
  public function setFixture($fixture) {
    $this->fixture = $fixture;
  }

  /**
   * @return string
   */
  public function getStart() {
    return $this->fixture;
  }

  /**
   * @param string $fixture
   */
  public function setStart($fixture) {
    $this->fixture = $fixture;
  }


}
