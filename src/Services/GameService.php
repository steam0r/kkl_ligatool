<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:43
 */

namespace KKL\Ligatool\Services;


use KKL\Ligatool\DB\Where;
use KKL\Ligatool\DB\Wordpress;
use KKL\Ligatool\Model\Game;
use KKL\Ligatool\Model\Set;

class GameService extends KKLModelService {

  /**
   * @return Game
   */
  public function getModel() {
    return new Game();
  }

  /**
   * @param int $id
   * @return Game|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return Game[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return Game|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return Game[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param $set Set
   * @return Game[]
   */
  public function bySet($set) {
    return $this->find(new Where('set_id', $set->getId()));
  }

  /**
   * FIXME use orm
   * @return mixed
   */
  public function getAllUpcomingGames() {

    $db = $this->getDb();
    $sql = "SELECT  m.id,
                        m.score_away,
                        m.fixture,
                        m.score_home,
                        m.location,
                        l.id AS league_id,
                        ht.name AS homename,
                        at.name AS awayname,
                        at.id AS awayid,
                        ht.id AS homeid
                FROM    " . $db->getPrefix() . "leagues AS l,
                        " . $db->getPrefix() . "seasons AS s,
                        " . $db->getPrefix() . "game_days AS gd,
                        " . $db->getPrefix() . "matches AS m,
                        " . $db->getPrefix() . "teams AS at,
                        " . $db->getPrefix() . "teams AS ht
                WHERE     s.id = l.current_season
                AND     l.active = 1
                AND     gd.id = s.current_game_day
                AND     m.game_day_id = gd.id
                AND     at.id = m.away_team
                AND     ht.id = m.home_team ORDER BY l.name ASC";

    return $db->get_results($sql);

  }

  /**
   * FIXME use orm
   * @param $league_id
   * @return mixed
   */
  public function getUpcomingGames($league_id) {

    $db = $this->getDb();
    $sql = "SELECT 	m.id,
						m.score_away,
						m.fixture,
						m.score_home,
						m.location,
            m.status,
						ht.name AS homename,
						at.name AS awayname,
						at.id AS awayid,
						ht.id AS homeid
				FROM 	" . $db->getPrefix() . "leagues AS l,
						" . $db->getPrefix() . "seasons AS s,
						" . $db->getPrefix() . "game_days AS gd,
						" . $db->getPrefix() . "matches AS m,
						" . $db->getPrefix() . "teams AS at,
						" . $db->getPrefix() . "teams AS ht
				WHERE 	l.id = '%s'
				AND 	s.id = l.current_season
				AND 	gd.id = s.current_game_day
				AND 	m.game_day_id = gd.id
				AND 	at.id = m.away_team
				AND 	ht.id = m.home_team";

    $query = $db->prepare($sql, $league_id);
    return $db->get_results($query);

  }

  /**
   * FIXME use orm
   * @param $teamid
   * @return mixed
   */
  public function getGamesForTeam($teamid) {

    $db = $this->getDb();

    $sql = "SELECT  m.id,
                        m.score_away,
                        m.fixture,
                        m.score_home,
                        m.location,
                        ht.name AS homename,
                        at.name AS awayname,
                        at.id AS awayid,
                        ht.id AS homeid
                FROM    " . $db->getPrefix() . "game_days AS gd,
                        " . $db->getPrefix() . "matches AS m,
                        " . $db->getPrefix() . "teams AS at,
                        " . $db->getPrefix() . "teams AS ht
                WHERE   (m.home_team = '" . esc_sql($teamid) . "' OR m.away_team = '" . esc_sql($teamid) . "')
                AND     m.game_day_id = gd.id
                AND     at.id = m.away_team
                AND     ht.id = m.home_team
                ORDER BY m.fixture ASC";

    $query = $db->prepare($sql, array());
    return $db->get_results($query);

  }

  /**
   * @return mixed
   * @deprecated use orm
   */
  public function getAllGamesForNextGameday() {

    $db = $this->getDb();
    $sql = "SELECT  m.id,
                        m.score_away,
                        m.fixture,
                        m.score_home,
                        m.location,
                        l.id AS league_id,
                        ht.name AS homename,
                        at.name AS awayname,
                        at.id AS awayid,
                        ht.id AS homeid
                FROM    " . $db->getPrefix() . "leagues AS l,
                        " . $db->getPrefix() . "matches AS m,
                        " . $db->getPrefix() . "teams AS at,
                        " . $db->getPrefix() . "teams AS ht
                JOIN " . $db->getPrefix() . "seasons AS s
                JOIN " . $db->getPrefix() . "game_days AS cgd ON cgd.id = s.current_game_day
                JOIN " . $db->getPrefix() . "game_days AS gd ON gd.season_id = s.id AND gd.number = (cgd.number + 1)
                WHERE   l.active = 1
                AND     s.id = l.current_season
                AND     m.game_day_id = gd.id
                AND     at.id = m.away_team
                AND     ht.id = m.home_team ORDER BY l.name ASC";

    return $db->get_results($sql);

  }

  /**
   * @return Wordpress
   * @deprecated use orm layer
   */
  private function getDb() {
    return new Wordpress();
  }

}