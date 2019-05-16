<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:29
 */

namespace KKL\Ligatool\DB;


class Limit {

  /**
   * @var int
   */
  private $offset = '';
  /**
   * @var int
   */
  private $limit = '';

  /**
   * OrderBy constructor.
   * @param int $limit
   * @param int $offset
   */
  public function __construct($limit, $offset = 0) {
    $this->limit = $limit;
    $this->offset = $offset;
  }

  /**
   * @return int
   */
  public function getOffset() {
    return $this->offset;
  }

  /**
   * @return int
   */
  public function getLimit() {
    return $this->limit;
  }

}