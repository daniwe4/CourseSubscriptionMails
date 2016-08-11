<?php
require_once(__DIR__ . "/../interfaces/MailSender.php");
/**
 * Mock for MailSender
 */
class mockMailSender implements MailSender {
	public $usr_id = null;
	public $subject = null;
	public $message = null;
	public $response = array();

	public function __construct($usr_id, $subject, $message) {
		$this->usr_id = $usr_id;
		$this->subject = $subject;
		$this->message = $message;
	}

	public function usr_id() {
		return $this->usr_id;
	}

	public function subject() {
		return $this->subject;
	}

	public function message() {
		return $this->message;
	}

	public function response() {
		return implode(' ', $response);
	}

	/**
	 * @inerhitdoc
	 */
	public function sendMail($usr_id, $subject, $message) {
		$this->$response = [$usr_id, $subject, $message];
		return "return aus mockMailSender->sendMail()";
	}
}