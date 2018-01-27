<?php
namespace KKL\Ligatool\Model;

abstract class BaseModel {

    /**
     * @SWG\Property(format="int64")
     * @var int
     */
    private $id;

    /**
     * @SWG\Property(example="1980-09-02 05:11:42")
     * @var string
     */
    private $createdAt;

    /**
     * @SWG\Property(example="1980-09-02 05:11:42")
     * @var string
     */
    private $updatedAt;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
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
