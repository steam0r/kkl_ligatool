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
 */
class Location extends SwaggerModel {
  
  /**
   * @var string
   * @OA\Property()
   */
  private $title;
  
  /**
   * @var string
   * @OA\Property()
   */
  private $description;
  
  /**
   * @var string
   * @OA\Property()
   */
  private $latitude;
  
  /**
   * @var string
   * @OA\Property()
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
