<?php

namespace KKL\Ligatool\Backend;

use scbAdminPage;

abstract class AdminPage extends scbAdminPage {
  
  protected $item;
  
  public function page_content() {
    if($this->errors) {
      print '<div id="message" class="error">' . __('save_error', 'kkl-ligatool') . '</div>';
    } elseif($this->success) {
      print '<div id="message" class="updated fade">' . __('save_success', 'kkl-ligatool') . '</div>';
    }
    $this->display_content();
    print '<script type="text/javascript">jQuery(".datetimepicker").datetimepicker({ "format":"Y-m-d H:i:s", lang:"de", "step": 15});</script>';
  }
  
  public abstract function display_content();
  
  function form_handler() {
    if($_POST['submit']) {
      $errors = $this->validate($_POST, $this->get_item());
      if(empty($errors)) {
        $this->setItem($this->save());
        $this->success = true;
        $this->redirect_after_save();
      } else {
        $this->errors = $errors;
      }
    }
  }
  
  public function validate($new_data, $old_data) {
    return array();
  }
  
  public abstract function get_item();
  
  function getItem() {
    return $this->item;
  }
  
  function setItem($item) {
    $this->item = $item;
  }
  
  public function save() {
    return true;
  }
  
  function redirect_after_save() {
    return false;
  }
  
  function cleanDate($dateString) {
    $date = $dateString;
    if($dateString == '0000-00-00 00:00:00') {
      $date = null;
    }
    return $date;
  }
  
}
