<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 18:47
 */

namespace KKL\Ligatool\Model;

use KKL\Ligatool\ServiceBroker;

/**
 * @SWG\Definition(required={"home", "away"}, type="object")
 * @ORM_Type              Entity
 * @ORM_Table "kkl_matches"
 * @ORM_AllowSchemaUpdate True
 */
class Match extends KKLPropertyModel {

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $score_away;


  /**
   * @var string
   * @SWG\Property(example="1980-09-02 05:11:42")
   * @ORM_Column_Type   DATETIME
   * @ORM_Column_Null   NULL
   */
  protected $fixture;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $score_home;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $location;

  /**
   * @var string
   * @SWG\Property(example="1. Liga")
   * @ORM_Column_Type   TEXT
   * @ORM_Column_Null   NULL
   */
  protected $notes;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $status;


  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $away_team;


  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $game_day_id;


  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $home_team;

  /**
   * @return int
   */
  public function getScoreAway() {
    return $this->score_away;
  }

  /**
   * @param int $score_away
   */
  public function setScoreAway($score_away) {
    $this->score_away = $score_away;
  }

  /**
   * @return string
   */
  public function getFixture() {
    return $this->fixture;
  }

  /**
   * @param string $fixture
   */
  public function setFixture($fixture) {
    $this->fixture = $fixture;
  }

  /**
   * @return int
   */
  public function getScoreHome() {
    return $this->score_home;
  }

  /**
   * @param int $score_home
   */
  public function setScoreHome($score_home) {
    $this->score_home = $score_home;
  }

  /**
   * @return string
   */
  public function getLocation() {
    return $this->location;
  }

  /**
   * @param string $location
   */
  public function setLocation($location) {
    $this->location = $location;
  }

  /**
   * @return string
   */
  public function getNotes() {
    return $this->notes;
  }

  /**
   * @param string $notes
   */
  public function setNotes($notes) {
    $this->notes = $notes;
  }

  /**
   * @return int
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * @param int $status
   */
  public function setStatus($status) {
    $this->status = $status;
  }

  /**
   * @return int
   */
  public function getAwayTeam() {
    return $this->away_team;
  }

  /**
   * @param int $away_team
   */
  public function setAwayTeam($away_team) {
    $this->away_team = $away_team;
  }

  /**
   * @return int
   */
  public function getGameDayId() {
    return $this->game_day_id;
  }

  /**
   * @param int $game_day_id
   */
  public function setGameDayId($game_day_id) {
    $this->game_day_id = $game_day_id;
  }

  /**
   * @return int
   */
  public function getHomeTeam() {
    return $this->home_team;
  }

  /**
   * @param int $home_team
   */
  public function setHomeTeam($home_team) {
    $this->home_team = $home_team;
  }

  /**
   * @return KKLModelService
   */
  protected function getPropertyService() {
    return ServiceBroker::getMatchPropertyService();
  }
}
