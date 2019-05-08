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

}
