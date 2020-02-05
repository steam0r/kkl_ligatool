<?php


namespace KKL\Ligatool\Model;


class Schedule {

  private $matches = array();
  private $number;
  private $day;

  /**
   * @return Match[]
   */
  public function getMatches() {
    return $this->matches;
  }

  /**
   * @param array $matches
   */
  public function setMatches($matches) {
    $this->matches = $matches;
  }

  /**
   * @return mixed
   */
  public function getNumber() {
    return $this->number;
  }

  /**
   * @param mixed $number
   */
  public function setNumber($number) {
    $this->number = $number;
  }

  /**
   * @return mixed
   */
  public function getDay() {
    return $this->day;
  }

  /**
   * @param mixed $day
   */
  public function setDay($day) {
    $this->day = $day;
  }

}