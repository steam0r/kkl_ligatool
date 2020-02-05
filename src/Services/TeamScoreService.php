<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:43
 */

namespace KKL\Ligatool\Services;


use KKL\Ligatool\Model\GameDay;
use KKL\Ligatool\Model\Team;
use KKL\Ligatool\Model\TeamScore;

class TeamScoreService extends KKLModelService {

  /**
   * @return TeamScore
   */
  public function getModel() {
    return new TeamScore();
  }

  /**
   * @param int $id
   * @return TeamScore|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return TeamScore[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return TeamScore[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return TeamScore|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }

  /**
   * @param Team $team
   * @param GameDay
   * @return TeamScore[]
   */
  public function forTeamUntilGameDay($team, GameDay $day) {
    return array();
  }

}