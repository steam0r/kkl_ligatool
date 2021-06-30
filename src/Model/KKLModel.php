<?php

namespace KKL\Ligatool\Model;

use KKL\Ligatool\DB\Manager;
use Symlink\ORM\Exceptions\FailedToInsertException;
use Symlink\ORM\Models\BaseModel;

/**
 * Class KKLModel
 * @package KKL\Ligatool\Model
 * @ORM_Type              Entity
 */
abstract class KKLModel extends BaseModel {

  /**
   * @var string
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @ORM_Column_Type   datetime
   * @ORM_Column_Null   DEFAULT NULL
   */
  protected $created_at;

  /**
   * @var string
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @ORM_Column_Type   datetime
   * @ORM_Column_Null   DEFAULT NULL
   */
  protected $updated_at;

  public function save() {
    $orm = Manager::getManager();
    if (!$this->getId()) {
      $orm->persist($this);
    }
    try {
      $orm->flush();
    } catch (FailedToInsertException $e) {
      error_log("FailedToInsertException: " . $e->getMessage());
    }
  }

  public function delete() {
    $orm = Manager::getManager();
    $orm->remove($this);
    try {
      $orm->flush();
    } catch (FailedToInsertException $e) {
      error_log("FailedToInsertException: " . $e->getMessage());
    }
  }

  public function setId($id) {
	  $this->ID = $id;
  }

	/**
   * @return string
   */
  public function getCreatedAt() {
    return $this->created_at;
  }

  /**
   * @param string $created_at
   */
  public function setCreatedAt($created_at) {
    $this->created_at = $created_at;
  }

  /**
   * @return string
   */
  public function getUpdatedAt() {
    return $this->updated_at;
  }

  /**
   * @param string $updated_at
   */
  public function setUpdatedAt($updated_at) {
    $this->updated_at = $updated_at;
  }

}
