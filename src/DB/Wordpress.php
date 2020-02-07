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

  public function installWordpressDatabase() {
    // $version = get_option(static::$VERSION_KEY);
    // if ( !$version || $version < static::$VERSION ) {
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
  }

  /**
   * @deprecated use orm
   */
  public function get_results($sql) {
    return $this->getDb()->get_results($sql);
  }

  /**
   * @deprecated use orm
   */
  public function prepare($sql, $args) {
    return $this->getDb()->prepare($sql, $args);
  }

}
