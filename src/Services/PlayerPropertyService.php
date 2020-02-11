<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:43
 */

namespace KKL\Ligatool\Services;


use KKL\Ligatool\DB\Where;
use KKL\Ligatool\Model\Player;
use KKL\Ligatool\Model\PlayerProperty;

class PlayerPropertyService extends KKLModelService {

  /**
   * @return PlayerProperty
   */
  public function getModel() {
    return new PlayerProperty();
  }

  /**
   * @param int $id
   * @return PlayerProperty|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return PlayerProperty[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return PlayerProperty[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return PlayerProperty|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }

  /**
   * @param $playerId
   * @param $key
   * @return PlayerProperty|null
   */
  public function byPlayerAndKey($playerId, $key) {
    return $this->findOne([
      new Where('objectId', $playerId),
      new Where('property_key', $key)
    ]);
  }

  /**
   * @param Player $player
   * @param array $properties
   */
  public function setPlayerProperties($player, $properties) {
    foreach ($properties as $key => $value) {
      $prop = $this->byPlayerAndKey($player->getId(), $key);
      if ($prop) {
        $prop->delete();
      }
      if ($value !== false) {
        $newProp = new PlayerProperty();
        $newProp->setObjectId($player->getId());
        $newProp->setPropertyKey($key);
        $newProp->setValue($value);
        $newProp->save();
      }
    }
  }

}