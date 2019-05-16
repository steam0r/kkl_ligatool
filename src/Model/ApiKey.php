<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 18:47
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"name", "key"}, type="object")
 * @ORM_Type              Entity
 * @ORM_Table "kkl_api_keys"
 * @ORM_AllowSchemaUpdate True
 */
class ApiKey extends KKLModel {

  /**
   * @var string
   * @SWG\Property()
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $name;

  /**
   * @var string
   * @SWG\Property()
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $api_key;

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getApiKey() {
    return $this->api_key;
  }

  /**
   * @param string $api_key
   */
  public function setApiKey($api_key) {
    $this->api_key = $api_key;
  }


}
