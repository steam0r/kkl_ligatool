<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 14:28
 */

namespace KKL\Ligatool\Model;

use KKL\Ligatool\DB\Where;

/**
 * Class KKLPropertyModel
 * @package KKL\Ligatool\Model
 * @ORM_Type              Entity
 */
abstract class KKLPropertyModel extends KKLModel {

  private $cachedProperties = array();

  /**
   * @return KKLModelService
   */
  protected abstract function getPropertyService();

  /**
   * @param string $key
   * @return Property|null
   */
  public function getProperty($key) {
    foreach ($this->getProperties() as $property) {
      if ($property->getObjectId() == $this->getId() && $property->getPropertyKey() === $key) {
        return $property;
      }
    }
    return null;
  }

  /**
   * @return Property[]
   */
  public function getProperties() {
    if ($this->cachedProperties) {
      return $this->cachedProperties;
    }
    $propService = $this->getPropertyService();
    $this->cachedProperties = $propService->find(new Where('objectId', $this->getId(), '='));
    return $this->cachedProperties;
  }

}