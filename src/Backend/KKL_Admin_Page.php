<?php
namespace KKL\Ligatool\Backend;

use scbAdminPage;

abstract class KKL_Admin_Page extends scbAdminPage {

	protected $item;

	public abstract function get_item();
    public abstract function display_content();

	public function validate($new_data, $old_data) {
		return array();
	}

	public function save() {
		return true;
	}

	public function page_content() {
		if($this->errors) {
			print '<div id="message" class="error">' . __('save_error', 'kkl-ligatool') . '</div>' ;
		}else if($this->success) {
			print '<div id="message" class="updated fade">' . __('save_success', 'kkl-ligatool') . '</div>' ;
		}
        $this->display_content();
        print '<script type="text/javascript">jQuery(".datetimepicker").datetimepicker({ "format":"Y-m-d H:i:s", lang:"de", "step": 15});</script>';
	}

    function form_handler() {
        if($_POST['submit']) {
        	$errors = $this->validate($_POST, $this->get_item());
            if(empty($errors)) {
                $this->setItem($this->save());
                $this->success = true;
                $this->redirect_after_save();
            }else{
            	$this->errors = $errors;
            }
        }
    }

    function redirect_after_save() {
        return false;
    }

    function setItem($item) {
    	$this->item = $item;
    }

    function getItem() {
    	return $this->item;
    }

    function cleanDate($dateString) {
	$date = $dateString;
	if($dateString == '0000-00-00 00:00:00') {
		$date = null;
	}
	return $date;
    }

}
