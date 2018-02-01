<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 18:47
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"propertyKey", "objectId"}, type="object")
 */
class Property extends BaseModel {

    /**
     * @var string
     * @SWG\Property()
     */
    private $propertyKey;

    /**
     * @SWG\Property(format="int64")
     * @var int
     */
    private $objectId;

    /**
     * @var string
     * @SWG\Property()
     */
    private $text;

    /**
     * @var string
     * @SWG\Property()
     */
    private $value;

    /**
     * @return string
     */
    public function getPropertyKey() {
        return $this->propertyKey;
    }

    /**
     * @param string $propertyKey
     */
    public function setPropertyKey($propertyKey) {
        $this->propertyKey = $propertyKey;
    }

    /**
     * @return int
     */
    public function getObjectId() {
        return $this->objectId;
    }

    /**
     * @param int $objectId
     */
    public function setObjectId($objectId) {
        $this->objectId = $objectId;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text) {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value) {
        $this->value = $value;
    }

}
