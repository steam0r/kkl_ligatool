<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 18:47
 */

namespace KKL\Ligatool\Model;

/**
 * @OA\Schema()
 */
class GameDay extends SwaggerModel {
  
  /**
   * @var int
   * @OA\Property()
   */
  private $seasonId;
  
  /**
   * @var int
   * @OA\Property(format="int64")
   */
  private $number;
  
  /**
   * @var string
   * @OA\Property(example="1980-09-02 05:11:42")
   */
  private $fixture;
  
  /**
   * @var string
   * @OA\Property(example="1980-09-02 05:11:42")
   */
  private $endDate;
  
  /**
   * @return int
   */
  public function getSeasonId() {
    return $this->seasonId;
  }
  
  /**
   * @param int $seasonId
   */
  public function setSeasonId($seasonId) {
    $this->seasonId = $seasonId;
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
  public function getEndDate() {
    return $this->endDate;
  }
  
  /**
   * @param string $endDate
   */
  public function setEndDate($endDate) {
    $this->endDate = $endDate;
  }
  
}
