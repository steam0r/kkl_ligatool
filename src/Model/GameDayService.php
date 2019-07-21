<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:43
 */

namespace KKL\Ligatool\Model;


use KKL\Ligatool\DB\Where;
use KKL\Ligatool\ServiceBroker;

class GameDayService extends KKLModelService {

  /**
   * @return GameDay
   */
  public function getModel() {
    return new GameDay();
  }

  /**
   * @param int $id
   * @return GameDay|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return GameDay[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return GameDay[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return GameDay|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }

  /**
   * @param int $leagueId
   * @return false|GameDay
   */
  public function currentForLeague($leagueId) {
    $leagueService = ServiceBroker::getLeagueService();
    $league = $leagueService->byId($leagueId);
    $result = false;
    if ($league && $league->getCurrentSeason()) {
      $result = $this->currentForSeason($league->getCurrentSeason());
    }
    return $result;
  }

  /**
   * @param int $seasonId
   * @return false|GameDay
   */
  public function currentForSeason($seasonId) {
    $seasonService = ServiceBroker::getSeasonService();
    $season = $seasonService->byId($seasonId);
    $result = false;
    if ($season && $season->getCurrentGameDay()) {
      $result = $this->byId($season->getCurrentGameDay());
    }
    return $result;
  }

  public function allForSeason($seasonId) {
    return $this->find(new Where('season_id', $seasonId, '='));
  }
}