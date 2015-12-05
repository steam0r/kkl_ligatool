<?php

class KKL_GameDay_Admin_Page extends KKL_Admin_Page {

        function get_item() {
                if($this->item) return $this->item;
                if($_GET['id']) {
                        $db = new KKL_DB();
                        $this->setItem($db->getGameDay($_GET['id']));
                }
                return $this->item;

        }

        function setup() {
                $this->args = array(
                        'page_title' => __('game_day', 'kkl-ligatool'),
                        'page_slug' => 'kkl_game_days_admin_page',
                        'parent' => NULL                    
                );
        }

        function display_content() {

            $day = $this->get_item();

            $number_options = array("" => __('please_select', 'kkl-ligatool'));
            for($i = 1; $i < 21; $i++) {
                $number_options[$i] = $i;
            }

            $db = new KKL_DB();
            $seasons = $db->getSeasons();
            $season_options = array("" => __('please_select', 'kkl-ligatool'));
            foreach($seasons as $season) {
                    $season_options[$season->id] = $season->name;
            }

            echo $this->form_table( array(
                    array(
                                'type' => 'hidden',
                                'name' => 'id',
                                'value' => $day->id
                    ),
                    array(
                            'title' => __('number', 'kkl-ligatool'),
                            'type' => 'select',
                            'name' => 'number',
                            'choices' => $number_options,
                            'selected' => ($this->errors) ? $_POST['season'] : $day->number,
                            'extra' => ($this->errors['number']) ? array('style' => "border-color: red;") : array()
                    ),
                    array(
                            'title' => __('season', 'kkl-ligatool'),
                            'type' => 'select',
                            'name' => 'season',
                            'choices' => $season_options,
                            'selected' => ($this->errors) ? $_POST['season'] : $day->season_id,
                            'extra' => ($this->errors['season']) ? array('style' => "border-color: red;") : array()
                    ),
                    array(
                            'title' => __('start_date', 'kkl-ligatool'),
                            'type' => 'text',
                            'name' => 'start_date',
                            'extra' => array('class' => 'datetimepicker'),
                            'value' => $day->fixture
                    ),
                    array(
                            'title' => __('end_date', 'kkl-ligatool'),
                            'type' => 'text',
                            'name' => 'end_date',
                            'extra' => array('class' => 'datetimepicker'),
                            'value' => $day->end
                    )
            ) );
        }

        function validate($new_data, $old_data) {
                $errors = array();

                if(!$new_data['number']) $errors['number'] = true;
                if(!$new_data['season']) $errors['season'] = true;

                return $errors;
        }

        function save() {

            $start_date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $_POST['start_date'])));
            $end_date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $_POST['end_date'])));

            $day = new stdClass;
            $day->id = $_POST['id'];
            $day->number = $_POST['number'];
            $day->start_date =  $start_date;
            $day->end_date = $end_date;
            $day->season_id = $_POST['season'];
           
            $db = new KKL_DB();
            $day = $db->createOrUpdateGameDay($day);

            return $day;

        }

}
