<?php

class KKL_Tasks_NewGameDay {

	public static function execute() {

		error_log("RUNNING GAMEDAY CHECKER:");

		$db = new KKL_DB();		

		$leagues = $db->getLeagues();
		foreach($leagues as $league) {
			error_log("checking league: " . utf8_decode($league->name)); 
			$current_season = $db->getSeason($league->current_season);
			if($current_season && $current_season->active) {
				error_log("- current season is " . utf8_decode($current_season->name));
				$current_day = $db->getGameDay($current_season->current_game_day);
				if($current_day) {
					error_log("-- current day is " . $current_day->number);
					$next_day = $db->getNextGameDay($current_day);
					$now = time();
					if($next_day->fixture) {
						error_log("-- next day is " . $next_day->number);
						$fixture = strtotime($next_day->fixture);
						error_log("--- now: " . $now);
						error_log("--- fixture: " . $fixture);
						error_log("--- diff: " . ($now - $fixture));
						if($fixture < $now) {
							error_log("--- setting next day");
							$db->setCurrentGameDay($current_season, $next_day);
						}
					}
				}
			}
		}
	}

}

?>
