<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 18:47
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"title"}, type="object")
 */
class Location extends SwaggerModel {
  
  /**
   * @var string
   * @SWG\Property()
   */
  private $title;
  
  /**
   * @var string
   * @SWG\Property()
   */
  private $description;
  
  /**
   * @var string
   * @SWG\Property()
   */
  private $latitude;
  
  /**
   * @var string
   * @SWG\Property()
   */
  private $longitude;
  
  /**
   * @return string
   */
  public function getTitle() {
    return $this->title;
  }
  
  /**
   * @param string $title
   */
  public function setTitle($title) {
    $this->title = $title;
  }
  
  /**
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }
  
  /**
   * @param string $description
   */
  public function setDescription($description) {
    $this->description = $description;
  }
  
  /**
   * @return string
   */
  public function getLatitude() {
    return $this->latitude;
  }
  
  /**
   * @param string $latitude
   */
  public function setLatitude($latitude) {
    $this->latitude = $latitude;
  }
  
  /**
   * @return string
   */
  public function getLongitude() {
    return $this->longitude;
  }
  
  /**
   * @param string $longitude
   */
  public function setLongitude($longitude) {
    $this->longitude = $longitude;
  }
  
}
