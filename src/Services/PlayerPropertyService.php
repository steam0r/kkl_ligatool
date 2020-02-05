<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:43
 */

namespace KKL\Ligatool\Services;


use KKL\Ligatool\DB\Wordpress;
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
   * @param $player
   * @param $properties
   * @deprecated use orm
   */
  public function setPlayerProperties($player, $properties) {
    $db = $this->getDb();
    foreach ($properties as $key => $value) {
      $db->delete($db->getPrefix() . 'player_properties', array('objectId' => $player->ID, 'property_key' => $key));
      if ($value !== false) {
        $db->insert($db->getPrefix() . 'player_properties', array('objectId' => $player->ID, 'property_key' => $key, 'value' => $value,), array('%d', '%s', '%s'));
      }
    }
  }

  /**
   * @return Wordpress
   * @deprecated use orm layer
   */
  private function getDb() {
    return new Wordpress();
  }

}