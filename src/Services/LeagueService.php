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
use KKL\Ligatool\Model\League;
use KKL\Ligatool\ServiceBroker;

class LeagueService extends KKLModelService {

  /**
   * @return League
   */
  public function getModel() {
    return new League();
  }

  /**
   * @param int $id
   * @return League|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return League[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return League[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return League|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }

  /**
   * @param int $dayId
   * @return false|League
   */
  public function byGameDay($dayId) {
    $gameDayService = ServiceBroker::getGameDayService();
    $gameDay = $gameDayService->byId($dayId);
    $result = false;
    if ($gameDay) {
      $result = $this->bySeason($gameDay->getSeasonId());
    }
    return $result;
  }

  /**
   * @param int $seasonId
   * @return false|League
   */
  public function bySeason($seasonId) {
    $result = false;
    $seasonService = ServiceBroker::getSeasonService();
    $season = $seasonService->byId($seasonId);
    if ($season) {
      $result = $this->byId($season->getLeagueId());
    }
    return $result;

  }

  /**
   * @return League[]
   */
  public function getInactive() {
    $leagueService = ServiceBroker::getLeagueService();
    return $leagueService->find(
      new Where('active', 0, '='),
      new OrderBy('name', 'ASC')
    );
  }

  /**
   * @return League[]
   */
  public function getActive() {
    $leagueService = ServiceBroker::getLeagueService();
    return $leagueService->find(
      new Where('active', 1, '='),
      new OrderBy('name', 'ASC')
    );
  }

  /*
   * @param null $slug
   * @return League|null
   */
  public function bySlug($slug) {
    return $this->findOne(new Where('code', $slug, '='));
  }
}