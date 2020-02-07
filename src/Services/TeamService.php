<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:43
 */

namespace KKL\Ligatool\Services;


use KKL\Ligatool\DB\OrderBy;
use KKL\Ligatool\DB\Where;
use KKL\Ligatool\Model\Team;
use KKL\Ligatool\ServiceBroker;

class TeamService extends KKLModelService {

  /**
   * @return Team
   */
  public function getModel() {
    return new Team();
  }

  /**
   * @param int $id
   * @return Team|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return Team[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return Team[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return Team|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }

  /**
   * @param $seasonId
   * @return Team[]
   */
  public function forSeason($seasonId) {
    $teamService = ServiceBroker::getTeamService();
    return $teamService->find(new Where('season_id', $seasonId, '='));
  }

  /**
   * @param $id
   * @return Team[]
   */
  public function byClub($id) {
    return $this->find(new Where('club_id', $id, '='));
  }

  /**
   * @param $teamName
   * @return Team[]
   */
  public function byName($teamName) {
    return $this->find(new Where('name', $teamName, '='));
  }

  /**
   * @param $clubId
   * @return Team|null
   */
  public function getCurrentTeamForClub($clubId) {
    $teamPropertyService = ServiceBroker::getTeamPropertyService();
    $teams = $this->find(
      new Where('club_id', $clubId),
      new OrderBy('ID', 'DESC')
    );
    $team = null;
    if (!empty($teams)) {
      $team = $teams[0];
      $properties = $teamPropertyService->byTeam($team->getId());
      $team->properties = $properties;
    }
    return $team;
  }

}