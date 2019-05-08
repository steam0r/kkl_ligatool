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
 * @ORM_Table "kkl_team_scores"
 * @ORM_AllowSchemaUpdate True
 */
class TeamScore extends KKLModel {

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $draw;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $gamesAgainst;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $gamesFor;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $goalsAgainst;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $goalsFor;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $loss;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $position;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $score;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $win;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $gameDay_id;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $team_id;
  
}
