<?php

class KKL_Team_Admin_Page extends KKL_Admin_Page {

        private $team;

        function get_item() {
                if($this->item) return $this->item;
                if($_GET['id']) {
                        $db = new KKL_DB();
                        $this->setItem($db->getTeam($_GET['id']));
                }
                return $this->item;

        }

        function setup() {

                $this->args = array(
                        'page_title' => __('team', 'kkl-ligatool'),
                        'page_slug' => 'kkl_teams_admin_page',
                        'parent' => NULL                    
                );
        }

        function display_content() {

                $team = $this->get_item();

                $db = new KKL_DB();
                $seasons = $db->getSeasons();
                $season_options = array("" => __('please_select', 'kkl-ligatool'));
                foreach($seasons as $season) {
                        $season_options[$season->id] = $season->name;
                }

                $clubs = $db->getClubs();
                $club_options = array("" => __('please_select', 'kkl-ligatool'));
                foreach($clubs as $club) {
                        $club_options[$club->id] = $club->name;
                }

								$players = $db->getPlayers();
                $captain_options = array("" => __('please_select', 'kkl-ligatool'));
                foreach($players as $player) {
                        $captain_options[$player->id] = $player->first_name . " " . $player->last_name . " (" . $player->email . ")";
                }

                $locations = $db->getLocations();
                $location_options = array("" => __('please_select', 'kkl-ligatool'));
                foreach($locations as $location) {
                        $location_options[$location->id] = $location->title;
                }

                $leaguewinner_checked = ($team->properties && array_key_exists('current_league_winner', $team->properties));
                if($this->errors && $_POST['current_league_winner']) {
                    $leaguewinner_checked = true;
                } 

                $cupwinner_checked = ($team->properties && array_key_exists('current_cup_winner', $team->properties));
                if($this->errors && $_POST['current_cup_winner']) {
                    $cupwinner_checked = true;
                } 

                echo $this->form_table( array(
                        array(
                                'type' => 'hidden',
                                'name' => 'id',
                                'value' => $team->id
                        ),
                        array(
                                'title' => __('name', 'kkl-ligatool'),
                                'type' => 'text',
                                'name' => 'name',
                                'value' => ($this->errors) ? $_POST['name'] : $team->name,
                                'extra' => ($this->errors['name']) ? array('style' => "border-color: red;") : array()
                        ),
                        array(
                                'title' => __('url_code', 'kkl-ligatool'),
                                'type' => 'text',
                                'name' => 'short_name',
                                'value' => ($this->errors) ? $_POST['short_name'] : $team->short_name,
                                'extra' => ($this->errors['short_name']) ? array('style' => "border-color: red;") : array()
                        ),
                        array(
                                'title' => __('season', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'season',
                                'choices' => $season_options,
                                'selected' => ($this->errors) ? $_POST['season'] : $team->season_id,
                                'extra' => ($this->errors['season']) ? array('style' => "border-color: red;") : array()
                        ),
                        array(
                                'title' => __('club', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'club',
                                'choices' => $club_options,
                                'selected' => ($this->errors) ? $_POST['club'] : $team->club_id,
                                'extra' => ($this->errors['club']) ? array('style' => "border-color: red;") : array()
                        ),
                        array(
                                'title' => __('location', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'location',
                                'choices' => $location_options,
                                'selected' => ($this->errors) ? $_POST['location'] : $team->properties['location']
                        ), 
                        array(
                                'title' => __('captain', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'captain',
                                'choices' => $captain_options,
                                'selected' => ($this->errors) ? $_POST['captain'] : $team->properties['captain'],
                                'extra' => ($this->errors['captain']) ? array('style' => "border-color: red;") : array()
                        ),
                        array(
                                'title' => __('vice-captain', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'vice_captain',
                                'choices' => $captain_options,
                                'selected' => ($this->errors) ? $_POST['vice_captain'] : $team->properties['vice_captain'],
                                'extra' => ($this->errors['vice_captain']) ? array('style' => "border-color: red;") : array()
                        ),
                        array(
                                'title' => __('current_league_winner', 'kkl-ligatool'),
                                'type' => 'checkbox',
                                'name' => 'current_league_winner',
                                'checked' => $leaguewinner_checked
                        ),
                        array(
                                'title' => __('current_cup_winner', 'kkl-ligatool'),
                                'type' => 'checkbox',
                                'name' => 'current_cup_winner',
                                'checked' => $cupwinner_checked
                        )
                ) );
        }

        function validate($new_data, $old_data) {
                $errors = array();

                if(!$new_data['name']) $errors['name'] = true;
                if(!$new_data['short_name']) $errors['short_name'] = true;
                if(!$new_data['season']) $errors['season'] = true;
                if(!$new_data['club']) $errors['club'] = true;

                return $errors;
        }

        function save() {

                $team = new stdClass;
                $team->id = $_POST['id'];
                $team->name = $_POST['name'];
                $team->short_name = $_POST['short_name'];
                $team->season_id = $_POST['season'];
                $team->club_id = $_POST['club'];
               
                $db = new KKL_DB();
                $team = $db->createOrUpdateTeam($team);

                $properties = array();
                $properties['location'] = false;
                $properties['current_league_winner'] = false;
                $properties['current_cup_winner'] = false;
                $properties['captain'] = false;
                $properties['vice_captain'] = false;
                if($_POST['location']) $properties['location'] = $_POST['location'];
                if($_POST['captain']) $properties['captain'] = $_POST['captain'];
                if($_POST['vice_captain']) $properties['vice_captain'] = $_POST['vice_captain'];
                if($_POST['current_league_winner']) $properties['current_league_winner'] = "true";
                if($_POST['current_cup_winner']) $properties['current_cup_winner'] = "true";

                if(!empty($properties)) $db->setTeamProperties($team, $properties);

                return $db->getTeam($team->id);

				}

}
