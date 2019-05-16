<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 18:47
 */

namespace KKL\Ligatool\Model;

use KKL\Ligatool\ServiceBroker;

/**
 * @SWG\Definition(required={"firstName"}, type="object")
 * @ORM_Type              Entity
 * @ORM_Table "kkl_players"
 * @ORM_AllowSchemaUpdate True
 */
class Player extends KKLPropertyModel {

  /**
   * @var string
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @ORM_Column_Type   DATETIME
   * @ORM_Column_Null   NULL
   */
  protected $birthdate;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $country_code;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $description;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $draws;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $first_name;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $last_name;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $email;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $phone;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $logo;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $losses;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $nick_name;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $status;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $wins;

  /**
   * @return string
   */
  public function getBirthdate() {
    return $this->birthdate;
  }

  /**
   * @param string $birthdate
   */
  public function setBirthdate($birthdate) {
    $this->birthdate = $birthdate;
  }

  /**
   * @return string
   */
  public function getCountryCode() {
    return $this->country_code;
  }

  /**
   * @param string $country_code
   */
  public function setCountryCode($country_code) {
    $this->country_code = $country_code;
  }

  /**
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * @param string $description
   */
  public function setDescription($description) {
    $this->description = $description;
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
   * @return string
   */
  public function getFirstName() {
    return $this->first_name;
  }

  /**
   * @param string $first_name
   */
  public function setFirstName($first_name) {
    $this->first_name = $first_name;
  }

  /**
   * @return string
   */
  public function getLastName() {
    return $this->last_name;
  }

  /**
   * @param string $last_name
   */
  public function setLastName($last_name) {
    $this->last_name = $last_name;
  }

  /**
   * @return string
   */
  public function getEmail() {
    return $this->email;
  }

  /**
   * @param string $email
   */
  public function setEmail($email) {
    $this->email = $email;
  }

  /**
   * @return string
   */
  public function getPhone() {
    return $this->phone;
  }

  /**
   * @param string $phone
   */
  public function setPhone($phone) {
    $this->phone = $phone;
  }

  /**
   * @return string
   */
  public function getLogo() {
    return $this->logo;
  }

  /**
   * @param string $logo
   */
  public function setLogo($logo) {
    $this->logo = $logo;
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
   * @return string
   */
  public function getNickName() {
    return $this->nick_name;
  }

  /**
   * @param string $nick_name
   */
  public function setNickName($nick_name) {
    $this->nick_name = $nick_name;
  }

  /**
   * @return string
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * @param string $status
   */
  public function setStatus($status) {
    $this->status = $status;
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
   * @return KKLModelService
   */
  protected function getPropertyService() {
    return ServiceBroker::getPlayerPropertyService();
  }
}
