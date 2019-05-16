<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 17:54
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"code", "name"}, type="object")
 * @ORM_Type              Entity
 * @ORM_Table "kkl_club_has_awards"
 * @ORM_AllowSchemaUpdate True
 */
class ClubAward extends KKLModel {

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $club_id;

  /**
   * @var int
   * @SWG\Property(format="int64")
   * @ORM_Column_Type   int
   * @ORM_Column_Null   NULL
   */
  protected $award_id;

  /**
   * @return int
   */
  public function getClubId() {
    return $this->club_id;
  }

  /**
   * @param int $club_id
   */
  public function setClubId($club_id) {
    $this->club_id = $club_id;
  }

  /**
   * @return int
   */
  public function getAwardId() {
    return $this->award_id;
  }

  /**
   * @param int $award_id
   */
  public function setAwardId($award_id) {
    $this->award_id = $award_id;
  }


}
