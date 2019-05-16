<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:29
 */

namespace KKL\Ligatool\DB;


class Where {

  /**
   * @var string
   */
  private $field = '';
  /**
   * @var mixed
   */
  private $value = '';
  /**
   * @var string
   */
  private $operator = '';

  /**
   * OrderBy constructor.
   * @param string $field
   * @param mixed $value
   * @param string $operator
   */
  public function __construct($field, $value, $operator = '=') {
    $this->field = $field;
    $this->value = $value;
    $this->operator = $operator;
  }

  /**
   * @return string
   */
  public function getField() {
    return $this->field;
  }

  /**
   * @return mixed
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * @return string
   */
  public function getOperator() {
    return $this->operator;
  }

}