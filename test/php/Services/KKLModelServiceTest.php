<?php

namespace Services;

use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Services\KKLModelService;
use PHPUnit\Framework\TestCase;

abstract class KKLModelServiceTest extends TestCase {

  /**
   * @var KKLModelService
   */
  protected $service;

  protected function setUp(): void {
    ServiceBroker::init('TEST');
  }

  protected abstract function initData();

  /*
  public function testById() {
    $model = $this->service->byId(1);
    $this->assertNotNull($model);
  }
  */


}