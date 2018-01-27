<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 17:57
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"name", "league", "startDate", "endDate"}, type="object")
 */
class Season extends BaseModel {

    /**
     * @var boolean
     * @SWG\Property()
     */
    private $active;

    /**
     * @var string
     * @SWG\Property(example="1. Liga")
     */
    private $name;

    /**
     * @SWG\Property(example="1980-09-02 05:11:42")
     * @var string
     */
    private $startDate;

    /**
     * @SWG\Property(example="1980-09-02 05:11:42")
     * @var string
     */
    private $endDate;

    /**
     * @SWG\Property(format="int64")
     * @var int
     */
    private $league;

    /**
     * @SWG\Property(format="int64")
     * @var int
     */
    private $currentGameDay;

}
