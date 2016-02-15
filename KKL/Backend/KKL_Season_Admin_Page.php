<?php

class KKL_Season_Admin_Page extends KKL_Admin_Page {

        function get_item() {
                if($this->item) return $this->item;
                if($_GET['id']) {
                        $db = new KKL_DB();
                        $this->setItem($db->getSeason($_GET['id']));
                }
                return $this->item;

        }

        function setup() {
                $this->args = array(
                        'page_title' => __('season', 'kkl-ligatool'),
                        'page_slug' => 'kkl_seasons_admin_page',
                        'parent' => NULL                    
                );
        }

        function display_content() {

                $season = $this->get_item();

                $db = new KKL_DB();
                $leagues = $db->getLeagues();
                $season_options = array("" => __('please_select', 'kkl-ligatool'));
                foreach($leagues as $league) {
                        $league_options[$league->id] = $league->name;
                }

                $days = $db->getGameDaysForSeason($season->id);
                $day_options = array("" => __('please_select', 'kkl-ligatool'));
                foreach($days as $day) {
                        $day_options[$day->id] = 'Spieltag ' . $day->number;
                }

                $active_checked = ($season->active == 1);
                if($this->errors && $_POST['active']) {
                    $active_checked = true;
                } 

                echo $this->form_table( array(
                    array(
                                'type' => 'hidden',
                                'name' => 'id',
                                'value' => $season->id
                        ),
                        array(
                                'title' => __('name', 'kkl-ligatool'),
                                'type' => 'text',
                                'name' => 'name',
                                'value' => ($this->errors) ? $_POST['name'] : $season->name,
                                'extra' => ($this->errors['name']) ? array('style' => "border-color: red;") : array()
                        ),
                        array(
                                'title' => __('league', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'league',
                                'choices' => $league_options,
                                'selected' => ($this->errors) ? $_POST['league'] : $season->league_id,
                                'extra' => ($this->errors['league']) ? array('style' => "border-color: red;") : array()
                        ),
                        array(
                                'title' => __('start_date', 'kkl-ligatool'),
                                'type' => 'text',
                                'name' => 'start_date',
                                'value' => $season->start_date,
                                'extra' => array('class' => 'datetimepicker')
                        ),
                        array(
                                'title' => __('end_date', 'kkl-ligatool'),
                                'type' => 'text',
                                'name' => 'end_date',
                                'value' => $season->end_date,
                                'extra' => array('class' => 'datetimepicker')
                        ),
                        array(
                                'title' => __('current_game_day', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'current_game_day',
                                'choices' => $day_options,
                                'selected' => $season->current_game_day
                        ),
                         array(
                                'title' => __('active', 'kkl-ligatool'),
                                'type' => 'checkbox',
                                'name' => 'active',
                                'checked' => $active_checked
                        )
                ) );
        }

        function validate($new_data, $old_data) {
                $errors = array();

                if(!$new_data['name']) $errors['name'] = true;
                if(!$new_data['league']) $errors['league'] = true;

                return $errors;
        }

        function save() {

            $start_date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $_POST['start_date'])));
            $end_date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $_POST['end_date'])));

            $season = new stdClass;
            $season->id = $_POST['id'];
            $season->name = $_POST['name'];
            $season->start_date = $start_date;
            $season->end_date = $end_date;
            $season->active = ($_POST['active']) ? 1 : 0;
            $season->current_game_day = $_POST['current_game_day'];
            $season->league_id = $_POST['league'];
           
            $db = new KKL_DB();
            $season = $db->createOrUpdateSeason($season);

            return $season;

        }

}
