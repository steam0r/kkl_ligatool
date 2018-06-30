<?php

namespace KKL\Ligatool\DB;

use KKL\Ligatool\DB;
use KKL\Ligatool\Model\ApiKey;
use Symlink\ORM\Mapping;

class Wordpress extends DB {
  
  public static $VERSION_KEY = "kkl_wordpress_db_version";
  public static $VERSION = "3";
  
  public function getLeagueAdmins() {
    $sql = "SELECT p.* FROM players AS p " . "JOIN player_properties AS pp ON pp.objectId = p.id " . "AND pp.property_key = 'member_ligaleitung' " . "AND pp.value = 'true' " . "ORDER BY p.first_name, p.last_name ASC";
    $players = $this->getDb()->get_results($sql);
    foreach($players as $player) {
      $properties = $this->getPlayerProperties($player->id);
      $player->properties = $properties;
      $player->role = 'ligaleitung';
    }
    return $players;
  }
  
  public function installWordpressDatabase() {
    $version = get_option( static::$VERSION_KEY );
    if ( !$version || $version < static::$VERSION ) {
      $mapper = Mapping::getMapper();
      $mapper->updateSchema(ApiKey::class);
      update_option( static::$VERSION_KEY, static::$VERSION );
    }
  }
  
  public function installWordpressData() {
    // TODO: fill database with initial data, this needs every table under orm control
  }
}
