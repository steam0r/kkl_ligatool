<?php

namespace KKL\Ligatool\Services;

class ScoringService {

  public static $LOSS = "loss";
  public static $DRAW = "draw";
  public static $WIN = "win";

  public function getPointsForMatchResult($key) {
    // TODO: maybe store this in database on a per season base, to make 3 point per win possible...
    switch ($key) {
      case self::$WIN:
        $score = 2;
        break;

      case self::$DRAW:
        $score = 1;
        break;

      case self::$LOSS:
      default:
        $score = 0;
        break;
    }
    return $score;
  }
}