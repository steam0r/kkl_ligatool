<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 17:57
 */

namespace KKL\Ligatool\Model;

use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Services\SeasonPropertyService;

/**
 * @SWG\Definition(required={"name", "league", "startDate", "endDate"}, type="object")
 * @ORM_Type              Entity
 * @ORM_Table "kkl_seasons"
 * @ORM_AllowSchemaUpdate True
 */
class Season extends KKLPropertyModel {

  /**
   * @var boolean
   * @SWG\Property()
   * @ORM_Column_Type   TINYINT
   * @ORM_Column_Null   NULL
   */
  protected $active;

  /**
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @var string
   * @ORM_Column_Type   DATETIME
   * @ORM_Column_Null   NULL
   */
  protected $end_date;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $name;

  /**
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @var string
   * @ORM_Column_Type   DATETIME
   * @ORM_Column_Null   NULL
   */
  protected $start_date;

  /**
   * @SWG\Property(format="int64")
   * @var int
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $league_id;

  /**
   * @SWG\Property(format="int64")
   * @var int
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $current_game_day;

  /**
   * @return bool
   */
  public function isActive() {
    return boolval($this->active);
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
  public function getEndDate() {
    return $this->end_date;
  }

  /**
   * @param string $end_date
   */
  public function setEndDate($end_date) {
    $this->end_date = $end_date;
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
    return $this->start_date;
  }

  /**
   * @param string $start_date
   */
  public function setStartDate($start_date) {
    $this->start_date = $start_date;
  }

  /**
   * @return int
   */
  public function getLeagueId() {
    return $this->league_id;
  }

  /**
   * @param int $league_id
   */
  public function setLeagueId($league_id) {
    $this->league_id = $league_id;
  }

  /**
   * @return int
   */
  public function getCurrentGameDay() {
    return $this->current_game_day;
  }

  /**
   * @param int $current_game_day
   */
  public function setCurrentGameDay($current_game_day) {
    $this->current_game_day = $current_game_day;
  }

  /**
   * @return SeasonPropertyService
   */
  protected function getPropertyService() {
    return ServiceBroker::getSeasonPropertyService();
  }

  /**
   * @param string $key
   * @return SeasonProperty
   */
  public function getProperty($key) {
    return parent::getProperty($key);
  }


  /**
   * @return SeasonProperty[]
   */
  public function getProperties() {
    return parent::getProperties();
  }

}
