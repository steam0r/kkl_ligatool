<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:43
 */

namespace KKL\Ligatool\Services;


use KKL\Ligatool\Model\ClubProperty;

class ClubPropertyService extends KKLModelService {

  /**
   * @return ClubProperty
   */
  public function getModel() {
    return new ClubProperty();
  }

  /**
   * @param int $id
   * @return ClubProperty|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return ClubProperty[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return ClubProperty[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return ClubProperty|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }
}