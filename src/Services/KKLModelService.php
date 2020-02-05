<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:43
 */

namespace KKL\Ligatool\Services;


use Exception;
use KKL\Ligatool\DB\Limit;
use KKL\Ligatool\DB\Manager;
use KKL\Ligatool\DB\OrderBy;
use KKL\Ligatool\DB\Where;
use KKL\Ligatool\Model\KKLModel;
use Symlink\ORM\Repositories\BaseRepository;

abstract class KKLModelService {

  /**
   * @var BaseRepository
   */
  private $repository;

  /**
   * @return KKLModel
   */
  public abstract function getModel();


  protected function getRepository() {
    if ($this->repository) {
      return $this->repository;
    }
    $orm = Manager::getManager();
    $this->repository = $orm->getRepository(get_class($this->getModel()));
    return $this->repository;
  }

  /**
   * @param KKLModel $model
   * @return false|KKLModel
   */
  public function save($model) {
    $model->setUpdatedAt(strftime("%Y-%m-%d %H:%M:%S"));
    $model->save();
    return $this->byId($model->getId());
  }

  /**
   * @param $id int
   * @return KKLModel
   */
  public function getOrCreate($id) {
    $model = $this->byId($id);
    if (!$model) {
      $model = $this->getModel();
      $model->setCreatedAt(strftime("%Y-%m-%d %H:%M:%S"));
    }
    $orm = Manager::getManager();
    $orm->track($model);
    return $model;
  }

  /**
   * @param KKLModel $model
   * @return false|KKLModel
   */
  public function createOrUpdate($model) {
    return $this->save($model);
  }

  /**
   * @param KKLModel $model
   */
  public function delete($model) {
    $model->delete();
  }

  /**
   * @param int $id
   * @return KKLModel|false
   */
  public function byId($id) {
    return $this->getRepository()->find($id);
  }

  /**
   * @param OrderBy|null $orderBy
   * @return KKLModel[]
   */
  public function getAll($orderBy = null) {
    $orm = Manager::getManager();
    $repo = $orm->getRepository(get_class($this->getModel()));
    $builder = $repo->createQueryBuilder();
    $result = [];
    try {
      if ($orderBy) {
        $builder->orderBy($orderBy->getField(), $orderBy->getDirection());
      } else {
        $order = new OrderBy();
        $builder->orderBy($order->getField(), $order->getDirection());
      }
      $result = $builder->buildQuery()->getResults(true);
      if (!$result) {
        return [];
      }
    } catch (Exception $e) {
      error_log($e->getMessage());
    }
    return $result;
  }

  /**
   * @param Where|Where[]|null $where
   * @param OrderBy|null $orderBy
   * @param Limit|null $limit
   * @return KKLModel[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    $repo = $this->getRepository();
    $builder = $repo->createQueryBuilder();
    $result = [];
    try {
      if ($where) {
        if (is_array($where)) {
          foreach ($where as $w) {
            $builder->where($w->getField(), $w->getValue(), $w->getOperator());
          }
        } else {
          $builder->where($where->getField(), $where->getValue(), $where->getOperator());
        }
      }
      if ($orderBy) {
        $builder->orderBy($orderBy->getField(), $orderBy->getDirection());
      } else {
        $order = new OrderBy();
        $builder->orderBy($order->getField(), $order->getDirection());
      }
      if ($limit) {
        $builder->limit($limit->getLimit(), $limit->getOffset());
      }
      $result = $builder->buildQuery()->getResults(true);
      if (!$result) {
        return [];
      }
    } catch (Exception $e) {
      error_log($e->getMessage());
    }
    return $result;

  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return KKLModel|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    $results = $this->find($where, $orderBy, $limit);
    if ($results) {
      return $results[0];
    } else {
      return null;
    }
  }

  /**
   * the models table name without the wordpress prefix
   * @return string
   */
  public function getTableName() {
    return $this->getModel()->getTableName();
  }

  /**
   * the actual table name of the model, including the wordpress prefix
   * @return string
   */
  public function getFullTableName() {
    global $wpdb;
    return $wpdb->prefix . $this->getModel()->getTableName();
  }

}