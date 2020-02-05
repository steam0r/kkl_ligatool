<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 17:54
 */

namespace KKL\Ligatool\Model;

use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Services\ClubPropertyService;

/**
 * @SWG\Definition(required={"code", "name"}, type="object")
 * @ORM_Type              Entity
 * @ORM_Table "kkl_clubs"
 * @ORM_AllowSchemaUpdate True
 */
class Club extends KKLPropertyModel {

  /**
   * @var string
   * @SWG\Property(example="Rakete Kalk")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $name;

  /**
   * @var string
   * @SWG\Property(example="Beste")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $description;

  /**
   * @var string
   * @SWG\Property(example="https://upload.wikimedia.org/wikipedia/commons/2/27/Pershing-2_two_stage_version.jpg")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $logo;

  /**
   * @var string
   * @SWG\Property(example="de")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $country_code;

  /**
   * @var string
   * @SWG\Property(example="rakete")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $short_name;

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
  public function getShortName() {
    return $this->short_name;
  }

  /**
   * @param string $short_name
   */
  public function setShortName($short_name) {
    $this->short_name = $short_name;
  }


  /**
   * @return ClubPropertyService
   */
  protected function getPropertyService() {
    return ServiceBroker::getClubPropertyService();
  }
}
