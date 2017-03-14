<?php

class KKL_Player_Admin_Page extends KKL_Admin_Page {

        private $team;

        function get_item() {
                if($this->item) return $this->item;
                if($_GET['id']) {
                        $db = new KKL_DB();
                        $this->setItem($db->getPlayer($_GET['id']));
                }
                return $this->item;

        }

        function setup() {

                $this->args = array(
                        'page_title' => __('player', 'kkl-ligatool'),
                        'page_slug' => 'kkl_players_admin_page',
                        'parent' => NULL                    
                );
        }

        function display_content() {

					$player = $this->get_item();

                $next = array();
                $next[0] = __('back to overview', 'kkl-ligatool');
                $next[1] = __('create new player', 'kkl-ligatool');

                $ligaleitung_checked = ($player->properties && array_key_exists('member_ligaleitung', $player->properties));
                if($this->errors && $_POST['member_ligaleitung']) {
                    $ligaleitung_checked = true;
                } 

								$slack_alias = "";
								if($player->properties && array_key_exists('slack_alias', $player->properties)) {
									$slack_alias = $player->properties['slack_alias'];
								
								}
                if($this->errors && $_POST['slack_alias']) {
                    $slack_alias = $_POST['slack_alias'];
                } 

                echo $this->form_table( array(
                        array(
                                'type' => 'hidden',
                                'name' => 'id',
                                'value' => $player->id
                        ),
                        array(
                                'title' => __('firstname', 'kkl-ligatool'),
                                'type' => 'text',
                                'name' => 'first_name',
                                'value' => ($this->errors) ? $_POST['first_name'] : $player->first_name,
                                'extra' => ($this->errors['first_name']) ? array('style' => "border-color: red;") : array()
                        ),
                        array(
                                'title' => __('lastname', 'kkl-ligatool'),
                                'type' => 'text',
                                'name' => 'last_name',
                                'value' => ($this->errors) ? $_POST['last_name'] : $player->last_name,
                                'extra' => ($this->errors['last_name']) ? array('style' => "border-color: red;") : array()
                        ),
												array(
                                'title' => __('email', 'kkl-ligatool'),
                                'type' => 'text',
                                'name' => 'email',
                                'value' => ($this->errors) ? $_POST['email'] : $player->email,
                                'extra' => ($this->errors['email']) ? array('style' => "border-color: red;") : array()
                        ),
												array(
                                'title' => __('member_ligaleitung', 'kkl-ligatool'),
                                'type' => 'checkbox',
                                'name' => 'member_ligaleitung',
                                'checked' => $ligaleitung_checked
												),
												array(
                                'title' => __('slack_alias', 'kkl-ligatool'),
                                'type' => 'text',
                                'name' => 'slack_alias',
                                'value' => ($this->errors) ? $_POST['slack_alias'] : $slack_alias,
                                'extra' => ($this->errors['slack_alias']) ? array('style' => "border-color: red;") : array()
                        ),
                        array(
                                'title' => __('next_page', 'kkl-ligatool'),
                                'type' => 'select',
                                'name' => 'next_page',
                                'value' => $next,
                                'selected' => ($this->errors) ? $_POST['next_page'] : null,
                        ),
               ) );
        }

        function validate($new_data, $old_data) {
                $errors = array();

                if(!$new_data['first_name']) $errors['name'] = true;
                if(!$new_data['last_name']) $errors['short_name'] = true;
                if(!$new_data['email']) $errors['season'] = true;

                return $errors;
        }

        function save() {

                $player = new stdClass;
                $player->id = $_POST['id'];
                $player->first_name = $_POST['first_name'];
                $player->last_name = $_POST['last_name'];
                $player->email = $_POST['email'];
               
                $db = new KKL_DB();
                $player = $db->createOrUpdatePlayer($player);

                $properties = array();
                $properties['member_ligaleitung'] = false;
                $properties['slack_alias'] = false;
                if($_POST['member_ligaleitung']) $properties['member_ligaleitung'] = "true";
                if($_POST['slack_alias']) $properties['slack_alias'] = $_POST['slack_alias'];

                if(!empty($properties)) $db->setPlayerProperties($player, $properties);

                return $db->getPlayer($player->id);

				}

        function redirect_after_save() {
            $next_page = $_POST['next_page'];
            if($next_page && $next_page == 1) {
                $page = menu_page_url("kkl_players_admin_page", false);
            }else{
                $page = menu_page_url("kkl_ligatool_players", false);
            }

            wp_redirect($page);
            exit();
        }


}
