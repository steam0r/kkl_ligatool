<?php

namespace KKL\Ligatool\DB;

use KKL\Ligatool\DB;
use KKL\Ligatool\Model\ApiKey;
use KKL\Ligatool\Model\Award;
use KKL\Ligatool\Model\Club;
use KKL\Ligatool\Model\ClubAward;
use KKL\Ligatool\Model\ClubProperty;
use KKL\Ligatool\Model\Game;
use KKL\Ligatool\Model\GameDay;
use KKL\Ligatool\Model\League;
use KKL\Ligatool\Model\LeagueProperty;
use KKL\Ligatool\Model\Location;
use KKL\Ligatool\Model\Match;
use KKL\Ligatool\Model\MatchProperty;
use KKL\Ligatool\Model\Player;
use KKL\Ligatool\Model\PlayerProperty;
use KKL\Ligatool\Model\Season;
use KKL\Ligatool\Model\SeasonProperty;
use KKL\Ligatool\Model\Set;
use KKL\Ligatool\Model\SetAwayPlayer;
use KKL\Ligatool\Model\SetHomePlayer;
use KKL\Ligatool\Model\Team;
use KKL\Ligatool\Model\TeamPlayer;
use KKL\Ligatool\Model\TeamPlayerProperty;
use KKL\Ligatool\Model\TeamProperty;
use KKL\Ligatool\Model\TeamScore;
use Symlink\ORM\Mapping;

class Wordpress extends DB {

  public static $VERSION_KEY = "kkl_wordpress_db_version";
  public static $VERSION = "4";

  public function getLeagueAdmins() {
    $sql = "SELECT p.* FROM players AS p " . "JOIN player_properties AS pp ON pp.objectId = p.id " . "AND pp.property_key = 'member_ligaleitung' " . "AND pp.value = 'true' " . "ORDER BY p.first_name, p.last_name ASC";
    $players = $this->getDb()->get_results($sql);
    foreach ($players as $player) {
      $properties = $this->getPlayerProperties($player->ID);
      $player->properties = $properties;
      $player->role = 'ligaleitung';
    }
    return $players;
  }

  public function installWordpressDatabase() {
    $version = get_option(static::$VERSION_KEY);
    //if ( !$version || $version < static::$VERSION ) {
    $mapper = Mapping::getMapper();
    $mapper->updateSchema(ApiKey::class);
    $mapper->updateSchema(Award::class);
    $mapper->updateSchema(Club::class);
    $mapper->updateSchema(ClubAward::class);
    $mapper->updateSchema(ClubProperty::class);
    $mapper->updateSchema(Game::class);
    $mapper->updateSchema(GameDay::class);
    $mapper->updateSchema(League::class);
    $mapper->updateSchema(LeagueProperty::class);
    $mapper->updateSchema(Location::class);
    $mapper->updateSchema(Match::class);
    $mapper->updateSchema(MatchProperty::class);
    $mapper->updateSchema(Player::class);
    $mapper->updateSchema(PlayerProperty::class);
    $mapper->updateSchema(Season::class);
    $mapper->updateSchema(SeasonProperty::class);
    $mapper->updateSchema(Set::class);
    $mapper->updateSchema(SetAwayPlayer::class);
    $mapper->updateSchema(SetHomePlayer::class);
    $mapper->updateSchema(Team::class);
    $mapper->updateSchema(TeamPlayer::class);
    $mapper->updateSchema(TeamPlayerProperty::class);
    $mapper->updateSchema(TeamProperty::class);
    $mapper->updateSchema(TeamScore::class);
    update_option(static::$VERSION_KEY, static::$VERSION);
    //}
  }

  public function installWordpressData() {
    // TODO: fill database with initial data, this needs every table under orm control
  }
}
