<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 27.01.18
 * Time: 18:47
 */

namespace KKL\Ligatool\Model;

/**
 * @SWG\Definition(required={"title"}, type="object")
 */
class Location extends BaseModel {

    /**
     * @var string
     * @SWG\Property()
     */
    private $title;

    /**
     * @var string
     * @SWG\Property()
     */
    private $description;

    /**
     * @var string
     * @SWG\Property()
     */
    private $latitude;

    /**
     * @var string
     * @SWG\Property()
     */
    private $longitude;


}
