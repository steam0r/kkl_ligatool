<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\DB;
use KKL\Ligatool\Model\Club;
use KKL\Ligatool\Model\League;
use stdClass;

class LeagueAdminPage extends AdminPage {


  function setup() {
    $this->args = array(
      'page_title' => __('league', 'kkl-ligatool'),
      'page_slug' => 'kkl_leagues_admin_page',
      'parent' => null
    );
  }

  function display_content() {

    $league = $this->get_item();

    $db = new DB\Wordpress();
    $seasons = $db->getSeasons();
    $season_options = array("" => __('please_select', 'kkl-ligatool'));
    foreach ($seasons as $season) {
      $season_options[$season->getId()] = $season->getName();
    }

    $active_checked = $league->isActive();
    if ($this->errors && $_POST['active']) {
      $active_checked = true;
    }

    echo $this->form_table(
      array(
        array(
          'type' => 'hidden',
          'name' => 'id',
          'value' => $league->getId()
        ),
        array(
          'title' => __('name', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'name',
          'value' => ($this->errors) ? $_POST['name'] : $league->getName(),
          'extra' => ($this->errors['name']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('url_code', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'url_code',
          'value' => ($this->errors) ? $_POST['url_code'] : $league->getCode(),
          'extra' => ($this->errors['url_code']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('active', 'kkl-ligatool'),
          'type' => 'checkbox',
          'name' => 'active',
          'checked' => $active_checked
        ),
        array(
          'title' => __('current_season', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'season',
          'choices' => $season_options,
          'selected' => ($this->errors) ? $_POST['season'] : $league->getCurrentSeason(),
          'extra' => ($this->errors['season']) ? array('style' => "border-color: red;") : array()
        )
      )
    );
  }

  /**
   * @return League
   */
  function get_item() {
    if ($this->item)
      return $this->item;
    if ($_GET['id']) {
      $db = new DB\Wordpress();
      $this->setItem($db->getLeague($_GET['id']));
    } else{
      $this->setItem(new League());
    }
    return $this->item;

  }

  function validate($new_data, $old_data) {
    $errors = array();

    if (!$new_data['name'])
      $errors['name'] = true;
    if (!$new_data['url_code'])
      $errors['url_code'] = true;

    return $errors;
  }

  function save() {

    $league = new stdClass;
    $league->ID = $_POST['id'];
    $league->name = $_POST['name'];
    $league->code = $_POST['url_code'];
    $league->active = ($_POST['active']) ? 1 : 0;
    $league->current_season = $_POST['season'];

    $db = new DB\Wordpress();
    $league = $db->createOrUpdateLeague($league);

    return $league;

  }

}
