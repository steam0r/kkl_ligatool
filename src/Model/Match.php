<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 18:47
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"home", "away"}, type="object")
 * @ORM_Type              Entity
 * @ORM_Table "kkl_matches"
 * @ORM_AllowSchemaUpdate True
 */
class Match extends KKLModel {


  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $score_away;


  /**
   * @var string
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @ORM_Column_Type   DATETIME
   * @ORM_Column_Null   NULL
   */
  private $fixture;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $score_home;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $location;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  private $notes;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $status;


  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $away_team;


  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $game_day_id;


  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  private $home_team;

}
