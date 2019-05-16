<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 18:47
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"title"}, type="object")
 * @ORM_Type              Entity
 * @ORM_Table "kkl_locations"
 * @ORM_AllowSchemaUpdate True
 */
class Location extends KKLModel {

  /**
   * @var string
   * @SWG\Property()
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $title;

  /**
   * @var string
   * @SWG\Property()
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $description;

  /**
   * @var string
   * @SWG\Property()
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $lat;

  /**
   * @var string
   * @SWG\Property()
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $lng;

  /**
   * @return string
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * @param string $title
   */
  public function setTitle($title) {
    $this->title = $title;
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
  public function getLatitude() {
    return $this->lat;
  }

  /**
   * @param string $lat
   */
  public function setLatitude($lat) {
    $this->lat = $lat;
  }

  /**
   * @return string
   */
  public function getLongitude() {
    return $this->lng;
  }

  /**
   * @param string $lng
   */
  public function setLongitude($lng) {
    $this->lng = $lng;
  }

}
