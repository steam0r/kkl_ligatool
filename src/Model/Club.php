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
 * @ORM_Table "kkl_clubs"
 * @ORM_AllowSchemaUpdate True
 */
class Club extends KKLModel {

  /**
   * @var string
   * @SWG\Property(example="Rakete Kalk")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $name;

  /**
   * @var string
   * @SWG\Property(example="Beste")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $description;

  /**
   * @var string
   * @SWG\Property(example="https://upload.wikimedia.org/wikipedia/commons/2/27/Pershing-2_two_stage_version.jpg")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $logo;

  /**
   * @var string
   * @SWG\Property(example="de")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $country_code;

  /**
   * @var string
   * @SWG\Property(example="rakete")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $short_name;
  
  
}
