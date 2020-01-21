<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:43
 */

namespace KKL\Ligatool\Services;


use KKL\Ligatool\Model\TeamPlayerProperty;

class TeamPlayerPropertyService extends KKLModelService {

  /**
   * @return TeamPlayerProperty
   */
  public function getModel() {
    return new TeamPlayerProperty();
  }

  /**
   * @param int $id
   * @return TeamPlayerProperty|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return TeamPlayerProperty[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return TeamPlayerProperty[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return TeamPlayerProperty|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }

  /**
   * @param $team
   * @param $properties
   * @deprecated use orm
   */
  public function setTeamProperties($team, $properties) {
    foreach ($properties as $key => $value) {
      $this->getDb()->delete(static::$prefix . 'team_properties', array('objectId' => $team->ID, 'property_key' => $key));
      if ($value !== false) {
        $this->getDb()->insert(static::$prefix . 'team_properties', array('objectId' => $team->ID, 'property_key' => $key, 'value' => $value,), array('%d', '%s', '%s'));
      }
    }
  }

}