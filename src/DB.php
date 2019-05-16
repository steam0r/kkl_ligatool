<?php

namespace KKL\Ligatool;

use Symlink\ORM\Manager;
use wpdb;

abstract class DB {

  /**
   * @var string wordpress db prefix
   */
  protected static $prefix;

  /**
   * @deprecated move to orm
   * @var wpdb wordpres db layer
   */
  protected static $db;

  /**
   * @var Manager simple wordpress orm layer
   */
  protected static $orm;

  public function __construct() {
    global $wpdb;
    static::$prefix = $wpdb->prefix . 'kkl_';
    if (static::$db === null) {
      static::$db = $wpdb;
    }
    if (static::$orm === null) {
      static::$orm = Manager::getManager();
    }
  }

  /**
   * @deprecated user orm layer for new functions, implement databse-code in db-layer not in modules
   * @return wpdb
   */
  public function getDb() {
    return static::$db;
  }

  /**
   * @return Manager
   */
  protected function getOrm() {
    return static::$orm;
  }

}
