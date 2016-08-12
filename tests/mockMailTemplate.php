<?php
require_once(__DIR__ . "/../interfaces/MailTemplate.php");

use CaT\Plugins\CourseSubscriptionMails as Mails;

/**
 * Mock for MailTemplate
 */
class mockMailTemplate implements Mails\interfaces\MailTemplate {
	public $event_name;
	public $usr_id;
	public $crs_id;
	public $return_msg = "test_ok";

	/**
	 * @inerhitdoc
	 */
	public function getMailFor($event_name, $usr_id, $crs_id) {
		$this->event_name = $event_name;
		$this->usr_id = $usr_id;
		$this->crs_id = $crs_id;


		return $this->return_msg;
	}
}