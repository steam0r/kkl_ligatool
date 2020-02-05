<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 17:54
 */

namespace KKL\Ligatool\Model;

use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Services\TeamPlayerPropertyService;

/**
 * @SWG\Definition(required={"code", "name"}, type="object")
 * @ORM_Type              Entity
 * @ORM_Table "kkl_team_players"
 * @ORM_AllowSchemaUpdate True
 */
class TeamPlayer extends KKLPropertyModel {

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $player_id;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $season_id;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $team_id;

  /**
   * @return TeamPlayerPropertyService
   */
  protected function getPropertyService() {
    return ServiceBroker::getTeamPlayerPropertyService();
  }
}
