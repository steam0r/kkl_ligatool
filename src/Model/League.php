<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 17:54
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"code", "name"}, type="object")
 * @ORM_Type              Entity
 * @ORM_Table "kkl_leagues"
 * @ORM_AllowSchemaUpdate True
 */
class League extends KKLModel {
  
  /**
   * @var boolean
   * @SWG\Property()
   * @ORM_Column_Type   TINYINT
   * @ORM_Column_Null   NULL
   */
  private $active;
  
  /**
   * @var string
   * @SWG\Property(example="koeln1")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $code;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $name;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $current_season;

}
