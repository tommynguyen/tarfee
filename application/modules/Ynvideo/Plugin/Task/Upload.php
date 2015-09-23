<?php
class Ynvideo_Plugin_Task_Upload extends Core_Plugin_Task_Abstract {
	public function execute() {
		Engine_Api::_() -> ynvideo() -> uploadVideosChannel();
	}
}