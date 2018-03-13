<?php

namespace KKL\Ligatool\Backend;

use KKL\Ligatool\DB;
use stdClass;

class LocationAdminPage extends AdminPage {
  
  private $team;
  
  function setup() {
    $this->args = array('page_title' => __('location', 'kkl-ligatool'), 'page_slug' => 'kkl_locations_admin_page', 'parent' => null);
  }
  
  function display_content() {
    
    $location = $this->get_item();
    
    echo $this->form_table(array(array('type' => 'hidden', 'name' => 'id', 'value' => $location->id), array('title' => __('name', 'kkl-ligatool'), 'type' => 'text', 'name' => 'title', 'value' => ($this->errors) ? $_POST['title'] : $location->title, 'extra' => ($this->errors['title']) ? array('style' => "border-color: red;") : array()), array('title' => __('address', 'kkl-ligatool'), 'type' => 'text', 'name' => 'description', 'value' => $location->description), array('title' => __('latitude', 'kkl-ligatool'), 'type' => 'text', 'name' => 'lat', 'value' => ($this->errors) ? $_POST['lat'] : $location->lat, 'extra' => ($this->errors['lat']) ? array('style' => "border-color: red;") : array()), array('title' => __('longitude', 'kkl-ligatool'), 'type' => 'text', 'name' => 'lng', 'value' => ($this->errors) ? $_POST['lng'] : $location->lng, 'extra' => ($this->errors['lng']) ? array('style' => "border-color: red;") : array())));
  }
  
  function get_item() {
    if($this->item)
      return $this->item;
    if($_GET['id']) {
      $db = new DB\Wordpress();
      $this->setItem($db->getLocation($_GET['id']));
    }
    return $this->item;
  }
  
  function validate($new_data, $old_data) {
    $errors = array();
    
    if(!$new_data['title'])
      $errors['title'] = true;
    if(!$new_data['lat'])
      $errors['lat'] = true;
    if(!$new_data['lng'])
      $errors['lng'] = true;
    
    return $errors;
  }
  
  function save() {
    
    $location = new stdClass;
    $location->id = $_POST['id'];
    $location->title = $_POST['title'];
    $location->description = $_POST['description'];
    $location->lat = $_POST['lat'];
    $location->lng = $_POST['lng'];
    
    $db = new DB\Wordpress();
    return $db->createOrUpdateLocation($location);
    
  }
  
}
