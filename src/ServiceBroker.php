<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 15:55
 */

namespace KKL\Ligatool;

use KKL\Ligatool\Services\ApiKeyService;
use KKL\Ligatool\Services\AwardService;
use KKL\Ligatool\Services\ClubPropertyService;
use KKL\Ligatool\Services\ClubService;
use KKL\Ligatool\Services\GameDayService;
use KKL\Ligatool\Services\GameService;
use KKL\Ligatool\Services\LeaguePropertyService;
use KKL\Ligatool\Services\LeagueService;
use KKL\Ligatool\Services\LocationService;
use KKL\Ligatool\Services\MatchPropertyService;
use KKL\Ligatool\Services\MatchService;
use KKL\Ligatool\Services\PlayerPropertyService;
use KKL\Ligatool\Services\PlayerService;
use KKL\Ligatool\Services\RankingService;
use KKL\Ligatool\Services\ScheduleService;
use KKL\Ligatool\Services\SeasonPropertyService;
use KKL\Ligatool\Services\SeasonService;
use KKL\Ligatool\Services\SetService;
use KKL\Ligatool\Services\TeamPlayerPropertyService;
use KKL\Ligatool\Services\TeamPlayerService;
use KKL\Ligatool\Services\TeamPropertyService;
use KKL\Ligatool\Services\TeamScoreService;
use KKL\Ligatool\Services\TeamService;

class ServiceBroker {

  private static $apiKeyService;
  private static $awardService;
  private static $clubService;
  private static $clubPropertyService;
  private static $gameService;
  private static $gameDayService;
  private static $leagueService;
  private static $leaguePropertyService;
  private static $locationService;
  private static $matchService;
  private static $matchPropertyService;
  private static $playerService;
  private static $playerPropertyService;
  private static $seasonService;
  private static $seasonPropertyService;
  private static $setService;
  private static $teamService;
  private static $teamPropertyService;
  private static $teamPlayerService;
  private static $teamPlayerPropertyService;
  private static $teamScoreService;
  private static $rankingService;
  private static $scheduleService;

  /**
   * @param string $environment
   */
  public static function init($environment) {
    if (strtoupper($environment) === 'LIVE') {
      self::$apiKeyService = new ApiKeyService();
      self::$awardService = new AwardService();
      self::$clubService = new ClubService();
      self::$clubPropertyService = new ClubPropertyService();
      self::$gameService = new GameService();
      self::$gameDayService = new GameDayService();
      self::$leagueService = new LeagueService();
      self::$leaguePropertyService = new LeaguePropertyService();
      self::$locationService = new LocationService();
      self::$matchService = new MatchService();
      self::$matchPropertyService = new MatchPropertyService();
      self::$playerService = new PlayerService();
      self::$playerPropertyService = new PlayerPropertyService();
      self::$seasonService = new SeasonService();
      self::$seasonPropertyService = new SeasonPropertyService();
      self::$setService = new SetService();
      self::$teamService = new TeamService();
      self::$teamPropertyService = new TeamPropertyService();
      self::$teamPlayerService = new TeamPlayerService();
      self::$teamPlayerPropertyService = new TeamPlayerPropertyService();
      self::$teamScoreService = new TeamScoreService();
      self::$rankingService = new RankingService();
      self::$scheduleService = new ScheduleService();
    }
  }

  /**
   * @return ApiKeyService
   */
  public static function getApiKeyService() {
    return self::$apiKeyService;
  }

  /**
   * @return AwardService
   */
  public static function getAwardService() {
    return self::$awardService;
  }

  /**
   * @return ClubService
   */
  public static function getClubService() {
    return self::$clubService;
  }

  /**
   * @return ClubPropertyService
   */
  public static function getClubPropertyService() {
    return self::$clubPropertyService;
  }

  /**
   * @return GameService
   */
  public static function getGameService() {
    return self::$gameService;
  }

  /**
   * @return GameDayService
   */
  public static function getGameDayService() {
    return self::$gameDayService;
  }

  /**
   * @return LeagueService
   */
  public static function getLeagueService() {
    return self::$leagueService;
  }

  /**
   * @return LeaguePropertyService
   */
  public static function getLeaguePropertyService() {
    return self::$leaguePropertyService;
  }

  /**
   * @return LocationService
   */
  public static function getLocationService() {
    return self::$locationService;
  }

  /**
   * @return MatchService
   */
  public static function getMatchService() {
    return self::$matchService;
  }

  /**
   * @return MatchPropertyService
   */
  public static function getMatchPropertyService() {
    return self::$matchPropertyService;
  }

  /**
   * @return PlayerService
   */
  public static function getPlayerService() {
    return self::$playerService;
  }

  /**
   * @return PlayerPropertyService
   */
  public static function getPlayerPropertyService() {
    return self::$playerPropertyService;
  }

  /**
   * @return SeasonService
   */
  public static function getSeasonService() {
    return self::$seasonService;
  }

  /**
   * @return SeasonPropertyService
   */
  public static function getSeasonPropertyService() {
    return self::$seasonPropertyService;
  }

  /**
   * @return SetService
   */
  public static function getSetService() {
    return self::$setService;
  }

  /**
   * @return TeamService
   */
  public static function getTeamService() {
    return self::$teamService;
  }

  /**
   * @return TeamPropertyService
   */
  public static function getTeamPropertyService() {
    return self::$teamPropertyService;
  }

  /**
   * @return TeamPlayerService
   */
  public static function getTeamPlayerService() {
    return self::$teamPlayerService;
  }

  /**
   * @return TeamPlayerPropertyService
   */
  public static function getTeamPlayerPropertyService() {
    return self::$teamPlayerPropertyService;
  }

  /**
   * @return TeamScoreService
   */
  public static function getTeamScoreService() {
    return self::$teamScoreService;
  }

  /**
   * @return RankingService
   */
  public static function getRankingService() {
    return self::$rankingService;
  }

  /**
   * @return ScheduleService
   */
  public static function getScheduleService() {
    return self::$scheduleService;
  }

}