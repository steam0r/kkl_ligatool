<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 16.05.19
 * Time: 10:43
 */

namespace KKL\Ligatool\Services;


use KKL\Ligatool\DB\Where;
use KKL\Ligatool\Model\Team;
use KKL\Ligatool\Model\TeamProperty;
use KKL\Ligatool\ServiceBroker;

class TeamPropertyService extends KKLModelService {

  /**
   * @return TeamProperty
   */
  public function getModel() {
    return new TeamProperty();
  }

  /**
   * @param int $id
   * @return TeamProperty|false
   */
  public function byId($id) {
    return parent::byId($id);
  }

  /**
   * @param null $orderBy
   * @return TeamProperty[]
   */
  public function getAll($orderBy = null) {
    return parent::getAll($orderBy);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return TeamProperty[]
   */
  public function find($where = null, $orderBy = null, $limit = null) {
    return parent::find($where, $orderBy, $limit);
  }

  /**
   * @param null $where
   * @param null $orderBy
   * @param null $limit
   * @return TeamProperty|null
   */
  public function findOne($where = null, $orderBy = null, $limit = null) {
    return parent::findOne($where, $orderBy, $limit);
  }

  /**
   * @param $key
   * @return TeamProperty[]
   */
  public function byKey($key) {
    return $this->find(new Where('property_key', $key));
  }

  /**
   * @param null $teamId
   * @return array
   */
  public function byTeam($teamId) {
    $results = $this->find(new Where('objectId', $teamId, '='));
    $playerService = ServiceBroker::getPlayerService();
    $locationService = ServiceBroker::getLocationService();
    $properties = array();
    foreach ($results as $result) {
      $properties[$result->getPropertyKey()] = $result->getValue();
      if ($result->getPropertyKey() == 'captain') {
        $player = $playerService->byId($result->getValue());
        $properties['captain_email'] = $player->getEmail();
      } elseif ($result->getPropertyKey() == 'vice_captain') {
        $player = $playerService->byId($result->getValue());
        $properties['vice_captain_email'] = $player->getEmail();
      } else {
        $location = $locationService->byId($result->getValue());
        $properties['location_name'] = $location->getTitle();
        $properties['lat'] = $location->getLatitude();
        $properties['lng'] = $location->getLongitude();
      }
    }
    return $properties;
  }

  /**
   * @param $teamId
   * @param $key
   * @return TeamProperty|null
   */
  public function byTeamAndKey($teamId, $key) {
    return $this->findOne([
      new Where('objectId', $teamId),
      new Where('property_key', $key)
    ]);
  }

  /**
   * @param Team $team
   * @param array $properties
   */
  public function setTeamProperties($team, $properties) {
    foreach ($properties as $key => $value) {
      $prop = $this->byTeamAndKey($team->getId(), $key);
      if ($prop) {
        $prop->delete();
      }
      if ($value !== false) {
        $newProp = new TeamProperty();
        $newProp->setObjectId($team->getId());
        $newProp->setPropertyKey($key);
        $newProp->setValue($value);
        $newProp->save();
      }
    }
  }
}