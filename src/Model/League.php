<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 17:54
 */

namespace KKL\Ligatool\Model;

use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Services\LeaguePropertyService;

/**
 * @SWG\Definition(required={"code", "name"}, type="object")
 * @ORM_Type              Entity
 * @ORM_Table "kkl_leagues"
 * @ORM_AllowSchemaUpdate True
 */
class League extends KKLPropertyModel {

  /**
   * @var boolean
   * @SWG\Property()
   * @ORM_Column_Type   TINYINT
   * @ORM_Column_Null   NULL
   */
  protected $active;

  /**
   * @var string
   * @SWG\Property(example="koeln1")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $code;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $name;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $current_season;

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
  public function getCode() {
    return $this->code;
  }

  /**
   * @param string $code
   */
  public function setCode($code) {
    $this->code = $code;
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
   * @return int
   */
  public function getCurrentSeason() {
    return $this->current_season;
  }

  /**
   * @param int $current_season
   */
  public function setCurrentSeason($current_season) {
    $this->current_season = $current_season;
  }

  /**
   * @return LeaguePropertyService
   */
  protected function getPropertyService() {
    return ServiceBroker::getLeaguePropertyService();
  }
}
