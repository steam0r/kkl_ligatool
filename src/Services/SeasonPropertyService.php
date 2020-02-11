<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:43
 */

namespace KKL\Ligatool\Services;


use KKL\Ligatool\DB\Where;
use KKL\Ligatool\Model\Season;
use KKL\Ligatool\Model\SeasonProperty;

class SeasonPropertyService extends KKLModelService {

  /**
   * @return SeasonProperty
   */
  public function getModel() {
    return new SeasonProperty();
  }

  /**
   * @param int $id
   * @return SeasonProperty|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return SeasonProperty[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return SeasonProperty[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return SeasonProperty|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }

  /**
   * @param null $seasonId
   * @return array
   */
  public function bySeason($seasonId) {
    $results = $this->find(new Where('objectId', $seasonId, '='));
    $properties = array();
    foreach ($results as $result) {
      $properties[$result->getPropertyKey()] = $result->getValue();
    }
    return $properties;
  }

  /**
   * @param $seasonId
   * @param $key
   * @return SeasonProperty|null
   */
  public function bySeasonAndKey($seasonId, $key) {
    return $this->findOne([
      new Where('objectId', $seasonId),
      new Where('property_key', $key)
    ]);
  }

  /**
   * @param Season $season
   * @param array $properties
   */
  public function setSeasonProperties($season, $properties) {
    foreach ($properties as $key => $value) {
      $prop = $this->bySeasonAndKey($season->getId(), $key);
      if ($prop) {
        $prop->delete();
      }
      if ($value !== false) {
        $newProp = new SeasonProperty();
        $newProp->setObjectId($season->getId());
        $newProp->setPropertyKey($key);
        $newProp->setValue($value);
        $newProp->save();
      }
    }
  }

}