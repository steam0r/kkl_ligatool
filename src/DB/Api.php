<?php

namespace KKL\Ligatool\DB;

use KKL\Ligatool\KKL_DB;

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

  public function getEmbeddable($table, $field, $id) {
    $sql = "SELECT * FROM " . esc_sql($table) . " WHERE " . esc_sql($field) . " = '" . esc_sql($id) . "'";
    return $this->getDb()->get_results($sql);
  }

  //public function getTeam($teamId) {
  //  $sql = "SELECT * FROM teams WHERE id = '" . esc_sql($teamId) . "'";
  //  return $this->getDb()->get_row($sql);
  //}

}
