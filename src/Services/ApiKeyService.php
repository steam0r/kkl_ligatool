<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:43
 */

namespace KKL\Ligatool\Services;


use KKL\Ligatool\DB\Where;
use KKL\Ligatool\Model\ApiKey;

class ApiKeyService extends KKLModelService {

  /**
   * @return ApiKey
   */
  public function getModel() {
    return new ApiKey();
  }

  /**
   * @param int $id
   * @return ApiKey|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return ApiKey[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return ApiKey[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return ApiKey|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }

  /**
   * @param $key
   * @return ApiKey|null
   */
  public function byKey($key) {
    return $this->findOne(new Where('api_key', $key, '='));
  }


}