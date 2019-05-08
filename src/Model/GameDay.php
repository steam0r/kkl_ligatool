<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 18:47
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"fixture", "seasonId"}, type="object")
 * @ORM_Type              Entity
 * @ORM_Table "kkl_game_days"
 * @ORM_AllowSchemaUpdate True
 */
class GameDay extends KKLModel {
  
  /**
   * @var int
   * @SWG\Property()
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $season_id;
  
  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $number;
  
  /**
   * @var string
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @ORM_Column_Type   DATETIME
   * @ORM_Column_Null   NULL
   */
  private $fixture;
  
  /**
   * @var string
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @ORM_Column_Type   DATETIME
   * @ORM_Column_Null   NULL
   */
  private $end;
  
}
