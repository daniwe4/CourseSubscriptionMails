<?php
require_once(__DIR__ . "/../interfaces/MailTemplate.php");
/**
 * Mock for MailTemplate
 */
class mockMailTemplate implements MailTemplate {
	public $usr_id = null;
	public $event_name = null;
	public $crs_id = null;

	public function __construct($usr_id, $event_name, $crs_id) {
		$this->usr_id = $usr_id;
		$this->event_name = $event_name;
		$this->crs_id = $crs_id;
	}

	public function usr_id() {
		return $this->usr_id;
	}

	public function event_name() {
		return $this->event_name;
	}

	public function crs_id() {
		return $this->crs_id;
	}

	/**
	 * @inerhitdoc
	 */
	public function getMailFor($event_name, $user_id, $crs_id) {
		response = array($event_name, $user_id, $crs_id);
		return response;
	}
}