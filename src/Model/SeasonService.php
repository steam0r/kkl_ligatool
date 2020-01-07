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

class SeasonService extends KKLModelService {

  /**
   * @return Season
   */
  public function getModel() {
    return new Season();
  }

  /**
   * @param int $id
   * @return Season|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return Season[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return Season[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return Season|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }

  /**
   * @param int $dayId
   * @return false|Season
   */
  public function byGameDay($dayId) {
    $gameDayService = ServiceBroker::getGameDayService();
    $gameDay = $gameDayService->byId($dayId);
    $result = false;
    if ($gameDay) {
      $result = $this->byId($gameDay->getSeasonId());
    }
    return $result;
  }

  public function currentByLeague($leagueId) {
    $leagueService = ServiceBroker::getLeagueService();
    $league = $leagueService->byId($leagueId);
    $result = false;
    if ($league && $league->getCurrentSeason()) {
      $result = $this->byId($league->getCurrentSeason());
    }
    return $result;
  }

  /**
   * @param $leagueId
   * @return Season[]
   */
  public function byLeague($leagueId) {
    $seasonService = ServiceBroker::getSeasonService();
    return $seasonService->find(new Where('league_id', $leagueId, '='));
  }

}