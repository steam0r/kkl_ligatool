<?php

namespace KKL\Ligatool;

use KKL\Ligatool\DB\OrderBy;

require __DIR__ . '../vendor/autoload.php';

$service = ServiceBroker::getPlayerService();
$players = $service->getAll(new OrderBy('first_name', 'ASC'));
foreach ($players as $player) {
  $api_request = 'https://www.kickerligakoeln.de/wp-kkl/mailinfo.php?q=' . $player->getEmail();
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $api_request
  ));
  $result = curl_exec($curl);
  $api_data = json_decode($result, true);
  $do = true;
  $sql = "INSERT INTO team_properties(value, property_key, objectId) VALUES('" . $player->getId() . "',";
  foreach ($api_data['Rolle'] as $rolle) {
    if ($rolle == "Kapitän") {
      $sql .= "'captain',";
    } else if ($rolle == "Vize Kapitän") {
      $sql .= "'vice_captain',";
    } else {
      $do = false;
    }
  }
  foreach ($api_data['Teams'] as $team) {
    $name = $api_data[$team][0];
    if ($name) {
      $teamService = ServiceBroker::getTeamService();
      $teams = $teamService->byName($name);
      $current = array_pop($teams);
      if ($current) {
        $sql .= "'" . $current->id . "');";
      } else {
        $do = false;
      }
    } else {
      $do = false;
    }
  }
  if ($do) {
    print $sql . "\n";
  }
}
