<?php

namespace Services;

use KKL\Ligatool\Mocks\LeagueServiceMock;
use KKL\Ligatool\ServiceBroker;
use KKL\Ligatool\Services\LeagueService;

class LeagueServiceTest extends KKLModelServiceTest {

  protected function setUp(): void {
    ServiceBroker::init('TEST');
  }

  public function testServiceCreation() {
    $leagueService = ServiceBroker::getLeagueService();
    $this->assertInstanceOf(LeagueService::class, $leagueService);
  }

}
