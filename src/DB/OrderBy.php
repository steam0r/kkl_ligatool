<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:29
 */

namespace KKL\Ligatool\DB;


class OrderBy {

  /**
   * @var string
   */
  private $field = '';
  /**
   * @var string
   */
  private $direction = '';

  /**
   * OrderBy constructor.
   * @param string $field
   * @param string $direction
   */
  public function __construct($field = 'ID', $direction = 'ASC') {
    $this->field = $field;
    $this->direction = strtoupper($direction);
  }

  /**
   * @return string
   */
  public function getField() {
    return $this->field;
  }

  /**
   * @return string
   */
  public function getDirection() {
    return $this->direction;
  }

}