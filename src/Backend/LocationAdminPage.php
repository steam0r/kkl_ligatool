<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\Model\Location;
use KKL\Ligatool\ServiceBroker;

class LocationAdminPage extends AdminPage {

  function setup() {
    $this->args = array(
      'page_title' => __('location', 'kkl-ligatool'),
      'page_slug' => 'kkl_locations_admin_page',
      'parent' => null
    );
  }

  function display_content() {

    $location = $this->get_item();

    echo $this->form_table(
      array(
        array(
          'type' => 'hidden',
          'name' => 'id',
          'value' => $location->getId()
        ),
        array(
          'title' => __('name', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'title',
          'value' => ($this->errors) ? $_POST['title'] : $location->getTitle(),
          'extra' => ($this->errors['title']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('address', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'description',
          'value' => $location->getDescription()
        ),
        array(
          'title' => __('latitude', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'lat',
          'value' => ($this->errors) ? $_POST['lat'] : $location->getLatitude(),
          'extra' => ($this->errors['lat']) ? array('style' => "border-color: red;") : array()
        ),
        array(
          'title' => __('longitude', 'kkl-ligatool'),
          'type' => 'text',
          'name' => 'lng',
          'value' => ($this->errors) ? $_POST['lng'] : $location->getLongitude(),
          'extra' => ($this->errors['lng']) ? array('style' => "border-color: red;") : array()
        )
      )
    );
  }

  /**
   * @return Location|null
   */
  function get_item() {
    if ($this->item)
      return $this->item;
    if ($_GET['id']) {
      $locationService = ServiceBroker::getLocationService();
      $this->setItem($locationService->byId($_GET['id']));
    } else {
      $this->setItem(new Location());
    }
    return $this->item;
  }

  function validate($new_data, $old_data) {
    $errors = array();

    if (!$new_data['title'])
      $errors['title'] = true;
    if (!$new_data['lat'])
      $errors['lat'] = true;
    if (!$new_data['lng'])
      $errors['lng'] = true;

    return $errors;
  }

  function save() {

    $service = ServiceBroker::getLocationService();
    $location = $service->getOrCreate($_POST['id']);
    $location->setTitle($_POST['title']);
    $location->setDescription($_POST['description']);
    $location->setLatitude($_POST['lat']);
    $location->setLongitude($_POST['lng']);

    return $service->createOrUpdate($location);

  }

}
