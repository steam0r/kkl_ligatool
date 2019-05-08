<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 17:57
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"name", "league", "startDate", "endDate"}, type="object")
 * @ORM_Type              Entity
 * @ORM_Table "kkl_seasons"
 * @ORM_AllowSchemaUpdate True
 */
class Season extends KKLModel {

  /**
   * @var boolean
   * @SWG\Property()
   * @ORM_Column_Type   TINYINT
   * @ORM_Column_Null   NULL
   */
  private $active;

  /**
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @var string
   * @ORM_Column_Type   DATETIME
   * @ORM_Column_Null   NULL
   */
  private $end_date;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $name;

  /**
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @var string
   * @ORM_Column_Type   DATETIME
   * @ORM_Column_Null   NULL
   */
  private $start_date;

  /**
   * @SWG\Property(format="int64")
   * @var int
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $league_id;

  /**
   * @SWG\Property(format="int64")
   * @var int
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $current_game_day;

}
