<?php

namespace KKL\Ligatool\Model;

abstract class SwaggerModel {

  /**
   * @SWG\Property(format="int64")
   * @var int
   */
  protected $ID;

  /**
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @var string
   */
  protected $createdAt;

  /**
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @var string
   */
  protected $updatedAt;

  /**
   * @return mixed
   */
  public function getId() {
    return $this->ID;
  }

  /**
   * @param mixed $id
   */
  public function setId($id) {
    $this->ID = $id;
  }

  /**
   * @return mixed
   */
  public function getCreatedAt() {
    return $this->createdAt;
  }

  /**
   * @param mixed $createdAt
   */
  public function setCreatedAt($createdAt) {
    $this->createdAt = $createdAt;
  }

  /**
   * @return mixed
   */
  public function getUpdatedAt() {
    return $this->updatedAt;
  }

  /**
   * @param mixed $updatedAt
   */
  public function setUpdatedAt($updatedAt) {
    $this->updatedAt = $updatedAt;
  }

}
