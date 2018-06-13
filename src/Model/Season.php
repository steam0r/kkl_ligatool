<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 17:57
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"name", "league", "startDate", "endDate"}, type="object")
 */
class Season extends BaseModel {
  
  /**
   * @var boolean
   * @SWG\Property()
   */
  private $active;
  
  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   */
  private $name;
  
  /**
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @var string
   */
  private $startDate;
  
  /**
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @var string
   */
  private $endDate;
  
  /**
   * @SWG\Property(format="int64")
   * @var int
   */
  private $league;
  
  /**
   * @SWG\Property(format="int64")
   * @var int
   */
  private $currentGameDay;
  
  /**
   * @return bool
   */
  public function isActive() {
    return $this->active;
  }
  
  /**
   * @param bool $active
   */
  public function setActive($active) {
    $this->active = $active;
  }
  
  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }
  
  /**
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }
  
  /**
   * @return string
   */
  public function getStartDate() {
    return $this->startDate;
  }
  
  /**
   * @param string $startDate
   */
  public function setStartDate($startDate) {
    $this->startDate = $startDate;
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
  
  /**
   * @return int
   */
  public function getLeague() {
    return $this->league;
  }
  
  /**
   * @param int $league
   */
  public function setLeague($league) {
    $this->league = $league;
  }
  
  /**
   * @return int
   */
  public function getCurrentGameDay() {
    return $this->currentGameDay;
  }
  
  /**
   * @param int $currentGameDay
   */
  public function setCurrentGameDay($currentGameDay) {
    $this->currentGameDay = $currentGameDay;
  }
  
}
