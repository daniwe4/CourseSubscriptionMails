<?php
require_once(__DIR__ . "/../interfaces/MailSender.php");

use CaT\Plugins\CourseSubscriptionMails as Mails;

/**
 * Mock for MailSender
 */
class mockMailSender implements Mails\interfaces\MailSender {
	public $message = "";
	public $subject = "";
	public $usr_id = "";
	/**
	 * @inerhitdoc
	 */
	public function sendMail($usr_id, $subject, $message) {
		$this->message = $message;
		$this->subject = $subject;
		$this->usr_id = $usr_id;
	}
}