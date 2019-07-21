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
use KKL\Ligatool\ServiceBroker;
use Symlink\ORM\Mapping;

class Wordpress extends DB {

  public static $VERSION_KEY = "kkl_wordpress_db_version";
  public static $VERSION = "4";

  /**
   * @param $league
   * @return Season[]
   */
  public function getSeasonsByLeague($league) {
    $seasonService = ServiceBroker::getSeasonService();
    return $seasonService->find(new Where('league_id', $league, '='));
  }

  /**
   * @return League[]
   */
  public function getInactiveLeagues() {
    $leagueService = ServiceBroker::getLeagueService();
    return $leagueService->find(
      new Where('active', 0, '='),
      new OrderBy('name', 'ASC')
    );
  }

  /**
   * @param GameDay $day
   * @return GameDay|null
   */
  public function getPreviousGameDay($day) {
    $number = $day->getNumber() - 1;
    if ($number < 1) {
      return null;
    }
    $gameDayService = ServiceBroker::getGameDayService();
    return $gameDayService->findOne([
      new Where('season_id', $day->getSeasonId(), '='),
      new Where('number', $number, '=')
    ]);
  }

  /**
   * @param GameDay $day
   * @return GameDay|null
   */
  public function getNextGameDay($day) {
    $number = $day->getNumber() + 1;
    $gameDayService = ServiceBroker::getGameDayService();
    return $gameDayService->findOne([
      new Where('season_id', $day->getSeasonId(), '='),
      new Where('number', $number, '=')
    ]);
  }

  /**
   * @param $mailAddress
   * @return Player|null
   */
  public function getPlayerByMailAddress($mailAddress) {
    $playerService = ServiceBroker::getPlayerService();
    return $playerService->findOne(new Where('email', $mailAddress, '='));
  }

  /**
   * @param $seasonId
   * @return Team[]
   */
  public function getTeamsForSeason($seasonId) {
    $teamService = ServiceBroker::getTeamService();
    return $teamService->find(new Where('season_id', $seasonId, '='));
  }

  /**
   * @return League[]
   */
  public function getActiveLeagues() {
    $leagueService = ServiceBroker::getLeagueService();
    return $leagueService->find(
      new Where('active', 1, '='),
      new OrderBy('name', 'ASC')
    );
  }

  /**
   * @param $id
   * @return Club|null
   */
  public function getClubData($id) {
    $clubService = ServiceBroker::getClubService();
    return $clubService->findOne(new Where('short_name', $id, '='));
  }

  /**
   * @return ApiKey[]
   */
  public function getApiKeys() {
    $apiKeyService = ServiceBroker::getApiKeyService();
    return $apiKeyService->getAll();
  }

  /**
   * @param string $key
   * @return ApiKey|null
   */
  public function getApiKey($key) {
    $apiKeyService = ServiceBroker::getApiKeyService();
    return $apiKeyService->findOne(new Where('api_key', $key, '='));
  }

  /**
   * @return Team[]
   */
  public function getTeams() {
    $teamService = ServiceBroker::getTeamService();
    return $teamService->getAll(new OrderBy('name', 'ASC'));
  }

  /**
   * @param $locationId
   * @return false|Location
   */
  public function getLocation($locationId) {
    $locationService = ServiceBroker::getLocationService();
    return $locationService->byId($locationId);
  }

  /**
   * @param int $playerId
   * @return false|Player
   */
  public function getPlayer($playerId) {
    $playerService = ServiceBroker::getPlayerService();
    return $playerService->byId($playerId);
  }

  /**
   * @param int $clubId
   * @return false|Club
   */
  public function getClub($clubId) {
    $clubService = ServiceBroker::getClubService();
    return $clubService->byId($clubId);
  }

  /**
   * @param int $teamId
   * @return false|Team
   */
  public function getTeam($teamId) {
    $teamService = ServiceBroker::getTeamService();
    return $teamService->byId($teamId);
  }

  /**
   * @param int $matchId
   * @return false|Match
   */
  public function getMatch($matchId) {
    $matchService = ServiceBroker::getMatchService();
    return $matchService->byId($matchId);
  }

  /**
   * @return Location[]
   */
  public function getLocations() {
    $locationService = ServiceBroker::getLocationService();
    return $locationService->getAll();
  }

  /**
   * @param int $leagueId
   * @return false|GameDay
   */
  public function getCurrentGameDayForLeague($leagueId) {
    $gameDayService = ServiceBroker::getGameDayService();
    return $gameDayService->currentForLeague($leagueId);
  }

  /**
   * @param int $seasonId
   * @return false|GameDay
   */
  public function getCurrentGameDayForSeason($seasonId) {
    $gameDayService = ServiceBroker::getGameDayService();
    return $gameDayService->currentForSeason($seasonId);
  }

  public function getGameDaysForSeason($seasonId) {
    $gameDayService = ServiceBroker::getGameDayService();
    return $gameDayService->allForSeason($seasonId);
  }

  /**
   * @param $dayId
   * @return false|GameDay
   */
  public function getGameDay($dayId) {
    $gameDayService = ServiceBroker::getGameDayService();
    return $gameDayService->byId($dayId);
  }

  /**
   * @return GameDay[]
   */
  public function getGameDays() {
    $gameDayService = ServiceBroker::getGameDayService();
    return $gameDayService->getAll(new OrderBy('fixture', 'ASC'));
  }

  /**
   * @param Season $season
   * @param $day
   */
  public function setCurrentGameDay($season, $day) {
    $season->setCurrentGameDay($day->ID);
    $season->save();
  }

  /**
   * @param int $dayId
   * @return false|Season
   */
  public function getSeasonForGameday($dayId) {
    $seasonService = ServiceBroker::getSeasonService();
    return $seasonService->byGameDay($dayId);
  }

  /**
   * @param int $seasonId
   * @return false|Season
   */
  public function getSeason($seasonId) {
    $seasonService = ServiceBroker::getSeasonService();
    return $seasonService->byId($seasonId);
  }

  /**
   * @param int $leagueId
   * @return false|Season
   */
  public function getCurrentSeason($leagueId) {
    $seasonService = ServiceBroker::getSeasonService();
    return $seasonService->currentByLeague($leagueId);
  }

  /**
   * @return Season[]
   */
  public function getSeasons() {
    $seasonService = ServiceBroker::getSeasonService();
    return $seasonService->getAll(new OrderBy('start_date', 'ASC'));
  }

  /**
   * @return League[]
   */
  public function getLeagues() {
    $leagueService = ServiceBroker::getLeagueService();
    return $leagueService->getAll(new OrderBy('name', 'ASC'));
  }

  /**
   * @param int $dayId
   * @return League|false
   */
  public function getLeagueForGameday($dayId) {
    $leagueService = ServiceBroker::getLeagueService();
    return $leagueService->byGameDay($dayId);
  }

  /**
   * @param int $seasonId
   * @return League|false
   */
  public function getLeagueForSeason($seasonId) {
    $leagueService = ServiceBroker::getLeagueService();
    return $leagueService->bySeason($seasonId);
  }

  /**
   * @param int $leagueId
   * @return League|false
   */
  public function getLeague($leagueId) {
    $leagueService = ServiceBroker::getLeagueService();
    return $leagueService->byId($leagueId);
  }

  /**
   * @return Club[]
   */
  public function getClubs() {
    $service = ServiceBroker::getClubService();
    return $service->getAll(new OrderBy('name', 'ASC'));
  }


  /**
   * @return Player[]
   */
  public function getPlayers() {
    $service = ServiceBroker::getPlayerService();
    return $service->getAll(new OrderBy('first_name', 'ASC'));
  }

  /**
   * @return Player[]
   */
  public function getLeagueAdmins() {
    $playerService = ServiceBroker::getPlayerService();
    return $playerService->findByProperty('member_ligaleitung', 'true');
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

  public function getMatchesByGameDay($gameDayId) {
    $matchService = ServiceBroker::getMatchService();
    return $matchService->byGameDay($gameDayId);
  }
}
