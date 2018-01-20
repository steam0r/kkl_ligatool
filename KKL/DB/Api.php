<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 20.01.18
 * Time: 10:34
 */

class KKL_DB_Api extends KKL_DB {

  public function getLeagueAdmins() {
    $sql = "SELECT p.* FROM players AS p " .
      "JOIN player_properties AS pp ON pp.objectId = p.id " .
      "AND pp.property_key = 'member_ligaleitung' " .
      "AND pp.value = 'true' " .
      "ORDER BY p.first_name, p.last_name ASC";
    $players = $this->getDb()->get_results($sql);
    foreach ($players as $player) {
      $player->role = 'ligaleitung';
    }
    return $players;
  }

}
