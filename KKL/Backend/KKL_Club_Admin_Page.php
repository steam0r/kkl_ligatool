<?php

class KKL_Club_Admin_Page extends KKL_Admin_Page {

        function get_item() {
                if($this->item) return $this->item;
                if($_GET['id']) {
                        $db = new KKL_DB();
                        $this->setItem($db->getClub($_GET['id']));
                }
                return $this->item;

        }

        function setup() {
                $this->args = array(
                        'page_title' => __('club', 'kkl-ligatool'),
                        'page_slug' => 'kkl_clubs_admin_page',
                        'parent' => NULL                    
                );
        }

        function display_content() {

                $club = $this->get_item();

                echo $this->form_table( array(
                        array(
                                'type' => 'hidden',
                                'name' => 'id',
                                'value' => $club->id
                        ),
                        array(
                                'title' => __('name', 'kkl-ligatool'),
                                'type' => 'text',
                                'name' => 'name',
                                'value' => ($this->errors) ? $_POST['name'] : $club->name,
                                'extra' => ($this->errors['name']) ? array('style' => "border-color: red;") : array()
                        ),
                        array(
                                'title' => __('url_code', 'kkl-ligatool'),
                                'type' => 'text',
                                'name' => 'short_name',
                                'value' => ($this->errors) ? $_POST['short_name'] : $club->short_name,
                                'extra' => ($this->errors['short_name']) ? array('style' => "border-color: red;") : array()
                        ),
                        array(
                                'title' => __('description', 'kkl-ligatool'),
                                'type' => 'textarea',
                                'name' => 'description',
                                'value' => $club->description,
                                'extra' => array( 'rows' => 7, 'cols' => 100 )
                        )
                        /*
                        array(
                                'title' => __('image', 'kkl-ligatool'),
                                'type' => 'file',
                                'name' => 'image',
                        )
                        */
                ) );
        }

         function validate($new_data, $old_data) {
                $errors = array();

                if(!$new_data['name']) $errors['name'] = true;
                if(!$new_data['short_name']) $errors['short_name'] = true;

                return $errors;
        }

        function save() {

                $club = new stdClass;
                $club->id = $_POST['id'];
                $club->name = $_POST['name'];
                $club->short_name = $_POST['short_name'];
                $club->description = $_POST['description'];
               
                $db = new KKL_DB();
                $club = $db->createOrUpdateClub($club);

                return $club;

        }

}
