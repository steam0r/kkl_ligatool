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
use KKL\Ligatool\Model\Player;
use KKL\Ligatool\ServiceBroker;

class PlayerService extends KKLModelService {

  /**
   * @return Player
   */
  public function getModel() {
    return new Player();
  }

  /**
   * @param int $id
   * @return Player|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return Player[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return Player[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return Player|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }

  /**
   * @param $key
   * @param $value
   * @return Player[]
   */
  public function findByProperty($key, $value = false) {
    $playerPropertyService = ServiceBroker::getPlayerPropertyService();
    $wheres = [new Where('property_key', $key, '=')];
    if ($value) {
      $wheres[] = new Where('value', $value, '=');
    }
    $properties = $playerPropertyService->find();
    $playerIds = [];
    foreach ($properties as $property) {
      $playerIds[] = $property->getObjectId();
    }
    $result = [];
    if (!empty($playerIds)) {
      $result = $this->find(new Where('ID', $playerIds, 'IN'));
    }
    return $result;
  }

  /**
   * @param $mailAddress
   * @return Player|null
   */
  public function byMailAddress($mailAddress) {
    $playerService = ServiceBroker::getPlayerService();
    return $playerService->findOne(new Where('email', $mailAddress, '='));
  }

  public function getLeagueAdmins() {
    return $this->findByProperty('member_ligaleitung', 'true');
  }

  /**
   * @return array
   * @deprecated use orm
   */
  public function getCaptainsContactData() {
    $db = $this->getDb();
    $sql = "SELECT " .
      "p.first_name as first_name, p.last_name as last_name, p.email as email, p.phone as phone, " .
      "t.name as team, t.short_name as team_short, " .
      "l.name as league, l.code as league_short, loc.title as location " .
      "FROM " . $db->getPrefix() . "team_properties AS tp " .
      "JOIN " . $db->getPrefix() . "players AS p ON p.id = tp.value " .
      "JOIN " . $db->getPrefix() . "teams AS t ON t.id = tp.objectId " .
      "JOIN " . $db->getPrefix() . "seasons AS s ON s.id = t.season_id AND s.active = '1' " .
      "JOIN " . $db->getPrefix() . "leagues AS l ON l.id = s.league_id " .
      "LEFT JOIN " . $db->getPrefix() . "team_properties AS lp ON t.id = lp.objectId AND lp.property_key = 'location' " .
      "LEFT JOIN " . $db->getPrefix() . "locations AS loc ON loc.id = lp.value " .
      "WHERE tp.property_key = 'captain' ";
    $data = $db->get_results($sql);
    $captains = array();
    foreach ($data as $d) {
      $d->role = 'captain';
      $captains[] = $d;
    }

    return $captains;
  }

  /**
   * @return array
   * @deprecated use orm
   */
  public function getViceCaptainsContactData() {
    $db = $this->getDb();
    $sql = "SELECT " .
      "p.first_name as first_name, p.last_name as last_name, p.email as email, p.phone as phone, " .
      "t.name as team, t.short_name as team_short, " .
      "l.name as league, l.code as league_short, loc.title as location " .
      "FROM " . $db->getPrefix() . "team_properties AS tp " .
      "JOIN " . $db->getPrefix() . "players AS p ON p.id = tp.value " .
      "JOIN " . $db->getPrefix() . "teams AS t ON t.id = tp.objectId " .
      "JOIN " . $db->getPrefix() . "seasons AS s ON s.id = t.season_id AND s.active = '1' " .
      "JOIN " . $db->getPrefix() . "leagues AS l ON l.id = s.league_id " .
      "LEFT JOIN " . $db->getPrefix() . "team_properties AS lp ON t.id = lp.objectId AND lp.property_key = 'location' " .
      "LEFT JOIN " . $db->getPrefix() . "locations AS loc ON loc.id = lp.value " .
      "WHERE tp.property_key = 'vice_captain' ";
    $data = $db->get_results($sql);
    $captains = array();
    foreach ($data as $d) {
      $d->role = 'vice_captain';
      $captains[] = $d;
    }

    return $captains;
  }

  /**
   * @return Wordpress
   * @deprecated use orm layer
   */
  private function getDb() {
    return new Wordpress();
  }

}