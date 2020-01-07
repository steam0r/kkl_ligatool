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

class PlayerService extends KKLModelService {

  /**
   * @return Player
   */
  public function getModel() {
    return new Player();
  }

  /**
   * @param int $id
   * @return Player|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return Player[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return Player[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return Player|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }

  /**
   * @param $key
   * @param $value
   * @return Player[]
   */
  public function findByProperty($key, $value = false) {
    $playerPropertyService = ServiceBroker::getPlayerPropertyService();
    $wheres = [new Where('property_key', $key, '=')];
    if ($value) {
      $wheres[] = new Where('value', $value, '=');
    }
    $properties = $playerPropertyService->find();
    $playerIds = [];
    foreach ($properties as $property) {
      $playerIds[] = $property->getObjectId();
    }
    $result = [];
    if (!empty($playerIds)) {
      $result = $this->find(new Where('ID', $playerIds, 'IN'));
    }
    return $result;
  }

  /**
   * @param $mailAddress
   * @return Player|null
   */
  public function byMailAddress($mailAddress) {
    $playerService = ServiceBroker::getPlayerService();
    return $playerService->findOne(new Where('email', $mailAddress, '='));
  }

  public function getLeagueAdmins() {
    return $this->findByProperty('member_ligaleitung', 'true');
  }

}