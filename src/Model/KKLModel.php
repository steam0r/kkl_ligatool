<?php

namespace KKL\Ligatool\Model;

use Symlink\ORM\Manager;
use Symlink\ORM\Models\BaseModel;

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
  
  /**
   * @return mixed
   */
  public function getCreatedAt() {
    return $this->get('created_at');
  }
  
  /**
   * @param mixed $createdAt
   */
  public function setCreatedAt($createdAt) {
    $this->set('created_at', $createdAt);
  }
  
  /**
   * @return mixed
   */
  public function getUpdatedAt() {
    return $this->get('updated_at');
  }
  
  /**
   * @param mixed $updatedAt
   */
  public function setUpdatedAt($updatedAt) {
    $this->set('updated_at', $updatedAt);
  }
  
  public function save() {
    $orm = Manager::getManager();
    $orm->persist($this);
    $orm->flush();
  }
  
  public function delete() {
    $orm = Manager::getManager();
    $orm->remove($this);
    $orm->flush();
  }

  public function getFullTableName() {
    global $wpdb;
    return $wpdb->prefix . $this->getTableName();
  }

}
