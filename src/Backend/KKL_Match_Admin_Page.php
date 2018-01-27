<?php
namespace KKL\Ligatool\Backend;

use KKL\Ligatool\DB;
use stdClass;

class KKL_Match_Admin_Page extends KKL_Admin_Page {

        private $match;
        private $game_day;

        function get_item() {
                if($this->match) return $this->match;
                if($_GET['id']) {
                        $db = new DB\KKL_DB_Wordpress();
                        $this->match = $db->getMatch($_GET['id']);
                }
                return $this->match;
        }

        function get_game_day() {
                if($this->game_day) return $this->game_day;
                $item = $this->get_item();
                if($item) {
                        $db = new DB\KKL_DB_Wordpress();
                        $this->game_day = $db->getGameDay($item->game_day_id);
                }else if($_GET['gameDayId']) {
                        $db = new DB\KKL_DB_Wordpress();
                        $this->game_day = $db->getGameDay($_GET['gameDayId']);
                }
                return $this->game_day;
        }

        function get_game_days() {
                $db = new DB\KKL_DB_Wordpress();
                return $db->getGameDaysForSeason($this->get_game_day()->season_id);
        }

        function setup() {
                $this->args = array(
                        'page_title' => __('match', 'kkl-ligatool'),
                        'page_slug' => 'kkl_matches_admin_page',
                        'parent' => NULL
                );
        }

        function get_goals_array() {
                $goals = array();
                for($i = 0; $i <= 100; $i++) {
                        $goals[$i] = $i;
                }
                return $goals;
        }

         function get_score_array() {
                $score = array();
                for($i = 0; $i <= 20; $i++) {
                        $score[$i] = $i;
                }
                return $score;
        }

				function display_content() {

								$match = $this->get_item();

                $db = new DB\KKL_DB_Wordpress();
                $db_locations = $db->getLocations();
                $locations = array();
                $locations[0] = __('unknown location', 'kkl-ligatool');
                foreach($db_locations as $location) {
                        $locations[$location->id] = $location->title;
								}
								if(!$match->location && !$_POST['location']) {
									$home = $db->getTeam($match->home_team);
									if($home) {
										$match->location = $home->properties['location'];
									}
								}

                $current_game_day = $this->get_game_day();
                $db_game_days = $this->get_game_days();

                $game_days = array();
                foreach($db_game_days as $game_day) {
                        $game_days[$game_day->id] = $game_day->number;
                }

                $db_teams = $db->getTeamsForSeason($current_game_day->season_id);
                $teams = array();
                foreach($db_teams as $team) {
                        $teams[$team->id] = $team->name;
                }

                $db_games = $db->getMatchesByGameDay($current_game_day->id);
                $games = array();
                $games[0] = __('back to overview', 'kkl-ligatool');
                $games[1] = __('create new game', 'kkl-ligatool');
                foreach($db_games as $game) {
                    $home = $db->getTeam($game->home_team);
                    $away = $db->getTeam($game->away_team);
                    $games[$game->id] = $home->name . " - " . $away->name;
                }

                $final_checked = ($match->status == 3);
                if($this->errors && $_POST['final_score']) {
                    $final_checked = true;
                }

								$fixture = $this->cleanDate($match->fixture);
                echo $this->form_table( array(
                        array(
                                'type' => 'hidden',
                                'name' => 'id',
                                'value' => $match->id
                        ),
                        array(
                                'title' => __('game_day', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'game_day',
                                'value' => $game_days,
                                'selected' => ($this->errors) ? $_POST['game_day'] : $current_game_day->id,
                        ),
                        array(
                                'title' => __('fixture', 'kkl-ligatool'),
                                'type' => 'text',
                                'name' => 'fixture',
                                'value' => $fixture,
                                'extra' => array('class' => "pickfixture")
                        ),
                        array(
                                'title' => __('team_home', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'team_home',
                                'value' => $teams,
                                'selected' => ($this->errors) ? $_POST['team_home'] : $match->home_team,
                        ),
                        array(
                                'title' => __('team_away', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'team_away',
                                'value' => $teams,
                                'selected' => ($this->errors) ? $_POST['team_away'] : $match->away_team,
                        ),
                        array(
                                'title' => __('location', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'location',
																'value' => $locations,
                                'selected' => ($this->errors) ? $_POST['location'] : $match->location,
                        ),
                        array(
                                'title' => __('goals_home', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'goals_home',
                                'value' => $this->get_goals_array(),
                                'selected' => ($this->errors) ? $_POST['goals_home'] : $match->goals_home
                        ),
                        array(
                                'title' => __('goals_away', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'goals_away',
                                'value' => $this->get_goals_array(),
                                'selected' => ($this->errors) ? $_POST['goals_away'] : $match->goals_away
                        ),
                        array(
                                'title' => __('score_home', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'score_home',
                                'value' => $this->get_score_array(),
                                'selected' => ($this->errors) ? $_POST['score_home'] : $match->score_home
                        ),
                        array(
                                'title' => __('score_away', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'score_away',
                                'value' => $this->get_score_array(),
                                'selected' => ($this->errors) ? $_POST['score_away'] : $match->score_away
                        ),
                        array(
                                'title' => __('final_score', 'kkl-ligatool'),
                                'type' => 'checkbox',
                                'name' => 'final_score',
                                'checked' => $final_checked
                        ),
                        array(
                                'title' => __('description', 'kkl-ligatool'),
                                'type' => 'textarea',
                                'name' => 'description',
                                'value' => ($this->errors) ? $_POST['description'] : $match->notes,
                                'extra' => array( 'rows' => 7, 'cols' => 100 )
                        ),
                        array(
                                'title' => __('next_game', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'next_game',
                                'value' => $games,
                                'selected' => ($this->errors) ? $_POST['next_game'] : null,
                        ),
                ) );
        }

        function validate($new_data, $old_data) {
                $errors = array();

                if(!$new_data['game_day']) $errors['game_day'] = true;
                if(!$new_data['team_home']) $errors['team_home'] = true;
                if(!$new_data['team_away']) $errors['team_away'] = true;

                return $errors;
        }

        function save() {

                $match = new stdClass;
                $match->id = $_POST['id'];
                $match->game_day_id =  $_POST['game_day'];
                $match->fixture = $this->cleanDate($_POST['fixture']);
                $match->home_team = $_POST['team_home'];
                $match->away_team = $_POST['team_away'];
                $match->location = $_POST['location'];
                $match->goals_home = $_POST['goals_home'];
                $match->goals_away = $_POST['goals_away'];
                $match->score_home = $_POST['score_home'];
                $match->score_away = $_POST['score_away'];
                $match->notes = $_POST['description'];
                if($_POST['final_score']) {
                    $match->status = 3;
                }else{
                    $match->status = -1;
                }

                $db = new DB\KKL_DB_Wordpress();
                $this->match = $db->createOrUpdateMatch($match);

                return $this->match;

        }

        function redirect_after_save() {
            $next_game = $_POST['next_game'];
            if($next_game && $next_game > 1) {
                $page = menu_page_url("kkl_matches_admin_page", false);
                $page = $page . "&id=" . $next_game;
            }else if($next_game && $next_game == 1) {
                $page = menu_page_url("kkl_matches_admin_page", false);
                $page = $page . "&gameDayId=" . $this->match->game_day_id;
            }else{
                $page = menu_page_url("kkl_ligatool_matches", false);
                $page = $page . "&game_day_filter=" . $this->match->game_day_id;
            }

            wp_redirect($page);
            exit();
        }

}
