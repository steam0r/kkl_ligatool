<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\DB;
use KKL\Ligatool\Model\Player;
use stdClass;

class PlayerAdminPage extends AdminPage {

  function setup() {

    $this->args = array(
      'page_title' => __('player', 'kkl-ligatool'),
      'page_slug' => 'kkl_players_admin_page',
      'parent' => null
    );
  }

  function display_content() {

    $player = $this->get_item();

    $next = array();
    $next[0] = __('back to overview', 'kkl-ligatool');
    $next[1] = __('create new player', 'kkl-ligatool');

    $ligaleitung_checked = false;
    $ligaleitungProperty = $player->getProperty('member_ligaleitung');
    if ($ligaleitungProperty) {
      $ligaleitung_checked = $ligaleitungProperty->getValue();
    }
    if ($this->errors && $_POST['member_ligaleitung']) {
      $ligaleitung_checked = true;
    }

    $slack_alias = "";
    $slackProperty = $player->getProperty('slack_alias');
    if ($slackProperty) {
      $slack_alias = $slackProperty->getValue();
    }
    if ($this->errors && $_POST['slack_alias']) {
      $slack_alias = $_POST['slack_alias'];
    }

    $ligaleitung_address = "";
    $llAddressProperty = $player->getProperty('ligaleitung_address');
    if ($llAddressProperty) {
      $ligaleitung_address = $llAddressProperty->getValue();
    }
    if ($this->errors && $_POST['ligaleitung_address']) {
      $ligaleitung_address = $_POST['ligaleitung_address'];
    }

    echo $this->form_table(
      array(
        array(
          'type' => 'hidden',
          'name' => 'id',
          'value' => $player->getId()
        ),
        array(
          'title' => __('firstname', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'first_name',
          'value' => ($this->errors) ? $_POST['first_name'] : $player->getFirstName(),
          'extra' => ($this->errors['first_name']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('lastname', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'last_name',
          'value' => ($this->errors) ? $_POST['last_name'] : $player->getLastName(),
          'extra' => ($this->errors['last_name']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('email', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'email',
          'value' => ($this->errors) ? $_POST['email'] : $player->getEmail(),
          'extra' => ($this->errors['email']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('phone', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'phone',
          'value' => ($this->errors) ? $_POST['phone'] : $player->getPhone(),
          'extra' => ($this->errors['phone']) ? array('style' => "border-color: red;") : array()
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
          'title' => __('ligaleitung_address', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'ligaleitung_address',
          'value' => ($this->errors) ? $_POST['ligaleitung_address'] : $ligaleitung_address,
          'extra' => ($this->errors['ligaleitung_address']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('next_page', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'next_page',
          'value' => $next,
          'selected' => ($this->errors) ? $_POST['next_page'] : null
        )
      )
    );
  }

  /**
   * @return Player|null
   */
  function get_item() {
    if ($this->item)
      return $this->item;
    if ($_GET['id']) {
      $db = new DB\Wordpress();
      $this->setItem($db->getPlayer($_GET['id']));
    }
    return $this->item;

  }

  function validate($new_data, $old_data) {
    $errors = array();

    if (!$new_data['first_name'])
      $errors['name'] = true;
    if (!$new_data['last_name'])
      $errors['short_name'] = true;

    return $errors;
  }

  function save() {

    $player = new stdClass;
    $player->ID = $_POST['id'];
    $player->first_name = $_POST['first_name'];
    $player->last_name = $_POST['last_name'];
    $player->email = $_POST['email'];
    $player->phone = $_POST['phone'];

    $db = new DB\Wordpress();
    $player = $db->createOrUpdatePlayer($player);

    $properties = array();
    $properties['member_ligaleitung'] = false;
    $properties['slack_alias'] = false;
    $properties['ligaleitung_address'] = false;
    if ($_POST['member_ligaleitung'])
      $properties['member_ligaleitung'] = "true";
    if ($_POST['slack_alias'])
      $properties['slack_alias'] = $_POST['slack_alias'];
    if ($_POST['ligaleitung_address'])
      $properties['ligaleitung_address'] = $_POST['ligaleitung_address'];

    if (!empty($properties))
      $db->setPlayerProperties($player, $properties);

    return $db->getPlayer($player->ID);

  }

  function redirect_after_save() {
    $next_page = $_POST['next_page'];
    if ($next_page && $next_page == 1) {
      $page = menu_page_url("kkl_players_admin_page", false);
    } else {
      $page = menu_page_url("kkl_ligatool_players", false);
    }

    wp_redirect($page);
    exit();
  }


}
