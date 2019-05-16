<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 18:47
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"propertyKey", "objectId"}, type="object")
 */
abstract class Property extends KKLModel {

  /**
   * @var string
   * @SWG\Property()
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $property_key;

  /**
   * @SWG\Property(format="int64")
   * @var int
   * @ORM_Column_Type   INT
   * @ORM_Column_Null   NULL
   */
  protected $objectId;

  /**
   * @var string
   * @SWG\Property()
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $text;

  /**
   * @var string
   * @SWG\Property()
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $value;

  /**
   * @return string
   */
  public function getPropertyKey() {
    return $this->property_key;
  }

  /**
   * @param string $property_key
   */
  public function setPropertyKey($property_key) {
    $this->property_key = $property_key;
  }

  /**
   * @return int
   */
  public function getObjectId() {
    return $this->objectId;
  }

  /**
   * @param int $objectId
   */
  public function setObjectId($objectId) {
    $this->objectId = $objectId;
  }

  /**
   * @return string
   */
  public function getText() {
    return $this->text;
  }

  /**
   * @param string $text
   */
  public function setText($text) {
    $this->text = $text;
  }

  /**
   * @return string
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * @param string $value
   */
  public function setValue($value) {
    $this->value = $value;
  }

}
