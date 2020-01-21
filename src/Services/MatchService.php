<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:43
 */

namespace KKL\Ligatool\Services;


use KKL\Ligatool\DB\Where;
use KKL\Ligatool\Model\Match;

class MatchService extends KKLModelService {

  /**
   * @return Match
   */
  public function getModel() {
    return new Match();
  }

  /**
   * @param int $id
   * @return Match|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return Match[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return Match[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return Match|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }

  public function byGameDay($gameDayId) {
    return $this->find(new Where("game_day_id", $gameDayId, '='));
  }

  /**
   * FIXME use orm
   * @param $matches
   * @return array
   */
  public function topMatches($matches) {
    $seasons = array();
    $days = array();
    foreach ($matches as $match) {
      $days[$match->season_id] = $match->day_number;
      $seasons[$match->season_id] = array("name" => $match->league, "code" => $match->leaguecode);
    }

    $potentialMatches = array();
    foreach ($days as $season => $day) {
      $sql = "SELECT " . "team_scores.team_id, " . "sum(team_scores.score) as score, " . "sum(team_scores.gamesFor - team_scores.gamesAgainst) as gameDiff " . "FROM game_days, " . "team_scores  " . "WHERE game_days.season_id='" . $season . "' " . "AND game_days.number <= '" . $day . "' " . "AND gameDay_id=game_days.id " . "GROUP BY team_id " . "ORDER BY score DESC, gameDiff DESC ";
      $dbh = $this->getDb();
      $stmt = $dbh->prepare($sql, array());
      $ranking = $dbh->get_results($stmt);
      $first = $ranking[0];
      $second = $ranking[1];
      $lastButOne = $ranking[count($ranking) - 2];
      $last = $ranking[count($ranking) - 1];
      $potentialMatches[$seasons[$season]['name']] = array("top" => array($first->team_id, $second->team_id), "bottom" => array($lastButOne->team_id, $last->team_id));
    }

    $topMatches = array();
    foreach ($matches as $match) {
      $league = $match->league;
      $home = $match->home_id;
      $away = $match->away_id;
      if (in_array($home, $potentialMatches[$league]['top']) && in_array($away, $potentialMatches[$league]['top'])) {
        $topMatches[$league] = array("type" => "top", "home" => $match->home, "away" => $match->away, "leaguecode" => $match->leaguecode, "contact" => $match->slack);
      }
      if (in_array($home, $potentialMatches[$league]['bottom']) && in_array($away, $potentialMatches[$league]['bottom'])) {
        $topMatches[$league] = array("type" => "bottom", "home" => $match->home, "away" => $match->away, "leaguecode" => $match->leaguecode, "contact" => $match->slack);
      }
    }
    return $topMatches;
  }

  /**
   * FIXME use orm
   * @param $days_off
   * @return mixed
   */
  public function reminderMatches($days_off) {
    $sql = "select l.name as league, s.id as season_id, g.number as day_number, l.code as leaguecode, home.name as home, away.name as away, home.id as home_id, away.id as away_id, loc.title as location, CONCAT(home.name,' gg. ', away.name) as title, slack.value as slack " . "from leagues as l " . "JOIN seasons as s ON s.id = l.current_season " . "JOIN game_days AS g ON g.id = s.current_game_day " . "JOIN game_days AS ug ON ug.season_id = s.id and ug.number = g.number + 1 AND date(ug.fixture) = date(NOW() + INTERVAL " . $days_off . " DAY) " . "JOIN `matches` AS m ON m.game_day_id = ug.id " . "JOIN teams as home ON m.home_team = home.id " . "JOIN teams AS away ON m.away_team = away.id " . "LEFT JOIN team_properties AS p ON p.objectId = home.id AND p.property_key = 'location' " . "LEFT JOIN locations AS loc ON loc.id = p.value " . "left join season_properties AS sp ON sp.objectId = s.id AND sp.property_key = 'season_admin' " . "left join players AS adm ON adm.id = sp.value " . "left join player_properties as slack ON adm.id = slack.objectId AND slack.property_key = 'slack_alias' " . "where l.active = 1 order by l.name ASC";
    $dbh = $this->getDb();
    $stmt = $dbh->prepare($sql, array());
    return $dbh->get_results($stmt);
  }
}