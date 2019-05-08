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
  private $title;
  
  /**
   * @var string
   * @SWG\Property()
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $description;
  
  /**
   * @var string
   * @SWG\Property()
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $lat;
  
  /**
   * @var string
   * @SWG\Property()
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $lng;

}
