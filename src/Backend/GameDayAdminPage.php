<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\DB\OrderBy;
use KKL\Ligatool\Model\GameDay;
use KKL\Ligatool\ServiceBroker;

class GameDayAdminPage extends AdminPage {

  function setup() {
    $this->args = array(
      'page_title' => __('game_day', 'kkl-ligatool'),
      'page_slug' => 'kkl_game_days_admin_page',
      'parent' => null
    );
  }

  function display_content() {

    $day = $this->get_item();

    $number_options = array("" => __('please_select', 'kkl-ligatool'));
    for ($i = 1; $i < 21; $i++) {
      $number_options[$i] = $i;
    }

    $seasonService = ServiceBroker::getSeasonService();
    $seasons = $seasonService->getAll(new OrderBy('start_date', 'ASC'));
    $season_options = array("" => __('please_select', 'kkl-ligatool'));
    foreach ($seasons as $season) {
      $season_options[$season->getId()] = $season->getName();
    }

    echo $this->form_table(
      array(
        array(
          'type' => 'hidden',
          'name' => 'id',
          'value' => $day->getId()
        ),
        array(
          'title' => __('number', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'number',
          'choices' => $number_options,
          'selected' => ($this->errors) ? $_POST['season'] : $day->getNumber(),
          'extra' => ($this->errors['number']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('season', 'kkl-ligatool'),
          'type' => 'select',
          'name' => 'season',
          'choices' => $season_options,
          'selected' => ($this->errors) ? $_POST['season'] : $day->getSeasonId(),
          'extra' => ($this->errors['season']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('start_date', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'start_date',
          'extra' => array('class' => 'datetimepicker'),
          'value' => $this->cleanDate($day->getFixture())
        ),
        array(
          'title' => __('end_date', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'end_date',
          'extra' => array('class' => 'datetimepicker'),
          'value' => $day->getEnd())
      )
    );
  }

  /**
   * @return GameDay|null
   */
  function get_item() {
    if ($this->item)
      return $this->item;
    if ($_GET['id']) {
      $gameDayService = ServiceBroker::getGameDayService();
      $this->setItem($gameDayService->byId($_GET['id']));
    } else {
      $this->setItem(new GameDay());
    }
    return $this->item;

  }

  function validate($new_data, $old_data) {
    $errors = array();

    if (!$new_data['number'])
      $errors['number'] = true;
    if (!$new_data['season'])
      $errors['season'] = true;

    return $errors;
  }

  function save() {

    $start_date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $_POST['start_date'])));
    $end_date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $_POST['end_date'])));

    $service = ServiceBroker::getGameDayService();
    $day = $service->getOrCreate($_POST['id']);
    $day->setNumber($_POST['number']);
    $day->setStart($start_date);
    $day->setEnd($end_date);
    $day->setSeasonId($_POST['season']);

    $day = $service->createOrUpdate($day);

    return $day;

  }

}
