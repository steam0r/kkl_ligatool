<?php

namespace Services;

use KKL\Ligatool\Model\League;
use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Services\LeagueService;

class LeagueServiceTest extends KKLModelServiceTest {

  /**
   * @var LeagueService
   */
  protected $service;

  protected function setUp(): void {
    parent::setUp();;
    $this->service = ServiceBroker::getLeagueService();
    $this->assertInstanceOf(LeagueService::class, $this->service);
  }

  protected function initData() {
    $league1 = new League();
    $league1->setActive(true);

    $league2 = new League();
    $league2->setActive(false);
  }


}
