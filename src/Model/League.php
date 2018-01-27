<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 17:54
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"code", "name"}, type="object")
 */
class League extends BaseModel {

    /**
     * @var boolean
     * @SWG\Property()
     */
    private $active;

    /**
     * @var string
     * @SWG\Property(example="koeln1")
     */
    private $code;

    /**
     * @var string
     * @SWG\Property(example="1. Liga")
     */
    private $name;

    /**
     * @SWG\Property(format="int64")
     * @var int
     */
    private $currentSeason;

    /**
     * @return mixed
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active) {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code) {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCurrentSeason() {
        return $this->currentSeason;
    }

    /**
     * @param mixed $currentSeason
     */
    public function setCurrentSeason($currentSeason) {
        $this->currentSeason = $currentSeason;
    }


}
