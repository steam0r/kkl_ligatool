<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 18:47
 */

namespace KKL\Ligatool\Model;

/**
 * @OA\Schema()
 * @ORM_Type              Entity
 * @ORM_Table "kkl_api_keys"
 * @ORM_AllowSchemaUpdate True
 */
class ApiKey extends KKLModel {
  
  /**
   * @var string
   * @OA\Property()
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NOT NULL
   */
  protected $name;
  
  /**
   * @var string
   * @OA\Property()
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NOT NULL
   */
  protected $api_key;
  
  /**
   * @return string
   */
  public function getName() {
    return $this->get('name');
  }
  
  /**
   * @param string $name
   */
  public function setName($name) {
    $this->set('name', $name);
  }
  
  /**
   * @return string
   */
  public function getApiKey() {
    return $this->get('api_key');
  }
  
  /**
   * @param string $key
   */
  public function setApiKey($key) {
    $this->set('api_key', $key);
  }
  
  
}
