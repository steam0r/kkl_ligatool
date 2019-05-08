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
 * @ORM_Table "kkl_awards"
 * @ORM_AllowSchemaUpdate True
 */
class Award extends KKLModel {

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $name;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $description;

  /**
   * @var string
   * @SWG\Property(example="https://upload.wikimedia.org/wikipedia/de/thumb/b/be/Viktoria_Pokal.jpg/220px-Viktoria_Pokal.jpg")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $logo;
  
  
}
